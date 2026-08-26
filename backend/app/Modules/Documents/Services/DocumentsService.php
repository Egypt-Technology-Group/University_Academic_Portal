<?php
declare(strict_types=1);

namespace App\Modules\Documents\Services;

use App\Models\AuditLog;
use App\Models\User;
use App\Modules\Documents\Models\DownloadDocument;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

class DocumentsService
{
    /**
     * Get documents matching filter criteria.
     */
    public function getDocuments(array $filters = []): Collection
    {
        $query = DownloadDocument::query();

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['target_audience'])) {
            $audience = $filters['target_audience'];
            $query->where(function ($q) use ($audience) {
                $q->where('target_audience', $audience)
                    ->orWhere('target_audience', 'all');
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', filter_var($filters['is_featured'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($filters['is_archived'])) {
            $query->where('is_archived', filter_var($filters['is_archived'], FILTER_VALIDATE_BOOLEAN));
        }

        if (!empty($filters['search']) || !empty($filters['q'])) {
            $search = (string) ($filters['search'] ?? $filters['q']);
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                    ->orWhere('title->ar', 'like', "%{$search}%")
                    ->orWhere('description->en', 'like', "%{$search}%")
                    ->orWhere('description->ar', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('sort_order')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get documents grouped by category.
     */
    public function getGroupedDocuments(array $filters = []): Collection
    {
        return $this->getDocuments($filters)->groupBy('category');
    }

    /**
     * Increment download counter and return the refreshed document.
     */
    public function incrementDownload(DownloadDocument $document): DownloadDocument
    {
        $document->increment('download_count');
        $document->refresh();

        return $document;
    }

    /**
     * Store and upload a new document into repository.
     */
    public function createDocument(array $data, ?UploadedFile $file = null, ?User $uploader = null): DownloadDocument
    {
        $filePath = $data['file_path'] ?? '/downloads/regulation_sample.pdf';
        $fileSize = $data['file_size'] ?? '2.4 MB';
        $fileType = $data['file_type'] ?? 'PDF';

        // Process real uploaded file from device
        if ($file) {
            $storedPath = $file->store('documents_repo', 'public');
            $filePath = '/storage/' . $storedPath;
            $bytes = $file->getSize();
            $fileSize = $bytes >= 1048576
                ? number_format($bytes / 1048576, 1) . ' MB'
                : number_format($bytes / 1024, 0) . ' KB';
            $fileType = strtoupper($file->getClientOriginalExtension());
        }

        $doc = DownloadDocument::create([
            'category' => $data['category'],
            'title' => [
                'ar' => $data['title_ar'],
                'en' => $data['title_en'],
            ],
            'description' => [
                'ar' => $data['description_ar'] ?? '',
                'en' => $data['description_en'] ?? '',
            ],
            'version' => $data['version'] ?? '1.0',
            'status' => $data['status'] ?? 'published',
            'target_audience' => $data['target_audience'] ?? 'all',
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'is_archived' => false,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'file_type' => $fileType,
            'download_count' => 0,
            'effective_date' => $data['effective_date'] ?? now(),
        ]);

        AuditLog::record(
            action: 'create',
            auditable: $doc,
            oldValues: null,
            newValues: [
                'category' => $doc->category,
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
            ],
            module: 'documents',
            descriptionAr: "رفع ونشر وثيقة جديدة في المستودع: {$data['title_ar']}",
            descriptionEn: "Uploaded and published new document: {$data['title_en']}",
            severity: 'info',
            status: 'success'
        );

        return $doc;
    }

    /**
     * Update an existing document.
     */
    public function updateDocument(DownloadDocument $doc, array $data, ?UploadedFile $file = null): DownloadDocument
    {
        $oldValues = $doc->only(['category', 'title', 'version', 'status', 'is_archived']);

        if ($file) {
            $storedPath = $file->store('documents_repo', 'public');
            $doc->file_path = '/storage/' . $storedPath;
            $bytes = $file->getSize();
            $doc->file_size = $bytes >= 1048576
                ? number_format($bytes / 1048576, 1) . ' MB'
                : number_format($bytes / 1024, 0) . ' KB';
            $doc->file_type = strtoupper($file->getClientOriginalExtension());
        } elseif (isset($data['file_path'])) {
            $doc->file_path = $data['file_path'];
            if (isset($data['file_size'])) {
                $doc->file_size = $data['file_size'];
            }
            if (isset($data['file_type'])) {
                $doc->file_type = $data['file_type'];
            }
        }

        if (isset($data['category'])) {
            $doc->category = $data['category'];
        }
        if (isset($data['title_ar'])) {
            $doc->setTranslation('title', 'ar', $data['title_ar']);
        }
        if (isset($data['title_en'])) {
            $doc->setTranslation('title', 'en', $data['title_en']);
        }
        if (isset($data['description_ar'])) {
            $doc->setTranslation('description', 'ar', $data['description_ar']);
        }
        if (isset($data['description_en'])) {
            $doc->setTranslation('description', 'en', $data['description_en']);
        }
        if (isset($data['version'])) {
            $doc->version = $data['version'];
        }
        if (isset($data['status'])) {
            $doc->status = $data['status'];
        }
        if (isset($data['target_audience'])) {
            $doc->target_audience = $data['target_audience'];
        }
        if (isset($data['is_featured'])) {
            $doc->is_featured = (bool) $data['is_featured'];
        }
        if (isset($data['is_archived'])) {
            $doc->is_archived = (bool) $data['is_archived'];
        }
        if (isset($data['effective_date'])) {
            $doc->effective_date = $data['effective_date'];
        }

        $doc->save();

        AuditLog::record(
            action: 'update',
            auditable: $doc,
            oldValues: $oldValues,
            newValues: $doc->only(['category', 'title', 'version', 'status', 'is_archived']),
            module: 'documents',
            descriptionAr: "تحديث وتعديل وثيقة: {$doc->title}",
            descriptionEn: "Updated document: {$doc->title}",
            severity: 'info',
            status: 'success'
        );

        return $doc;
    }

    /**
     * Toggle archive status of document.
     */
    public function toggleArchiveDocument(DownloadDocument $doc): DownloadDocument
    {
        $oldArchived = $doc->is_archived;
        $doc->is_archived = !$doc->is_archived;
        $doc->status = $doc->is_archived ? 'archived' : 'published';
        $doc->save();

        AuditLog::record(
            action: 'toggle_archive',
            auditable: $doc,
            oldValues: ['is_archived' => $oldArchived],
            newValues: ['is_archived' => $doc->is_archived],
            module: 'documents',
            descriptionAr: $doc->is_archived ? "أرشفة وثيقة: {$doc->title}" : "إلغاء أرشفة وثيقة: {$doc->title}",
            descriptionEn: $doc->is_archived ? "Archived document: {$doc->title}" : "Unarchived document: {$doc->title}",
            severity: 'info',
            status: 'success'
        );

        return $doc;
    }

    /**
     * Delete document from repository.
     */
    public function deleteDocument(DownloadDocument $doc): void
    {
        $title = $doc->title;
        $id = $doc->id;
        $doc->delete();

        AuditLog::record(
            action: 'delete',
            auditable: DownloadDocument::class,
            oldValues: ['id' => $id, 'title' => $title],
            newValues: null,
            module: 'documents',
            descriptionAr: "حذف الوثيقة نهائياً: {$title}",
            descriptionEn: "Deleted document: {$title}",
            severity: 'warning',
            status: 'success'
        );
    }
}
