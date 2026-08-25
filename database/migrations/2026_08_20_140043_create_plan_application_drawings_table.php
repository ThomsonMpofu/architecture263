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
        Schema::create('plan_application_drawings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('plan_application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();

            // Sequential per plan application (1, 2, 3, ...) — every upload
            // is kept as a new version rather than overwriting the last
            // one, so there's always a record of what was submitted when.
            $table->unsignedInteger('version');

            $table->string('path');
            $table->string('original_name');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_application_drawings');
    }
};
