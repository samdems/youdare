<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "name",
        "slug",
        "description",
        "is_default",
        "default_for_gender",
        "min_spice_level",
        "user_id",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "is_default" => "boolean",
        "default_for_gender" => "string",
        "min_spice_level" => "integer",
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from name if not provided
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }

            // Auto-set user_id when creating a tag
            if (auth()->check() && empty($tag->user_id)) {
                $tag->user_id = auth()->id();
            }
        });
    }

    /**
     * Get the user that created this tag.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tasks that have this tag.
     */
    public function tasks()
    {
        return $this->belongsToMany(Task::class, "task_tag");
    }
}
