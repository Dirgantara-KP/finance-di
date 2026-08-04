<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class FixServeUrl
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('FIXSERVEURL RAN '.$request->path());

        if (app()->environment('local', 'testing')) {
            $this->fixUrlIfServe($request);
        }

        return $next($request);
    }

    private function fixUrlIfServe(Request $request): void
    {
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? '';
        $isArtisanServe = str_contains($serverSoftware, 'Development Server');

        $appUrl = config('app.url');
        $currentHost = $request->getHost();
        $currentPort = (int) $request->getPort();
        $appHost = parse_url($appUrl, PHP_URL_HOST) ?? '';
        $appPort = (int) (parse_url($appUrl, PHP_URL_PORT) ?? 0);

        $isLocalhost = in_array($currentHost, ['localhost', '127.0.0.1'], true);
        $portMismatch = $appPort > 0 && $currentPort !== $appPort && $isLocalhost;
        $hostMismatch = $appHost !== '' && $appHost !== $currentHost && $isLocalhost;

        if ($isArtisanServe || $portMismatch || $hostMismatch) {
            $scheme = $request->headers->get('X-Forwarded-Proto', $request->getScheme());
            $portSuffix = $currentPort !== 80 && $currentPort !== 443 ? ':'.$currentPort : '';
            /** @var UrlGenerator $urlGenerator */
            $urlGenerator = app(UrlGenerator::class);
            $urlGenerator->forceRootUrl($scheme.'://'.$currentHost.$portSuffix);
        }
    }
}
