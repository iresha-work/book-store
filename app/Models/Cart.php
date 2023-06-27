<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory , SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cart';

    /**
     * Get the items form the cart.
     */
    public function items()
    {
        return $this->hasMany(CartItem::class , 'cart_id' , 'id');
    }

    /**
     * Get the cart copun_id relate.
     */
    public function copun()
    {
        return $this->belongsTo(Coupon::class,'copun_id' , 'id');
    }
}
