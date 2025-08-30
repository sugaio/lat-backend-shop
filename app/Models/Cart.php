<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'product_id', 'customer_id', 'price', 'qty', 'weight'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
