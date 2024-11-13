<?php

namespace App\Models\Payments;

use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceTransaction extends Model
{
    use HasFactory;
    protected $table = 'balance_transactions';

    protected $fillable = [
        'customer_id',
        'payment_id',
        'order_id',
        'amount', //can be negative or positive
        'balance',
        'description',
        'created_by',
    ];


    // relations
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payment()
    {
        return $this->belongsTo(CustomerPayment::class, 'payment_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
