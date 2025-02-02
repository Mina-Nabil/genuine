<?php

namespace App\Models\Payments;

use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BalanceTransaction extends Model
{
    use HasFactory;
    protected $table = 'balance_transactions';

    protected $fillable = [
        'transactionable_id',
        'transactionable_type',
        'payment_id',
        'order_id',
        'amount', //can be negative or positive
        'balance',
        'description',
        'created_by',
    ];

    public static function createBalanceTransaction(Model $model, $amount, $description = null, $order_id = null): bool
    {
        try {
            return DB::transaction(function () use ($model, $amount, $description, $order_id) {
                // Update the balance in the given model
                $model->balance += $amount;
                $model->save();

                // Create the balance transaction
                self::create([
                    'order_id' => $order_id,
                    'transactionable_id' => $model->id,
                    'transactionable_type' => $model->getMorphClass(),
                    'amount' => $amount,
                    'balance' => $model->balance,
                    'description' => $description,
                    'created_by' => auth()->id(), // Assumes the user is authenticated
                ]);

                return true;
            });
        } catch (Exception $e) {
            report($e);
            AppLog::error('Failed to create balance transaction', $e->getMessage());
            return false;
        }
    }

    public function scopeUserTransactions($query , $user_id){
        return $query->where('transactionable_type',User::MORPH_TYPE)
        ->where('transactionable_id',$user_id);
    }
    
    public function getForDriverAttribute(){
        return $this->transactionable_type === User::MORPH_TYPE;
    }

    // relations
    public function transactionable()
    {
        return $this->morphTo();
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
