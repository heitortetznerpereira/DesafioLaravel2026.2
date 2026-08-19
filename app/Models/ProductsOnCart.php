<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Psy\CodeCleaner\FunctionReturnInWriteContextPass;

class ProductsOnCart extends Model
{
    //
    protected $table = "products_on_cart";

    protected $fillable = [
        "user_id",
        "product_id",
        "amount"
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
