<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PromoCode;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class StripeController extends Controller
{
    public function __construct()
    {
        // Set Stripe API key
        Stripe::setApiKey(config("services.stripe.secret"));
    }

    /**
     * Show the Go Pro page
     *
     * @return RedirectResponse|View
     */
    public function showGoPro(): RedirectResponse|View
    {
        /** @var User|null $user */
        $user = Auth::user();

        // If already pro, redirect to game
        if ($user && $user->isPro()) {
            return redirect()
                ->route("game")
                ->with("success", "You already have a Pro account!");
        }

        $amount = env("STRIPE_PRO_AMOUNT", 199); // Default to $9.99

        return view("stripe.go-pro", compact("amount"));
    }

    /**
     * Validate promo code via API
     *
     * @return JsonResponse
     */
    public function validatePromoCode(Request $request): JsonResponse
    {
        $code = $request->input("code");

        if (!$code) {
            return response()->json([
                "valid" => false,
                "message" => "Please enter a promo code",
            ]);
        }

        // Convert to uppercase for case-insensitive matching
        $code = strtoupper($code);

        $promoCode = PromoCode::where("code", $code)
            ->where("is_active", true)
            ->first();

        if (!$promoCode) {
            return response()->json([
                "valid" => false,
                "message" => "Invalid or inactive promo code",
            ]);
        }

        return response()->json([
            "valid" => true,
            "code" => $promoCode->code,
            "percent_off" => $promoCode->percent_off,
        ]);
    }

    /**
     * Create Stripe Checkout Session
     *
     * @return RedirectResponse
     */
    public function createCheckoutSession(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()
                ->route("login")
                ->with("error", "Please log in to upgrade to Pro.");
        }

        if ($user->isPro()) {
            return redirect()
                ->route("game")
                ->with("success", "You already have a Pro account!");
        }

        try {
            $amount = (int) env("STRIPE_PRO_AMOUNT", 199); // Amount in cents

            // Check for promo code
            $promoCode = $request->input("promo_code");
            $discount = 0;

            if ($promoCode) {
                // Convert to uppercase for case-insensitive matching
                $promoCode = strtoupper($promoCode);

                $promo = PromoCode::where("code", $promoCode)
                    ->where("is_active", true)
                    ->first();

                if ($promo) {
                    $discount = $promo->percent_off;
                    // Calculate discounted amount and round to integer
                    $amount = (int) round($amount * (1 - $discount / 100));

                    // Log for debugging
                    \Log::info("Promo code applied", [
                        "code" => $promoCode,
                        "discount_percent" => $discount,
                        "final_amount" => $amount,
                    ]);
                }
            }

            // Create or retrieve Stripe customer
            $customerId = $user->stripe_customer_id;

            if (!$customerId) {
                $customer = \Stripe\Customer::create([
                    "email" => $user->email,
                    "name" => $user->name,
                    "metadata" => [
                        "user_id" => $user->id,
                    ],
                ]);
                $customerId = $customer->id;
            }

            $session = StripeSession::create([
                "payment_method_types" => ["card"],
                "customer" => $customerId,
                "line_items" => [
                    [
                        "price_data" => [
                            "currency" => "usd",
                            "product_data" => [
                                "name" =>
                                    "YouDare Pro - Lifetime Access" .
                                    ($discount > 0
                                        ? " ({$discount}% discount applied)"
                                        : ""),
                                "description" =>
                                    "Unlock premium features with lifetime access to YouDare Pro!",
                            ],
                            "unit_amount" => (int) $amount,
                        ],
                        "quantity" => 1,
                    ],
                ],
                "mode" => "payment",
                "success_url" =>
                    route("stripe.success") .
                    "?session_id={CHECKOUT_SESSION_ID}",
                "cancel_url" => route("stripe.cancel"),
                "client_reference_id" => $user->id,
                "metadata" => [
                    "user_id" => $user->id,
                    "promo_code" => $promoCode ?? "",
                    "discount_percent" => $discount,
                ],
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            return redirect()
                ->route("stripe.go-pro")
                ->with(
                    "error",
                    "Failed to create checkout session: " . $e->getMessage(),
                );
        }
    }

    /**
     * Handle successful payment
     *
     * @return RedirectResponse|View
     */
    public function success(Request $request): RedirectResponse|View
    {
        $sessionId = $request->get("session_id");

        if (!$sessionId) {
            return redirect()->route("game")->with("error", "Invalid session.");
        }

        try {
            $session = StripeSession::retrieve($sessionId);

            if ($session->payment_status === "paid") {
                /** @var User|null $user */
                $user = Auth::user();

                if (!$user) {
                    return redirect()
                        ->route("login")
                        ->with("error", "Please log in.");
                }

                // Check if user is already pro
                if (!$user->isPro()) {
                    // Get customer ID - create if not exists
                    $customerId = $session->customer;

                    if (!$customerId) {
                        // Create a customer if Stripe didn't create one
                        $customer = \Stripe\Customer::create([
                            "email" => $user->email,
                            "name" => $user->name,
                            "metadata" => [
                                "user_id" => $user->id,
                            ],
                        ]);
                        $customerId = $customer->id;
                    }

                    // Activate pro account
                    $user->activatePro($customerId, $session->payment_intent);
                }

                return view("stripe.success", ["user" => $user]);
            }

            return redirect()
                ->route("stripe.go-pro")
                ->with("error", "Payment was not successful.");
        } catch (\Exception $e) {
            return redirect()
                ->route("stripe.go-pro")
                ->with(
                    "error",
                    "Failed to verify payment: " . $e->getMessage(),
                );
        }
    }

    /**
     * Handle cancelled payment
     *
     * @return View
     */
    public function cancel(): View
    {
        return view("stripe.cancel");
    }
}
