<?php

namespace App\Http\Controllers\Api;

trait ApiResponse
{
    protected function successResponse(
        mixed $data = null,
        string $message = 'Berhasil',
        int $status = 200
    ) {
        // Mengembalikan response JSON ketika request berhasil
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    protected function errorResponse(
        string $message = 'Terjadi kesalahan',
        int $status = 400,
        mixed $errors = null
    ) {
        // Mengembalikan response JSON ketika request gagal
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
