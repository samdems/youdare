<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\MagicLinkNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view("auth.login");
    }

    /**
     * Handle magic link request (login or auto-register).
     */
    public function sendMagicLink(Request $request)
    {
        $validated = $request->validate([
            "name" => "nullable|string|max:255",
            "email" => "required|email|max:255",
        ]);

        $user = User::where("email", $validated["email"])->first();

        if (!$user) {
            // Auto-create account if it doesn't exist
            $defaultName = $validated["name"] ?? null;

            if (!$defaultName) {
                // Generate name from email: split on . and - and capitalize each word
                $emailPrefix = explode("@", $validated["email"])[0];
                $nameParts = preg_split("/[.\-_]/", $emailPrefix);
                $defaultName = implode(" ", array_map("ucfirst", $nameParts));
            }

            $user = User::create([
                "name" => $defaultName,
                "email" => $validated["email"],
            ]);
        }

        $this->generateAndSendMagicLink($user);

        return back()->with(
            "success",
            "Check your email for a magic login link!",
        );
    }

    /**
     * Verify magic link and log in user.
     */
    public function verifyMagicLink(Request $request, string $token)
    {
        $user = User::where("login_token", $token)
            ->where("login_token_expires_at", ">", now())
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                "token" =>
                    "This login link is invalid or has expired. Please request a new one.",
            ]);
        }

        // Clear the token
        $user->update([
            "login_token" => null,
            "login_token_expires_at" => null,
        ]);

        // Log in the user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->intended(route("game"))
            ->with("success", "Welcome back, " . $user->name . "!");
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route("login")
            ->with("success", "You have been logged out successfully.");
    }

    /**
     * Generate and send a magic link to the user.
     */
    protected function generateAndSendMagicLink(User $user): void
    {
        // Generate a secure random token
        $token = Str::random(64);

        // Save token and expiration (15 minutes)
        $user->update([
            "login_token" => $token,
            "login_token_expires_at" => now()->addMinutes(15),
        ]);

        // Generate the login URL
        $loginUrl = route("magic-link.verify", ["token" => $token]);

        // Send the notification
        $user->notify(new MagicLinkNotification($loginUrl));
    }
}
