<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->enum('status', ['draft', 'published'])->default('published')->after('published_at');
        });

        DB::table('blog_posts')
            ->where(function ($query) {
                $query->whereNull('published_at')->orWhere('published_at', '>', now());
            })
            ->update(['status' => 'draft']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
