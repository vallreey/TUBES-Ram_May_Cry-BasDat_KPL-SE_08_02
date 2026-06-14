<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FiltersKudaQuery;
use App\Models\Kuda;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    use FiltersKudaQuery;

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
        $query = Kuda::with(['peternakan', 'lisensi', 'transaksi'])
            ->where('status_jual', $status);

        $kuda = $this->applyKudaSearchAndFilters($query, $request)->get();
        $page = $status;

        return view('admin.marketplace.index', compact('kuda', 'page'));
    }
}
