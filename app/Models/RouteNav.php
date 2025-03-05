<?php

namespace App\Models;

use App\Models\Orders\Order;
use App\Models\Users\User;
use App\Services\GoogleMapsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class RouteNav extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'route_nav';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'driver_id',
        'day',
        'response',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'day' => 'date',
        'response' => 'array',
    ];


    public static function getBestRoute($driverId, Carbon $day, $origin, $destination)
    {
        Log::info('Origin');
        Log::info($origin);
        Log::info('Origin end');

        Log::info('Destination');
        Log::info($destination);
        Log::info('Destination end');

        $route = self::where('driver_id', $driverId)->where('day', $day)->first();

        if (!$route) {
            $waypoints = [];
            $orders = Order::shift($driverId, $day)->get();
            $orders = $orders->filter(function ($order) {
                return $order->valid_location_url;
            })->values();
            Log::info('Orders Count : '.count($orders));
            
            $orders->each(function ($order) use (&$waypoints) {
                $waypoints[] = $order->valid_location_url;
            });

            if (count($waypoints) > 2) {
                $googleMapsService = new GoogleMapsService();
                $route = $googleMapsService->getOptimizedRoute($origin, $destination, $waypoints);
                Log::info('Route');
                Log::info($route);
                Log::info('Route end');
                foreach ($route as $key => $value) {
                    $orders[$value]->driver_order = $key+1;
                    $orders[$value]->save();
                }
                $route = self::create([
                    'driver_id' => $driverId,
                    'day' => $day,
                    'response' => json_encode($orders),
                ]);
            } else {
                throw new \Exception('Not enough waypoints to generate a route');
            }
        }

        return $route->response;
    }

    /**
     * Get the driver that owns the route navigation.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
