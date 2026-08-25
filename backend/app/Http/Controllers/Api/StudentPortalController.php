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
        ]);
    }
}
