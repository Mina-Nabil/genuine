<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Events\AppNotification;
use App\Models\Orders\Order;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const MORPH_TYPE = 'user';

    const FILES_DIRECTORY = 'users/';

    const TYPE_ADMIN = 'admin';
    const TYPE_INVENTORY = 'inventory';
    const TYPE_SALES = 'sales';
    const TYPE_DRIVER = 'driver';

    const TYPES = [self::TYPE_ADMIN, self::TYPE_INVENTORY, self::TYPE_SALES, self::TYPE_DRIVER];

    protected $fillable = ['username', 'first_name', 'last_name', 'type', 'email', 'phone', 'id_number', 'id_doc_url', 'driving_license_number', 'driving_license_doc_url', 'car_license_number', 'car_license_doc_url', 'password', 'image_url', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    public static function newUser(
        $username,
        $first_name,
        $last_name,
        $type,
        $password,
        $email = null,
        $phone = null,
        $id_number = null,
        $id_doc_url = null,
        $driving_license_number = null,
        $driving_license_doc_url = null,
        $car_license_number = null,
        $car_license_doc_url = null,
        $image_url = null,
        $shift_title = null, // Driver-specific field
        $weight_limit = null, // Driver-specific field
        $order_quantity_limit = null, // Driver-specific field
        $car_type = null, // Driver-specific field
        $car_model = null, // Driver-specific field
        $shift_start_time = null,
        $shift_end_time = null,
    ): self|false {
        try {
            // Check if the user already exists
            $exists = self::userExists($username);
            if ($exists) {
                return $exists;
            }

            // Create a new user instance
            $user = new self([
                'username' => $username,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone,
                'id_number' => $id_number,
                'id_doc_url' => $id_doc_url,
                'driving_license_number' => $driving_license_number,
                'driving_license_doc_url' => $driving_license_doc_url,
                'car_license_number' => $car_license_number,
                'car_license_doc_url' => $car_license_doc_url,
                'type' => $type,
                'image_url' => $image_url,
                'is_active' => 1,
                // Hash the password before storing
                'password' => bcrypt($password),
            ]);

            // Save the user
            $user->save();

            // Create a corresponding driver instance if the user is a driver
            if ($type === self::TYPE_DRIVER) {
                $driver = new Driver([
                    'user_id' => $user->id,
                    'shift_title' => $shift_title,
                    'weight_limit' => $weight_limit,
                    'start_time' => $shift_start_time,
                    'end_time' => $shift_end_time,
                    'order_quantity_limit' => $order_quantity_limit,
                    'car_type' => $car_type,
                    'car_model' => $car_model,
                    'is_available' => true,
                ]);

                // Save the driver record
                $driver->save();
            }

            // Log user creation
            AppLog::info('User created', "User $username created");
            return $user;
        } catch (Exception $e) {
            // Log the error
            AppLog::error('Adding user failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function editInfo($username, $first_name, $last_name, $type, $email = null, $phone = null, $id_number = null, $id_doc_url = null, $driving_license_number = null, $driving_license_doc_url = null, $car_license_number = null, $car_license_doc_url = null, $image_url = null, $password = null): bool
    {
        try {
            // Update user attributes
            $this->username = $username;
            $this->first_name = $first_name;
            $this->last_name = $last_name;
            $this->email = $email;
            $this->phone = $phone;
            $this->id_number = $id_number;
            $this->id_doc_url = $id_doc_url;
            $this->driving_license_number = $driving_license_number;
            $this->driving_license_doc_url = $driving_license_doc_url;
            $this->car_license_number = $car_license_number;
            $this->car_license_doc_url = $car_license_doc_url;
            $this->type = $type;
            $this->image_url = $image_url;

            // Only update password if provided
            if ($password) {
                $this->password = bcrypt($password);
            }

            // Save the updated user
            if ($this->save()) {
                AppLog::info('User updated', "User $username updated");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Updating user failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function switchSession($username)
    {
        // Assuming 'username' is the attribute to identify the user instead of user_id
        $availableSessions = $this->tmp_access_to()->get()->pluck('username')->toArray(); // Changed to pluck 'username'

        if (!in_array($username, $availableSessions)) {
            return false;
        }

        // Find the user by username
        $user = User::where('username', $username)->first(); // Assuming you have a User model
        if ($user) {
            Auth::loginUsingId($user->id); // Log in using the user's ID
            Session::put('original_session_id', $this->to_id); // Store the original session ID
        }

        return true; // Indicate successful switch
    }

    public function toggleActivation(): bool
    {
        try {
            // Toggle the is_active field (flip between true/false)
            $this->is_active = !$this->is_active;

            // Save the updated status
            if ($this->save()) {
                $status = $this->is_active ? 'activated' : 'deactivated';
                AppLog::info('User activation toggled', "User {$this->username} has been {$status}");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Toggling user activation failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    public function addTempAccess($to_username, Carbon $expiry)
    {
        if (Session::get('original_session_id')) {
            return false;
        }

        try {
            // Assuming 'to_username' is the attribute for identifying users for temporary access
            return $this->tmp_access()->firstOrCreate(
                [
                    'to_username' => $to_username, // Assuming you will store username instead of ID
                ],
                [
                    'expiry' => $expiry->format('Y-m-d'), // Store expiry date
                ],
            );
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function getAvailableSessions()
    {
        $users = new Collection(); // Initialize a new Collection for users
        $original_session = Session::get('original_session_id'); // Get the original session ID

        if ($original_session) {
            // If there is an original session, find the user by their ID
            $user = User::find($original_session); // Assuming 'original_session_id' holds the user's ID
            if ($user) {
                $users->push($user); // Add the user to the collection if found
            }
        } else {
            // If no original session exists, retrieve temporary access users
            foreach ($this->tmp_access_to as $ta) {
                $user = User::find($ta->from_id); // Find the user using the 'from_id' from tmp_access_to
                if ($user) {
                    $users->push($user); // Add the user to the collection if found
                }
            }
        }

        return $users; // Return the collection of available users
    }

    public function changePassword($password): bool
    {
        try {
            $this->password = bcrypt($password);
            if ($this->save()) {
                AppLog::info('Password updated', "New password for $this->username");
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            AppLog::error('Changing user password failed', $e->getMessage());
            report($e);
            return false;
        }
    }

    /**
     * @return string|bool true if login is successful, error message string if the login failed
     */
    public static function login($username, $password): string|self
    {
        $user = self::where('username', $username)->first();
        if ($user == null) {
            return 'Username not found';
        }
        if (
            Auth::attempt([
                'username' => $user->username,
                'password' => $password,
            ])
        ) {
            AppLog::info('User logged in');
            return $user;
        } else {
            return 'Incorrect password';
        }
    }

    public function pushNotification($title, $message, $route)
    {
        try {
            $this->notifications()->create([
                'sender_id' => Auth::user() ? Auth::id() : null,
                'title' => $title,
                'route' => $route,
            ]);

            event(
                new AppNotification(
                    [
                        'title' => $title,
                        'message' => $message,
                        'route' => $route,
                    ],
                    $this,
                ),
            );
        } catch (Exception $e) {
            report($e);
        }
    }

    public function markNotificationsAsSeenByRoute($route)
    {
        try {
            $now = Carbon::now();
            $this->notifications()
                ->whereNull('seen_at')
                ->where('route', $route)
                ->update([
                    'seen_at' => $now,
                ]);
        } catch (Exception $e) {
            report($e);
        }
    }

    public function getUnseenNotfCount()
    {
        return $this->notifications()->whereNull('seen_at')->selectRaw('count(*) as unseen')->first()->unseen;
    }

    //scope
    public function scopeOrderStatisticsBetween($query, $fromDate, $toDate)
    {
        return $query
            ->select('users.*')
            ->selectRaw('COUNT(o1.id) as total_orders')
            ->selectRaw('SUM(o1.total_amount) as total_amount')
            ->selectRaw('SUM((SELECT (SUM(order_products.quantity * products.weight)) 
            from order_products 
            join products on order_products.product_id = products.id 
            where o1.id = order_products.order_id and order_products.deleted_at is null )) as total_weight')
            ->selectRaw('COUNT(DISTINCT o1.zone_id) as total_zones')
            ->selectRaw('COUNT(DISTINCT Date(o1.delivery_date)) as total_days')
            ->selectRaw('GROUP_CONCAT(DISTINCT zones.name ORDER BY zones.name ASC) as zone_names')
            ->selectRaw('GROUP_CONCAT(DISTINCT o1.id) as ids')
            ->selectRaw('COUNT(DISTINCT o1.zone_id) as total_zones')
            ->selectRaw('SUM((SELECT SUM(amount) from customer_payments as c2 where o1.id = c2.order_id)) as total_paid')

            ->join('drivers', 'drivers.user_id', '=', 'users.id')
            ->join('orders as o1', 'drivers.id', '=', 'o1.driver_id')
            ->leftJoin('zones', 'o1.zone_id', '=', 'zones.id')

            ->whereIn('o1.status', Order::OK_STATUSES)
            ->whereBetween('o1.delivery_date', [$fromDate->format('Y-m-d 00:00:00'), $toDate->format('Y-m-d 23:59:59')])

            ->groupBy('users.id')
            ->orderByDesc('total_orders');
    }


    public function scopeAdmin($query)
    {
        return $query->where('type', self::TYPE_ADMIN);
    }

    public function scopeSales($query)
    {
        return $query->where('type', self::TYPE_SALES);
    }

    public function scopeInventory($query)
    {
        return $query->where('type', self::TYPE_INVENTORY);
    }

    public function scopeDriver($query)
    {
        return $query->where('type', self::TYPE_DRIVER);
    }

    public function scopeSearch($query, $search)
    {
        $splittedText = explode(' ', $search);
        foreach ($splittedText as $tmp) {
            $query->where(function ($q) use ($tmp) {
                $q->orwhere('username', 'LIKE', "%$tmp%");
                $q->orwhere('first_name', 'LIKE', "%$tmp%");
                $q->orwhere('last_name', 'LIKE', "%$tmp%");
                $q->orwhere('phone', 'LIKE', "%$tmp%");
                $q->orwhere('id_number', 'LIKE', "%$tmp%");
                $q->orwhere('driving_license_number', 'LIKE', "%$tmp%");
                $q->orwhere('email', 'LIKE', "%$tmp%");
            });
        }
        return $query;
    }

    //attributes
    public function getHomePageAttribute()
    {
        return $this->is_admin ? 'dashboard' : ($this->is_inventory ? 'orders/inventory' : '/');
    }

    public function getIsAdminAttribute()
    {
        return $this->type == self::TYPE_ADMIN;
    }

    public function getIsSalesAttribute()
    {
        return $this->type == self::TYPE_SALES;
    }

    public function getIsInventoryAttribute()
    {
        return $this->type == self::TYPE_INVENTORY;
    }

    public function getIsDriverAttribute()
    {
        return $this->type == self::TYPE_DRIVER;
    }

    public function getFullNameAttribute()
    {
        return ucwords($this->first_name . ' ' . $this->last_name);
    }

    // Placeholder for userExists function
    public static function userExists($username)
    {
        return self::where('username', $username)->first();
    }

    //relations
    public function logs(): HasMany
    {
        return $this->hasMany(AppLog::class);
    }

    public function latest_notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest(); //->limit(6)
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    //auth
    public function getAuthPassword()
    {
        return $this->password;
    }
}
