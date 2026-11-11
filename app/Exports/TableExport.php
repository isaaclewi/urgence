<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export Excel générique : un tableau d'en-têtes + des lignes.
 * Les textes restent des textes (matricules « 00123 », téléphones…).
 */
class TableExport extends DefaultValueBinder implements
    FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithTitle, WithCustomValueBinder
{
    public function __construct(
        private array $headings,
        private array $rows,
        private string $title = 'Export'
    ) {
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        // Excel : 31 caractères max, sans [ ] : * ? / \
        $clean = preg_replace('/[\[\]:*?\/\\\\]/', ' ', $this->title);

        return mb_substr(trim($clean) ?: 'Export', 0, 31);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function bindValue(Cell $cell, mixed $value): bool
    {
        if (is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
