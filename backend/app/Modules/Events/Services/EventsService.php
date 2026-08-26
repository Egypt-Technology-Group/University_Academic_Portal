<?php
declare(strict_types=1);

namespace App\Modules\Events\Services;

use App\Models\AuditLog;
use App\Models\User;
use App\Modules\Events\Models\Event;
use App\Modules\Events\Models\EventAttendee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class EventsService
{
    /**
     * Get events with filter options (upcoming, past, all) and optional pagination.
     */
    public function getEvents(array $filters = []): LengthAwarePaginator|Collection
    {
        $query = Event::query();

        $filter = $filters['filter'] ?? null;
        $isPast = $filter === 'past' || (!empty($filters['past']) && filter_var($filters['past'], FILTER_VALIDATE_BOOLEAN));
        $isAll = $filter === 'all' || (!empty($filters['all']) && filter_var($filters['all'], FILTER_VALIDATE_BOOLEAN));

        if ($isPast) {
            $query->where('end_time', '<', now())->orderBy('start_time', 'desc');
        } elseif ($isAll) {
            $query->orderBy('start_time', 'asc');
        } else {
            // Default: upcoming events
            $query->where('end_time', '>=', now())->orderBy('start_time', 'asc');
        }

        $perPage = (int) ($filters['per_page'] ?? 15);
        $shouldPaginate = !empty($filters['paginate']) && filter_var($filters['paginate'], FILTER_VALIDATE_BOOLEAN);

        return $shouldPaginate ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get event by slug or ID.
     */
    public function getEvent(string|int $identifier): Event
    {
        if (is_numeric($identifier)) {
            return Event::findOrFail((int) $identifier);
        }

        return Event::where('slug', $identifier)->firstOrFail();
    }

    /**
     * Register an attendee for an event.
     */
    public function registerAttendee(Event $event, array $data): EventAttendee
    {
        return $event->attendees()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => 'registered',
        ]);
    }

    /**
     * Create a new event.
     */
    public function createEvent(array $data, ?UploadedFile $coverImageFile = null, ?User $organizer = null): Event
    {
        $slug = Str::slug($data['title_en'] ?? 'event') . '-' . rand(100, 999);

        $coverImage = $data['cover_image'] ?? 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1200&q=80';
        if ($coverImageFile) {
            $path = $coverImageFile->store('event_covers', 'public');
            $coverImage = '/storage/' . $path;
        }

        $event = Event::create([
            'title' => [
                'ar' => $data['title_ar'],
                'en' => $data['title_en'],
            ],
            'slug' => $slug,
            'location' => [
                'ar' => $data['location_ar'],
                'en' => $data['location_en'],
            ],
            'organizer' => [
                'ar' => $data['organizer_ar'],
                'en' => $data['organizer_en'],
            ],
            'description' => [
                'ar' => $data['description_ar'],
                'en' => $data['description_en'],
            ],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'] ?? null,
            'cover_image' => $coverImage,
        ]);

        AuditLog::record(
            action: 'create',
            auditable: $event,
            oldValues: null,
            newValues: ['title_ar' => $data['title_ar'], 'title_en' => $data['title_en'], 'start_time' => $data['start_time']],
            module: 'events',
            descriptionAr: "جدولة ونشر فعالية جديدة: {$data['title_ar']}",
            descriptionEn: "Created and scheduled event: {$data['title_en']}",
            severity: 'info',
            status: 'success'
        );

        return $event;
    }

    /**
     * Update an existing event.
     */
    public function updateEvent(Event $event, array $data, ?UploadedFile $coverImageFile = null): Event
    {
        $oldValues = $event->only(['title', 'location', 'start_time', 'end_time']);

        if (isset($data['title_ar'])) {
            $event->setTranslation('title', 'ar', $data['title_ar']);
        }
        if (isset($data['title_en'])) {
            $event->setTranslation('title', 'en', $data['title_en']);
        }
        if (isset($data['location_ar'])) {
            $event->setTranslation('location', 'ar', $data['location_ar']);
        }
        if (isset($data['location_en'])) {
            $event->setTranslation('location', 'en', $data['location_en']);
        }
        if (isset($data['organizer_ar'])) {
            $event->setTranslation('organizer', 'ar', $data['organizer_ar']);
        }
        if (isset($data['organizer_en'])) {
            $event->setTranslation('organizer', 'en', $data['organizer_en']);
        }
        if (isset($data['description_ar'])) {
            $event->setTranslation('description', 'ar', $data['description_ar']);
        }
        if (isset($data['description_en'])) {
            $event->setTranslation('description', 'en', $data['description_en']);
        }
        if (isset($data['start_time'])) {
            $event->start_time = $data['start_time'];
        }
        if (array_key_exists('end_time', $data)) {
            $event->end_time = $data['end_time'];
        }
        if ($coverImageFile) {
            $path = $coverImageFile->store('event_covers', 'public');
            $event->cover_image = '/storage/' . $path;
        } elseif (isset($data['cover_image'])) {
            $event->cover_image = $data['cover_image'];
        }

        $event->save();

        AuditLog::record(
            action: 'update',
            auditable: $event,
            oldValues: $oldValues,
            newValues: $event->only(['title', 'location', 'start_time', 'end_time']),
            module: 'events',
            descriptionAr: "تحديث وتعديل بيانات الفعالية: {$event->title}",
            descriptionEn: "Updated event: {$event->title}",
            severity: 'info',
            status: 'success'
        );

        return $event;
    }

    /**
     * Delete an event.
     */
    public function deleteEvent(Event $event): void
    {
        $title = $event->title;
        $id = $event->id;

        $event->delete();

        AuditLog::record(
            action: 'delete',
            auditable: Event::class,
            oldValues: ['id' => $id, 'title' => $title],
            newValues: null,
            module: 'events',
            descriptionAr: "إلغاء وحذف الفعالية: {$title}",
            descriptionEn: "Deleted event: {$title}",
            severity: 'warning',
            status: 'success'
        );
    }
}
