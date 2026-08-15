<?php

namespace App\Http\Middleware;

use App\Services\UserActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        // Only log page views for authenticated users
        if (auth()->check()) {
            $responseTimeMs = (int) round((microtime(true) - $startTime) * 1000);

            UserActivityLogger::logPageView(
                user: auth()->user(),
                request: $request,
                responseStatus: $response->status(),
                responseTimeMs: $responseTimeMs
            );
        }

        return $response;
    }
}
