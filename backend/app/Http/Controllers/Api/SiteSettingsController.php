<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteSettingsController extends Controller
{
    /**
     * Get all public site settings for frontend consumption.
     */
    public function getPublicSettings(): JsonResponse
    {
        $settings = SiteSetting::where('is_public', true)->get();
        
        $keyed = [];
        foreach ($settings as $setting) {
            $keyed[$setting->key] = $setting->value;
        }

        return response()->json([
            'success' => true,
            'settings' => $keyed,
        ]);
    }

    /**
     * Get all settings (including private / admin) for Admin Dashboard.
     */
    public function index(Request $request): JsonResponse
    {
        $settings = SiteSetting::all();

        $keyed = [];
        foreach ($settings as $setting) {
            $keyed[$setting->key] = [
                'id' => $setting->id,
                'key' => $setting->key,
                'group' => $setting->group,
                'value' => $setting->value,
                'is_public' => $setting->is_public,
                'updated_at' => $setting->updated_at,
            ];
        }

        return response()->json([
            'success' => true,
            'settings' => $keyed,
        ]);
    }

    /**
     * Update one or multiple site settings.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required',
            'settings.*.group' => 'nullable|string',
            'settings.*.is_public' => 'nullable|boolean',
        ]);

        $updated = [];

        foreach ($validated['settings'] as $item) {
            $setting = SiteSetting::updateOrCreate(
                ['key' => $item['key']],
                [
                    'value' => $item['value'],
                    'group' => $item['group'] ?? 'general',
                    'is_public' => $item['is_public'] ?? true,
                ]
            );
            $updated[$setting->key] = $setting->value;
        }

        return response()->json([
            'success' => true,
            'message' => 'Site settings successfully updated.',
            'settings' => $updated,
        ]);
    }

    /**
     * Update a single specific site setting by key.
     */
    public function updateSingle(Request $request, string $key): JsonResponse
    {
        $validated = $request->validate([
            'value' => 'required',
            'group' => 'nullable|string',
            'is_public' => 'nullable|boolean',
        ]);

        $setting = SiteSetting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $validated['value'],
                'group' => $validated['group'] ?? 'general',
                'is_public' => $validated['is_public'] ?? true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Setting '{$key}' successfully updated.",
            'setting' => [
                'key' => $setting->key,
                'group' => $setting->group,
                'value' => $setting->value,
                'is_public' => $setting->is_public,
            ],
        ]);
    }

    /**
     * Reset site settings to default seed values.
     */
    public function resetToDefaults(): JsonResponse
    {
        $seeder = new \Database\Seeders\SiteSettingsSeeder();
        $seeder->run();

        return response()->json([
            'success' => true,
            'message' => 'Site settings successfully reset to factory defaults.',
        ]);
    }
}
