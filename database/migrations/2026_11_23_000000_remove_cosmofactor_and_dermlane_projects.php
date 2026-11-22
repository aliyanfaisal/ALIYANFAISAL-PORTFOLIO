<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('projects')
            ->whereIn('slug', ['cosmofactor', 'dermlane'])
            ->orWhere('external_url', 'like', '%cosmofactor.com%')
            ->orWhere('external_url', 'like', '%dermlane.com%')
            ->delete();
    }

    public function down(): void
    {
        //
    }
};
