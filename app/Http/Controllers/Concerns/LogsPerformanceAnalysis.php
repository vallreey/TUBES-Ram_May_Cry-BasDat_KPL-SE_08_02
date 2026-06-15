<?php

namespace App\Http\Controllers\Concerns;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait LogsPerformanceAnalysis
{
    private const DEFAULT_SORT = 'terbaru';
    private const LOG_TITLE_PREFIX = 'Performance Analysis';

    /**
     * Mengukur performa proses tanpa mengubah hasil data yang dikembalikan.
     */
    private function measurePerformance(
        string $featureName,
        Request $request,
        Closure $callback,
        array $additionalContext = []
    ): mixed {
        $result = null;
        $startTime = microtime(true);

        // Mengaktifkan query log sebelum proses dijalankan
        $this->startQueryLog();

        try {
            $result = $callback();

            return $result;
        } finally {
            // Menyimpan hasil pengukuran performa ke laravel.log
            $this->writePerformanceLog(
                $featureName,
                $request,
                $startTime,
                $result,
                $additionalContext
            );
        }
    }

    private function writePerformanceLog(
        string $featureName,
        Request $request,
        float $startTime,
        mixed $result,
        array $additionalContext
    ): void {
        // Mengambil query yang dijalankan selama proses berlangsung
        $queries = $this->stopQueryLog();

        Log::info(
            $this->buildLogTitle($featureName),
            $this->buildLogContext($request, $startTime, $queries, $result, $additionalContext)
        );
    }

    private function buildLogTitle(string $featureName): string
    {
        // Membuat judul log agar mudah dicari di laravel.log
        return self::LOG_TITLE_PREFIX . " - {$featureName}";
    }

    private function buildLogContext(
        Request $request,
        float $startTime,
        array $queries,
        mixed $result,
        array $additionalContext
    ): array {
        // Menggabungkan data filter, waktu eksekusi, jumlah query, dan context tambahan
        return array_merge([
            'search' => $request->input('search'),
            'gender' => $request->input('gender'),
            'sort' => $request->input('sort', self::DEFAULT_SORT),
            'execution_time_ms' => $this->calculateExecutionTime($startTime),
            'query_count' => count($queries),
            'query_time_ms' => $this->sumQueryTime($queries),
            'total_data' => $this->resolveTotalData($result),
        ], $additionalContext);
    }

    private function startQueryLog(): void
    {
        // Membersihkan query lama lalu mulai mencatat query baru
        DB::flushQueryLog();
        DB::enableQueryLog();
    }

    private function stopQueryLog(): array
    {
        // Mengambil query log lalu mematikan query logger
        $queries = DB::getQueryLog();

        DB::disableQueryLog();

        return $queries;
    }

    private function calculateExecutionTime(float $startTime): float
    {
        // Mengubah durasi proses dari detik menjadi millisecond
        return $this->formatMilliseconds(microtime(true) - $startTime);
    }

    private function resolveTotalData(mixed $result): int
    {
        // Mengambil total data dari paginator
        if ($result instanceof LengthAwarePaginator) {
            return (int) $result->total();
        }

        // Mengambil total data dari array atau collection
        if (is_countable($result)) {
            return count($result);
        }

        // Mengambil total data dari object yang memiliki method count
        if (is_object($result) && method_exists($result, 'count')) {
            return (int) $result->count();
        }

        return 0;
    }

    private function sumQueryTime(array $queries): float
    {
        // Menjumlahkan semua waktu query database
        $totalTime = array_reduce(
            $queries,
            fn (float $total, array $query): float => $total + (float) ($query['time'] ?? 0),
            0.0
        );

        return round($totalTime, 2);
    }

    private function formatMilliseconds(float $seconds): float
    {
        // Membulatkan waktu eksekusi agar log lebih mudah dibaca
        return round($seconds * 1000, 2);
    }
}
