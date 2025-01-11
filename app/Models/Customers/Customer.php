<?php

namespace App\Models\Customers;

use App\Models\Orders\Order;
use App\Models\Orders\PeriodicOrder;
use App\Models\Payments\BalanceTransaction;
use App\Models\Payments\CustomerPayment;
use App\Models\Pets\Pet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;
use App\Models\Users\AppLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Customer extends Model
{
    use HasFactory;

    const MORPH_TYPE = 'customer';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'location_url',
        'zone_id',
        'monthly_weight_target',
        'note',
        'creator_id'
    ];

    public $ordersKGs = [];

    // Create a new customer
    public static function newCustomer($name, $address = null, $phone, $location_url = null, $zone_id = null)
    {
        try {
            $customer = new self();
            $customer->name = $name;
            $customer->address = $address;
            $customer->phone = $phone;
            $customer->location_url = $location_url;
            $customer->zone_id = $zone_id;
            $customer->creator_id = Auth::id();

            if ($customer->save()) {
                AppLog::info('Customer created', "Customer $name created successfully.", loggable: $customer);
                return $customer;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Customer creation failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    //get customer or null if name is exact
    public static function findByName($name)
    {
        return self::byName($name)->first();
    }

    // import customers data
    public static function importData($file)
    {
        $spreadsheet = IOFactory::load($file);
        if (!$spreadsheet) {
            throw new Exception('Failed to read files content');
        }
        $activeSheet = $spreadsheet->getSheet(0);
        $highestRow = $activeSheet->getHighestDataRow();

        for ($i = 2; $i <= $highestRow; $i++) {
            $name       = $activeSheet->getCell('B' . $i)->getValue();
            $zone_name  = $activeSheet->getCell('C' . $i)->getValue();
            $phone      = $activeSheet->getCell('L' . $i)->getValue();
            $address    = $activeSheet->getCell('M' . $i)->getValue();

            if ($phone && !str_starts_with($phone, '0')) $phone .= '0';
            if (!$name) {
                continue;
            }
            $zone = null;

            if ($zone_name) {
                $zone = Zone::getZoneByName($zone_name, true);
            }

            self::firstOrCreate([
                "name"  =>  $name,
            ], [
                "phone"  =>  $phone,
                "address" =>    $address,
                "zone_id" =>    $zone?->id,
            ]);
        }
    }


    // Edit customer info
    public function editInfo($name, $address = null, $phone, $location_url = null, $zone_id = null)
    {
        try {
            $this->name = $name;
            $this->address = $address;
            $this->phone = $phone;
            $this->location_url = $location_url;
            $this->zone_id = $zone_id;

            if ($this->save()) {
                AppLog::info('Customer updated', "Customer $name updated successfully.", loggable: $this);
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Updating customer failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function editNote($note = null)
    {
        try {
            $this->note = $note;

            if ($this->save()) {
                AppLog::info('Customer note updated', "Customer $this->name note updated successfully.");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Updating customer failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function setMonthlyWeightTarget(int $target)
    {
        try {
            $this->monthly_weight_target = $target;

            if ($this->save()) {
                AppLog::info('Monthly weight target updated', "Customer $this->name Monthly weight target updated successfully.");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Updating customer failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    // Method to add a new pet for this customer
    public function addPet($name, $category, $type, $bdate, $note = null)
    {
        try {
            $pet = new Pet();
            $pet->name = $name;
            $pet->category = $category;
            $pet->type = $type;
            $pet->bdate = $bdate;
            $pet->note = $note;

            // Associate the pet with the current customer
            if ($this->pets()->save($pet)) {
                AppLog::info('Pet added', "Pet $name added for customer {$this->name}.", loggable: $this);
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Adding pet failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function addFollowup($title, $call_time, $desc = null): Followup|false
    {
        try {
            $res = $this->followups()->create([
                'creator_id' => Auth::id(),
                'title' => $title,
                'call_time' => Carbon::parse($call_time),
                'desc' => $desc,
            ]);
            AppLog::info('Follow-up created', loggable: $res);
            return $res;
        } catch (Exception $e) {
            AppLog::error("Can't create followup", desc: $e->getMessage());
            report($e);
            return false;
        }
    }

    public function appendKGTotal(Carbon $start, Carbon $end)
    {
        $this->ordersKGs[] = $this->orders()
            ->deliveryBetween($start, $end)
            ->join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'products.id', '=', 'order_products.product_id')
            ->groupBy('orders.id')
            ->selectRaw('SUM(products.weight * order_products.quantity) as week_weight')
            ->first()?->week_weight;
    }

    // Delete customer and optionally associated pets
    public function deleteCustomer(): bool
    {
        if (!$this->orders->isEmpty()) {
            return false;
        }
        try {
            $this->pets()->delete();

            if ($this->delete()) {
                AppLog::info('Customer deleted', "Customer {$this->name} deleted successfully.");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Deleting customer failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function addToBalanceWithPayment($amount, $paymentMethod, $paymentDate, $note = 'Balance update')
    {
        try {
            DB::transaction(function () use ($amount, $paymentMethod, $paymentDate, $note) {
                /** @var User */
                $loggedInUser = Auth::user();
                if ($loggedInUser && !$loggedInUser->can('updateCustomerBalance', $this)) {
                    return false;
                }

                // Ensure the amount is positive (to add to balance)
                if ($amount <= 0) {
                    throw new Exception('Amount to be added must be positive.');
                }

                // Step 1: Increase the customer balance
                $this->balance += $amount;
                $this->save();

                // Step 2: Create a positive balance transaction
                BalanceTransaction::create([
                    'customer_id' => $this->id,
                    'amount' => $amount, // Positive amount added to the balance
                    'balance' => $this->balance,
                    'description' => $note ?? 'Add to balance',
                    'created_by' => $loggedInUser->id,
                    // 'balance' => $this->balance,
                ]);

                $new_type_balance = CustomerPayment::calculateNewBalance($amount, $paymentMethod);

                // Step 3: Create the payment record for the added amount
                CustomerPayment::create([
                    'customer_id' => $this->id,
                    'amount' => $amount, // Payment amount (same as the added balance)
                    'payment_method' => $paymentMethod,
                    'type_balance' => $new_type_balance,
                    'payment_date' => $paymentDate,
                    'note' => $note ?? 'Add to balance',
                    'created_by' => $loggedInUser->id,
                ]);

                // Step 4: Log the action (optional)
                AppLog::info("Added {$amount} to {$this->name}'s balance and created payment", loggable: $this);
            });
            return true;
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            report($e);
            AppLog::error('Failed to add to balance and create payment', $e->getMessage(), loggable: $this);
            return false;
        }
    }

    public function addComment(string $comment): void
    {
        AppLog::comment($comment, $desc = null, loggable: $this);
    }

    public function canDeductFromBalance($amount)
    {
        // Check if the customer has enough balance
        if ($this->balance >= $amount) {
            return true;
        }

        return false;
    }

    public function countPets(): int
    {
        return $this->pets()->count();
    }

    public function getTotalOrdersAttribute()
    {
        return $this->orders()->count();
    }

    public static function exportReport($searchText, $zone_id = null, Carbon $created_from = null, Carbon $created_to = null, $creator_id = null)
    {
        $customers = self::report(
            $searchText,
            $zone_id,
            $created_from,
            $created_to,
            $creator_id
        )->get();

        $template = IOFactory::load(resource_path('import/customers_report.xlsx'));
        if (!$template) {
            throw new Exception('Failed to read template file');
        }
        $newFile = $template->copy();
        $activeSheet = $newFile->getActiveSheet();

        $i = 2;
        /** @var User */

        foreach ($customers as $payment) {

            $activeSheet->getCell('A' . $i)->setValue($payment->sold_policy->policy_number);


            $i++;
        }

        $writer = new Xlsx($newFile);
        $file_path = "'/downloads/payments_export.xlsx";
        $public_file_path = storage_path($file_path);
        $writer->save($public_file_path);

        return response()->download($public_file_path)->deleteFileAfterSend(true);
    }

    //attributes
    public function getLastOrderIdAttribute()
    {
        return $this->orders()->latest()->first()?->id;
    }

    // Scopes
    public function scopeByZones($query, array $zones)
    {
        $query->whereIn('customers.zone_id', $zones);
    }

    public function scopeOrderedBetween($query, Carbon $start, Carbon $end)
    {
        if (!joined($query, 'orders')) {
            $query->join('orders.customer_id', '=', 'customers.id');
        }
        $query->where(function ($q) use ($start, $end) {
            $q->where('orders.delivery_date', '>=', $start->format('Y-m-d 00:00:00'))
                ->where('orders.delivery_date', '<=', $end->format('Y-m-d 23:59:59'));
        });
    }


    public function scopeReport($query, $searchText = null, $zone_id = null, Carbon $created_from = null, Carbon $created_to = null, $creator_id = null)
    {
        return $query->select('customers.*')
            ->when($zone_id, fn($q) => $q->where('customers.zone_id', $zone_id))
            ->when($searchText, fn($q) => $q->search($searchText))
            ->when($created_from, fn($q) => $q->where('customers.created_at', '>=', $created_from->format('Y-m-d 00:00:00')))
            ->when($created_to, fn($q) => $q->where('customers.created_at', '<=', $created_to->format('Y-m-d 23:59:59')))
            ->when($creator_id, fn($q) => $q->where('customers.creator_id', $creator_id));
    }

    public function scopeZone($query, $zone_id = null)
    {
        return $query->when($zone_id, fn($q) => $q->where('customers.zone_id', $zone_id));
    }


    public function scopeSearch($query, $term)
    {
        $term = "%{$term}%";
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', $term)
                ->orWhere('address', 'like', $term)
                ->orWhere('phone', 'like', $term)
                ->orWhereHas('zone', function ($zoneQuery) use ($term) {
                    $zoneQuery->where('name', 'like', $term);
                });
        });
    }

    public function scopeByName($query, $name)
    {
        return $query->where('name', '=', $name);
    }

    // Relations
    public function payments(): HasMany
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(AppLog::class, 'loggable_id')->where('loggable_type', self::MORPH_TYPE);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(BalanceTransaction::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function periodicOrders()
    {
        return $this->hasMany(PeriodicOrder::class);
    }

    public function followups(): MorphMany
    {
        return $this->morphMany(Followup::class, 'called');
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }
}
