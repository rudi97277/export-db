<?php

namespace Rudi97277\ExportDb\Interfaces;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

interface ExportQueryInterface
{
    /**
     * @return Collection
     */
    public function collection(): Collection;

    /**
     * @return array
     */
    public function headings(): array;

    /**
     * @param  mixed  $item
     * @return array
     */
    public function map($item): array;


    /**
     * @return string
     */
    public function title(): string;


    /**
     * @return string
     * starting cell for the sheet
     */
    public function startCell(): string;

    /**
     * @return array
     * registered event. return empty array if not used
     */
    public function registerEvents(): array;
}
