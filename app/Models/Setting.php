<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'site_name', 'site_description', 'default_og_image', 'contact_email', 'admin_email',
        'auto_approve_posts', 'auto_approve_comments', 'github_url', 'linkedin_url', 'fiverr_url', 'upwork_url',
        'telegram_enabled', 'telegram_chat_id', 'telegram_delay_minutes', 'telegram_include_excerpt',
        'telegram_include_hashtags', 'telegram_channel_url', 'whatsapp_channel_url',
        'email_logging_enabled',
    ];

    protected $casts = [
        'auto_approve_posts' => 'boolean',
        'auto_approve_comments' => 'boolean',
        'telegram_enabled' => 'boolean',
        'telegram_include_excerpt' => 'boolean',
        'telegram_include_hashtags' => 'boolean',
        'email_logging_enabled' => 'boolean',
    ];

    /**
     * Where contact and demo-request notifications go: the admin email from Settings, else the mail config default.
     */
    public static function adminRecipient(): ?string
    {
        return self::current()->admin_email ?: config('mail.contact_recipient');
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate(['id' => 1], [
            'site_name' => config('app.name'),
            'auto_approve_posts' => true,
            'auto_approve_comments' => true,
            'telegram_enabled' => true,
            'telegram_delay_minutes' => 0,
            'telegram_include_excerpt' => true,
            'telegram_include_hashtags' => true,
            'email_logging_enabled' => true,
        ]);
    }
}
