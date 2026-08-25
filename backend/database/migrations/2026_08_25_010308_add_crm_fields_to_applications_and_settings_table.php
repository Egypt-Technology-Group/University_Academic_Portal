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
        Schema::table('applications', function (Blueprint $table) {
            $table->string('scholarship_name')->nullable()->after('decision_reason');
            $table->unsignedTinyInteger('scholarship_discount_percent')->default(0)->after('scholarship_name');
            $table->unsignedInteger('waitlist_position')->nullable()->after('scholarship_discount_percent');
            $table->enum('enrollment_status', ['pending', 'documents_verified', 'tuition_paid', 'enrolled', 'withdrawn'])
                ->default('pending')
                ->after('waitlist_position');
            $table->json('verification_checklist')->nullable()->after('enrollment_status');
            $table->json('communication_logs')->nullable()->after('verification_checklist');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'scholarship_name',
                'scholarship_discount_percent',
                'waitlist_position',
                'enrollment_status',
                'verification_checklist',
                'communication_logs',
            ]);
        });
    }
};
