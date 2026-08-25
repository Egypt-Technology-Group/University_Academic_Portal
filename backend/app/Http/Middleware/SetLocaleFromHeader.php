<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('locale')
            ?: $request->query('lang')
            ?: $request->header('X-Locale');

        if (!$locale && $request->hasHeader('Accept-Language')) {
            $header = $request->header('Accept-Language');
            if (preg_match('/^([a-z]{2})/i', $header, $matches)) {
                $locale = strtolower($matches[1]);
            }
        }

        if ($locale && in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
