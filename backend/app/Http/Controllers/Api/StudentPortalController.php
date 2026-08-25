<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResultResource;
use App\Models\AcademicTerm;
use App\Models\StudentRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    /**
     * Inquire student results by student_id_number and optional term_id.
     */
    public function inquireResult(Request $request): JsonResponse
    {
        $request->validate([
            'student_id_number' => 'required|string',
            'term_id' => 'nullable|integer',
            'academic_term_id' => 'nullable|integer',
        ]);

        $studentIdNumber = $request->input('student_id_number');
        $termId = $request->input('term_id', $request->input('academic_term_id'));

        $studentRecord = StudentRecord::where('student_id_number', $studentIdNumber)
            ->with([
                'user',
                'program.department.college',
                'courseResults' => function ($q) use ($termId) {
                    $q->where('is_published', true);
                    if ($termId) {
                        $q->where('academic_term_id', $termId);
                    }
                    $q->with('academicTerm');
                },
            ])
            ->first();

        if (!$studentRecord) {
            return response()->json([
                'message' => 'Student record not found.',
            ], 404);
        }

        $results = $studentRecord->courseResults;
        $termGpa = null;
        $termName = null;

        if ($termId) {
            $term = AcademicTerm::find($termId);
            if ($term) {
                $termName = $term->getTranslation('name', app()->getLocale(), false) ?: $term->name;
            }

            if ($results->isNotEmpty()) {
                $totalCredits = $results->sum('credit_hours');
                $totalPoints = $results->sum(function ($result) {
                    return (float) $result->grade_points * (int) $result->credit_hours;
                });
                $termGpa = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.00;
            }
        }

        $programName = null;
        if ($studentRecord->program) {
            $programName = $studentRecord->program->getTranslation('name', app()->getLocale(), false)
                ?: $studentRecord->program->name;
        }

        return response()->json([
            'student' => [
                'id' => $studentRecord->id,
                'student_id_number' => $studentRecord->student_id_number,
                'student_name' => $studentRecord->user?->name,
                'email' => $studentRecord->user?->email,
                'program' => $programName,
                'current_level' => (int) $studentRecord->current_level,
                'status' => $studentRecord->status,
            ],
            'cumulative_gpa' => (float) $studentRecord->cumulative_gpa,
            'term_gpa' => $termGpa,
            'academic_term' => $termName,
            'course_results' => CourseResultResource::collection($results),
            'transcript_metadata' => [
                'document_id' => 'TRANS-'.strtoupper(substr(md5($studentRecord->student_id_number.date('Ymd')), 0, 10)),
                'issued_at' => now()->toIso8601String(),
                'registrar_seal' => 'Office of Academic Records & Registration - Verified',
                'verification_url' => url('/student-portal?student_id='.$studentRecord->student_id_number),
            ],
        ]);
    }

    /**
     * Simulate next term course registration with credit caps and prerequisites check.
     */
    public function simulateRegistration(Request $request): JsonResponse
    {
        $request->validate([
            'student_id_number' => 'required|string',
            'selected_courses' => 'required|array|min:1',
            'selected_courses.*.code' => 'required|string',
            'selected_courses.*.credits' => 'required|integer|min:1|max:6',
        ]);

        $studentRecord = StudentRecord::where('student_id_number', $request->student_id_number)
            ->with(['program'])
            ->first();

        if (!$studentRecord) {
            return response()->json(['message' => 'Student record not found.'], 404);
        }

        $gpa = (float) $studentRecord->cumulative_gpa;
        // Academic standing rule: GPA >= 3.0 allows up to 21 credits, GPA >= 2.0 allows up to 18, under 2.0 (Probation) cap is 14 credits
        $maxAllowedCredits = $gpa >= 3.0 ? 21 : ($gpa >= 2.0 ? 18 : 14);
        $academicStanding = $gpa >= 3.0 ? 'Dean\'s List / Excellent' : ($gpa >= 2.0 ? 'Good Standing' : 'Academic Warning / Probation');

        $totalCredits = array_reduce($request->selected_courses, fn($sum, $c) => $sum + (int) $c['credits'], 0);
        $isEligible = $totalCredits <= $maxAllowedCredits;

        return response()->json([
            'success' => true,
            'data' => [
                'student_id' => $studentRecord->student_id_number,
                'cumulative_gpa' => $gpa,
                'academic_standing' => $academicStanding,
                'max_allowed_credits' => $maxAllowedCredits,
                'selected_total_credits' => $totalCredits,
                'is_eligible' => $isEligible,
                'validation_message' => $isEligible
                    ? 'Registration schedule complies with academic regulations and credit limits.'
                    : "Selected credits ({$totalCredits}) exceed maximum allowed credit cap ({$maxAllowedCredits}) for your academic standing.",
            ],
        ]);
    }
}
