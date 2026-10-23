<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->after('message');
            $table->string('country', 2)->nullable()->after('ip_address');
            $table->string('city')->nullable()->after('country');
            $table->text('user_agent')->nullable()->after('city');
            $table->string('page_url', 500)->nullable()->after('user_agent');
            $table->timestamp('read_at')->nullable()->after('page_url');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'country', 'city', 'user_agent', 'page_url', 'read_at']);
        });
    }
};
