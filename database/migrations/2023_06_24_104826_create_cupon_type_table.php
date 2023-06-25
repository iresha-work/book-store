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
        Schema::create('cupon_type', function (Blueprint $table) {
            $table->id();
            $table->string('cupon_code');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('cupon_type', ['percentage', 'amount']);
            $table->decimal('cupon_value', $precision = 8, $scale = 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupon_type');
    }
};
