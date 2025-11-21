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
        Schema::create('systems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(0)->index();
            $table->string('system_name', 150);
            $table->string('system_url', 150)->unique()->index();
            $table->enum('type', ['System', 'Company', 'Office', 'Outlet'])->default('System');
            $table->enum('status', ['Active', 'inActive'])->default('Active');
            $table->json('owner')->nullable();
            $table->json('configurations')->nullable();
            $table->json('global_product_options')->nullable();
            $table->json('settings')->nullable();
            $table->json('logs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('systems');
    }
};
