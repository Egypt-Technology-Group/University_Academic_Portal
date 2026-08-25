<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitApplicationRequest;
use App\Http\Resources\AdmissionCycleResource;
use App\Http\Resources\ApplicationResource;
use App\Http\Resources\ProgramResource;
use App\Models\AdmissionCycle;
use App\Models\Application;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdmissionController extends Controller
{
    /**
     * Returns current open admission cycle with available programs.
     */
    public function activeCycle(): JsonResponse
    {
        $cycle = AdmissionCycle::where('is_open', true)->first();
        $programs = Program::where('is_active', true)->with(['department.college'])->get();

        return response()->json([
            'cycle' => $cycle ? new AdmissionCycleResource($cycle) : null,
            'programs' => ProgramResource::collection($programs),
        ]);
    }

    /**
     * Creates application, generates tracking code, handles document attachments, returns application data.
     */
    public function submitApplication(SubmitApplicationRequest $request): JsonResponse
    {
        $cycleId = $request->input('admission_cycle_id')
            ?: AdmissionCycle::where('is_open', true)->value('id');

        $year = date('Y');
        do {
            $randomCode = strtoupper(Str::random(5));
            $applicationNumber = "APP-{$year}-{$randomCode}";
        } while (Application::where('application_number', $applicationNumber)->exists());

        $application = Application::create([
            'application_number' => $applicationNumber,
            'admission_cycle_id' => $cycleId,
            'program_id' => $request->input('program_id'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'national_id' => $request->input('national_id'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'high_school_score' => $request->input('high_school_score'),
            'status' => 'submitted',
            'notes' => $request->input('notes'),
        ]);

        // Handle file uploads or array payloads for documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $key => $file) {
                $docType = is_string($key) ? $key : ($request->input("document_types.{$key}") ?? 'document');
                $path = $file->store("applications/{$application->id}", 'public');
                $application->documents()->create([
                    'document_type' => $docType,
                    'file_path' => $path,
                    'verification_status' => 'pending',
                ]);
            }
        } elseif ($request->has('documents') && is_array($request->input('documents'))) {
            foreach ($request->input('documents') as $doc) {
                if (is_array($doc)) {
                    $application->documents()->create([
                        'document_type' => $doc['type'] ?? $doc['document_type'] ?? 'document',
                        'file_path' => $doc['path'] ?? $doc['file_path'] ?? '',
                        'verification_status' => $doc['verification_status'] ?? 'pending',
                    ]);
                }
            }
        }

        $application->load(['program.department.college', 'admissionCycle', 'documents']);

        return (new ApplicationResource($application))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Lookup by application_number & national_id or email, returns application status & documents.
     */
    public function trackApplication(Request $request): JsonResponse|ApplicationResource
    {
        $request->validate([
            'application_number' => 'required|string',
            'national_id' => 'required_without:email|nullable|string',
            'email' => 'required_without:national_id|nullable|email',
        ]);

        $query = Application::where('application_number', $request->input('application_number'));

        if ($request->filled('national_id')) {
            $query->where('national_id', $request->input('national_id'));
        }

        if ($request->filled('email')) {
            $query->where('email', $request->input('email'));
        }

        $application = $query->with(['program.department.college', 'admissionCycle', 'documents'])->first();

        if (!$application) {
            return response()->json([
                'message' => 'Application not found matching the provided credentials.',
            ], 404);
        }

        return new ApplicationResource($application);
    }
}
