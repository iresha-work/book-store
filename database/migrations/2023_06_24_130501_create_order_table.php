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
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('cart_id');
            $table->string('session_id');
            $table->unsignedBigInteger('copun_id')->nullable();
            $table->decimal('copun_val', $precision = 8, $scale = 2)->default('0.00');
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->decimal('discount_val', $precision = 8, $scale = 2)->default('0.00');
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('customer_id')->references('id')->on('customer');
            $table->foreign('cart_id')->references('id')->on('cart');
            $table->foreign('copun_id')->references('id')->on('cupon_type');
            $table->foreign('discount_id')->references('id')->on('discount_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
