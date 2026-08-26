<?php
declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\ModuleManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function __construct(
        protected ModuleManager $moduleManager
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $moduleId
     */
    public function handle(Request $request, Closure $next, string $moduleId): Response
    {
        if (!$this->moduleManager->isEnabled($moduleId)) {
            return response()->json([
                'message' => "Module [{$moduleId}] is currently disabled.",
                'error' => 'module_disabled',
                'module_id' => $moduleId,
            ], 404);
        }

        return $next($request);
    }
}