<?php

namespace App\Models\Payments;

use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use App\Models\Users\AppLog;
use App\Models\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BalanceTransaction extends Model
{
    use HasFactory;
    protected $table = 'balance_transactions';

    const WITHDRAWAL_TYPES = [self::WD_TYPE_ADVANCE, self::WD_TYPE_SALARY, self::WD_TYPE_X2, self::WD_TYPE_ROAD_FEES];
    const WD_TYPE_ADVANCE = 'سلفة';
    const WD_TYPE_SALARY = 'مرتب';
    const WD_TYPE_X2 = 'x2';
    const WD_TYPE_ROAD_FEES = 'كارتة';
    const WD_TYPE_PURCHASES = 'مشتريات';

    protected $fillable = [
        'transactionable_id',
        'transactionable_type',
        'payment_id',
        'customer_id',
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

    public function scopeDateRange($query, $from, $to)
    {
        $from = Carbon::parse($from)->startOfDay();
        $to = Carbon::parse($to)->endOfDay();
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function scopeCountOrderDelivery($query)
    {
        return $query->where('description', 'like', '%توصيل أوردر%')->count('id');
    }

    public function scopeTotalOrderDelivery($query)
    {
        return $query->where('description', 'like', '%توصيل أوردر%')->sum('amount');
    }

    public function scopeTotalStartDayDelivery($query)
    {
        return $query->where('description', 'like', '%بداية توصيل%')->sum('amount');
    }

    public function scopeTotalReturn($query)
    {
        return $query->where('description', 'like', '%رجوع%')->sum('amount');
    }

    public function scopeUserTransactions($query, $user_id)
    {
        return $query->where('transactionable_type', User::MORPH_TYPE)->where('transactionable_id', $user_id);
    }

    public function scopeWithdrawalTypeSum($query, $withdrawalType)
    {
        // Check if the withdrawal type exists in the constants
        if (!in_array($withdrawalType, [self::WD_TYPE_ADVANCE, self::WD_TYPE_SALARY, self::WD_TYPE_X2, self::WD_TYPE_ROAD_FEES])) {
            return 0; // Return 0 if the type is invalid
        }

        return $query->where('description', 'like', '%' . $withdrawalType . '%')->sum('amount');
    }

    public function getForDriverAttribute()
    {
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
