<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'deliverydetails_id';

    protected $fillable = [
        'delivery_id',
        'product_id',
        'quantity_product',
        'unit',
        'unit_cost',
        'total'
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'delivery_id', 'delivery_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
