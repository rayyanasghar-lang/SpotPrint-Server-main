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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 25)->unique();
            $table->enum('category', ['Gift Card', 'Coupon'])->default('Coupon');
            $table->enum('type', ['Fixed', 'Percentage']);
            $table->decimal('value', 10, 2);
            $table->text('description')->nullable();
            $table->json('details')->nullable();
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('per_user_limit')->default(1);
            $table->integer('used_count')->default(0);
            $table->enum('status', ['Yes', 'No'])->default('Yes');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('coupon_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->index();
            $table->foreignId('user_id')->index();
            $table->foreignId('order_id');
            $table->timestamp('redeemed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('coupon_redemptions');
    }
};
