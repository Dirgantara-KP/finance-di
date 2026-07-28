<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ApiResponse
{
    public static function success(mixed $data = null, string $message = 'Berhasil', int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public static function error(
        string $message = 'Terjadi kesalahan',
        int $code = 400,
        string $errorCode = 'ERROR',
        ?array $errors = null,
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'code' => $errorCode,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    public static function paginated($paginator, string $message = 'Berhasil'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public static function created(mixed $data = null, string $message = 'Data berhasil dibuat'): JsonResponse
    {
        return self::success(data: $data, message: $message, code: 201);
    }

    public static function noContent(string $message = 'Data berhasil dihapus'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], 204);
    }

    public static function validation(ValidationException $exception): JsonResponse
    {
        return self::error(
            message: 'Validasi gagal.',
            code: 422,
            errorCode: 'VALIDATION_ERROR',
            errors: $exception->errors(),
        );
    }

    public static function notFound(string $message = 'Data yang dicari tidak ditemukan.'): JsonResponse
    {
        return self::error($message, 404, 'NOT_FOUND');
    }

    public static function forbidden(string $message = 'Anda tidak memiliki akses untuk melakukan ini.'): JsonResponse
    {
        return self::error($message, 403, 'FORBIDDEN');
    }

    public static function unauthorized(string $message = 'Anda belum terautentikasi.'): JsonResponse
    {
        return self::error($message, 401, 'UNAUTHENTICATED');
    }

    public static function tooManyRequests(string $message = 'Terlalu banyak request. Silakan coba lagi nanti.'): JsonResponse
    {
        return self::error($message, 429, 'RATE_LIMITED');
    }

    public static function serverError(string $message = 'Terjadi kesalahan internal server.'): JsonResponse
    {
        return self::error($message, 500, 'INTERNAL_ERROR');
    }

    public static function serviceUnavailable(string $message = 'Sistem sedang mengalami gangguan. Silakan coba lagi nanti.'): JsonResponse
    {
        return self::error($message, 503, 'SERVICE_UNAVAILABLE');
    }
}
