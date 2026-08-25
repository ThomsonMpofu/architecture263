<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Relax NOT NULL constraints so an in-progress "draft" plan application can
 * be saved with only the fields completed so far (save-and-continue). Full
 * validation is enforced in the application layer only when the architect
 * finalizes/submits the draft. Raw SQL is used since doctrine/dbal (needed
 * for Schema::table()->change()) isn't installed.
 */
return new class extends Migration
{
    private const COLUMNS = [
        'stand_no' => 'VARCHAR(255)',
        'postal_address' => 'TEXT',
        'estimated_cost' => 'DECIMAL(15,2)',
        'purpose' => 'VARCHAR(255)',
        'project_type' => 'VARCHAR(255)',
        'owner_name' => 'VARCHAR(255)',
        'owner_address' => 'TEXT',
        'supervision' => 'VARCHAR(255)',
        'area_ground_floor' => 'DECIMAL(10,2)',
        'area_total' => 'DECIMAL(10,2)',
    ];

    public function up(): void
    {
        foreach (self::COLUMNS as $column => $type) {
            DB::statement("ALTER TABLE plan_applications MODIFY COLUMN {$column} {$type} NULL");
        }

        DB::statement('ALTER TABLE plan_applications MODIFY COLUMN plan_no VARCHAR(255) NULL');
    }

    public function down(): void
    {
        foreach (self::COLUMNS as $column => $type) {
            DB::statement("ALTER TABLE plan_applications MODIFY COLUMN {$column} {$type} NOT NULL");
        }

        DB::statement('ALTER TABLE plan_applications MODIFY COLUMN plan_no VARCHAR(255) NOT NULL');
    }
};
