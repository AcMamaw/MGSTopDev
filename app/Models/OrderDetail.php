<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\Joborder;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'orderdetails';
    protected $primaryKey = 'orderdetails_id';

    protected $fillable = [
        'order_id',
        'stock_id',
        'quantity',
        'price',
        'total',
        'color',
        'size'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function stock()
    {
        return $this->belongsTo(Inventory::class, 'stock_id', 'stock_id');
    }

    // NEW: Add this relationship for job orders
    public function jobOrders()
    {
        return $this->hasMany(Joborder::class, 'orderdetails_id', 'orderdetails_id');
    }

      public function product()
    {
        // adjust foreign/local keys if your columns differ
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'stock_id', 'stock_id')->with('product');
    }

}