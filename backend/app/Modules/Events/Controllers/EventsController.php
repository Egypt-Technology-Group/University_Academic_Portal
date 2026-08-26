<?php
declare(strict_types=1);

namespace App\Modules\Events\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Events\Models\Event;
use App\Modules\Events\Requests\RegisterEventAttendeeRequest;
use App\Modules\Events\Resources\EventResource;
use App\Modules\Events\Services\EventsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventsController extends Controller
{
    public function __construct(
        protected readonly EventsService $eventsService
    ) {}

    /**
     * List events (upcoming or past).
     */
    public function events(Request $request): AnonymousResourceCollection
    {
        $events = $this->eventsService->getEvents($request->all());

        return EventResource::collection($events);
    }

    /**
     * Register an attendee for an academic event.
     */
    public function registerForEvent(RegisterEventAttendeeRequest $request, int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $attendee = $this->eventsService->registerAttendee($event, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Attendance registered successfully.',
            'data' => $attendee,
        ], 201);
    }
}
