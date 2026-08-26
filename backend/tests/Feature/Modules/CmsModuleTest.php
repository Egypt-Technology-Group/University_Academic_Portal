<?php
declare(strict_types=1);

namespace Tests\Feature\Modules;

use App\Core\ModuleManager;
use App\Models\User;
use App\Modules\Cms\Models\Announcement;
use App\Modules\Cms\Models\NewsArticle;
use App\Modules\Cms\Models\NewsCategory;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CmsModuleTest extends TestCase
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
        $this->installTestLicense(['cms']);
        $this->moduleManager->enable('cms');

        $this->adminUser = User::where('email', 'admin@university.edu.eg')->first();
        $this->adminToken = $this->adminUser->createToken('test-token')->plainTextToken;
    }

    public function test_cms_module_metadata(): void
    {
        $module = $this->moduleManager->get('cms');

        $this->assertNotNull($module);
        $this->assertEquals('cms', $module->getId());
        $this->assertEquals('إدارة المحتوى والأخبار', $module->getName('ar'));
        $this->assertEquals([], $module->getDependencies());
        $this->assertEquals([
            'news_categories',
            'news_articles',
            'announcements',
        ], $module->getOwnedTables());
    }

    public function test_public_cms_routes_are_accessible_when_enabled(): void
    {
        $this->getJson('/api/v1/news')->assertStatus(200);
        $this->getJson('/api/v1/announcements')->assertStatus(200);

        $article = NewsArticle::first();
        if ($article) {
            $this->getJson("/api/v1/news/{$article->slug}")->assertStatus(200);
        }
    }

    public function test_public_cms_routes_return_404_when_disabled(): void
    {
        $this->moduleManager->disable('cms');

        $this->getJson('/api/v1/news')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'cms',
            ]);

        $this->getJson('/api/v1/announcements')
            ->assertStatus(404)
            ->assertJson([
                'error' => 'module_disabled',
                'module_id' => 'cms',
            ]);
    }

    public function test_admin_cms_crud_operations(): void
    {
        $category = NewsCategory::first();

        // 1. Create News
        $createNews = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/v1/admin/news', [
                'category_id' => $category->id,
                'title_ar' => 'خبر تجريبي مميز',
                'title_en' => 'Featured Test News',
                'body_ar' => 'تفاصيل الخبر بالعربية',
                'body_en' => 'News details in English',
                'is_featured' => true,
            ]);

        $createNews->assertStatus(201)
            ->assertJsonPath('data.title', 'Featured Test News');

        $newsId = $createNews->json('data.id');

        // 2. Update News
        $updateNews = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/v1/admin/news/{$newsId}", [
                'title_en' => 'Updated Featured Test News',
            ]);

        $updateNews->assertStatus(200);

        // 3. Delete News
        $deleteNews = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->deleteJson("/api/v1/admin/news/{$newsId}");

        $deleteNews->assertStatus(200);

        // 4. Create Announcement
        $createAnn = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->postJson('/api/v1/admin/announcements', [
                'title_ar' => 'إعلان هام',
                'title_en' => 'Important Alert',
                'content_ar' => 'محتوى الإعلان بالعربية',
                'content_en' => 'Content in English',
                'target_audience' => 'all',
                'priority' => 'urgent',
            ]);

        $createAnn->assertStatus(201);
        $annId = $createAnn->json('data.id');

        // 5. Update Announcement
        $updateAnn = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/v1/admin/announcements/{$annId}", [
                'title_en' => 'Updated Alert',
            ]);

        $updateAnn->assertStatus(200);

        // 6. Delete Announcement
        $deleteAnn = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->deleteJson("/api/v1/admin/announcements/{$annId}");

        $deleteAnn->assertStatus(200);
    }
}
