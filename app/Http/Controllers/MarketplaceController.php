<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FiltersKudaQuery;
use App\Http\Controllers\Concerns\LogsPerformanceAnalysis;
use App\Models\Kuda;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    use FiltersKudaQuery, LogsPerformanceAnalysis;

    public function index(Request $request)
    {
        return $this->showMarketplaceByStatus(Kuda::STATUS_TERSEDIA, $request);
    }

    public function terjual(Request $request)
    {
        return $this->showMarketplaceByStatus(Kuda::STATUS_TERJUAL, $request);
    }

    private function showMarketplaceByStatus(string $status, Request $request)
    {
        $query = Kuda::with(['peternakan', 'lisensi', 'transaksi', 'ayah', 'ibu'])
            ->where('status_jual', $status);

        $kuda = $this->measurePerformance(
            $this->getMarketplacePerformanceName($status),
            $request,
            fn () => $this->applyKudaSearchAndFilters($query, $request)->get(),
            ['status_jual' => $status]
        );

        $page = $status;

        return view('admin.marketplace.index', compact('kuda', 'page'));
    }

    private function getMarketplacePerformanceName(string $status): string
    {
        return match ($status) {
            Kuda::STATUS_TERJUAL => 'Marketplace Kuda Terjual',
            default => 'Marketplace Kuda Tersedia',
        };
    }
}
