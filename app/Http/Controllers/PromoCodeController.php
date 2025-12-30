<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promoCodes = PromoCode::orderBy("created_at", "desc")->paginate(20);

        return view("promo-codes.index", compact("promoCodes"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("promo-codes.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "code" => "required|string|max:255|unique:promo_codes,code",
            "percent_off" => "required|integer|min:1|max:100",
        ]);

        // Convert code to uppercase
        $validated["code"] = strtoupper($validated["code"]);

        // Convert checkbox value to boolean (checkboxes only send value when checked)
        $validated["is_active"] = $request->has("is_active") ? true : false;

        $promoCode = PromoCode::create($validated);

        return redirect()
            ->route("promo-codes.index")
            ->with("success", "Promo code created successfully!");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PromoCode $promoCode)
    {
        return view("promo-codes.edit", compact("promoCode"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PromoCode $promoCode)
    {
        $validated = $request->validate([
            "code" =>
                "required|string|max:255|unique:promo_codes,code," .
                $promoCode->id,
            "percent_off" => "required|integer|min:1|max:100",
        ]);

        // Convert code to uppercase
        $validated["code"] = strtoupper($validated["code"]);

        // Convert checkbox value to boolean (checkboxes only send value when checked)
        $validated["is_active"] = $request->has("is_active") ? true : false;

        $promoCode->update($validated);

        return redirect()
            ->route("promo-codes.index")
            ->with("success", "Promo code updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PromoCode $promoCode)
    {
        $promoCode->delete();

        return redirect()
            ->route("promo-codes.index")
            ->with("success", "Promo code deleted successfully!");
    }
}
