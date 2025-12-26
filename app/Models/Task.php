<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "type",
        "spice_rating",
        "description",
        "tags_to_remove",
        "draft",
        "user_id",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "draft" => "boolean",
        "spice_rating" => "integer",
        "tags_to_remove" => "array",
    ];

    /**
     * The possible task types.
     */
    const TYPE_TRUTH = "truth";
    const TYPE_DARE = "dare";

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-set user_id when creating a task
        static::creating(function ($task) {
            if (auth()->check() && empty($task->user_id)) {
                $task->user_id = auth()->id();
            }
        });
    }

    /**
     * Get the user that created this task.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the available task types.
     *
     * @return array
     */
    public static function getTypes()
    {
        return [self::TYPE_TRUTH, self::TYPE_DARE];
    }

    /**
     * Check if the task is a truth.
     *
     * @return bool
     */
    public function isTruth()
    {
        return $this->type === self::TYPE_TRUTH;
    }

    /**
     * Check if the task is a dare.
     *
     * @return bool
     */
    public function isDare()
    {
        return $this->type === self::TYPE_DARE;
    }

    /**
     * Check if the task is a draft.
     *
     * @return bool
     */
    public function isDraft()
    {
        return $this->draft;
    }

    /**
     * Scope a query to only include published tasks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where("draft", false);
    }

    /**
     * Scope a query to only include draft tasks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDrafts($query)
    {
        return $query->where("draft", true);
    }

    /**
     * Scope a query to only include truth tasks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTruths($query)
    {
        return $query->where("type", self::TYPE_TRUTH);
    }

    /**
     * Scope a query to only include dare tasks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDares($query)
    {
        return $query->where("type", self::TYPE_DARE);
    }

    /**
     * Scope a query to filter by spice rating.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $minRating
     * @param  int|null  $maxRating
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBySpiceLevel($query, $minRating, $maxRating = null)
    {
        $query->where("spice_rating", ">=", $minRating);

        if ($maxRating !== null) {
            $query->where("spice_rating", "<=", $maxRating);
        }

        return $query;
    }

    /**
     * Get the spice level as a descriptive string.
     *
     * @return string
     */
    public function getSpiceLevelAttribute()
    {
        return match ($this->spice_rating) {
            1 => "Mild",
            2 => "Medium",
            3 => "Hot",
            4 => "Extra Hot",
            5 => "Extreme",
            default => "Unknown",
        };
    }

    /**
     * Get the tags for this task.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, "task_tag");
    }

    /**
     * Scope a query to only include tasks that have specific tags.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array|int  $tagIds
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithTags($query, $tagIds)
    {
        return $query->whereHas("tags", function ($q) use ($tagIds) {
            $q->whereIn("tags.id", (array) $tagIds);
        });
    }

    /**
     * Scope a query to only include tasks that have all specified tags.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $tagIds
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAllTags($query, $tagIds)
    {
        foreach ((array) $tagIds as $tagId) {
            $query->whereHas("tags", function ($q) use ($tagId) {
                $q->where("tags.id", $tagId);
            });
        }
        return $query;
    }

    /**
     * Remove tags from a player when this task is completed.
     *
     * @param  Player  $player
     * @return array Array of removed tag IDs
     */
    public function removeTagsFromPlayer(Player $player)
    {
        if (empty($this->tags_to_remove) || !is_array($this->tags_to_remove)) {
            return [];
        }

        $removedTags = [];
        foreach ($this->tags_to_remove as $tagId) {
            if ($player->tags()->where("tag_id", $tagId)->exists()) {
                $player->tags()->detach($tagId);
                $removedTags[] = $tagId;
            }
        }

        return $removedTags;
    }

    /**
     * Get the tags that will be removed when this task is completed.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRemovableTags()
    {
        if (empty($this->tags_to_remove) || !is_array($this->tags_to_remove)) {
            return collect([]);
        }

        return Tag::whereIn("id", $this->tags_to_remove)->get();
    }
}
