<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice', 'customer_id', 'courier', 'service', 'cost_courier', 'weight', 'name', 'phone', 'address', 'status', 'grand_total'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
