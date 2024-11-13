<?php

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Orders\Order;
use App\Models\Products\Combo;
use App\Models\Products\Product;
use App\Models\Users\Driver;
use App\Models\Users\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->string('customer_name');
            $table->string('shipping_address');
            $table->string('location_url');
            $table->string('customer_phone');
            $table->foreignIdFor(Zone::class)->constrained();
            $table->foreignIdFor(Driver::class)->nullable()->constrained();
            // $table->enum('payment_method', Order::PAYMENT_METHODS); 
            $table->enum('periodic_option', Order::PERIODIC_OPTIONS)->nullable();
            $table->enum('status', Order::STATUSES)->nullable();
            // $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('delivery_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->date('delivery_date')->nullable();
            $table->boolean('is_paid');
            $table->string('note')->nullable();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        // Order items table to store order items
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Product::class);
            $table->foreignIdFor(Combo::class)->nullable()->constrained();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('order_removed_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Product::class)->constrained();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_removed_products');
        Schema::dropIfExists('order_products');
        Schema::dropIfExists('orders');
    }
};
