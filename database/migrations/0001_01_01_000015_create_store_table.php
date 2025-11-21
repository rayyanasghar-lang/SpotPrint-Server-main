<?php

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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->index()->default(0); // No foreign key constraint, just an index.
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['Active', 'inActive'])->default('inActive');
            $table->boolean('show_in_menu')->default(false);
            $table->json('metadata')->nullable(); // Holds additional data like images, banners, etc.
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->json('category_ids')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->integer('stock')->default(0); // Inventory management.
            $table->boolean('is_active')->default(true);
            $table->json('options')->nullable(); // Stores product options like size, material, color, etc.
            $table->json('metadata')->nullable(); // Holds additional data like images, features, etc.
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Cart', 'Order'])->default('Cart');
            $table->unsignedBigInteger('user_id')->nullable()->index(); // Nullable for guest checkout.
            $table->string('session_id')->nullable(); // Session ID to identify guest users' carts.
            $table->string('order_number')->unique(); // Unique identifier for the order.
            
            $table->json('address_info'); // billing_address, shipping_address
            $table->json('metadata')->nullable(); // Holds additional data like notes.

            $table->json('price_detail'); // will hold subtotal, discount, tax, shipping_cost, coupon_snapshot etc
            $table->decimal('total', 10, 2)->default(0.00);  // Final total amount after discount, tax, and shipping

            $table->enum('order_status', ['Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Completed', 'Cancelled', 'Expired'])->default('Pending');
            $table->enum('payment_status', ['Unpaid', 'Paid', 'Cancelled', 'Failed', 'Overdue', 'Refunded'])->default('Unpaid');
            $table->json('status_logs')->nullable(); // Logs to track changes in order_status, payment_status like shipped_at, .

            $table->string('shipping_carrier')->nullable();  // Shipping carrier (e.g., FedEx, DHL)
            $table->string('tracking_number')->nullable();  // Tracking number provided by the shipping company

            $table->string('payment_method')->nullable();  // Payment method (e.g., credit card, PayPal, Stripe)
            $table->string('transaction_id')->nullable(); // Transaction ID provided by the payment gateway.
            $table->json('payment_details')->nullable();  // Payment details
            $table->json('payment_logs')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index(); // Link to the order.
            $table->unsignedBigInteger('product_id')->index(); // Reference to the product.

            $table->json('product_snapshot')->nullable(); // Holds the snapshot of product at the time of order.
            $table->json('selected_options')->nullable();  // Selected options of the product
            $table->json('files')->nullable();  // user uploaded files required for Selected options in the product

            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0.00); // Price at the time of order.
            $table->decimal('total_price', 10, 2)->default(0.00); // Total price (quantity * unit price).
            $table->json('price_detail')->nullable();  
            $table->json('status_logs')->nullable(); // Track changes in order item status.
            $table->timestamps();
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
        Schema::dropIfExists('products');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');
    }
};
