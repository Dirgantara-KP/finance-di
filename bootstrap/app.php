<?php

use App\Exceptions\DuplicateTransactionException;
use App\Exceptions\ExportException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InvalidPeriodException;
use App\Http\Middleware\LogRequestResponse;
use App\Http\Middleware\TrackRequestId;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(
            prepend: [TrackRequestId::class, LogRequestResponse::class],
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => ($request->is('api/*') ||
                $request->expectsJson()) &&
                ! $request->hasHeader('X-Livewire'),
        );

        $exceptions->renderable(function (QueryException $e, Request $request) {
            $message = strtolower($e->getMessage());
            $isConnectionError =
                str_contains($message, 'connection') ||
                str_contains($message, 'refused') ||
                str_contains($message, 'unknown database') ||
                str_contains($message, 'access denied') ||
                str_contains($message, 'no such file or directory') ||
                str_contains($message, 'could not find driver');

            if ($isConnectionError) {
                Log::critical('Database connection failed', [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'Sistem sedang mengalami gangguan. Silakan coba lagi.',
                            'code' => 'DATABASE_CONNECTION_ERROR',
                        ],
                        503,
                    );
                }

                return response()->view(
                    'errors.503',
                    [
                        'message' => 'Sistem sedang mengalami gangguan database.',
                    ],
                    503,
                );
            }
        });

        $exceptions->renderable(function (
            ModelNotFoundException $e,
            Request $request,
        ) {
            if ($request->hasHeader('X-Livewire')) {
                return null;
            }

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Data yang dicari tidak ditemukan.',
                    'code' => 'NOT_FOUND',
                ],
                404,
            );
        });

        $exceptions->renderable(function (
            InsufficientBalanceException $e,
            Request $request,
        ) {
            if ($request->hasHeader('X-Livewire')) {
                return null;
            }

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'code' => 'INSUFFICIENT_BALANCE',
                ],
                422,
            );
        });

        $exceptions->renderable(function (
            DuplicateTransactionException $e,
            Request $request,
        ) {
            if ($request->hasHeader('X-Livewire')) {
                return null;
            }

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'code' => 'DUPLICATE_ENTRY',
                ],
                409,
            );
        });

        $exceptions->renderable(function (
            ExportException $e,
            Request $request,
        ) {
            if ($request->hasHeader('X-Livewire')) {
                return null;
            }

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'code' => 'EXPORT_FAILED',
                ],
                500,
            );
        });

        $exceptions->renderable(function (
            InvalidPeriodException $e,
            Request $request,
        ) {
            if ($request->hasHeader('X-Livewire')) {
                return null;
            }

            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'code' => 'INVALID_PERIOD',
                ],
                422,
            );
        });

        $exceptions->renderable(function (
            TooManyRequestsException $e,
            Request $request,
        ) {
            if ($request->hasHeader('X-Livewire')) {
                return null;
            }

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terlalu banyak request. Silakan tunggu sebentar.',
                    'code' => 'RATE_LIMITED',
                ],
                429,
            );
        });

        $exceptions->renderable(function (
            NotFoundHttpException $e,
            Request $request,
        ) {
            if ($request->hasHeader('X-Livewire')) {
                return null;
            }

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Halaman atau endpoint tidak ditemukan.',
                    'code' => 'NOT_FOUND',
                ],
                404,
            );
        });

        $exceptions->renderable(function (
            MethodNotAllowedHttpException $e,
            Request $request,
        ) {
            if ($request->hasHeader('X-Livewire')) {
                return null;
            }

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Metode request tidak diizinkan.',
                    'code' => 'METHOD_NOT_ALLOWED',
                ],
                405,
            );
        });
    })
    ->create();
