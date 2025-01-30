<?php

namespace Rudi97277\ExportDb\Generators;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Rudi97277\ExportDb\Interfaces\ExportQueryInterface;

class ExcelGenerator implements
    FromCollection,
    WithTitle,
    WithMapping,
    WithHeadings,
    WithCustomStartCell,
    ShouldAutoSize,
    WithStrictNullComparison,
    WithEvents,
    ExportQueryInterface
{
    /**
     * @var int Starting row for the export
     */
    private $startRow = 1;

    /**
     * @var string Starting column for the export
     */
    private $startCol = 'A';

    /**
     * @var int Counter for row numbers
     */
    private $no = 0;

    /**
     * Constructor to initialize properties.
     *
     * @param string $queryFunction The SQL query function to retrieve data.
     * @param array<string,mixed> $generatorData Data to be passed to the query.
     * @param array<string,mixed> $formatterFormat Formatting rules for the export.
     * @param string $title The title of the sheet.
     * @param string $exportType The type of export (e.g., xlsx, csv).
     */
    public function __construct(
        private string $queryFunction,
        private array $generatorData,
        private array $formatterFormat,
        private string $title,
        private string $exportType
    ) {}

    /**
     * Get the collection of data for the export.
     *
     * @return Collection
     */
    public function collection(): Collection
    {
        $generatorData = str_contains($this->queryFunction, '?') ? array_values($this->generatorData) : $this->generatorData;
        // Execute the query and return the results as a collection
        return collect(DB::select($this->queryFunction, $generatorData));
    }

    /**
     * Define the headings for the export file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'No', // Column for numbering
            ...collect($this->formatterFormat)->pluck('name')->toArray() // Dynamic headings based on formatter
        ];
    }

    /**
     * Map the data items to the format defined by formatter.
     *
     * @param mixed $item The item to format.
     * @return array<string,mixed> Formatted data array.
     */
    public function map(mixed $item): array
    {
        $formatted = [];
        foreach ($this->formatterFormat as $formatter) {
            // Format the value according to the defined pattern
            $formattedString = preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($item) {
                return $item->{$matches[1]} ?? ''; // Replace placeholders with actual values
            }, $formatter['value']);

            $formatted[] = $formattedString; // Add formatted value to the array
        }
        return [
            'no' => ++$this->no, // Increment and return row number
            ...$formatted // Add formatted values to the return array
        ];
    }

    /**
     * Get the title of the export sheet.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->title; // Return the title set in the constructor
    }

    /**
     * Define the starting cell for the export.
     *
     * @return string
     */
    public function startCell(): string
    {
        return "{$this->startCol}{$this->startRow}"; // Return the starting cell in Excel notation
    }

    /**
     * Register events to be triggered during the export.
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Apply autofilter to the exported data
                $event->sheet->getDelegate()->setAutoFilter("{$this->startCell()}:" . $event->sheet->getDelegate()->getHighestColumn() . $this->startRow);
                // Set the orientation of the page to landscape
                $event->sheet->getDelegate()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            },
        ];
    }
}
