<?php

namespace App\Support\LocalPdf;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Exporter khusus data kuda.
 * Menggunakan SimplePdf agar export PDF tetap lokal tanpa dompdf/snappy/mpdf.
 */
class KudaPdfExporter
{
    private const MARGIN = 32;
    private const TABLE_FONT_SIZE = 8;
    private const TABLE_LINE_HEIGHT = 10;

    public function generate(Collection $kuda, User $user, Request $request): string
    {
        // Membuat halaman PDF landscape agar tabel data kuda lebih lega.
        $pdf = new SimplePdf('landscape');
        $pageNumber = 1;

        // Menyiapkan bagian header dan judul kolom sebelum data ditulis.
        $y = $this->drawHeader($pdf, $user, $request, $kuda->count(), $pageNumber);
        $this->drawTableHeader($pdf, $y);
        $y -= 24;

        foreach ($kuda->values() as $index => $item) {
            // Mengubah model kuda menjadi format array yang siap dicetak ke tabel PDF.
            $row = $this->mapKudaRow($item, $index + 1);
            $rowHeight = $this->calculateRowHeight($pdf, $row);

            if ($y - $rowHeight < 48) {
                // Membuat halaman baru ketika ruang tabel di halaman saat ini sudah habis.
                $this->drawFooter($pdf, $pageNumber);
                $pdf->addPage();
                $pageNumber++;
                $y = $this->drawHeader($pdf, $user, $request, $kuda->count(), $pageNumber);
                $this->drawTableHeader($pdf, $y);
                $y -= 24;
            }

            $this->drawTableRow($pdf, $row, $y, $rowHeight);
            $y -= $rowHeight;
        }

        if ($kuda->count() === 0) {
            // Menampilkan pesan kosong jika tidak ada data sesuai filter aktif.
            $pdf->text(self::MARGIN, $y - 8, 'Belum ada data kuda untuk filter ini.', 10);
        }

        $this->drawFooter($pdf, $pageNumber);

        return $pdf->output();
    }

    public static function filename(): string
    {
        return 'data-kuda-' . now()->format('Ymd-His') . '.pdf';
    }

    private function drawHeader(SimplePdf $pdf, User $user, Request $request, int $totalData, int $pageNumber): float
    {
        // Mengatur posisi header agar konsisten di setiap halaman PDF.
        $left = self::MARGIN;
        $right = $pdf->width() - self::MARGIN;
        $top = $pdf->height() - self::MARGIN;

        $pdf->boldText($left, $top, 'Laporan Data Kuda', 16);
        $pdf->text($left, $top - 16, 'OreNoAiba - Export data kuda dalam format PDF', 9);
        $pdf->text($right, $top, 'Halaman ' . $pageNumber, 9, 'right');
        $pdf->text($right, $top - 14, 'Dicetak: ' . now()->format('d/m/Y H:i'), 9, 'right');

        $pdf->line($left, $top - 26, $right, $top - 26, 0.7);

        $filterInfo = $this->filterInfo($request);
        $pdf->text($left, $top - 44, 'User: ' . ($user->nama_lengkap ?? '-') . ' | Role: ' . ucfirst($user->role ?? '-') . ' | Total data: ' . $totalData, 9);
        $pdf->text($left, $top - 58, 'Filter: ' . $filterInfo, 9);

        return $top - 82;
    }

    private function drawFooter(SimplePdf $pdf, int $pageNumber): void
    {
        // Menambahkan garis footer dan nomor halaman pada bagian bawah PDF.
        $left = self::MARGIN;
        $right = $pdf->width() - self::MARGIN;

        $pdf->line($left, 32, $right, 32, 0.4);
        $pdf->text($left, 20, 'Dokumen dibuat otomatis oleh library lokal SimplePdf.', 8);
        $pdf->text($right, 20, 'Halaman ' . $pageNumber, 8, 'right');
    }

    private function drawTableHeader(SimplePdf $pdf, float $y): void
    {
        // Menggambar baris header tabel berdasarkan konfigurasi kolom.
        $columns = $this->columns();
        $x = self::MARGIN;
        $headerHeight = 22;

        foreach ($columns as $column) {
            $pdf->rectangle($x, $y - $headerHeight, $column['width'], $headerHeight, true, 0.90);
            $pdf->boldText($x + 4, $y - 14, $column['label'], 8);
            $x += $column['width'];
        }
    }

    private function drawTableRow(SimplePdf $pdf, array $row, float $y, float $rowHeight): void
    {
        // Menggambar satu baris data kuda lengkap dengan border dan teks wrapping.
        $columns = $this->columns();
        $x = self::MARGIN;

        foreach ($columns as $column) {
            $pdf->rectangle($x, $y - $rowHeight, $column['width'], $rowHeight);

            $lines = $pdf->wrappedLines((string) ($row[$column['key']] ?? '-'), $column['width'] - 8, self::TABLE_FONT_SIZE);
            $lineY = $y - 13;

            foreach ($lines as $line) {
                if ($lineY < $y - $rowHeight + 5) {
                    break;
                }

                $pdf->text($x + 4, $lineY, $line, self::TABLE_FONT_SIZE);
                $lineY -= self::TABLE_LINE_HEIGHT;
            }

            $x += $column['width'];
        }
    }

    private function calculateRowHeight(SimplePdf $pdf, array $row): float
    {
        // Menghitung tinggi baris berdasarkan jumlah baris teks terpanjang.
        $maxLines = 1;

        foreach ($this->columns() as $column) {
            $lines = $pdf->wrappedLines((string) ($row[$column['key']] ?? '-'), $column['width'] - 8, self::TABLE_FONT_SIZE);
            $maxLines = max($maxLines, count($lines));
        }

        return max(22, ($maxLines * self::TABLE_LINE_HEIGHT) + 10);
    }

    private function mapKudaRow($item, int $number): array
    {
        // Merapikan data relasi kuda agar aman ditampilkan di laporan PDF.
        $lisensi = '-';

        if ($item->lisensi) {
            $lisensi = $item->lisensi->nomor_sertifikat ?? 'Ada lisensi';

            if (!empty($item->lisensi->status)) {
                $lisensi .= ' (' . ucfirst($item->lisensi->status) . ')';
            }
        }

        return [
            'no' => (string) $number,
            'nama' => $item->nama_kuda ?? '-',
            'jenis' => $item->jenis_kuda ?? '-',
            'gender' => ucfirst($item->gender ?? '-'),
            'peternakan' => $item->peternakan->nama_peternakan ?? '-',
            'status' => ucfirst($item->status_jual ?? '-'),
            'harga' => 'Rp ' . number_format((float) ($item->harga_buka ?? 0), 0, ',', '.'),
            'lisensi' => $lisensi,
        ];
    }

    private function columns(): array
    {
        // Mengatur daftar kolom dan lebar masing-masing kolom pada tabel PDF.
        return [
            ['key' => 'no', 'label' => 'No', 'width' => 28],
            ['key' => 'nama', 'label' => 'Nama Kuda', 'width' => 125],
            ['key' => 'jenis', 'label' => 'Jenis', 'width' => 90],
            ['key' => 'gender', 'label' => 'Gender', 'width' => 60],
            ['key' => 'peternakan', 'label' => 'Peternakan', 'width' => 130],
            ['key' => 'status', 'label' => 'Status', 'width' => 75],
            ['key' => 'harga', 'label' => 'Harga', 'width' => 90],
            ['key' => 'lisensi', 'label' => 'Lisensi', 'width' => 180],
        ];
    }

    private function filterInfo(Request $request): string
    {
        // Menyusun informasi filter aktif supaya muncul di header laporan.
        $items = [];

        $items[] = 'Search=' . ($request->filled('search') ? $request->query('search') : 'semua');
        $items[] = 'Gender=' . ($request->filled('gender') ? ucfirst((string) $request->query('gender')) : 'semua');
        $items[] = 'Sort=' . ($request->filled('sort') ? (string) $request->query('sort') : 'terbaru');

        return implode(' | ', $items);
    }
}
