<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Core\Exceptions\ModuleDependencyException;
use App\Core\ModuleManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModuleManagementController extends Controller
{
    public function __construct(
        protected ModuleManager $moduleManager
    ) {
    }

    /**
     * List all registered modules with their metadata, dependencies, owned tables, and active status.
     *
     * GET /api/v1/modules
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->header('X-App-Locale', $request->get('locale', app()->getLocale() ?: 'ar'));
        $modules = $this->moduleManager->all();

        $data = [];
        foreach ($modules as $id => $module) {
            $isEnabled = $this->moduleManager->isEnabled($id);
            $canEnable = $this->moduleManager->canEnable($id);
            $canDisable = $this->moduleManager->canDisable($id);

            $data[] = [
                'id' => $id,
                'name' => [
                    'ar' => $module->getName('ar'),
                    'en' => $module->getName('en'),
                ],
                'display_name' => $module->getName($locale),
                'description' => [
                    'ar' => $module->getDescription('ar'),
                    'en' => $module->getDescription('en'),
                ],
                'display_description' => $module->getDescription($locale),
                'version' => $module->getVersion(),
                'dependencies' => $module->getDependencies(),
                'owned_tables' => $module->getOwnedTables(),
                'is_entitled' => $this->moduleManager->getEntitlementManager()->isModuleEntitled($id),
                'is_enabled' => $isEnabled,
                'can_enable' => $canEnable['can_enable'],
                'can_disable' => $canDisable['can_disable'],
            ];
        }

        return response()->json([
            'data' => $data,
            'meta' => [
                'total' => count($data),
                'enabled_count' => count($this->moduleManager->getEnabledIds()),
            ],
        ]);
    }

    /**
     * Return dependency tree and validation status for a specific module.
     *
     * GET /api/v1/modules/{id}/dependencies
     */
    public function dependencies(string $id): JsonResponse
    {
        $module = $this->moduleManager->get($id);
        if (!$module) {
            return response()->json([
                'message' => "Module [{$id}] not found.",
                'error' => 'module_not_found',
            ], 404);
        }

        $canEnableInfo = $this->moduleManager->canEnable($id);
        $canDisableInfo = $this->moduleManager->canDisable($id);
        $isEnabled = $this->moduleManager->isEnabled($id);

        // Find which active or registered modules depend on this module
        $allModules = $this->moduleManager->all();
        $dependents = [];
        foreach ($allModules as $modId => $mod) {
            if (in_array($id, $mod->getDependencies(), true)) {
                $dependents[] = [
                    'id' => $modId,
                    'name' => [
                        'ar' => $mod->getName('ar'),
                        'en' => $mod->getName('en'),
                    ],
                    'is_enabled' => $this->moduleManager->isEnabled($modId),
                ];
            }
        }

        return response()->json([
            'data' => [
                'id' => $id,
                'is_enabled' => $isEnabled,
                'dependencies' => $module->getDependencies(),
                'dependents' => $dependents,
                'can_enable' => $canEnableInfo['can_enable'],
                'missing_dependencies' => $canEnableInfo['missing_dependencies'] ?? [],
                'enable_block_reason' => $canEnableInfo['reason'] ?? null,
                'can_disable' => $canDisableInfo['can_disable'],
                'blocking_dependents' => $canDisableInfo['blocking_dependents'] ?? [],
                'disable_block_reason' => $canDisableInfo['reason'] ?? null,
            ],
        ]);
    }

    /**
     * Toggle module active state or explicitly set enabled status.
     *
     * PATCH /api/v1/modules/{id}/toggle
     */
    public function toggle(Request $request, string $id): JsonResponse
    {
        $module = $this->moduleManager->get($id);
        if (!$module) {
            return response()->json([
                'message' => "Module [{$id}] not found.",
                'error' => 'module_not_found',
            ], 404);
        }

        $currentState = $this->moduleManager->isEnabled($id);

        // Determine target state (if explicit 'enabled' is passed in request body, use it; otherwise invert)
        $targetState = $request->has('enabled')
            ? filter_var($request->input('enabled'), FILTER_VALIDATE_BOOLEAN)
            : !$currentState;

        // If target state is already current state, return early
        if ($targetState === $currentState) {
            return response()->json([
                'message' => "Module [{$id}] is already " . ($currentState ? 'enabled' : 'disabled') . '.',
                'data' => [
                    'id' => $id,
                    'is_enabled' => $currentState,
                ],
            ], 200);
        }

        try {
            if ($targetState) {
                $this->moduleManager->enable($id);
                $actionMessage = "Module [{$id}] has been enabled successfully.";
            } else {
                $this->moduleManager->disable($id);
                $actionMessage = "Module [{$id}] has been disabled successfully.";
            }

            return response()->json([
                'message' => $actionMessage,
                'data' => [
                    'id' => $id,
                    'is_enabled' => $targetState,
                ],
            ], 200);
        } catch (ModuleDependencyException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => 'dependency_conflict',
                'context' => $e->getContext(),
            ], 409);
        }
    }
}