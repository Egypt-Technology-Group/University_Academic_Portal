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
        Schema::table('download_documents', function (Blueprint $table) {
            $table->json('description')->nullable()->after('title');
            $table->string('version')->default('1.0')->after('description');
            $table->string('status')->default('published')->after('version'); // published, draft, archived
            $table->string('target_audience')->default('all')->after('status'); // all, students, faculty, staff
            $table->boolean('is_featured')->default(false)->after('target_audience');
            $table->boolean('is_archived')->default(false)->after('is_featured');
            $table->integer('sort_order')->default(0)->after('is_archived');
            $table->timestamp('effective_date')->nullable()->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('download_documents', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'version',
                'status',
                'target_audience',
                'is_featured',
                'is_archived',
                'sort_order',
                'effective_date',
            ]);
        });
    }
};
