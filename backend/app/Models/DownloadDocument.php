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
    ];

    protected $fillable = [
        'category',
        'title',
        'file_path',
        'file_size',
        'file_type',
        'download_count',
    ];

    protected function casts(): array
    {
        return [
            'download_count' => 'integer',
        ];
    }
}
