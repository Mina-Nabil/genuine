<?php

namespace App\Models\Payments;

use App\Models\Customers\Customer;
use App\Models\Materials\Supplier;
use App\Models\Materials\SupplierInvoice;
use App\Models\Orders\Order;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerPayment extends Model
{
    use HasFactory;

    protected $table = 'customer_payments';

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    protected $fillable = ['customer_id', 'order_id', 'supplier_id', 'invoice_id', 'amount', 'payment_method', 'type_balance', 'payment_date', 'note', 'created_by'];

    const PYMT_CASH = 'cash';
    const PYMT_BANK_TRANSFER = 'bank_transfer';
    const PYMT_WALLET = 'wallet';
    const PYMT_DEBIT = 'debit';
    const PAYMENT_METHODS = [self::PYMT_CASH, self::PYMT_BANK_TRANSFER, self::PYMT_WALLET];
    const PAYMENT_METHODS_WITH_DEBIT = [self::PYMT_CASH, self::PYMT_BANK_TRANSFER, self::PYMT_WALLET, self::PYMT_DEBIT];

    public static function createPayment($amount, $paymentMethod, $note, $title_id = null)
    {
        return DB::transaction(function () use ($amount, $paymentMethod, $note, $title_id) {
            try {

                /** @var User */
                $loggedInUser = Auth::user();
                if ($loggedInUser && !$loggedInUser->can('create', self::class)) {
                    return false;
                }

                $new_type_balance = self::calculateNewBalance(-$amount, $paymentMethod);

                $payment = self::create([
                    'amount' => -$amount,
                    'title_id' => $title_id,
                    'payment_method' => $paymentMethod,
                    'type_balance' => $new_type_balance,
                    'payment_date' => now()->format('Y-m-d'),
                    'created_by' => 2,
                    'note' => $note
                ]);

                $payment->save();
                AppLog::info('Payment Created', 'Payment created successfuly', loggable: $payment);
                return true;
            } catch (Exception $e) {
                report($e);
                AppLog::error('Failed creating payment', $e->getMessage());
                return false;
            }
        });
    }

    public function resetBalance()
    {
        $this->type_balance = $this->amount;
        $this->save();
    }

    public function recalculateBalance()
    {
        $latest_balance = self::paymentMethod($this->payment_method)->where('id', '<', $this->id)->orderByDesc('id')->limit(1)->first()?->type_balance ?? 0;
        $this->type_balance = $latest_balance + $this->amount;
        $this->save();
    }

    public static function calculateNewBalance(float $amount, string $paymentMethod): float
    {
        $old_payment_type_balance = CustomerPayment::where('payment_method', $paymentMethod)->latest('id')->value('type_balance');
        $new_payment_type_balance = ($old_payment_type_balance ?? 0) + $amount;

        return $new_payment_type_balance;
    }

    public function scopeSearch($query, $term)
    {
        $term = "%{$term}%";
        return $query
            ->select('customer_payments.*')
            ->leftjoin('suppliers', 'customer_payments.supplier_id', '=', 'suppliers.id')
            ->leftjoin('customers', 'customer_payments.customer_id', '=', 'customers.id')
            ->where(function ($q) use ($term) {
                $q->orwhere('suppliers.name', 'like', "%$term%")
                    ->orwhere('customers.name', 'like', "%$term%")
                    ->orWhere('customer_payments.note', 'like', "%$term%");
            });
    }



    public function scopeFrom($query, Carbon $date)
    {
        return $query->whereDate('payment_date', '>=', $date->format('Y-m-d'));
    }

    public function scopePaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Define relationship with the Order model.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    /**
     * Define relationship with the Order model.
     */
    public function invoice()
    {
        return $this->belongsTo(SupplierInvoice::class, 'invoice_id');
    }

    /**
     * Define relationship with the User model for the created_by user.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeTotalsByTitle($query, ?Carbon $startDate = null, ?Carbon $endDate = null)
    {
        $query = $query->selectRaw('
            customer_payments.title_id,
            payment_titles.title as title_name,
            payment_titles.limit as title_limit,
            SUM(customer_payments.amount) as total_amount,
            SUM(CASE WHEN payment_method = "cash" THEN amount ELSE 0 END) as cash_total,
            SUM(CASE WHEN payment_method = "bank_transfer" THEN amount ELSE 0 END) as bank_total,
            SUM(CASE WHEN payment_method = "wallet" THEN amount ELSE 0 END) as wallet_total,
            COUNT(*) as transaction_count
        ')
            ->leftJoin('payment_titles', 'payment_titles.id', '=', 'customer_payments.title_id')
            ->groupBy('customer_payments.title_id', 'payment_titles.title', 'payment_titles.limit');

        if ($startDate) {
            $query->where('customer_payments.created_at', '>=', $startDate->startOfDay());
        }

        if ($endDate) {
            $query->where('customer_payments.created_at', '<=', $endDate->endOfDay());
        }

        return $query;
    }

    public function scopeTodayCashIn($query)
    {
        return $query->whereDate('payment_date', today())
            ->where('amount', '>', 0)
            ->selectRaw('SUM(amount) as total_in');
    }

    public function scopeTodayCashOut($query)
    {
        return $query->whereDate('payment_date', today())
            ->where('amount', '<', 0)
            ->selectRaw('SUM(ABS(amount)) as total_out');
    }

    public function scopeTodayBalance($query)
    {
        return $query->whereDate('payment_date', today())
            ->selectRaw('SUM(amount) as today_balance');
    }
}
