<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequestResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        Log::info('Incoming request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $request->user()?->id,
            'request_id' => $request->header('X-Request-Id'),
        ]);

        $response = $next($request);

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        Log::info('Response sent', [
            'method' => $request->method(),
            'url' => $request->path(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'request_id' => $request->header('X-Request-Id'),
        ]);

        if ($duration > 3000) {
            Log::warning('Slow response detected', [
                'url' => $request->path(),
                'duration_ms' => $duration,
            ]);
        }

        return $response;
    }
}
