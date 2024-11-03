<?php

use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use App\Models\Payments\BalanceTransaction;
use App\Models\Payments\CustomerPayment;
use App\Models\Users\User;
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
            $table->foreignIdFor(Customer::class)->constrained();
            $table->foreignIdFor(CustomerPayment::class)->nullable()->constrained('customer_payments'); // Inflow
            $table->foreignIdFor(Order::class)->nullable()->constrained(); // Outflow
            $table->decimal('amount', 10, 2); //double 
            $table->string('description')->nullable(); // Explanation of the transaction
            $table->foreignIdFor(User::class, 'created_by')->constrained('users'); // User who recorded the transaction
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
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
        Schema::dropIfExists('balance_transactions');
    }
};
