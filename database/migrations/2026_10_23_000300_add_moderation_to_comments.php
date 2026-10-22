<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->string('status', 20)->default('approved')->index()->after('body');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('auto_approve_comments')->default(true)->after('auto_approve_posts');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('auto_approve_comments');
        });
    }
};
