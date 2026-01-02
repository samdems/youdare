<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PlayerGroup extends Model
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
        "color",
        "icon",
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
        static::creating(function ($playerGroup) {
            if (empty($playerGroup->slug)) {
                $playerGroup->slug = Str::slug($playerGroup->name);
            }
        });
    }

    /**
     * Get the players that belong to this group.
     */
    public function players()
    {
        return $this->hasMany(Player::class)->orderBy("name");
    }
}
