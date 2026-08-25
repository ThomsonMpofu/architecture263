<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('registration_no')->nullable()->after('username');
            $table->string('specialty')->nullable()->after('registration_no');
            $table->string('firm_name')->nullable()->after('specialty');
            $table->timestamp('approved_at')->nullable()->after('is_suspended');
            $table->boolean('subscription_active')->default(false)->after('approved_at');
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'registration_no',
                'specialty',
                'firm_name',
                'approved_at',
                'subscription_active',
                'subscription_expires_at',
            ]);
        });
    }
};
