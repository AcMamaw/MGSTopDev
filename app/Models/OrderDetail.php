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
        'vat',
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
}