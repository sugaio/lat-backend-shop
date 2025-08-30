<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'invoice_id', 'product_id', 'qty', 'price'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
