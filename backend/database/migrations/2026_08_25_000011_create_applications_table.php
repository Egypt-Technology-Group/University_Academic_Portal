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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->foreignId('admission_cycle_id')->constrained('admission_cycles')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('national_id');
            $table->string('email');
            $table->string('phone');
            $table->decimal('high_school_score', 5, 2);
            $table->enum('status', ['draft', 'submitted', 'under_review', 'accepted', 'rejected'])->default('submitted');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
