<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Kuda;
use Illuminate\Http\Request;

trait FiltersKudaQuery
{
    private function applyKudaSearchAndFilters($query, Request $request)
    {
        $this->applyKudaSearch($query, $request);
        $this->applyKudaGenderFilter($query, $request);
        $this->applyKudaSorting($query, $request);

        return $query;
    }

    private function applyKudaSearch($query, Request $request): void
    {
        if (!$request->filled('search')) {
            return;
        }

        $search = $request->input('search');

        $query->where(function ($q) use ($search) {
            $q->where('nama_kuda', 'like', "%{$search}%")
                ->orWhere('jenis_kuda', 'like', "%{$search}%")
                ->orWhereHas('peternakan', function ($peternakanQuery) use ($search) {
                    $peternakanQuery->where('nama_peternakan', 'like', "%{$search}%");
                });
        });
    }

    private function applyKudaGenderFilter($query, Request $request): void
    {
        $gender = $request->input('gender');

        if (!in_array($gender, [Kuda::GENDER_JANTAN, Kuda::GENDER_BETINA], true)) {
            return;
        }

        $query->where('gender', $gender);
    }

    private function applyKudaSorting($query, Request $request): void
    {
        match ($request->input('sort', 'terbaru')) {
            'nama_asc' => $query->orderBy('nama_kuda', 'asc'),
            'nama_desc' => $query->orderBy('nama_kuda', 'desc'),
            'terlama' => $query->orderBy('created_at', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };
    }
}
