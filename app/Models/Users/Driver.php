<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    // Car Types
    const CAR_TYPES = [self::CAR_TYPE_SEDAN, self::CAR_TYPE_SUV, self::CAR_TYPE_PICKUP, self::CAR_TYPE_VAN, self::CAR_TYPE_MOTORCYCLE];
    const CAR_TYPE_SEDAN = 'sedan';
    const CAR_TYPE_SUV = 'suv';
    const CAR_TYPE_PICKUP = 'pickup';
    const CAR_TYPE_VAN = 'van';
    const CAR_TYPE_MOTORCYCLE = 'motorcycle';

    protected $fillable = ['user_id', 'weight_limit', 'order_quantity_limit', 'car_type', 'car_model', 'is_available'];

    

    public function scopeSearch($query, $searchTerm = null)
    {
        if (!is_null($searchTerm)) {
            return $query->where(function ($query) use ($searchTerm) {
                $query
                    ->where('weight_limit', 'like', '%' . $searchTerm . '%')
                    ->orWhere('order_quantity_limit', 'like', '%' . $searchTerm . '%')
                    ->orWhere('car_type', 'like', '%' . $searchTerm . '%')
                    ->orWhere('car_model', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('user', function ($query) use ($searchTerm) {
                        $query
                            ->where('username', 'like', '%' . $searchTerm . '%')
                            ->orWhere('first_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('type', 'like', '%' . $searchTerm . '%')
                            ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        return $query;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
