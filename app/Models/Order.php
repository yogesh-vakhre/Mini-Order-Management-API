<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'total_price',
        'status'
    ];

    /**
     * Define the relationship with the Product model
     *
     * @return \App\Models\Product  $product
     */
    public function product()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Define the relationship with the User model
     *
     * @return \App\Models\User  $user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
