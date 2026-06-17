<?php

namespace App\Http\Controllers\Concerns;

trait SecuresKudaSearchInput
{
    private const KudaSearchMaxLength = 100;

    /**
     * Membersihkan keyword search agar input terlalu panjang atau karakter kontrol
     * tidak langsung dipakai di query.
     */
    private function normalizeKudaSearchKeyword(?string $keyword): ?string
    {
        if ($keyword === null) {
            return null;
        }

        $keyword = preg_replace('/[[:cntrl:]]+/', '', $keyword);
        $keyword = trim((string) $keyword);

        if ($keyword === '') {
            return null;
        }

        return mb_substr($keyword, 0, self::KudaSearchMaxLength);
    }

    /**
     * Escape wildcard LIKE biar input seperti %, _, atau backslash
     * tidak mengubah pola pencarian secara bebas.
     */
    private function makeSecureLikeKeyword(string $keyword): string
    {
        $escapedKeyword = str_replace(
            ['\\', '%', '_'],
            ['\\\\', '\\%', '\\_'],
            $keyword
        );

        return "%{$escapedKeyword}%";
    }

    /**
     * Query LIKE dibuat dengan parameter binding.
     * Nama kolom tidak boleh dari request user, hanya dari whitelist internal controller.
     */
    private function secureLikeSql(string $column): string
    {
        return "{$column} LIKE ? ESCAPE '\\\\'";
    }
}
