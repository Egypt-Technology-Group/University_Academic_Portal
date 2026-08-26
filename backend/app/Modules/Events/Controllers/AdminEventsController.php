<?php
declare(strict_types=1);

namespace App\Modules\Events\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Events\Models\Event;
use App\Modules\Events\Requests\StoreEventRequest;
use App\Modules\Events\Requests\UpdateEventRequest;
use App\Modules\Events\Resources\EventResource;
use App\Modules\Events\Services\EventsService;
use Illuminate\Http\JsonResponse;

class AdminEventsController extends Controller
{
    public function __construct(
        protected readonly EventsService $eventsService
    ) {}

    public function storeEvent(StoreEventRequest $request): JsonResponse
    {
        $coverFile = $request->file('cover_image_file') ?? $request->file('image');
        $event = $this->eventsService->createEvent(
            $request->validated(),
            $coverFile,
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Event scheduled successfully.',
            'data' => new EventResource($event),
        ], 201);
    }

    public function updateEvent(UpdateEventRequest $request, int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $coverFile = $request->file('cover_image_file') ?? $request->file('image');

        $updated = $this->eventsService->updateEvent(
            $event,
            $request->validated(),
            $coverFile
        );

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully.',
            'data' => new EventResource($updated),
        ]);
    }

    public function deleteEvent(int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $this->eventsService->deleteEvent($event);

        return response()->json([
            'success' => true,
            'message' => 'Event cancelled and removed.',
        ]);
    }
}
