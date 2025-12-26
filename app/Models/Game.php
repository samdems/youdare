<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Game extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "name",
        "code",
        "status",
        "max_spice_rating",
        "settings",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "settings" => "array",
        "max_spice_rating" => "integer",
    ];

    /**
     * The possible game statuses.
     */
    const STATUS_WAITING = "waiting";
    const STATUS_ACTIVE = "active";
    const STATUS_COMPLETED = "completed";

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate game code if not provided
        static::creating(function ($game) {
            if (empty($game->code)) {
                $game->code = strtoupper(Str::random(6));
            }
        });
    }

    /**
     * Get the players for this game.
     */
    public function players()
    {
        return $this->hasMany(Player::class)->orderBy("order");
    }

    /**
     * Get the tags that are in play for this game.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, "game_tag");
    }

    /**
     * Get all available tags for this game (game tags + player tags).
     */
    public function getAllActiveTags()
    {
        $gameTags = $this->tags()->pluck("tags.id")->toArray();
        $playerTags = $this->players()
            ->with("tags")
            ->get()
            ->pluck("tags")
            ->flatten()
            ->pluck("id")
            ->unique()
            ->toArray();

        return array_unique(array_merge($gameTags, $playerTags));
    }

    /**
     * Get tasks that match the game's tags and max spice rating.
     */
    public function getAvailableTasks()
    {
        $tagIds = $this->getAllActiveTags();

        return Task::published()
            ->where("spice_rating", "<=", $this->max_spice_rating)
            ->when(count($tagIds) > 0, function ($query) use ($tagIds) {
                $query->whereHas("tags", function ($q) use ($tagIds) {
                    $q->whereIn("tags.id", $tagIds);
                });
            })
            ->get();
    }

    /**
     * Start the game.
     */
    public function start()
    {
        $this->update(["status" => self::STATUS_ACTIVE]);
    }

    /**
     * Complete the game.
     */
    public function complete()
    {
        $this->update(["status" => self::STATUS_COMPLETED]);
    }

    /**
     * Check if the game is waiting.
     */
    public function isWaiting()
    {
        return $this->status === self::STATUS_WAITING;
    }

    /**
     * Check if the game is active.
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the game is completed.
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Scope a query to only include active games.
     */
    public function scopeActive($query)
    {
        return $query->where("status", self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include waiting games.
     */
    public function scopeWaiting($query)
    {
        return $query->where("status", self::STATUS_WAITING);
    }

    /**
     * Find a game by its code.
     */
    public static function findByCode($code)
    {
        return static::where("code", strtoupper($code))->first();
    }
}
