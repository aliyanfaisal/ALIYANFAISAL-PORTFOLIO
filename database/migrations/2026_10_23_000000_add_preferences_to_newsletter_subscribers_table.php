<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->string('frequency', 20)->default('weekly')->after('email');
            $table->string('token', 64)->nullable()->unique()->after('frequency');
            $table->timestamp('unsubscribed_at')->nullable()->after('token');
            $table->timestamp('last_sent_at')->nullable()->after('unsubscribed_at');
        });

        DB::table('newsletter_subscribers')->whereNull('token')->orderBy('id')->each(
            fn ($row) => DB::table('newsletter_subscribers')->where('id', $row->id)->update(['token' => Str::random(48)])
        );
    }

    public function down(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->dropUnique(['token']);
            $table->dropColumn(['frequency', 'token', 'unsubscribed_at', 'last_sent_at']);
        });
    }
};
