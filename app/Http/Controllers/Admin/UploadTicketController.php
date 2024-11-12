<?php

namespace App\Http\Controllers\Admin;

use App\Models\Route;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Helpers\Utilities\ToastrHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Jobs\ProcessTicketPriceUpload;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class UploadTicketController extends Controller
{
    public function index()
    {
        $path = $this->getView('admin.upload.create');
        $para = [];
        $title = 'Upload Ticket';
        return $this->renderView($path, $para, $title);
    }

    // public function uploadTicketPrice(Request $request)
    // {
    //     // Validate the uploaded file
    //     $request->validate([
    //         'file' => 'required|mimes:xls,xlsx',
    //     ]);


    //     // Load the Excel file
    //     $file = $request->file('file');
    //     $spreadsheet = IOFactory::load($file->getPathname()); // Load the spreadsheet
    //     $sheet = $spreadsheet->getActiveSheet(); // Get the active sheet
    //     $data = $sheet->toArray(); // Get the data as an array

    //     // Check if data is empty
    //     if (empty($data) || empty($data[0])) {
    //         return response()->json(['error' => 'Uploaded file is empty.'], 400);
    //     }

    //     $jsonData = [];
    //     $indexCounter = 1; // Initialize counter for JSON keys

    //     // Process each row
    //     foreach ($data as $rowArray) {
    //         $stageName = trim($rowArray[0]);

    //         // Skip invalid stage names
    //         if (empty($stageName) || stripos($stageName, 'ROUTE') === 0) {
    //             continue;
    //         }

    //         // Extract prices
    //         $prices = array_slice($rowArray, 1);
    //         $numericPrices = $this->processPrices($prices, $spreadsheet, $stageName);

    //         if (empty($numericPrices)) {
    //             Log::warning("No valid prices found for stage '$stageName'.");
    //             continue;
    //         }

    //         // Store the data in the desired format
    //         $jsonData[$indexCounter++] = [
    //             'stage_name' => $stageName,
    //             'prices' => array_map('strval', $numericPrices), // Convert numeric prices to strings
    //         ];
    //     }

    //     if (empty($jsonData)) {
    //         return response()->json(['error' => 'No valid data found in the uploaded file.'], 400);
    //     }

    //     $firstStageName = reset($jsonData)['stage_name'];
    //     $lastStageName = end($jsonData)['stage_name'];

    //     // Use a transaction to ensure both save operations succeed or fail together
    //     DB::transaction(function () use ($firstStageName, $lastStageName, $jsonData) {
    //         $route = new Route();
    //         $route->route_from = $firstStageName;
    //         $route->route_to = $lastStageName;
    //         $route->save();

    //         Stage::create([
    //             'route_id' => $route->id,
    //             'stage_data' => json_encode($jsonData),
    //         ]);
    //     });

    //     return redirect()->route('upload.index');
    // }

    // /**
    //  * Process prices from the row.
    //  *
    //  * @param array $prices
    //  * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
    //  * @param string $stageName
    //  * @return array
    //  */
    // private function processPrices(array $prices, $spreadsheet, $stageName)
    // {
    //     $numericPrices = [];

    //     foreach ($prices as $price) {
    //         $cleanPrice = $this->cleanPrice($price);
    //         if ($cleanPrice !== null) {
    //             $numericPrices[] = floatval($cleanPrice);
    //         } else {
    //             Log::warning("Non-numeric or negative price found for '$stageName': '$price'");
    //         }
    //     }

    //     return $numericPrices;
    // }

    // /**
    //  * Clean and validate price values.
    //  *
    //  * @param mixed $price
    //  * @return string|null
    //  */
    // private function cleanPrice($price)
    // {
    //     if (is_string($price) && preg_match('/^=/i', $price)) {
    //         // Evaluate the formula directly
    //         return $this->evaluateFormula($spreadsheet, $price);
    //     }

    //     // Clean price values and ensure they are numeric
    //     $cleanPrice = preg_replace('/[^\d.]/', '', trim($price));
    //     return (is_numeric($cleanPrice) && floatval($cleanPrice) >= 0) ? $cleanPrice : null;
    // }

    // /**
    //  * Evaluate Excel formula.
    //  *
    //  * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
    //  * @param string $formula
    //  * @return float|null
    //  */
    // private function evaluateFormula($spreadsheet, $formula)
    // {
    //     // Create a new calculation engine
    //     $calculation = Calculation::getInstance($spreadsheet);

    //     // Create a new cell and evaluate
    //     return $calculation->calculate($formula);
    // }
    //   public function uploadTicketPrice(Request $request)
    // {
    //     // Validate the uploaded file
    //     $request->validate([
    //         'file' => 'required|mimes:xls,xlsx',
    //     ]);

    //     // Store the uploaded file
    //     $file = $request->file('file');
    //     $filePath = $file->store('ticket_prices');

    //     // Dispatch the job to process the uploaded file
    //     ProcessTicketPriceUpload::dispatch(Storage::path($filePath));

    //     ToastrHelper::success('file uploaded successfully');

    //     return redirect()->route('upload.index')->with('success', 'File uploaded and processing in the background.');
    // }
    public function uploadTicketPrice(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);

        // Store the uploaded file
        $file = $request->file('file');
        $filePath = $file->store('ticket_prices');

        // Load the Excel file for error checking
        try {
            $spreadsheet = IOFactory::load(Storage::path($filePath));
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();
        } catch (\Exception $e) {
            Log::error('Error loading file: ' . $e->getMessage());
            ToastrHelper::error('Failed to load the file. Please check the file format.');
            return redirect()->route('upload.index')->withErrors(['error' => 'Failed to load the file.']);
        }

        // Initialize an error variable
        $errorFound = false;
        $errorMessage = '';
        if (!isset($data[0][0])) {
            ToastrHelper::error('Route number is not found: ' . $errorMessage);
            return redirect()->route('upload.index')->withErrors(['error' => 'Route number is not found. ' . $errorMessage]);
        }

        // Check if data is empty
        if (empty($data) || empty($data[0])) {
            $errorMessage = 'Uploaded file is empty.';
            Log::error($errorMessage);
            $errorFound = true;
        } else {
            $routeNameInFile = trim($data[0][0]);
            if (stripos($routeNameInFile, 'ROUTE') !== 0) {
                $errorMessage = "The first row must start with 'ROUTE'. Found '$routeNameInFile' instead.";
                Log::error($errorMessage);
                $errorFound = true;
            } elseif (count($data) < 2 || empty(array_filter($data[1]))) {
                $errorMessage = 'The second row must contain stage names and prices.';
                Log::error($errorMessage);
                $errorFound = true;
                // } elseif (empty(array_filter($data[count($data) - 1]))) {
                //     $errorMessage = 'The last row must contain stage names and prices.';
                //     Log::error($errorMessage);
                //     $errorFound = true;
            }
        }

        // If any errors were found, notify the user and return
        if ($errorFound) {
            ToastrHelper::error('Failed to upload the file: ' . $errorMessage);
            return redirect()->route('upload.index')->withErrors(['error' => 'Failed to upload the file. ' . $errorMessage]);
        }

        ProcessTicketPriceUpload::dispatch(Storage::path($filePath));
        ToastrHelper::success('File uploaded successfully');
        return redirect()->route('upload.index')->with('success', 'File uploaded and processing in the background.');
    }
}
