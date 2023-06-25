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
        Schema::create('cart_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->unsignedBigInteger('copun_id')->nullable();
            $table->integer('qty');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('book_id')->references('id')->on('book');
            $table->foreign('cart_id')->references('id')->on('cart');
            $table->foreign('discount_id')->references('id')->on('discount_type');
            $table->foreign('copun_id')->references('id')->on('cupon_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_item');
    }
};
