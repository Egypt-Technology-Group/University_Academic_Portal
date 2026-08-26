<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Models\DownloadDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminCrudController extends Controller
{
    // Documents & Regulations Repository
    public function storeDocument(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'version' => 'nullable|string|max:20',
            'status' => 'nullable|in:published,draft,archived',
            'target_audience' => 'nullable|in:all,students,faculty,staff',
            'is_featured' => 'nullable|boolean',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png|max:51200', // 50MB max
            'file_path' => 'nullable|string',
            'file_size' => 'nullable|string',
            'file_type' => 'nullable|string',
            'effective_date' => 'nullable|date',
        ]);

        $filePath = $validated['file_path'] ?? '/downloads/regulation_sample.pdf';
        $fileSize = $validated['file_size'] ?? '2.4 MB';
        $fileType = $validated['file_type'] ?? 'PDF';

        // Process real uploaded file from device
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $storedPath = $uploadedFile->store('documents_repo', 'public');
            $filePath = '/storage/'.$storedPath;
            $bytes = $uploadedFile->getSize();
            $fileSize = $bytes >= 1048576 
                ? number_format($bytes / 1048576, 1).' MB' 
                : number_format($bytes / 1024, 0).' KB';
            $fileType = strtoupper($uploadedFile->getClientOriginalExtension());
        }

        $doc = DownloadDocument::create([
            'category' => $validated['category'],
            'title' => ['ar' => $validated['title_ar'], 'en' => $validated['title_en']],
            'description' => [
                'ar' => $validated['description_ar'] ?? '',
                'en' => $validated['description_en'] ?? '',
            ],
            'version' => $validated['version'] ?? '1.0',
            'status' => $validated['status'] ?? 'published',
            'target_audience' => $validated['target_audience'] ?? 'all',
            'is_featured' => (bool) ($validated['is_featured'] ?? false),
            'is_archived' => false,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'file_type' => $fileType,
            'download_count' => 0,
            'effective_date' => $validated['effective_date'] ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded and published to document & regulations repository.',
            'data' => new DocumentResource($doc),
        ], 201);
    }

    public function updateDocument(Request $request, int $id): JsonResponse
    {
        $doc = DownloadDocument::findOrFail($id);

        $validated = $request->validate([
            'category' => 'nullable|string',
            'title_ar' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'version' => 'nullable|string|max:20',
            'status' => 'nullable|in:published,draft,archived',
            'target_audience' => 'nullable|in:all,students,faculty,staff',
            'is_featured' => 'nullable|boolean',
            'is_archived' => 'nullable|boolean',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png|max:51200',
            'file_path' => 'nullable|string',
            'file_size' => 'nullable|string',
            'file_type' => 'nullable|string',
            'effective_date' => 'nullable|date',
        ]);

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $storedPath = $uploadedFile->store('documents_repo', 'public');
            $doc->file_path = '/storage/'.$storedPath;
            $bytes = $uploadedFile->getSize();
            $doc->file_size = $bytes >= 1048576 
                ? number_format($bytes / 1048576, 1).' MB' 
                : number_format($bytes / 1024, 0).' KB';
            $doc->file_type = strtoupper($uploadedFile->getClientOriginalExtension());
        } elseif (isset($validated['file_path'])) {
            $doc->file_path = $validated['file_path'];
            if (isset($validated['file_size'])) $doc->file_size = $validated['file_size'];
            if (isset($validated['file_type'])) $doc->file_type = $validated['file_type'];
        }

        if (isset($validated['category'])) $doc->category = $validated['category'];
        if (isset($validated['title_ar'])) $doc->setTranslation('title', 'ar', $validated['title_ar']);
        if (isset($validated['title_en'])) $doc->setTranslation('title', 'en', $validated['title_en']);
        if (isset($validated['description_ar'])) $doc->setTranslation('description', 'ar', $validated['description_ar']);
        if (isset($validated['description_en'])) $doc->setTranslation('description', 'en', $validated['description_en']);
        if (isset($validated['version'])) $doc->version = $validated['version'];
        if (isset($validated['status'])) $doc->status = $validated['status'];
        if (isset($validated['target_audience'])) $doc->target_audience = $validated['target_audience'];
        if (isset($validated['is_featured'])) $doc->is_featured = (bool) $validated['is_featured'];
        if (isset($validated['is_archived'])) $doc->is_archived = (bool) $validated['is_archived'];
        if (isset($validated['effective_date'])) $doc->effective_date = $validated['effective_date'];

        $doc->save();

        return response()->json([
            'success' => true,
            'message' => 'Document & regulations updated successfully.',
            'data' => new DocumentResource($doc),
        ]);
    }

    public function toggleArchiveDocument(int $id): JsonResponse
    {
        $doc = DownloadDocument::findOrFail($id);
        $doc->is_archived = !$doc->is_archived;
        $doc->status = $doc->is_archived ? 'archived' : 'published';
        $doc->save();

        return response()->json([
            'success' => true,
            'message' => $doc->is_archived ? 'Document archived.' : 'Document unarchived and restored.',
            'data' => new DocumentResource($doc),
        ]);
    }

    public function deleteDocument(int $id): JsonResponse
    {
        $doc = DownloadDocument::findOrFail($id);
        $doc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document permanently deleted.',
        ]);
    }
}
