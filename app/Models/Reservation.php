<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['scheduled_date', 'user_id', 'product_id', 'quantity', 'amount'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
