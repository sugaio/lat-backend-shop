<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'image', 'title', 'slug', 'category_id', 'description', 'weight', 'price', 'stock', 'discount'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
}
