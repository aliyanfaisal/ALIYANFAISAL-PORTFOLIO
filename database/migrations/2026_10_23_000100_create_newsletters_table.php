<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per scheduled run of a cadence, not per email.
        Schema::create('newsletters', function (Blueprint $table) {
            $table->id();
            $table->string('frequency', 20)->index();
            $table->date('run_date');
            $table->string('subject');
            $table->timestamp('window_start');
            $table->timestamp('window_end');
            $table->unsignedInteger('recipients_count')->default(0);
            $table->string('status', 20)->default('sending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['frequency', 'run_date']);
        });

        Schema::create('blog_post_newsletter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blog_post_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position')->default(0);

            $table->unique(['newsletter_id', 'blog_post_id']);
        });

        // The no-repeat guarantee: a subscriber can only ever receive a given post once.
        Schema::create('newsletter_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('newsletter_subscriber_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blog_post_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->unique(['newsletter_subscriber_id', 'blog_post_id'], 'newsletter_deliveries_subscriber_post_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_deliveries');
        Schema::dropIfExists('blog_post_newsletter');
        Schema::dropIfExists('newsletters');
    }
};
