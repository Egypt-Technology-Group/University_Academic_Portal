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
            $table->string('stage')->default('initial_screening')->after('status');
            $table->dateTime('interview_scheduled_at')->nullable()->after('stage');
            $table->dateTime('placement_test_at')->nullable()->after('interview_scheduled_at');
            $table->string('decision_reason')->nullable()->after('placement_test_at');
            $table->json('timeline')->nullable()->after('decision_reason');
            $table->string('reviewed_by')->nullable()->after('timeline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'stage',
                'interview_scheduled_at',
                'placement_test_at',
                'decision_reason',
                'timeline',
                'reviewed_by',
            ]);
        });
    }
};
