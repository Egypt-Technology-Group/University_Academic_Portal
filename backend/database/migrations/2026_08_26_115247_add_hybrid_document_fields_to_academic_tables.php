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
        Schema::table('official_statements', function (Blueprint $table) {
            if (!Schema::hasColumn('official_statements', 'workflow_mode')) {
                $table->string('workflow_mode')->default('structured')->after('statement_type'); // structured, file_only, both
            }
            if (!Schema::hasColumn('official_statements', 'document_path')) {
                $table->string('document_path')->nullable()->after('qr_payload');
            }
            if (!Schema::hasColumn('official_statements', 'file_name')) {
                $table->string('file_name')->nullable()->after('document_path');
            }
            if (!Schema::hasColumn('official_statements', 'file_size')) {
                $table->string('file_size')->nullable()->after('file_name');
            }
        });

        Schema::table('exam_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_schedules', 'workflow_mode')) {
                $table->string('workflow_mode')->default('structured')->after('exam_type'); // structured, file_only, both
            }
            if (!Schema::hasColumn('exam_schedules', 'timetable_document_path')) {
                $table->string('timetable_document_path')->nullable()->after('seating_capacity');
            }
            if (!Schema::hasColumn('exam_schedules', 'timetable_file_name')) {
                $table->string('timetable_file_name')->nullable()->after('timetable_document_path');
            }
            if (!Schema::hasColumn('exam_schedules', 'timetable_file_size')) {
                $table->string('timetable_file_size')->nullable()->after('timetable_file_name');
            }
        });

        Schema::table('programs', function (Blueprint $table) {
            if (!Schema::hasColumn('programs', 'study_plan_document_path')) {
                $table->string('study_plan_document_path')->nullable()->after('admission_requirements');
            }
            if (!Schema::hasColumn('programs', 'study_plan_file_name')) {
                $table->string('study_plan_file_name')->nullable()->after('study_plan_document_path');
            }
            if (!Schema::hasColumn('programs', 'study_plan_file_size')) {
                $table->string('study_plan_file_size')->nullable()->after('study_plan_file_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('official_statements', function (Blueprint $table) {
            $table->dropColumn(['workflow_mode', 'document_path', 'file_name', 'file_size']);
        });

        Schema::table('exam_schedules', function (Blueprint $table) {
            $table->dropColumn(['workflow_mode', 'timetable_document_path', 'timetable_file_name', 'timetable_file_size']);
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['study_plan_document_path', 'study_plan_file_name', 'study_plan_file_size']);
        });
    }
};
