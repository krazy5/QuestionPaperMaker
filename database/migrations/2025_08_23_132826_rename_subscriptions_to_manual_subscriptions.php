<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subscriptions') && ! Schema::hasTable('manual_subscriptions')) {
            Schema::rename('subscriptions', 'manual_subscriptions');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('manual_subscriptions') && ! Schema::hasTable('subscriptions')) {
            Schema::rename('manual_subscriptions', 'subscriptions');
        }
    }
};
