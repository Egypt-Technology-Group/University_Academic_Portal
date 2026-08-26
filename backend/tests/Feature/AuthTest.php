<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->installTestLicense(autoEnable: true);
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_returns_token_and_user_data_with_roles(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@university.edu.eg',
            'password' => 'admin123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'roles',
                    'permissions',
                ],
            ]);

        $this->assertNotEmpty($response->json('token'));
        $this->assertEquals('admin@university.edu.eg', $response->json('user.email'));
        $this->assertContains('super-admin', $response->json('user.roles'));
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@university.edu.eg',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['email'],
            ]);
    }

    public function test_me_endpoint_returns_authenticated_user_profile(): void
    {
        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@university.edu.eg',
            'password' => 'admin123',
        ]);
        $token = $login->json('token');

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'email' => 'admin@university.edu.eg',
                ],
            ]);
    }

    public function test_protected_admin_stats_and_settings_require_auth(): void
    {
        // Without token -> 401
        $this->getJson('/api/v1/admin/stats')->assertStatus(401);
        $this->getJson('/api/v1/admin/settings')->assertStatus(401);
        $this->getJson('/api/v1/admin/audit-logs')->assertStatus(401);

        // Login
        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@university.edu.eg',
            'password' => 'admin123',
        ]);
        $token = $login->json('token');

        // With token -> 200
        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/admin/stats')
            ->assertStatus(200);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/admin/settings')
            ->assertStatus(200);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/admin/audit-logs')
            ->assertStatus(200);
    }

    public function test_token_persists_across_multiple_sequential_and_concurrent_requests(): void
    {
        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@university.edu.eg',
            'password' => 'admin123',
        ]);
        $token = $login->json('token');

        // Simulate 10 rapid sequential requests (like dashboard loading multiple widgets)
        for ($i = 0; $i < 10; $i++) {
            $response = $this->withHeader('Authorization', "Bearer {$token}")
                ->getJson('/api/v1/admin/stats');
            $response->assertStatus(200);
        }

        // Applications list
        $apps = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/admin/applications');
        $apps->assertStatus(200);
    }

    public function test_logout_revokes_token_and_blocks_subsequent_requests(): void
    {
        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@university.edu.eg',
            'password' => 'admin123',
        ]);
        $token = $login->json('token');

        // Verify valid
        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me')
            ->assertStatus(200);

        // Logout
        $logout = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/logout');
        $logout->assertStatus(200);

        // Clear test container auth guard cache so next request evaluates the token against database
        $this->app['auth']->forgetGuards();

        // Subsequent call must fail with 401
        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me')
            ->assertStatus(401);
    }

    public function test_dashboard_initialization_flow_and_data_loading(): void
    {
        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@university.edu.eg',
            'password' => 'admin123',
        ]);
        $token = $login->json('token');

        // Dashboard bootstrap: /stats, /applications, /settings, /modules
        $statsResp = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/admin/stats');
        $statsResp->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_colleges',
                    'total_programs',
                    'total_faculty',
                    'total_students',
                    'total_applications',
                    'pending_applications',
                    'accepted_applications',
                    'rejected_applications',
                    'total_news',
                    'total_events',
                    'total_documents',
                    'active_admission_cycles',
                ],
            ]);

        $appsResp = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/admin/applications');
        $appsResp->assertStatus(200)
            ->assertJsonStructure(['success', 'data', 'meta']);

        $settingsResp = $this->getJson('/api/v1/settings');
        $settingsResp->assertStatus(200);

        $modulesResp = $this->getJson('/api/v1/modules');
        $modulesResp->assertStatus(200);
    }

    public function test_invalid_or_expired_bearer_token_returns_401(): void
    {
        $this->withHeader('Authorization', 'Bearer invalid-token-12345')
            ->getJson('/api/v1/admin/stats')
            ->assertStatus(401)
            ->assertJson([
                'error' => 'unauthenticated',
            ]);
    }

    public function test_states_or_unknown_endpoint_returns_404_not_401(): void
    {
        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@university.edu.eg',
            'password' => 'admin123',
        ]);
        $token = $login->json('token');

        // Unknown endpoint like /states should return 404 (not 401 unauthenticated)
        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/states')
            ->assertStatus(404);
    }

    public function test_refresh_simulation_validates_profile_and_roles(): void
    {
        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@university.edu.eg',
            'password' => 'admin123',
        ]);
        $token = $login->json('token');

        // Simulation of page refresh: client calls /auth/me to re-validate user
        $meResp = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me');

        $meResp->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'roles',
                    'permissions',
                ],
            ]);

        $roles = $meResp->json('user.roles');
        $this->assertIsArray($roles);
        $this->assertContains('super-admin', $roles);
    }
}
