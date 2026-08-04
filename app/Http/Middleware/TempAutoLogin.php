<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TempAutoLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('TEMP-AUTO-LOGIN FIRST LINE');

        return $next($request);
    }
}
