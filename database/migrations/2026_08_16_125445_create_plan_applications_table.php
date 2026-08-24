<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('engagement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('council_reviewer_id')->nullable()->constrained('users')->nullOnDelete();

            // Step 1: Project Details
            $table->string('plan_no')->unique();
            $table->string('stand_no');
            $table->text('postal_address');
            $table->decimal('estimated_cost', 15, 2);
            $table->string('purpose');
            $table->string('industry_type')->nullable();
            $table->string('project_type');

            // Step 2: Ownership & Professionals
            $table->string('owner_name');
            $table->text('owner_address');
            $table->string('owner_phone')->nullable();

            $table->string('architect_name')->nullable();
            $table->text('architect_address')->nullable();
            $table->string('architect_phone')->nullable();

            $table->string('contractor_name')->nullable();
            $table->text('contractor_address')->nullable();
            $table->string('contractor_phone')->nullable();

            $table->string('supervision');

            // Step 3: Dimensions & Specs
            $table->decimal('area_ground_floor', 10, 2);
            $table->decimal('area_first_floor', 10, 2)->nullable();
            $table->decimal('area_total', 10, 2);
            $table->decimal('area_outbuildings', 10, 2)->nullable();
            $table->string('fire_fighting_equipment')->nullable();

            // Status: pending, revision_requested, rejected, approved
            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_applications');
    }
};
