<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory , SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order';

    /**
     * Get the customer relate order.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id' , 'id');
    }

    /**
     * Get the items form the order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class , 'order_id' , 'id');
    }
}
