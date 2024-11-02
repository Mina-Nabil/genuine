<?php

use App\Models\Customers\Customer;
use App\Models\Orders\Order;
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
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class, 'customer_id')->constrained();
            $table->foreignIdFor(Order::class, 'order_id')->nullable()->constrained();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', CustomerPayment::PAYMENT_METHODS);
            $table->date('payment_date');
            $table->string('reference')->nullable();
            $table->string('note')->nullable();
            $table->enum('status', CustomerPayment::STATUSES)->default(CustomerPayment::STATUS_NEW);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_payments');
    }
};
