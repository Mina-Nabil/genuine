<?php

namespace App\Jobs;

use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use Carbon\Carbon;
use Database\Seeders\MyReadFilter;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ImportOrdersJob implements ShouldQueue
{
    use Queueable;

    private $from;
    private $to;
    /**
     * Create a new job instance.
     */
    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Job from $this->from $this->to started");

        /**  Create an Instance of our Read Filter  **/
        $filterSubset = new MyReadFilter($this->from, $this->to);

        /**  Create a new Reader of the type defined in $inputFileType  **/
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        /**  Tell the Reader that we want to use the Read Filter  **/
        $reader->setReadFilter($filterSubset);
        /**  Load only the rows and columns that match our filter to Spreadsheet  **/
        $spreadsheet = $reader->load(resource_path('import/Orders.xlsx'));
        if (!$spreadsheet) {
            throw new Exception('Failed to read files content');
        }

        $activeSheet = $spreadsheet->getActiveSheet();
        // Log::info($activeSheet);

        for ($i = $this->from; $i <= $this->to; $i++) {
            $client_name = $activeSheet->getCell('D' . $i)->getValue();
            $client_address = $activeSheet->getCell('F' . $i)->getValue();

            $totalAmount = $activeSheet->getCell('BG' . $i)->getValue();
            $deliveryAmount = $activeSheet->getCell('BF' . $i)->getValue();
            $ddate = $activeSheet->getCell('C' . $i)->getValue();
            if ($ddate)
                $ddate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int) $ddate);

            $note = $activeSheet->getCell('G' . $i)->getValue();

            $prod1Count = $activeSheet->getCell('J' . $i)->getValue();
            $prod1Price = $activeSheet->getCell('K' . $i)->getValue();

            $prod8Count = $activeSheet->getCell('L' . $i)->getValue();
            $prod8Price = $activeSheet->getCell('M' . $i)->getValue();

            $prod9Count = $activeSheet->getCell('N' . $i)->getValue();
            $prod9Price = $activeSheet->getCell('O' . $i)->getValue();

            $prod2Count = $activeSheet->getCell('P' . $i)->getValue();
            $prod2Price = $activeSheet->getCell('Q' . $i)->getValue();

            $prod4Count = $activeSheet->getCell('R' . $i)->getValue();
            $prod4Price = $activeSheet->getCell('S' . $i)->getValue();

            $prod3Count = $activeSheet->getCell('T' . $i)->getValue();
            $prod3Price = $activeSheet->getCell('U' . $i)->getValue();

            $prod5Count = $activeSheet->getCell('V' . $i)->getValue();
            $prod5Price = $activeSheet->getCell('W' . $i)->getValue();

            $prod20Count = $activeSheet->getCell('X' . $i)->getValue();
            $prod20Price = $activeSheet->getCell('Y' . $i)->getValue();

            $prod6Count = $activeSheet->getCell('Z' . $i)->getValue();
            $prod6Price = $activeSheet->getCell('AA' . $i)->getValue();

            $prod18Count = $activeSheet->getCell('AB' . $i)->getValue();
            $prod18Price = $activeSheet->getCell('AC' . $i)->getValue();

            $prod12Count = $activeSheet->getCell('AD' . $i)->getValue();
            $prod12Price = $activeSheet->getCell('AE' . $i)->getValue();

            $prod13Count = $activeSheet->getCell('AF' . $i)->getValue();
            $prod13Price = $activeSheet->getCell('AG' . $i)->getValue();

            $prod14Count = $activeSheet->getCell('AH' . $i)->getValue();
            $prod14Price = $activeSheet->getCell('AI' . $i)->getValue();

            if (!$client_name) {
                continue;
            }

            $customer = Customer::findByName($client_name);

            if (!$customer || !$ddate) {
                continue;
            }

            $products = [];
            if ($prod1Count && $prod1Price)
                array_push($products, [
                    "id"        =>  1,
                    "quantity"  =>  $prod1Count,
                    "price"     =>  $prod1Price,
                    "combo_id"  =>  null
                ]);
            if ($prod2Count && $prod2Price)
                array_push($products, [
                    "id"        =>  2,
                    "quantity"  =>  $prod2Count,
                    "price"     =>  $prod2Price,
                    "combo_id"  =>  null
                ]);
            if ($prod3Count && $prod3Price)
                array_push($products, [
                    "id"        =>  3,
                    "quantity"  =>  $prod3Count,
                    "price"     =>  $prod3Price,
                    "combo_id"  =>  null
                ]);
            if ($prod4Count && $prod4Price)
                array_push($products, [
                    "id"        =>  4,
                    "quantity"  =>  $prod4Count,
                    "price"     =>  $prod4Price,
                    "combo_id"  =>  null
                ]);
            if ($prod5Count && $prod5Price)
                array_push($products, [
                    "id"        =>  5,
                    "quantity"  =>  $prod5Count,
                    "price"     =>  $prod5Price,
                    "combo_id"  =>  null
                ]);
            if ($prod6Count && $prod6Price)
                array_push($products, [
                    "id"        =>  6,
                    "quantity"  =>  $prod6Count,
                    "price"     =>  $prod6Price,
                    "combo_id"  =>  null
                ]);
            if ($prod8Count && $prod8Price)
                array_push($products, [
                    "id"        =>  8,
                    "quantity"  =>  $prod8Count,
                    "price"     =>  $prod8Price,
                    "combo_id"  =>  null
                ]);
            if ($prod9Count && $prod9Price)
                array_push($products, [
                    "id"        =>  9,
                    "quantity"  =>  $prod9Count,
                    "price"     =>  $prod9Price,
                    "combo_id"  =>  null
                ]);
            if ($prod12Count && $prod12Price)
                array_push($products, [
                    "id"        =>  12,
                    "quantity"  =>  $prod12Count,
                    "price"     =>  $prod12Price,
                    "combo_id"  =>  null
                ]);
            if ($prod13Count && $prod13Price)
                array_push($products, [
                    "id"        =>  13,
                    "quantity"  =>  $prod13Count,
                    "price"     =>  $prod13Price,
                    "combo_id"  =>  null
                ]);
            if ($prod14Count && $prod14Price)
                array_push($products, [
                    "id"        =>  14,
                    "quantity"  =>  $prod14Count,
                    "price"     =>  $prod14Price,
                    "combo_id"  =>  null
                ]);
            if ($prod18Count && $prod18Price)
                array_push($products, [
                    "id"        =>  18,
                    "quantity"  =>  $prod18Count,
                    "price"     =>  $prod18Price,
                    "combo_id"  =>  null
                ]);
            if ($prod20Count && $prod20Price)
                array_push($products, [
                    "id"        =>  20,
                    "quantity"  =>  $prod20Count,
                    "price"     =>  $prod20Price,
                    "combo_id"  =>  null
                ]);


            Order::newOrder(
                $customer->id,
                $customer->name,
                $client_address ?? ($customer->address ?? "N/A"),
                $customer->phone,
                $customer->zone_id ?? 1,
                $customer->location_url,
                driverId: null,
                totalAmount: $totalAmount,
                deliveryAmount: $deliveryAmount,
                discountAmount: 0,
                deliveryDate: new Carbon($ddate),
                note: $note,
                products: $products,
                migrated: true
            );
        }
    }

}
