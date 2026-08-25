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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->json('name');
            $table->string('slug');
            $table->enum('degree_level', ['bachelor', 'master', 'doctorate', 'diploma']);
            $table->integer('duration_years')->default(4);
            $table->integer('credit_hours')->default(132);
            $table->json('curriculum')->nullable();
            $table->json('career_opportunities')->nullable();
            $table->json('tuition_fees')->nullable();
            $table->json('admission_requirements')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
