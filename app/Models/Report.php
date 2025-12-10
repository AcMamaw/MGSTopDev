<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $primaryKey = 'report_id';
    protected $table = 'reports';
    
    protected $fillable = [
        'payment_id',
        'stock_id',
        'order_id',
        'orderdetails_id',
        'delivery_id',
        'deliverydetails_id',
        'joborder_id',
        'generated_by',
        'category',
        'report_type',
        'coverage',
        'date_created',
    ];

    // Payment relationship
    public function payment()
    {
        return $this->belongsTo(\App\Models\Payment::class, 'payment_id', 'payment_id');
    }

    // Stock/Inventory relationship
    public function stock()
    {
        return $this->belongsTo(\App\Models\Inventory::class, 'stock_id', 'stock_id');
    }

    // Order relationship
    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class, 'order_id', 'order_id');
    }

    // Order Details relationship
    public function orderDetail()
    {
        return $this->belongsTo(\App\Models\OrderDetail::class, 'orderdetails_id', 'orderdetails_id');
    }

    // Delivery relationship
    public function delivery()
    {
        return $this->belongsTo(\App\Models\Delivery::class, 'delivery_id', 'delivery_id');
    }

    // Delivery Details relationship
    public function deliveryDetail()
    {
        return $this->belongsTo(\App\Models\DeliveryDetail::class, 'deliverydetails_id', 'deliverydetails_id');
    }

    // Job Order relationship
    public function jobOrder()
    {
        return $this->belongsTo(\App\Models\Joborder::class, 'joborder_id', 'joborder_id');
    }

    // Employee who generated the report
    public function generatedBy()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'generated_by', 'employee_id');
    }
}