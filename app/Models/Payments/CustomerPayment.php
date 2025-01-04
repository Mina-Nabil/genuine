<?php

namespace App\Models\Payments;

use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerPayment extends Model
{
    use HasFactory;

    protected $table = 'customer_payments';

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    protected $fillable = ['customer_id', 'order_id', 'amount', 'payment_method', 'type_balance', 'payment_date', 'note', 'created_by'];

    const PYMT_CASH = 'cash';
    const PYMT_BANK_TRANSFER = 'bank_transfer';
    const PYMT_WALLET = 'wallet';
    const PAYMENT_METHODS = [self::PYMT_CASH, self::PYMT_BANK_TRANSFER, self::PYMT_WALLET];

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
                $customerQuery->where('name', 'like', $term)
                    ->orWhere('address', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            });
        });
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

    /**
     * Define relationship with the User model for the created_by user.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
