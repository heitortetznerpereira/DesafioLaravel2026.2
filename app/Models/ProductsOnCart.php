<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsOnCart extends Model
{
    //
    protected $fillable = [
        "user_id",
        "product_id",
        "amount"
    ];
}
