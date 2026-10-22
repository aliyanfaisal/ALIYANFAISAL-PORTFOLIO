<?php

namespace App\Providers;

use App\Listeners\EmailLogger;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(MessageSending::class, [EmailLogger::class, 'sending']);
        Event::listen(MessageSent::class, [EmailLogger::class, 'sent']);
    }
}
