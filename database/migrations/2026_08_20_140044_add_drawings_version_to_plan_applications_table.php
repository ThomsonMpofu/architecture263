<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plan_applications', function (Blueprint $table) {
            // Which version (in plan_application_drawings) drawings_path
            // currently points at — kept in sync on every upload so
            // existing preview/download code doesn't need to change.
            $table->unsignedInteger('drawings_version')->default(0)->after('drawings_original_name');
        });

        // Backfill: any application that already has a drawings file gets
        // it recorded as version 1 of its history.
        DB::table('plan_applications')
            ->whereNotNull('drawings_path')
            ->orderBy('id')
            ->get(['id', 'drawings_path', 'drawings_original_name', 'submitted_by', 'created_at'])
            ->each(function ($application) {
                DB::table('plan_application_drawings')->insert([
                    'plan_application_id' => $application->id,
                    'uploaded_by' => $application->submitted_by,
                    'version' => 1,
                    'path' => $application->drawings_path,
                    'original_name' => $application->drawings_original_name,
                    'created_at' => $application->created_at,
                    'updated_at' => $application->created_at,
                ]);

                DB::table('plan_applications')->where('id', $application->id)->update(['drawings_version' => 1]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_applications', function (Blueprint $table) {
            $table->dropColumn('drawings_version');
        });
    }
};
