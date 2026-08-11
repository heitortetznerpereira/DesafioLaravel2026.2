<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        "name",
        "price",
        "amount",
        "description",
        "category_id",
        "creator_id",
        "image_path",
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, "creator_id");
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
