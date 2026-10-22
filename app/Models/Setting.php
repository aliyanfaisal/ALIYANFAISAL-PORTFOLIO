<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'site_name', 'site_description', 'default_og_image', 'contact_email',
        'auto_approve_posts', 'auto_approve_comments', 'github_url', 'linkedin_url', 'fiverr_url', 'upwork_url',
        'telegram_enabled', 'telegram_chat_id', 'telegram_delay_minutes', 'telegram_include_excerpt',
        'telegram_include_hashtags', 'telegram_channel_url', 'whatsapp_channel_url',
    ];

    protected $casts = [
        'auto_approve_posts' => 'boolean',
        'auto_approve_comments' => 'boolean',
        'telegram_enabled' => 'boolean',
        'telegram_include_excerpt' => 'boolean',
        'telegram_include_hashtags' => 'boolean',
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate(['id' => 1], [
            'site_name' => config('app.name'),
            'auto_approve_posts' => true,
            'auto_approve_comments' => true,
        ]);
    }
}
