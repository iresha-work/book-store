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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->unsignedBigInteger('copun_id')->nullable();
            $table->integer('qty');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('order');
            $table->foreign('book_id')->references('id')->on('book');
            $table->foreign('discount_id')->references('id')->on('discount_type');
            $table->foreign('copun_id')->references('id')->on('cupon_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
