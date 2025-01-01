<?php

namespace Database\Seeders;

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Orders\Order;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class OrderSeeder extends Seeder
{

    const START = 5;
    const END = 3500;
    const STEP = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        for ($k = 1; $k < 13; $k++) {
            if ($k != 11)
                for ($i = self::START; $i < self::END; $i += self::STEP) {
                    dispatch(fn() => self::importOrders(
                        resource_path("import/orders/2024 ($k).xlsx"),
                        $i,
                        $i + self::STEP
                    ));
                }
        }
    }

    public static function importOrders($filename, $from, $to)
    {
        Log::info("Job from $from $to started");

        /**  Create an Instance of our Read Filter  **/
        $filterSubset = new MyReadFilter($from, $to);

        /**  Create a new Reader of the type defined in $inputFileType  **/
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        /**  Tell the Reader that we want to use the Read Filter  **/
        $reader->setReadFilter($filterSubset);
        /**  Load only the rows and columns that match our filter to Spreadsheet  **/

        $spreadsheet = $reader->load($filename);

        if (!$spreadsheet) {
            throw new Exception('Failed to read files content');
        }

        $activeSheet = $spreadsheet->getSheet(0);
        $highestRow = $activeSheet->getHighestDataRow();

        for ($i = $from; $i <= $to; $i++) {
            $client_name = $activeSheet->getCell('D' . $i)->getValue();
            $client_address = $activeSheet->getCell('F' . $i)->getValue();

            $totalAmount = $activeSheet->getCell('BG' . $i)->getCalculatedValue();
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

            $prod19Count = $activeSheet->getCell('X' . $i)->getValue();
            $prod19Price = $activeSheet->getCell('Y' . $i)->getValue();

            $prod6Count = $activeSheet->getCell('Z' . $i)->getValue();
            $prod6Price = $activeSheet->getCell('AA' . $i)->getValue();

            $prod18Count = $activeSheet->getCell('AB' . $i)->getValue();
            $prod18Price = $activeSheet->getCell('AC' . $i)->getValue();

            $prod10Count = $activeSheet->getCell('AD' . $i)->getValue();
            $prod10Price = $activeSheet->getCell('AE' . $i)->getValue();

            $prod11Count = $activeSheet->getCell('AH' . $i)->getValue();
            $prod11Price = $activeSheet->getCell('AI' . $i)->getValue();

            $prod12Count = $activeSheet->getCell('AF' . $i)->getValue();
            $prod12Price = $activeSheet->getCell('AG' . $i)->getValue();

            $prod7Count = $activeSheet->getCell('AJ' . $i)->getValue();
            $prod7Price = $activeSheet->getCell('AK' . $i)->getValue();

            $prod14Count = $activeSheet->getCell('AN' . $i)->getValue();
            $prod14Price = $activeSheet->getCell('AO' . $i)->getValue();

            $prod15Count = $activeSheet->getCell('AP' . $i)->getValue();
            $prod15Price = $activeSheet->getCell('AQ' . $i)->getValue();

            $prod13Count = $activeSheet->getCell('AR' . $i)->getValue();
            $prod13Price = $activeSheet->getCell('AS' . $i)->getValue();

            $prod16Count = $activeSheet->getCell('AT' . $i)->getValue();
            $prod16Price = $activeSheet->getCell('AU' . $i)->getValue();

            $prod17Count = $activeSheet->getCell('AL' . $i)->getValue();
            $prod17Price = $activeSheet->getCell('AM' . $i)->getValue();

            $prod20Count = $activeSheet->getCell('AX' . $i)->getValue();
            $prod20Price = $activeSheet->getCell('AY' . $i)->getValue();

            $prod21Count = $activeSheet->getCell('AZ' . $i)->getValue();
            $prod21Price = $activeSheet->getCell('BA' . $i)->getValue();

            $prod22Count = $activeSheet->getCell('BB' . $i)->getValue();
            $prod22Price = $activeSheet->getCell('BC' . $i)->getValue();

            $prod23Count = $activeSheet->getCell('AV' . $i)->getValue();
            $prod23Price = $activeSheet->getCell('AW' . $i)->getValue();

            if (!$client_name) {
                continue;
            }

            if (!$ddate) {
                continue;
            }



            $updates = [];
            if ($client_address) $updates['address'] = $client_address;
            $customer = Customer::firstOrCreate([
                "name"   =>   $client_name,
            ], $updates);

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
            if ($prod7Count && $prod7Price)
                array_push($products, [
                    "id"        =>  7,
                    "quantity"  =>  $prod7Count,
                    "price"     =>  $prod7Price,
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
            if ($prod10Count && $prod10Price)
                array_push($products, [
                    "id"        =>  10,
                    "quantity"  =>  $prod10Count,
                    "price"     =>  $prod10Price,
                    "combo_id"  =>  null
                ]);
            if ($prod11Count && $prod11Price)
                array_push($products, [
                    "id"        =>  11,
                    "quantity"  =>  $prod11Count,
                    "price"     =>  $prod11Price,
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
            if ($prod15Count && $prod15Price)
                array_push($products, [
                    "id"        =>  15,
                    "quantity"  =>  $prod15Count,
                    "price"     =>  $prod15Price,
                    "combo_id"  =>  null
                ]);
            if ($prod16Count && $prod16Price)
                array_push($products, [
                    "id"        =>  16,
                    "quantity"  =>  $prod16Count,
                    "price"     =>  $prod16Price,
                    "combo_id"  =>  null
                ]);
            if ($prod17Count && $prod17Price)
                array_push($products, [
                    "id"        =>  17,
                    "quantity"  =>  $prod17Count,
                    "price"     =>  $prod17Price,
                    "combo_id"  =>  null
                ]);
            if ($prod18Count && $prod18Price)
                array_push($products, [
                    "id"        =>  18,
                    "quantity"  =>  $prod18Count,
                    "price"     =>  $prod18Price,
                    "combo_id"  =>  null
                ]);
            if ($prod19Count && $prod19Price)
                array_push($products, [
                    "id"        =>  19,
                    "quantity"  =>  $prod19Count,
                    "price"     =>  $prod19Price,
                    "combo_id"  =>  null
                ]);
            if ($prod20Count && $prod20Price)
                array_push($products, [
                    "id"        =>  20,
                    "quantity"  =>  $prod20Count,
                    "price"     =>  $prod20Price,
                    "combo_id"  =>  null
                ]);
            if ($prod21Count && $prod21Price)
                array_push($products, [
                    "id"        =>  21,
                    "quantity"  =>  $prod21Count,
                    "price"     =>  $prod21Price,
                    "combo_id"  =>  null
                ]);
            if ($prod22Count && $prod22Price)
                array_push($products, [
                    "id"        =>  22,
                    "quantity"  =>  $prod22Count,
                    "price"     =>  $prod22Price,
                    "combo_id"  =>  null
                ]);
            if ($prod23Count && $prod23Price)
                array_push($products, [
                    "id"        =>  23,
                    "quantity"  =>  $prod23Count,
                    "price"     =>  $prod23Price,
                    "combo_id"  =>  null
                ]);

            Log::info("Adding order $i");
            Order::newOrder(
                $customer->id,
                $customer->name,
                $client_address ?? ($customer->address ?? "N/A"),
                $customer->phone ?? "N/A",
                $customer->zone_id ?? 1,
                $customer->location_url,
                driverId: null,
                totalAmount: $totalAmount,
                deliveryAmount: $deliveryAmount ?? 0,
                discountAmount: 0,
                deliveryDate: new Carbon($ddate),
                note: $note,
                products: $products,
                creator_id: 1,
                migrated: true
            );
        }
    }
}


/**  Define a Read Filter class implementing \PhpOffice\PhpSpreadsheet\Reader\IReadFilter  */
class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{

    private $row_from;
    private $row_to;

    public function __construct($row_from, $row_to)
    {
        $this->row_from = $row_from;
        $this->row_to = $row_to;
    }


    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        //  Read rows 1 to 7 and columns A to E only
        if ($row >= $this->row_from && $row <= $this->row_to) {
            if (in_array($columnAddress, [
                'A',
                'B',
                'C',
                'D',
                'E',
                'F',
                'G',
                'H',
                'I',
                'J',
                'K',
                'L',
                'M',
                'N',
                'O',
                'P',
                'Q',
                'R',
                'S',
                'T',
                'U',
                'V',
                'W',
                'X',
                'Y',
                'Z',
                'AA',
                'AB',
                'AC',
                'AD',
                'AE',
                'AF',
                'AG',
                'AH',
                'AI',
                'AJ',
                'AL',
                'AM',
                'AN',
                'AO',
                'AP',
                'AQ',
                'AR',
                'AS',
                'AT',
                'AU',
                'AW',
                'AX',
                'AY',
                'AZ',
                'BA',
                'BB',
                'BC',
                'BD',
                'BE',
                'BF',
                'BG',
            ])) {
                return true;
            }
        }
        return false;
    }
}
