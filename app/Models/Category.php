<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name', 'slug', 'image'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) => url('/storage/categories/'.$value),
        );
    }
}
