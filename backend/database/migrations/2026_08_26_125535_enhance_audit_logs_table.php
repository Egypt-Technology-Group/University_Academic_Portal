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
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('actor_email')->nullable()->after('actor_name');
            $table->string('actor_role')->nullable()->after('actor_email');
            $table->string('module')->default('general')->after('action'); // auth, academic, admissions, cms, events, documents, services, settings
            $table->string('description_ar')->nullable()->after('auditable_id');
            $table->string('description_en')->nullable()->after('description_ar');
            $table->string('severity')->default('info')->after('new_values'); // info, notice, warning, critical, security
            $table->string('status')->default('success')->after('severity'); // success, failed, rejected
            $table->string('request_method', 10)->nullable()->after('status'); // GET, POST, PUT, PATCH, DELETE
            $table->string('request_url')->nullable()->after('request_method');
            $table->json('context')->nullable()->after('request_url');
            $table->string('integrity_hash', 64)->nullable()->after('user_agent'); // HMAC SHA-256 tamper-evident hash
            $table->string('previous_hash', 64)->nullable()->after('integrity_hash'); // Hash chain linkage
            
            $table->index(['module', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['severity', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['module', 'created_at']);
            $table->dropIndex(['action', 'created_at']);
            $table->dropIndex(['severity', 'created_at']);
            $table->dropIndex(['user_id', 'created_at']);
            
            $table->dropColumn([
                'actor_email',
                'actor_role',
                'module',
                'description_ar',
                'description_en',
                'severity',
                'status',
                'request_method',
                'request_url',
                'context',
                'integrity_hash',
                'previous_hash',
            ]);
        });
    }
};
