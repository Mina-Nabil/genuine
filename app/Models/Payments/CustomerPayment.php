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
    use HasFactory, SoftDeletes;

    protected $table = 'customer_payments';

    protected $fillable = [
        'customer_id',
        'order_id',
        'amount',
        'payment_method',
        'payment_date',
        'reference',
        'note',
        'status',
        'created_by',
        'closed_by',
    ];
    
    const PYMT_CASH = 'cash';
    const PYMT_CREDIT_CARD = 'credit_card';
    const PYMT_BANK_TRANSFER = 'bank_transfer';
    const PYMT_INSTAPAY = 'instapay';
    const PYMT_WALLET = 'wallet';
    const PAYMENT_METHODS = [self::PYMT_CASH, self::PYMT_CREDIT_CARD,self::PYMT_BANK_TRANSFER,self::PYMT_INSTAPAY,self::PYMT_WALLET];

    
    const STATUS_NEW = 'new';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';
    const STATUSES = [self::STATUS_NEW, self::STATUS_PAID, self::STATUS_CANCELLED];


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

    /**
     * Define relationship with the User model for the closed_by user.
     */
    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}