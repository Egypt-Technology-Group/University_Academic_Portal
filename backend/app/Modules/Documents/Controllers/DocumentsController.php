<?php
declare(strict_types=1);

namespace App\Modules\Documents\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Documents\Models\DownloadDocument;
use App\Modules\Documents\Resources\DocumentResource;
use App\Modules\Documents\Services\DocumentsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DocumentsController extends Controller
{
    public function __construct(
        protected readonly DocumentsService $documentsService
    ) {}

    /**
     * Downloadable documents grouped or filtered by category.
     */
    public function documents(Request $request): JsonResponse|AnonymousResourceCollection
    {
        if ($request->boolean('grouped')) {
            $groupedDocs = $this->documentsService->getGroupedDocuments($request->all());
            $data = [];
            foreach ($groupedDocs as $category => $items) {
                $data[$category] = DocumentResource::collection($items);
            }
            return response()->json(['data' => $data]);
        }

        $docs = $this->documentsService->getDocuments($request->all());

        return DocumentResource::collection($docs);
    }

    /**
     * Increment download counter and return file URL.
     */
    public function incrementDocumentDownload(int $id): JsonResponse
    {
        $document = DownloadDocument::findOrFail($id);
        $document = $this->documentsService->incrementDownload($document);

        return response()->json([
            'success' => true,
            'id' => $document->id,
            'download_count' => $document->download_count,
            'file_url' => asset('storage/' . $document->file_path),
            'file_path' => $document->file_path,
            'document' => new DocumentResource($document),
        ]);
    }
}
