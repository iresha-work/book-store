<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Expression;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('book', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->text('description')->unique();
            $table->json('images')->default(new Expression('(JSON_ARRAY())'));
            $table->decimal('price', $precision = 8, $scale = 2);
            $table->string('sequence');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('book_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book');
    }
};
