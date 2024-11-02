<?php

use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use App\Models\Payments\BalanceTransaction;
use App\Models\Payments\CustomerPayment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('balance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class, 'customer_id')->constrained();
            $table->foreignIdFor(CustomerPayment::class, 'payment_id')->nullable()->constrained('customer_payments'); // Inflow
            $table->foreignIdFor(Order::class, 'order_id')->nullable()->constrained(); // Outflow
            $table->decimal('amount', 10, 2);
            $table->enum('type', BalanceTransaction::TYPES); // 'in' for crediting balance, 'out' for debiting balance
            $table->string('description')->nullable(); // Explanation of the transaction
            $table->foreignId('created_by')->constrained('users'); // User who recorded the transaction
            $table->timestamps();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('balance', 10, 2)->default(0)->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_transactions');
    }
};
