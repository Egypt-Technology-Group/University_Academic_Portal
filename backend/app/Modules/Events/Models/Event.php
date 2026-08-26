<?php
declare(strict_types=1);

namespace App\Modules\Events\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Event extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'title',
        'location',
        'organizer',
        'description',
    ];

    protected $fillable = [
        'title',
        'slug',
        'location',
        'organizer',
        'description',
        'cover_image',
        'start_time',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(EventAttendee::class);
    }
}
