<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\AdmissionCycle;
use App\Models\Application;
use App\Models\College;
use App\Models\DownloadDocument;
use App\Models\Event;
use App\Models\FacultyProfile;
use App\Models\NewsArticle;
use App\Models\Program;
use App\Models\StudentRecord;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminDashboardController extends Controller
{
    /**
     * Return high-level summary KPI metrics for the Admin Dashboard.
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_colleges' => College::count(),
                'total_programs' => Program::count(),
                'total_faculty' => FacultyProfile::count(),
                'total_students' => StudentRecord::count(),
                'total_applications' => Application::count(),
                'pending_applications' => Application::where('status', 'submitted')->orWhere('status', 'under_review')->count(),
                'accepted_applications' => Application::where('status', 'accepted')->count(),
                'rejected_applications' => Application::where('status', 'rejected')->count(),
                'total_news' => NewsArticle::count(),
                'total_events' => Event::count(),
                'total_documents' => DownloadDocument::count(),
                'active_admission_cycles' => AdmissionCycle::where('is_open', true)->count(),
            ],
        ]);
    }

    /**
     * List applications for admin review with filtering by status and search.
     */
    public function applications(Request $request): JsonResponse
    {
        $query = Application::with(['program.department.college', 'documents', 'admissionCycle'])
            ->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $applications = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ApplicationResource::collection($applications),
            'meta' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'total' => $applications->total(),
                'per_page' => $applications->perPage(),
            ],
        ]);
    }

    /**
     * Update application status and committee review notes.
     */
    public function updateApplicationStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['draft', 'submitted', 'under_review', 'accepted', 'rejected'])],
            'notes' => 'nullable|string|max:1000',
        ]);

        $application = Application::with(['program', 'documents'])->findOrFail($id);
        $application->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $application->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully.',
            'data' => new ApplicationResource($application),
        ]);
    }
}
