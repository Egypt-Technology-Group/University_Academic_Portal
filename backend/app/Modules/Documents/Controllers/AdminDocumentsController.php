<?php
declare(strict_types=1);

namespace App\Modules\Documents\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Documents\Models\DownloadDocument;
use App\Modules\Documents\Requests\StoreDownloadDocumentRequest;
use App\Modules\Documents\Requests\UpdateDownloadDocumentRequest;
use App\Modules\Documents\Resources\DocumentResource;
use App\Modules\Documents\Services\DocumentsService;
use Illuminate\Http\JsonResponse;

class AdminDocumentsController extends Controller
{
    public function __construct(
        protected readonly DocumentsService $documentsService
    ) {}

    /**
     * Store and upload a new document into repository.
     */
    public function storeDocument(StoreDownloadDocumentRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $doc = $this->documentsService->createDocument(
            $request->validated(),
            $file,
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded and published to document & regulations repository.',
            'data' => new DocumentResource($doc),
        ], 201);
    }

    /**
     * Update an existing document.
     */
    public function updateDocument(UpdateDownloadDocumentRequest $request, int $id): JsonResponse
    {
        $doc = DownloadDocument::findOrFail($id);
        $file = $request->file('file');

        $updated = $this->documentsService->updateDocument(
            $doc,
            $request->validated(),
            $file
        );

        return response()->json([
            'success' => true,
            'message' => 'Document & regulations updated successfully.',
            'data' => new DocumentResource($updated),
        ]);
    }

    /**
     * Toggle archive status of document.
     */
    public function toggleArchiveDocument(int $id): JsonResponse
    {
        $doc = DownloadDocument::findOrFail($id);
        $updated = $this->documentsService->toggleArchiveDocument($doc);

        return response()->json([
            'success' => true,
            'message' => $updated->is_archived ? 'Document archived.' : 'Document unarchived and restored.',
            'data' => new DocumentResource($updated),
        ]);
    }

    /**
     * Delete document from repository.
     */
    public function deleteDocument(int $id): JsonResponse
    {
        $doc = DownloadDocument::findOrFail($id);
        $this->documentsService->deleteDocument($doc);

        return response()->json([
            'success' => true,
            'message' => 'Document permanently deleted.',
        ]);
    }
}
