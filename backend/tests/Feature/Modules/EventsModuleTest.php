<?php
declare(strict_types=1);

namespace Tests\Feature\Modules;

use App\Core\ModuleManager;
use App\Models\User;
use App\Modules\Events\Models\Event;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class EventsModuleTest extends TestCase
{
    use RefreshDatabase;

    protected ModuleManager $moduleManager;
    protected User $adminUser;
    protected string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        $this->seed(DatabaseSeeder::class);

        $this->moduleManager = $this->app->make(ModuleManager::class);
        $this->installTestLicense(['events']);
        $this->moduleManager->enable('events');

        $this->adminUser = User::where('email', 'admin@university.edu.eg')->first();
        $this->adminToken = $this->adminUser->createToken('test-token')->plainTextToken;
    }

    public function test_events_module_metadata(): void
    {
        $module = $this->moduleManager->get('events');

        $this->assertNotNull($module);
        $this->assertEquals('events', $module->getId());
        $this->assertEquals('الفعاليات والأنشطة', $module->getName('ar'));
        $this->assertEquals([], $module->getDependencies());
        $this->assertEquals([
            'events',
            'event_attendees',
        ], $module->getOwnedTables());
    }

    public function test_public_events_routes_are_accessible_when_enabled(): void
    {
        $this->getJson('/api/v1/events')->assertStatus(200);

        $event = Event::first();
        if ($event) {
            $regResponse = $this->postJson("/api/v1/events/{$event->id}/register", [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+201000000000',
            ]);
            $regResponse->assertStatus(201);
        }
    }

    public function test_public_events_routes_return_404_when_disabled(): void
    {
        $this->moduleManager->disable('events');

        $this->getJson('/api/v1/events')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'events',
            ]);
    }

    public function test_admin_events_crud_operations(): void
    {
        // 1. Create Event
        $createEvent = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/v1/admin/events', [
                'title_ar' => 'المؤتمر السنوي للتكنولوجيا 2026',
                'title_en' => 'Annual Tech Conference 2026',
                'location_ar' => 'قاعة المؤتمرات الكبرى',
                'location_en' => 'Grand Conference Hall',
                'organizer_ar' => 'كلية الحاسبات والذكاء الاصطناعي',
                'organizer_en' => 'Faculty of CS & AI',
                'description_ar' => 'وصف تفصيلي للفعالية السنوية والمحاور المطروحة.',
                'description_en' => 'Detailed description of the annual conference.',
                'start_time' => now()->addDays(10)->toIso8601String(),
                'end_time' => now()->addDays(11)->toIso8601String(),
            ]);

        $createEvent->assertStatus(201)
            ->assertJsonPath('data.title', 'Annual Tech Conference 2026');

        $eventId = $createEvent->json('data.id');

        // 2. Update Event
        $updateEvent = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/v1/admin/events/{$eventId}", [
                'title_en' => 'Updated Annual Tech Conference 2026',
            ]);

        $updateEvent->assertStatus(200);

        // 3. Delete Event
        $deleteEvent = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->deleteJson("/api/v1/admin/events/{$eventId}");

        $deleteEvent->assertStatus(200);
    }
}
