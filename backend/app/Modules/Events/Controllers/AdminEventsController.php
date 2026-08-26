<?php
declare(strict_types=1);

namespace App\Modules\Events\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Modules\Events\Models\Event;
use App\Modules\Events\Resources\EventResource;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminEventsController extends Controller
{
    public function storeEvent(Request $request): JsonResponse
    {
        // Handle input aliases from different frontend forms
        if ($request->filled('venue_ar') && !$request->filled('location_ar')) {
            $request->merge(['location_ar' => $request->input('venue_ar')]);
        }
        if ($request->filled('venue_en') && !$request->filled('location_en')) {
            $request->merge(['location_en' => $request->input('venue_en')]);
        } elseif (!$request->filled('location_en') && $request->filled('location_ar')) {
            $request->merge(['location_en' => $request->input('location_ar')]);
        }

        if (!$request->filled('organizer_ar')) {
            $request->merge(['organizer_ar' => 'إدارة الجامعة']);
        }
        if (!$request->filled('organizer_en')) {
            $request->merge(['organizer_en' => 'University Administration']);
        }

        if (!$request->filled('description_en') && $request->filled('description_ar')) {
            $request->merge(['description_en' => $request->input('description_ar')]);
        }

        if ($request->filled('banner_image') && !$request->filled('cover_image')) {
            $request->merge(['cover_image' => $request->input('banner_image')]);
        }

        // Format start_time / end_time if sent as date + time
        if ($request->filled('event_date') && $request->filled('start_time') && strlen((string)$request->input('start_time')) <= 8) {
            $date = $request->input('event_date');
            $time = $request->input('start_time');
            try {
                $request->merge(['start_time' => Carbon::parse("{$date} {$time}")->toIso8601String()]);
            } catch (\Exception $e) {
                // Keep raw start_time
            }
        }
        if ($request->filled('event_date') && $request->filled('end_time') && strlen((string)$request->input('end_time')) <= 8) {
            $date = $request->input('event_date');
            $time = $request->input('end_time');
            try {
                $request->merge(['end_time' => Carbon::parse("{$date} {$time}")->toIso8601String()]);
            } catch (\Exception $e) {
                // Keep raw end_time
            }
        }

        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'location_ar' => 'required|string',
            'location_en' => 'required|string',
            'organizer_ar' => 'required|string',
            'organizer_en' => 'required|string',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date',
            'cover_image' => 'nullable|string',
        ]);

        $slug = Str::slug($validated['title_en']).'-'.rand(100, 999);

        $event = Event::create([
            'title' => ['ar' => $validated['title_ar'], 'en' => $validated['title_en']],
            'slug' => $slug,
            'location' => ['ar' => $validated['location_ar'], 'en' => $validated['location_en']],
            'organizer' => ['ar' => $validated['organizer_ar'], 'en' => $validated['organizer_en']],
            'description' => ['ar' => $validated['description_ar'], 'en' => $validated['description_en']],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'cover_image' => $validated['cover_image'] ?? 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1200&q=80',
        ]);

        AuditLog::record(
            action: 'create',
            auditable: $event,
            oldValues: null,
            newValues: ['title_ar' => $validated['title_ar'], 'title_en' => $validated['title_en'], 'start_time' => $validated['start_time']],
            module: 'events',
            descriptionAr: "جدولة ونشر فعالية جديدة: {$validated['title_ar']}",
            descriptionEn: "Created and scheduled event: {$validated['title_en']}",
            severity: 'info',
            status: 'success'
        );

        return response()->json([
            'success' => true,
            'message' => 'Event scheduled successfully.',
            'data' => new EventResource($event),
        ], 201);
    }

    public function updateEvent(Request $request, int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $oldValues = $event->only(['title', 'location', 'start_time', 'end_time']);

        if ($request->filled('venue_ar') && !$request->filled('location_ar')) {
            $request->merge(['location_ar' => $request->input('venue_ar')]);
        }
        if ($request->filled('venue_en') && !$request->filled('location_en')) {
            $request->merge(['location_en' => $request->input('venue_en')]);
        }
        if ($request->filled('banner_image') && !$request->filled('cover_image')) {
            $request->merge(['cover_image' => $request->input('banner_image')]);
        }

        if ($request->filled('event_date') && $request->filled('start_time') && strlen((string)$request->input('start_time')) <= 8) {
            $date = $request->input('event_date');
            $time = $request->input('start_time');
            try {
                $request->merge(['start_time' => Carbon::parse("{$date} {$time}")->toIso8601String()]);
            } catch (\Exception $e) {
                // Keep raw start_time
            }
        }
        if ($request->filled('event_date') && $request->filled('end_time') && strlen((string)$request->input('end_time')) <= 8) {
            $date = $request->input('event_date');
            $time = $request->input('end_time');
            try {
                $request->merge(['end_time' => Carbon::parse("{$date} {$time}")->toIso8601String()]);
            } catch (\Exception $e) {
                // Keep raw end_time
            }
        }

        $validated = $request->validate([
            'title_ar' => 'sometimes|required|string|max:255',
            'title_en' => 'sometimes|required|string|max:255',
            'location_ar' => 'sometimes|required|string',
            'location_en' => 'sometimes|required|string',
            'organizer_ar' => 'sometimes|required|string',
            'organizer_en' => 'sometimes|required|string',
            'description_ar' => 'sometimes|required|string',
            'description_en' => 'sometimes|required|string',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'nullable|date',
            'cover_image' => 'nullable|string',
        ]);

        if (isset($validated['title_ar'])) $event->setTranslation('title', 'ar', $validated['title_ar']);
        if (isset($validated['title_en'])) $event->setTranslation('title', 'en', $validated['title_en']);
        if (isset($validated['location_ar'])) $event->setTranslation('location', 'ar', $validated['location_ar']);
        if (isset($validated['location_en'])) $event->setTranslation('location', 'en', $validated['location_en']);
        if (isset($validated['organizer_ar'])) $event->setTranslation('organizer', 'ar', $validated['organizer_ar']);
        if (isset($validated['organizer_en'])) $event->setTranslation('organizer', 'en', $validated['organizer_en']);
        if (isset($validated['description_ar'])) $event->setTranslation('description', 'ar', $validated['description_ar']);
        if (isset($validated['description_en'])) $event->setTranslation('description', 'en', $validated['description_en']);
        if (isset($validated['start_time'])) $event->start_time = $validated['start_time'];
        if (isset($validated['end_time'])) $event->end_time = $validated['end_time'];
        if (isset($validated['cover_image'])) $event->cover_image = $validated['cover_image'];

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

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully.',
            'data' => new EventResource($event),
        ]);
    }

    public function deleteEvent(int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $title = $event->title;
        $event->delete();

        AuditLog::record(
            action: 'delete',
            auditable: 'App\Modules\Events\Models\Event',
            oldValues: ['id' => $id, 'title' => $title],
            newValues: null,
            module: 'events',
            descriptionAr: "إلغاء وحذف الفعالية: {$title}",
            descriptionEn: "Deleted event: {$title}",
            severity: 'warning',
            status: 'success'
        );

        return response()->json([
            'success' => true,
            'message' => 'Event cancelled and removed.',
        ]);
    }
}
