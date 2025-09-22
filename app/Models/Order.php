<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'invoice_id', 'product_id', 'qty', 'price', 'invoice', 'product_name', 'image'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
