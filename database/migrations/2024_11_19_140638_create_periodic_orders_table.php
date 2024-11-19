<?php

use App\Models\Customers\Customer;
use App\Models\Customers\Zone;
use App\Models\Orders\Order;
use App\Models\Orders\PeriodicOrder;
use App\Models\Products\Combo;
use App\Models\Products\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('periodic_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->string('customer_name');
            $table->string('shipping_address');
            $table->string('location_url');
            $table->string('customer_phone');
            $table->foreignIdFor(Zone::class)->constrained();
            $table->string('order_name')->nullable();
            $table->enum('periodic_option', PeriodicOrder::PERIODIC_OPTIONS); // E.g., 'weekly', 'bi-weekly', 'monthly'
            $table->unsignedTinyInteger('order_day')->nullable(); // E.g., 'Monday' or '15' for monthly
            $table
                ->foreignId('last_order_id')
                ->nullable()
                ->constrained('orders') // Correct table name
                ->onDelete('set null');
            $table->text('note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('periodic_order_products', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignIdFor(PeriodicOrder::class)
                ->constrained()
                ->onDelete('cascade');
            $table->foreignIdFor(Product::class)->constrained();
            $table
                ->foreignIdFor(Combo::class)
                ->nullable()
                ->constrained();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodic_orders');
    }
};
