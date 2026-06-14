<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Kuda;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait FiltersKudaQuery
{
    use SecuresKudaSearchInput;

    private function applyKudaSearchAndFilters($query, Request $request)
    {
        $this->validateKudaQueryInput($request);
        $this->applyKudaSearch($query, $request);
        $this->applyKudaGenderFilter($query, $request);
        $this->applyKudaSorting($query, $request);

        return $query;
    }

    private function validateKudaQueryInput(Request $request): void
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'gender' => [
                'nullable',
                Rule::in([Kuda::GENDER_JANTAN, Kuda::GENDER_BETINA]),
            ],
            'sort' => [
                'nullable',
                Rule::in(['terbaru', 'terlama', 'nama_asc', 'nama_desc']),
            ],
        ]);
    }

    private function applyKudaSearch($query, Request $request): void
    {
        $search = $this->normalizeKudaSearchKeyword($request->input('search'));

        if ($search === null) {
            return;
        }

        $keyword = $this->makeSecureLikeKeyword($search);

        $query->where(function ($q) use ($keyword) {
            $q->whereRaw($this->secureLikeSql('nama_kuda'), [$keyword])
                ->orWhereRaw($this->secureLikeSql('jenis_kuda'), [$keyword])
                ->orWhereHas('peternakan', function ($peternakanQuery) use ($keyword) {
                    $peternakanQuery->whereRaw(
                        $this->secureLikeSql('nama_peternakan'),
                        [$keyword]
                    );
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
