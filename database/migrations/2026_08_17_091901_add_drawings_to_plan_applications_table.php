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
        Schema::table('plan_applications', function (Blueprint $table) {
            $table->string('drawings_path')->nullable()->after('fire_fighting_equipment');
            $table->string('drawings_original_name')->nullable()->after('drawings_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_applications', function (Blueprint $table) {
            $table->dropColumn(['drawings_path', 'drawings_original_name']);
        });
    }
};
