<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class DownloadDocument extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'title',
        'description',
    ];

    protected $fillable = [
        'category', // bylaws, schedules, forms, guides, regulations, policies
        'title',
        'description',
        'file_path',
        'file_size',
        'file_type',
        'version',
        'status', // published, draft, archived
        'target_audience', // all, students, faculty, staff
        'is_featured',
        'is_archived',
        'sort_order',
        'download_count',
        'effective_date',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_archived' => 'boolean',
            'sort_order' => 'integer',
            'download_count' => 'integer',
            'effective_date' => 'datetime',
        ];
    }
}
