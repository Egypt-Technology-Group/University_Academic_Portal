<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    /**
     * Display a paginated, searchable, filterable list of audit logs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = AuditLog::with('user:id,name,email')->latest('id');

        // Module filter
        if ($request->filled('module') && $request->module !== 'all') {
            $query->where('module', $request->module);
        }

        // Action filter
        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        // Severity filter
        if ($request->filled('severity') && $request->severity !== 'all') {
            $query->where('severity', $request->severity);
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search in actor name, email, IP, description, or auditable type
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('actor_name', 'like', "%{$search}%")
                  ->orWhere('actor_email', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('description_ar', 'like', "%{$search}%")
                  ->orWhere('description_en', 'like', "%{$search}%")
                  ->orWhere('auditable_type', 'like', "%{$search}%");
            });
        }

        $perPage = min(max((int) $request->get('per_page', 15), 5), 100);
        $logs = $query->paginate($perPage);

        // Calculate summary KPI stats
        $stats = [
            'total_logs' => AuditLog::count(),
            'today_logs' => AuditLog::whereDate('created_at', now()->toDateString())->count(),
            'security_events' => AuditLog::where('severity', 'security')->orWhere('severity', 'critical')->count(),
            'failed_actions' => AuditLog::where('status', 'failed')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
                'from' => $logs->firstItem(),
                'to' => $logs->lastItem(),
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * Get details for a single audit log entry with diff calculation.
     */
    public function show(int $id): JsonResponse
    {
        $log = AuditLog::with('user:id,name,email')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $log,
        ]);
    }

    /**
     * Verify cryptographic HMAC hash-chain integrity across log records.
     */
    public function verifyIntegrity(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 100), 500);
        $logs = AuditLog::orderBy('id', 'asc')->take($limit)->get();

        $brokenChain = [];
        $isValid = true;

        foreach ($logs as $log) {
            $computed = AuditLog::computeIntegrityHash($log->getAttributes(), $log->previous_hash);
            if ($log->integrity_hash && $log->integrity_hash !== $computed) {
                $isValid = false;
                $brokenChain[] = [
                    'id' => $log->id,
                    'action' => $log->action,
                    'created_at' => $log->created_at,
                    'expected' => $computed,
                    'actual' => $log->integrity_hash,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'is_valid' => $isValid,
            'checked_records_count' => $logs->count(),
            'broken_records' => $brokenChain,
            'message' => $isValid
                ? 'Cryptographic integrity verified: all audited transactions are intact and tamper-free.'
                : 'Integrity alert: Potential tampering detected in audit records.',
        ]);
    }

    /**
     * Export filtered audit logs to CSV for compliance and external auditing.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = AuditLog::latest('id');

        if ($request->filled('module') && $request->module !== 'all') {
            $query->where('module', $request->module);
        }
        if ($request->filled('severity') && $request->severity !== 'all') {
            $query->where('severity', $request->severity);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="audit_trail_report_' . date('Y_m_d_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel Arabic character compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID',
                'Timestamp',
                'Actor Name',
                'Actor Email',
                'Role',
                'Module',
                'Action',
                'Target Entity',
                'Target ID',
                'Description (AR)',
                'Description (EN)',
                'Severity',
                'Status',
                'IP Address',
                'Integrity Hash',
            ]);

            $query->chunk(200, function ($records) use ($handle) {
                foreach ($records as $r) {
                    fputcsv($handle, [
                        $r->id,
                        $r->created_at->toIso8601String(),
                        $r->actor_name,
                        $r->actor_email,
                        $r->actor_role,
                        $r->module,
                        $r->action,
                        class_basename($r->auditable_type),
                        $r->auditable_id,
                        $r->description_ar,
                        $r->description_en,
                        $r->severity,
                        $r->status,
                        $r->ip_address,
                        $r->integrity_hash,
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
