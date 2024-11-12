<?php

namespace App\Jobs;

use App\Models\Route;
use App\Models\Stage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use Illuminate\Support\Facades\DB;

class ProcessTicketPriceUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Load the Excel file
        $spreadsheet = IOFactory::load($this->filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        // Check if data is empty
        if (empty($data) || empty($data[0])) {
            Log::error('Uploaded file is empty.');
            return;
        }

        $routeNameInFile = trim($data[0][0]);
        if (stripos($routeNameInFile, 'ROUTE') !== 0) {
            $errorMessage = "The first row must start with 'ROUTE'. Found '$routeNameInFile' instead.";
            Log::error($errorMessage);
            return;
        }

        // Check the second row for stage names and prices
        if (count($data) < 2 || empty(array_filter($data[1]))) {
            $errorMessage = 'The second row must contain stage names and prices.';
            Log::error($errorMessage);
            return;
        }

        // // Check the last row for stage names and prices
        // if (empty(array_filter($data[count($data) - 1]))) {
        //     $errorMessage = 'The last row must contain stage names and prices.';
        //     Log::error($errorMessage);
        //     return;
        // }

        $jsonData = [];
        $indexCounter = 1; // Initialize counter for JSON keys

        // Process each row
        foreach ($data as $rowArray) {
            $stageName = trim($rowArray[0]);

            // Skip invalid stage names
            if (empty($stageName) || stripos($stageName, 'ROUTE') === 0) {
                continue;
            }

            // Extract prices
            $prices = array_slice($rowArray, 1);
            $numericPrices = $this->processPrices($prices, $spreadsheet, $stageName);

            if (empty($numericPrices)) {
                Log::warning("No valid prices found for stage '$stageName'.");
                continue;
            }

            // Store the data in the desired format
            $jsonData[$indexCounter++] = [
                'stage_name' => $stageName,
                'prices' => array_map('strval', $numericPrices), // Convert numeric prices to strings
            ];
        }

        if (empty($jsonData)) {
            Log::error('No valid data found in the uploaded file.');
            return;
        }

        $firstStageName = reset($jsonData)['stage_name'];
        $lastStageName = end($jsonData)['stage_name'];
        $routeNumber = $data[0][0];
        // Use a transaction to ensure both save operations succeed or fail together
        DB::transaction(function () use ($firstStageName, $lastStageName, $jsonData,$routeNumber) {
            $route = new Route();
            $route->route_number = $routeNumber;
            $route->route_from = $firstStageName;
            $route->route_to = $lastStageName;
            $route->type = 1;
            $route->save();

            Stage::create([
                'route_id' => $route->id,
                'stage_data' => json_encode($jsonData),
            ]);
        });

        // Optionally, delete the file after processing
        unlink($this->filePath);
    }

    /**
     * Process prices from the row.
     *
     * @param array $prices
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet
     * @param string $stageName
     * @return array
     */
    private function processPrices(array $prices, $spreadsheet, $stageName)
    {
        $numericPrices = [];

        foreach ($prices as $price) {
            $cleanPrice = $this->cleanPrice($price);
            if ($cleanPrice !== null) {
                $numericPrices[] = floatval($cleanPrice);
            } else {
                Log::warning("Non-numeric or negative price found for '$stageName': '$price'");
            }
        }

        return $numericPrices;
    }

    /**
     * Clean and validate price values.
     *
     * @param mixed $price
     * @return string|null
     */
    private function cleanPrice($price)
    {
        if (is_string($price) && preg_match('/^=/i', $price)) {
            // Evaluate the formula directly
            return $this->evaluateFormula($price);
        }

        // Clean price values and ensure they are numeric
        $cleanPrice = preg_replace('/[^\d.]/', '', trim($price));
        return (is_numeric($cleanPrice) && floatval($cleanPrice) >= 0) ? $cleanPrice : null;
    }

    /**
     * Evaluate Excel formula.
     *
     * @param string $formula
     * @return float|null
     */
    private function evaluateFormula($formula)
    {
        // Create a new calculation engine
        $calculation = Calculation::getInstance();
        return $calculation->calculate($formula);
    }
}
