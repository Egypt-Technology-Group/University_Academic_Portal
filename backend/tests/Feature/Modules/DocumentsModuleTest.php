<?php
declare(strict_types=1);

namespace Tests\Feature\Modules;

use App\Core\ModuleManager;
use App\Models\User;
use App\Modules\Documents\Models\DownloadDocument;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DocumentsModuleTest extends TestCase
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
        $this->moduleManager->enable('documents');

        $this->adminUser = User::where('email', 'admin@university.edu.eg')->first();
        $this->adminToken = $this->adminUser->createToken('test-token')->plainTextToken;
    }

    public function test_documents_module_metadata(): void
    {
        $module = $this->moduleManager->get('documents');

        $this->assertNotNull($module);
        $this->assertEquals('documents', $module->getId());
        $this->assertEquals('مركز الوثائق واللوائح', $module->getName('ar'));
        $this->assertEquals([], $module->getDependencies());
        $this->assertEquals([
            'download_documents',
        ], $module->getOwnedTables());
    }

    public function test_public_documents_routes_are_accessible_when_enabled(): void
    {
        $this->getJson('/api/v1/documents')->assertStatus(200);

        $doc = DownloadDocument::first();
        if ($doc) {
            $downloadResponse = $this->postJson("/api/v1/documents/{$doc->id}/download");
            $downloadResponse->assertStatus(200)
                ->assertJsonPath('success', true);
        }
    }

    public function test_public_documents_routes_return_404_when_disabled(): void
    {
        $this->moduleManager->disable('documents');

        $this->getJson('/api/v1/documents')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'documents',
            ]);
    }

    public function test_admin_documents_crud_operations(): void
    {
        // 1. Create Document
        $createDoc = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/v1/admin/documents', [
                'category' => 'bylaws',
                'title_ar' => 'لائحة الدراسات العليا المحدثة 2026',
                'title_en' => 'Updated Postgraduate Bylaw 2026',
                'description_ar' => 'الدليل الشامل للوائح والضوابط الأكاديمية.',
                'description_en' => 'Comprehensive academic bylaws guideline.',
                'version' => '2.0',
                'status' => 'published',
            ]);

        $createDoc->assertStatus(201)
            ->assertJsonPath('data.title', 'Updated Postgraduate Bylaw 2026');

        $docId = $createDoc->json('data.id');

        // 2. Update Document
        $updateDoc = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/v1/admin/documents/{$docId}", [
                'title_en' => 'Revised Postgraduate Bylaw 2026',
            ]);

        $updateDoc->assertStatus(200);

        // 3. Toggle Archive
        $toggleArchive = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson("/api/v1/admin/documents/{$docId}/toggle-archive");

        $toggleArchive->assertStatus(200)
            ->assertJsonPath('data.is_archived', true);

        // 4. Delete Document
        $deleteDoc = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->deleteJson("/api/v1/admin/documents/{$docId}");

        $deleteDoc->assertStatus(200);
    }
}
