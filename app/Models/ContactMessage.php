<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class ContactMessage extends Model
{
    protected $fillable = [
        'name', 'email', 'subject', 'message',
        'ip_address', 'country', 'city', 'user_agent', 'page_url', 'read_at',
    ];

    public function replies(): HasMany
    {
        return $this->hasMany(ContactMessageReply::class)->latest();
    }

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    /**
     * Where the sender came from. The country and city only exist when the host or a CDN (e.g. Cloudflare)
     * adds them as request headers; no third-party geolocation lookup is made.
     *
     * @return array<string, string|null>
     */
    public static function senderDetails(Request $request): array
    {
        $country = strtoupper((string) $request->header('CF-IPCountry', ''));

        return [
            'ip_address' => $request->ip(),
            'country' => preg_match('/^[A-Z]{2}$/', $country) && ! in_array($country, ['XX', 'T1'], true) ? $country : null,
            'city' => $request->header('CF-IPCity') ? mb_substr(urldecode($request->header('CF-IPCity')), 0, 255) : null,
            'user_agent' => $request->userAgent() ? mb_substr($request->userAgent(), 0, 1000) : null,
            'page_url' => mb_substr((string) $request->header('referer', ''), 0, 500) ?: null,
        ];
    }

    public function countryName(): ?string
    {
        if (! $this->country) {
            return null;
        }

        return class_exists(\Locale::class)
            ? (\Locale::getDisplayRegion('-'.$this->country, 'en') ?: $this->country)
            : $this->country;
    }
}
