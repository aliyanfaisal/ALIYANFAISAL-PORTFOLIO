<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->text('to');
            $table->string('subject')->nullable();
            $table->string('status', 20)->default('sending')->index();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('email_logging_enabled')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('email_logging_enabled');
        });

        Schema::dropIfExists('email_logs');
    }
};
