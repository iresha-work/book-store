<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    use HasFactory , SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cart_item';

    /**
     * Get the book relate.
     */
    public function book()
    {
        return $this->belongsTo(Book::class,'book_id' , 'id');
    }

    /**
     * Get the discount relate.
     */
    public function discount()
    {
        return $this->belongsTo(Discount::class,'discount_id' , 'id');
    }
}
