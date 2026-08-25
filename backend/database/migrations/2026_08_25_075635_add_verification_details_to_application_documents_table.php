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
        Schema::table('application_documents', function (Blueprint $table) {
            $table->boolean('is_original_verified')->default(false)->after('verification_status');
            $table->string('rejection_reason')->nullable()->after('is_original_verified');
            $table->text('reviewer_notes')->nullable()->after('rejection_reason');
            $table->timestamp('verified_at')->nullable()->after('reviewer_notes');
            $table->string('verified_by')->nullable()->after('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn([
                'is_original_verified',
                'rejection_reason',
                'reviewer_notes',
                'verified_at',
                'verified_by',
            ]);
        });
    }
};
