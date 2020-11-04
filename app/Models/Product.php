<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'stock'];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }
}
