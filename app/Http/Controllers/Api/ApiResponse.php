<?php

namespace App\Http\Controllers\Api;

trait ApiResponse
{
    protected function successResponse(mixed $data = null, string $message = 'Berhasil', int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function errorResponse(string $message = 'Terjadi kesalahan', int $status = 400, mixed $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
