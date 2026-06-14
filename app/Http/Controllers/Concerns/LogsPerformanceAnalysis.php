<?php

namespace App\Http\Controllers\Concerns;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait LogsPerformanceAnalysis
{
    /**
     * Mengukur performa proses pengambilan data tanpa mengubah hasil query.
     * Data performa disimpan ke storage/logs/laravel.log.
     */
    private function measurePerformance(
        string $featureName,
        Request $request,
        Closure $callback,
        array $additionalContext = []
    ): mixed {
        $result = null;
        $startTime = microtime(true);

        DB::flushQueryLog();
        DB::enableQueryLog();

        try {
            $result = $callback();

            return $result;
        } finally {
            $queries = DB::getQueryLog();
            DB::disableQueryLog();

            Log::info("Performance Analysis - {$featureName}", array_merge([
                'search' => $request->input('search'),
                'gender' => $request->input('gender'),
                'sort' => $request->input('sort', 'terbaru'),
                'execution_time_ms' => $this->formatMilliseconds(microtime(true) - $startTime),
                'query_count' => count($queries),
                'query_time_ms' => $this->sumQueryTime($queries),
                'total_data' => $this->resolveTotalData($result),
            ], $additionalContext));
        }
    }

    private function resolveTotalData(mixed $result): int
    {
        if ($result instanceof LengthAwarePaginator) {
            return (int) $result->total();
        }

        if (is_countable($result)) {
            return count($result);
        }

        if (is_object($result) && method_exists($result, 'count')) {
            return (int) $result->count();
        }

        return 0;
    }

    private function sumQueryTime(array $queries): float
    {
        $totalTime = array_reduce($queries, function (float $total, array $query): float {
            return $total + (float) ($query['time'] ?? 0);
        }, 0.0);

        return round($totalTime, 2);
    }

    private function formatMilliseconds(float $seconds): float
    {
        return round($seconds * 1000, 2);
    }
}
