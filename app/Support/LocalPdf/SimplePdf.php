<?php

namespace App\Support\LocalPdf;

/**
 * Library PDF lokal sederhana tanpa composer/package eksternal.
 * Cocok untuk laporan tabel seperti export data kuda.
 */
class SimplePdf
{
    private array $pages = [];
    private string $currentContent = '';
    private float $width;
    private float $height;

    public function __construct(string $orientation = 'landscape')
    {
        // Menentukan ukuran halaman PDF sesuai orientasi yang dipilih.
        if ($orientation === 'portrait') {
            $this->width = 595.28;
            $this->height = 841.89;
        } else {
            $this->width = 841.89;
            $this->height = 595.28;
        }

        $this->addPage();
    }

    public function width(): float
    {
        return $this->width;
    }

    public function height(): float
    {
        return $this->height;
    }

    public function addPage(): void
    {
        // Menyimpan konten halaman lama sebelum membuka halaman baru.
        if ($this->currentContent !== '') {
            $this->pages[] = $this->currentContent;
        }

        $this->currentContent = '';
    }

    public function pageCount(): int
    {
        return count($this->pages) + ($this->currentContent !== '' ? 1 : 0);
    }

    public function text(float $x, float $y, string $text, int $size = 10, string $align = 'left'): void
    {
        // Menambahkan teks normal ke halaman PDF dengan posisi yang ditentukan.
        $text = $this->prepareText($text);

        if ($align === 'right') {
            $x -= $this->textWidth($text, $size);
        } elseif ($align === 'center') {
            $x -= $this->textWidth($text, $size) / 2;
        }

        $this->currentContent .= sprintf(
            "BT /F1 %d Tf %.2F %.2F Td (%s) Tj ET\n",
            $size,
            $x,
            $y,
            $this->escapeText($text)
        );
    }

    public function boldText(float $x, float $y, string $text, int $size = 10, string $align = 'left'): void
    {
        // Menambahkan teks tebal untuk judul, header, atau label penting.
        $text = $this->prepareText($text);

        if ($align === 'right') {
            $x -= $this->textWidth($text, $size);
        } elseif ($align === 'center') {
            $x -= $this->textWidth($text, $size) / 2;
        }

        $this->currentContent .= sprintf(
            "BT /F2 %d Tf %.2F %.2F Td (%s) Tj ET\n",
            $size,
            $x,
            $y,
            $this->escapeText($text)
        );
    }

    public function line(float $x1, float $y1, float $x2, float $y2, float $lineWidth = 0.5): void
    {
        // Menggambar garis untuk pemisah header, footer, atau elemen tabel.
        $this->currentContent .= sprintf(
            "%.2F w %.2F %.2F m %.2F %.2F l S\n",
            $lineWidth,
            $x1,
            $y1,
            $x2,
            $y2
        );
    }

    public function rectangle(float $x, float $y, float $width, float $height, bool $filled = false, float $gray = 0.95): void
    {
        // Menggambar kotak yang digunakan sebagai border atau background sel tabel.
        if ($filled) {
            $this->currentContent .= sprintf(
                "%.3F g %.2F %.2F %.2F %.2F re f 0 g\n",
                $gray,
                $x,
                $y,
                $width,
                $height
            );
        }

        $this->currentContent .= sprintf(
            "0 g %.2F w %.2F %.2F %.2F %.2F re S\n",
            0.4,
            $x,
            $y,
            $width,
            $height
        );
    }

    public function wrappedLines(string $text, float $maxWidth, int $fontSize): array
    {
        // Memecah teks panjang agar tetap masuk ke dalam lebar kolom.
        $text = trim($this->prepareText($text));

        if ($text === '') {
            return ['-'];
        }

        $words = preg_split('/\s+/', $text) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;

            if ($this->textWidth($candidate, $fontSize) <= $maxWidth) {
                $current = $candidate;
                continue;
            }

            if ($current !== '') {
                $lines[] = $current;
            }

            if ($this->textWidth($word, $fontSize) > $maxWidth) {
                $chunks = $this->splitLongWord($word, $maxWidth, $fontSize);
                $lastIndex = count($chunks) - 1;

                foreach ($chunks as $index => $chunk) {
                    if ($index === $lastIndex) {
                        $current = $chunk;
                    } else {
                        $lines[] = $chunk;
                    }
                }
            } else {
                $current = $word;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines ?: ['-'];
    }

    public function textWidth(string $text, int $fontSize): float
    {
        // Menghitung perkiraan lebar teks untuk kebutuhan alignment dan wrapping.
        $text = $this->prepareText($text);
        $width = 0.0;
        $length = strlen($text);

        for ($i = 0; $i < $length; $i++) {
            $char = $text[$i];

            if ($char === ' ') {
                $width += 0.28;
            } elseif (in_array($char, ['i', 'l', 'I', '.', ',', ':', ';', '|', '!'], true)) {
                $width += 0.25;
            } elseif (in_array($char, ['m', 'w', 'M', 'W'], true)) {
                $width += 0.78;
            } elseif (ctype_upper($char)) {
                $width += 0.62;
            } else {
                $width += 0.50;
            }
        }

        return $width * $fontSize;
    }

    public function output(): string
    {
        // Menyusun struktur object PDF menjadi binary PDF yang siap diunduh.
        if ($this->currentContent !== '') {
            $this->pages[] = $this->currentContent;
            $this->currentContent = '';
        }

        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';

        $kids = [];
        $objectNumber = 5;

        foreach ($this->pages as $pageContent) {
            $contentObjectNumber = $objectNumber++;
            $pageObjectNumber = $objectNumber++;

            $objects[$contentObjectNumber] = "<< /Length " . strlen($pageContent) . " >>\nstream\n" . $pageContent . "endstream";
            $objects[$pageObjectNumber] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents %d 0 R >>',
                $this->width,
                $this->height,
                $contentObjectNumber
            );

            $kids[] = $pageObjectNumber . ' 0 R';
        }

        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($kids) . ' >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0 => 0];

        foreach ($objects as $number => $object) {
            $offsets[$number] = strlen($pdf);
            $pdf .= $number . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefPosition = strlen($pdf);
        $maxObjectNumber = max(array_keys($objects));
        $pdf .= "xref\n0 " . ($maxObjectNumber + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= $maxObjectNumber; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i] ?? 0);
        }

        $pdf .= "trailer\n<< /Size " . ($maxObjectNumber + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefPosition . "\n%%EOF";

        return $pdf;
    }

    private function splitLongWord(string $word, float $maxWidth, int $fontSize): array
    {
        // Memotong kata yang terlalu panjang agar tidak keluar dari kolom tabel.
        $chunks = [];
        $current = '';
        $length = strlen($word);

        for ($i = 0; $i < $length; $i++) {
            $candidate = $current . $word[$i];

            if ($this->textWidth($candidate, $fontSize) <= $maxWidth || $current === '') {
                $current = $candidate;
            } else {
                $chunks[] = $current;
                $current = $word[$i];
            }
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }

    private function prepareText(string $text): string
    {
        // Membersihkan teks dari HTML dan karakter yang kurang aman untuk PDF lokal.
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $text = str_replace(["\r", "\n", "\t"], ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text) ?: $text;

        $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $text);

        if ($converted !== false) {
            $text = $converted;
        }

        return trim($text);
    }

    private function escapeText(string $text): string
    {
        // Melakukan escape karakter khusus agar teks tidak merusak syntax PDF.
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
