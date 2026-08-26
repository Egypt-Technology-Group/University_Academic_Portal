<?php
declare(strict_types=1);

namespace App\Modules\Events\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Events\Models\Event;
use App\Modules\Events\Resources\EventResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventsController extends Controller
{
    /**
     * List events (upcoming or past).
     */
    public function events(Request $request): AnonymousResourceCollection
    {
        $query = Event::query();

        if ($request->input('filter') === 'past' || $request->boolean('past')) {
            $query->where('end_time', '<', now())->orderBy('start_time', 'desc');
        } elseif ($request->input('filter') === 'all' || $request->boolean('all')) {
            $query->orderBy('start_time', 'asc');
        } else {
            // Default: upcoming events
            $query->where('end_time', '>=', now())->orderBy('start_time', 'asc');
        }

        $perPage = (int) $request->input('per_page', 15);
        $events = $request->boolean('paginate') ? $query->paginate($perPage) : $query->get();

        return EventResource::collection($events);
    }

    /**
     * Register an attendee for an academic event.
     */
    public function registerForEvent(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
        ]);

        $event = Event::findOrFail($id);

        $attendee = $event->attendees()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => 'registered',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance registered successfully.',
            'data' => $attendee,
        ], 201);
    }
}
