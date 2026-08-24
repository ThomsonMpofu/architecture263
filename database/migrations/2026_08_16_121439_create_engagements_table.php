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
        Schema::create('engagements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('architect_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending_architect_approval');
            $table->timestamp('architect_approved_at')->nullable();
            $table->timestamp('blue_book_purchased_at')->nullable();
            $table->timestamp('client_signed_at')->nullable();
            $table->timestamp('architect_signed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engagements');
    }
};
