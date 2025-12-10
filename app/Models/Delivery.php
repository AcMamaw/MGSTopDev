<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends Model
{
    use HasFactory;

    protected $primaryKey = 'delivery_id';

    protected $fillable = [
        'supplier_id',
        'employee_id',
        'received_by',
        'delivery_date_request',
        'delivery_date_received',
        'status',
        'order_id',
    ];

    // Supplier who provided the items
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    // Employee who handled/encoded the delivery
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    // Employee who received the items
    public function receiver()
    {
        return $this->belongsTo(Employee::class, 'received_by', 'employee_id');
    }

    // Related order, if any
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    // Delivery line items / details
    public function deliverydetails(): HasMany
    {
        return $this->hasMany(DeliveryDetail::class, 'delivery_id', 'delivery_id');
    }

    
        // In Delivery model
    public function details(): HasMany
    {
        return $this->hasMany(DeliveryDetail::class, 'delivery_id', 'delivery_id');
    }
}
