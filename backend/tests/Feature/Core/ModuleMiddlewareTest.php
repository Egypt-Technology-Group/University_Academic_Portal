<?php
declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Core\BaseModule;
use App\Core\ModuleManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MiddlewareAcademicModule extends BaseModule
{
    protected string $id = 'academic-structure';
    protected array $name = ['ar' => 'الهيكل الأكاديمي', 'en' => 'Academic Structure'];
}

class MiddlewareAdmissionsModule extends BaseModule
{
    protected string $id = 'admissions';
    protected array $name = ['ar' => 'القبول والتسجيل', 'en' => 'Admissions'];
    protected array $dependencies = ['academic-structure'];
}

class ModuleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected ModuleManager $moduleManager;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        config(['modules.default_enabled' => []]);

        $this->moduleManager = $this->app->make(ModuleManager::class);
        $this->moduleManager->reset();

        $this->moduleManager->register(new MiddlewareAcademicModule());
        $this->moduleManager->register(new MiddlewareAdmissionsModule());

        // Register test routes with module.enabled middleware
        Route::get('/api/test-academic', function () {
            return response()->json(['message' => 'Academic module is accessible.']);
        })->middleware('module.enabled:academic-structure');

        Route::get('/api/test-admissions', function () {
            return response()->json(['message' => 'Admissions module is accessible.']);
        })->middleware('module.enabled:admissions');
    }

    public function test_route_returns_404_when_module_is_disabled(): void
    {
        $response = $this->getJson('/api/test-academic');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Module [academic-structure] is currently disabled.',
                'error' => 'module_disabled',
                'module_id' => 'academic-structure',
            ]);
    }

    public function test_route_returns_200_when_module_is_enabled(): void
    {
        $this->moduleManager->enable('academic-structure');

        $response = $this->getJson('/api/test-academic');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Academic module is accessible.',
            ]);
    }

    public function test_route_blocks_dependent_module_when_it_is_disabled(): void
    {
        // Even if base is enabled, dependent is disabled
        $this->moduleManager->enable('academic-structure');

        $response = $this->getJson('/api/test-admissions');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Module [admissions] is currently disabled.',
                'error' => 'module_disabled',
                'module_id' => 'admissions',
            ]);
    }

    public function test_route_allows_dependent_module_when_enabled(): void
    {
        $this->moduleManager->enable('academic-structure');
        $this->moduleManager->enable('admissions');

        $response = $this->getJson('/api/test-admissions');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Admissions module is accessible.',
            ]);
    }

    public function test_route_blocks_access_after_module_is_disabled(): void
    {
        $this->moduleManager->enable('academic-structure');
        $this->moduleManager->enable('admissions');

        // Verify accessible
        $this->getJson('/api/test-admissions')->assertStatus(200);

        // Disable module
        $this->moduleManager->disable('admissions');

        // Verify blocked
        $this->getJson('/api/test-admissions')
            ->assertStatus(404)
            ->assertJson([
                'module_id' => 'admissions',
            ]);
    }
}