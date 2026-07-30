<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackRequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-Id', Str::uuid()->toString());

        $request->headers->set('X-Request-Id', $requestId);

        $response = $next($request);

        if ($response instanceof Response) {
            $response->headers->set('X-Request-Id', $requestId);
        }

        return $response;
    }
}
