<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DownloadDocument;
use App\Models\Event;
use App\Models\NewsArticle;
use App\Models\StudentRecord;
use App\Modules\AcademicStructure\Models\College;
use App\Modules\AcademicStructure\Models\FacultyProfile;
use App\Modules\AcademicStructure\Models\Program;
use App\Modules\Admissions\Models\AdmissionCycle;
use App\Modules\Admissions\Models\Application;
use Illuminate\Http\JsonResponse;

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
}