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
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public static function createPayment($amount, $paymentMethod, $note)
    {
        return DB::transaction(function () use ($amount, $paymentMethod, $note) {
            try {

                /** @var User */
                $loggedInUser = Auth::user();
                if ($loggedInUser && !$loggedInUser->can('create', self::class)) {
                    return false;
                }

                $new_type_balance = self::calculateNewBalance(-$amount, $paymentMethod);

                $payment = self::create([
                    'amount' => -$amount,
                    'payment_method' => $paymentMethod,
                    'type_balance' => $new_type_balance,
                    'payment_date' => now()->format('Y-m-d'),
                    'created_by' => 2,
                    'note' => $note
                ]);

                $payment->save();
                AppLog::info('Payment Created','Payment created successfuly',loggable: $payment);
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
        Log::info($latest_balance);
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
        return $query->where(function ($q) use ($term) {
            $q->whereHas('customer', function ($customerQuery) use ($term) {
                $customerQuery->where('name', 'like', $term)->orWhere('address', 'like', $term)->orWhere('phone', 'like', $term);
            });
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
}
