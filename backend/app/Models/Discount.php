<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'value',
    ];

    protected $casts = [
        'value' => 'decimal:2',
    ];

    /**
     * Get the products for this discount.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_discount');
    }
}