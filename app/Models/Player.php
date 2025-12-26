<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "game_id",
        "name",
        "gender",
        "score",
        "is_active",
        "order",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "is_active" => "boolean",
        "score" => "integer",
        "order" => "integer",
    ];

    /**
     * Get the game this player belongs to.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the tags specific to this player.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, "player_tag");
    }

    /**
     * Get all tags available for this player (game tags + player tags).
     * Filters out tags that require a higher spice level than the game's max.
     */
    public function getAllAvailableTags()
    {
        $maxSpiceRating = $this->game->max_spice_rating;

        // Get game tags filtered by spice level
        $gameTags = $this->game
            ->tags()
            ->where("tags.min_spice_level", "<=", $maxSpiceRating)
            ->pluck("tags.id")
            ->toArray();

        // Get player tags filtered by spice level
        $playerTags = $this->tags()
            ->where("tags.min_spice_level", "<=", $maxSpiceRating)
            ->pluck("tags.id")
            ->toArray();

        return array_unique(array_merge($gameTags, $playerTags));
    }

    /**
     * Get tasks available for this player based on game and player tags.
     */
    public function getAvailableTasks()
    {
        $tagIds = $this->getAllAvailableTags();

        return Task::published()
            ->when(count($tagIds) > 0, function ($query) use ($tagIds) {
                $query->whereHas("tags", function ($q) use ($tagIds) {
                    $q->whereIn("tags.id", $tagIds);
                });
            })
            ->get();
    }

    /**
     * Get a random task for this player.
     */
    public function getRandomTask($type = null)
    {
        $tagIds = $this->getAllAvailableTags();

        $query = Task::published()->with("tags");

        if ($type) {
            $query->where("type", $type);
        }

        if (count($tagIds) > 0) {
            $query->whereHas("tags", function ($q) use ($tagIds) {
                $q->whereIn("tags.id", $tagIds);
            });
        }

        return $query->inRandomOrder()->first();
    }

    /**
     * Increment the player's score.
     */
    public function incrementScore($points = 1)
    {
        $this->increment("score", $points);
    }

    /**
     * Deactivate the player.
     */
    public function deactivate()
    {
        $this->update(["is_active" => false]);
    }

    /**
     * Activate the player.
     */
    public function activate()
    {
        $this->update(["is_active" => true]);
    }

    /**
     * Scope a query to only include active players.
     */
    public function scopeActive($query)
    {
        return $query->where("is_active", true);
    }
}
