<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promoCodes = [
            [
                "code" => "WELCOME20",
                "percent_off" => 20,
                "is_active" => true,
            ],
            [
                "code" => "SAVE50",
                "percent_off" => 50,
                "is_active" => true,
            ],
            [
                "code" => "NEWYEAR2024",
                "percent_off" => 30,
                "is_active" => false,
            ],
        ];

        foreach ($promoCodes as $promoCode) {
            PromoCode::create($promoCode);
        }
    }
}
