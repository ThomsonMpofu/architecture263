<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Blue Book (ACZ Conditions of Engagement & Scale of Fees) is a
 * standing credential the architect purchases and admin approves once —
 * not something bought per engagement/project. This column never belonged
 * on the engagement.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('engagements', function (Blueprint $table) {
            $table->dropColumn('blue_book_purchased_at');
        });
    }

    public function down(): void
    {
        Schema::table('engagements', function (Blueprint $table) {
            $table->timestamp('blue_book_purchased_at')->nullable();
        });
    }
};
