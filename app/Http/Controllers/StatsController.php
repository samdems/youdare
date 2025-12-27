<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Contracts\View\View;

class StatsController extends Controller
{
    /**
     * Display statistics about tasks.
     *
     * @return View
     */
    public function index(): View
    {
        // Get counts for each spice level and type
        $stats = [];

        for ($spiceLevel = 1; $spiceLevel <= 5; $spiceLevel++) {
            $stats[$spiceLevel] = [
                "spice_level" => $spiceLevel,
                "spice_name" => $this->getSpiceName($spiceLevel),
                "truths" => Task::query()
                    ->where("type", Task::TYPE_TRUTH)
                    ->where("spice_rating", $spiceLevel)
                    ->where("draft", false)
                    ->count(),
                "dares" => Task::query()
                    ->where("type", Task::TYPE_DARE)
                    ->where("spice_rating", $spiceLevel)
                    ->where("draft", false)
                    ->count(),
                "total" => Task::query()
                    ->where("spice_rating", $spiceLevel)
                    ->where("draft", false)
                    ->count(),
            ];
        }

        // Calculate totals
        $totalTruths = Task::query()
            ->where("type", Task::TYPE_TRUTH)
            ->where("draft", false)
            ->count();

        $totalDares = Task::query()
            ->where("type", Task::TYPE_DARE)
            ->where("draft", false)
            ->count();

        $grandTotal = Task::query()->where("draft", false)->count();

        $draftCount = Task::query()->where("draft", true)->count();

        return view(
            "stats.index",
            compact(
                "stats",
                "totalTruths",
                "totalDares",
                "grandTotal",
                "draftCount",
            ),
        );
    }

    /**
     * Get the descriptive name for a spice level.
     *
     * @param  int  $level
     * @return string
     */
    private function getSpiceName(int $level): string
    {
        return match ($level) {
            1 => "Mild",
            2 => "Medium",
            3 => "Hot",
            4 => "Extra Hot",
            5 => "Extreme",
            default => "Unknown",
        };
    }
}
