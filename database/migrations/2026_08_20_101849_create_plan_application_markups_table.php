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
        Schema::create('plan_application_markups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('plan_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 'pin' = single point marker, 'line' = start/end point marker.
            $table->string('type');

            // Drawing page the markup belongs to (page 1 unless a multi-page PDF).
            $table->unsignedInteger('page')->default(1);

            // Coordinates are stored as fractions (0-1) of the rendered page
            // width/height, so markups stay correctly positioned regardless
            // of viewport size or zoom level.
            $table->decimal('x', 8, 5);
            $table->decimal('y', 8, 5);
            $table->decimal('x2', 8, 5)->nullable();
            $table->decimal('y2', 8, 5)->nullable();

            $table->text('comment')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_application_markups');
    }
};
