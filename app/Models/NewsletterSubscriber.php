<?php

namespace App\Models;

use App\Enums\NewsletterFrequency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'frequency', 'unsubscribed_at', 'verified_at'];

    protected $casts = [
        'frequency' => NewsletterFrequency::class,
        'unsubscribed_at' => 'datetime',
        'verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'otp_sent_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];

    protected $attributes = [
        'frequency' => 'weekly',
    ];

    protected static function booted(): void
    {
        static::creating(function (NewsletterSubscriber $subscriber): void {
            $subscriber->token ??= Str::random(48);
        });
    }

    public function scopeActive(Builder $query): void
    {
        $query->whereNull('unsubscribed_at')->whereNotNull('verified_at');
    }

    public function isSubscribed(): bool
    {
        return $this->unsubscribed_at === null;
    }

    public function preferencesUrl(): string
    {
        return route('newsletter.preferences', $this->token);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Issues a fresh 6-digit code and returns it; only its hash is stored.
     */
    public function issueOtp(): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->forceFill([
            'otp_hash' => Hash::make($code),
            'otp_expires_at' => now()->addMinutes(10),
            'otp_sent_at' => now(),
            'otp_attempts' => 0,
        ])->save();

        return $code;
    }

    public function otpCooldownActive(): bool
    {
        return $this->otp_sent_at !== null && $this->otp_sent_at->gt(now()->subSeconds(60));
    }

    /**
     * Checks a submitted code. A wrong guess counts; five wrong guesses burn the code.
     */
    public function attemptOtp(string $code): bool
    {
        if ($this->otp_hash === null || $this->otp_expires_at === null || $this->otp_expires_at->isPast() || $this->otp_attempts >= 5) {
            return false;
        }

        if (! Hash::check($code, $this->otp_hash)) {
            $this->increment('otp_attempts');

            return false;
        }

        $this->forceFill([
            'verified_at' => now(),
            'unsubscribed_at' => null,
            'otp_hash' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
        ])->save();

        return true;
    }
}
