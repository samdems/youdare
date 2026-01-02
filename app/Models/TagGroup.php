<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TagGroup extends Model
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
        "sort_order",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "sort_order" => "integer",
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from name if not provided
        static::creating(function ($tagGroup) {
            if (empty($tagGroup->slug)) {
                $tagGroup->slug = Str::slug($tagGroup->name);
            }
        });
    }

    /**
     * Get the tags that belong to this group.
     */
    public function tags()
    {
        return $this->hasMany(Tag::class)->orderBy("name");
    }
}
