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
        Schema::table('plan_application_markups', function (Blueprint $table) {
            // Which drawing version this pin/line was placed on. A markup
            // only makes sense against the drawing it was drawn on — once
            // a new version is uploaded, old markups stay in the database
            // as a record but stop showing on the (now different) drawing.
            $table->unsignedInteger('drawing_version')->default(1)->after('page');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_application_markups', function (Blueprint $table) {
            $table->dropColumn('drawing_version');
        });
    }
};
