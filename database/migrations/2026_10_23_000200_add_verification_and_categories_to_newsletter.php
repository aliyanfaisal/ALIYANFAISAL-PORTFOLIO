<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->timestamp('verified_at')->nullable()->after('token');
            $table->string('otp_hash')->nullable()->after('verified_at');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_hash');
            $table->timestamp('otp_sent_at')->nullable()->after('otp_expires_at');
            $table->unsignedTinyInteger('otp_attempts')->default(0)->after('otp_sent_at');
        });

        // Everyone who signed up before verification existed is treated as already confirmed.
        DB::table('newsletter_subscribers')->whereNull('verified_at')->update(['verified_at' => DB::raw('created_at')]);

        // No rows for a subscriber means "all categories".
        Schema::create('category_newsletter_subscriber', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_subscriber_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            $table->unique(['newsletter_subscriber_id', 'category_id'], 'category_subscriber_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_newsletter_subscriber');

        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->dropColumn(['verified_at', 'otp_hash', 'otp_expires_at', 'otp_sent_at', 'otp_attempts']);
        });
    }
};
