<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "name",
        "email",
        "is_admin",
        "is_pro",
        "pro_expires_at",
        "stripe_customer_id",
        "stripe_payment_intent_id",
        "login_token",
        "login_token_expires_at",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ["remember_token", "login_token"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "is_admin" => "boolean",
            "is_pro" => "boolean",
            "pro_expires_at" => "datetime",
            "login_token_expires_at" => "datetime",
        ];
    }

    /**
     * Get the tasks created by the user.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the tags created by the user.
     */
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Check if the user has an active pro account.
     */
    public function isPro(): bool
    {
        if (!$this->is_pro) {
            return false;
        }

        // If pro_expires_at is null, it's lifetime pro
        if ($this->pro_expires_at === null) {
            return true;
        }

        // Check if pro subscription hasn't expired
        return $this->pro_expires_at->isFuture();
    }

    /**
     * Activate pro account.
     */
    public function activatePro(
        ?string $stripeCustomerId,
        ?string $stripePaymentIntentId,
    ): void {
        $this->is_pro = true;
        // Set expiration to null for lifetime pro
        $this->pro_expires_at = null;
        $this->stripe_customer_id = $stripeCustomerId;
        $this->stripe_payment_intent_id = $stripePaymentIntentId;
        $this->save();
    }
}
