<?php

namespace Rudi97277\ExportDb;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Rudi97277\ExportDb\DTOs\ExportDTO;
use Rudi97277\ExportDb\Generators\ExcelGenerator;
use Rudi97277\ExportDb\Models\ExportConfig;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GenerateReport
{
    /**
     * Generate an export report based on the given request parameters.
     *
     * @param Request $request The HTTP request containing parameters for the report.
     * @return BinaryFileResponse A response containing the generated report file.
     */
    public static function generate(Request $request, ExportDTO $dto = null): BinaryFileResponse
    {
        // Validate the request parameters
        $request->validate([
            'module' => 'required|string', // 'module' is required and must be a string
            'export_type' => 'required|in:xlsx,csv' // 'export_type' is required and must be either 'xlsx' or 'csv'
        ]);

        // Retrieve the export configuration based on the module
        $generator = ExportConfig::where('module', $request->module)->firstOrFail();

        // Validate additional parameters using the defined validator in the config
        $request->validate($generator->validator ?? []);

        // Merge default values with the request data for the generator
        $generatorData = array_merge($generator->default ?? [], $request->only(array_keys($generator->validator ?? [])));

        // Convert any array values to JSON strings for storage
        $generatorData = array_map(fn($item) => is_array($item) ? json_encode($item) : $item, $generatorData);

        // Create a new instance of the ExcelGenerator with the specified parameters
        $generator = new ExcelGenerator($generator->query, $generatorData, $generator->formatter, $generator->title, $request->export_type, $dto);

        // Generate a unique filename based on the current date and a random number
        $now = date('Y-m-d_H-i-s');
        $random = mt_rand(000, 100);
        $fullPath = "exports/{$now}{$random}_{$request->module}.$request->export_type";

        // Store the generated Excel file in the 'public' disk storage
        Excel::store($generator, $fullPath, 'public', ucfirst($request->export_type));

        // Register a shutdown function to delete the file after the script execution
        register_shutdown_function(function () use ($fullPath) {
            $filePath = Storage::path('public/' . $fullPath);
            if (file_exists($filePath)) {
                // unlink($filePath); // Delete the file if it exists
            }
        });

        // Return the generated file as a downloadable response
        return Response::download(Storage::path('public/' . $fullPath), "export.xlsx");
    }
}
