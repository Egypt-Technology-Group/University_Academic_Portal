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
        // 1. Electronic Student Service Requests
        Schema::create('student_service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->string('student_id_number')->index();
            $table->string('student_name');
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->string('service_type'); // enrollment_cert, transcript, course_exemption, postponement, id_card_replacement
            $table->json('purpose')->nullable();
            $table->string('status')->default('pending'); // pending, processing, approved, ready_for_pickup, rejected
            $table->text('admin_notes')->nullable();
            $table->string('handled_by')->nullable();
            $table->decimal('fee_amount', 8, 2)->default(0.00);
            $table->boolean('is_fee_paid')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 2. Verifiable Official Statements & Certificates
        Schema::create('official_statements', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_code')->unique();
            $table->string('student_id_number')->index();
            $table->string('student_name');
            $table->string('national_id');
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->string('statement_type'); // official_enrollment, completion_statement, english_proficiency, rank_statement
            $table->json('title');
            $table->json('recipient_entity')->nullable(); // To Whom It May Concern / Embassy / Ministry
            $table->string('verification_hash')->unique();
            $table->string('qr_payload')->nullable();
            $table->string('signatory_name')->default('Prof. Dr. Academic Dean');
            $table->string('signatory_title')->default('Dean of Student Affairs');
            $table->timestamp('issue_date');
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_revoked')->default(false);
            $table->timestamps();
        });

        // 3. Exam Schedules & Hall Invigilation
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('academic_term_id')->nullable()->constrained('academic_terms')->nullOnDelete();
            $table->string('course_code');
            $table->json('course_name');
            $table->string('exam_type')->default('final'); // midterm, final, practical, oral
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->json('hall_location'); // e.g. Hall 401, Central Auditorium
            $table->json('chief_invigilator')->nullable(); // Dr. Name
            $table->json('proctors_list')->nullable(); // List of TAs
            $table->integer('seating_capacity')->default(60);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('official_statements');
        Schema::dropIfExists('student_service_requests');
    }
};
