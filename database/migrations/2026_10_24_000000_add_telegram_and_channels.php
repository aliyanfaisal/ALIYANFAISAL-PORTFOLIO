<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('telegram_enabled')->default(true);
            $table->string('telegram_chat_id')->nullable();
            $table->unsignedSmallInteger('telegram_delay_minutes')->default(0);
            $table->boolean('telegram_include_excerpt')->default(true);
            $table->boolean('telegram_include_hashtags')->default(true);
            $table->string('telegram_channel_url')->nullable();
            $table->string('whatsapp_channel_url')->nullable();
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->timestamp('telegram_posted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn('telegram_posted_at');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'telegram_enabled', 'telegram_chat_id', 'telegram_delay_minutes', 'telegram_include_excerpt',
                'telegram_include_hashtags', 'telegram_channel_url', 'whatsapp_channel_url',
            ]);
        });
    }
};
