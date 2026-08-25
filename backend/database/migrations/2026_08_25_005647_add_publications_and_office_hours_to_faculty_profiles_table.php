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
        Schema::table('faculty_profiles', function (Blueprint $table) {
            $table->string('google_scholar_url')->nullable()->after('cv_path');
            $table->string('orcid_id')->nullable()->after('google_scholar_url');
            $table->json('office_hours')->nullable()->after('orcid_id');
            $table->json('publications')->nullable()->after('office_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculty_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'google_scholar_url',
                'orcid_id',
                'office_hours',
                'publications',
            ]);
        });
    }
};
