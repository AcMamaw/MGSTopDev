<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $table = 'stockadjustment';
    protected $primaryKey = 'stockadjustment_id';
    protected $fillable = [
        'stock_id',
        'employee_id',
        'adjustment_type',
        'quantity_adjusted',
        'request_date',
        'reason',
        'status',
        'adjusted_by',
        'approved_by',
    ];

    // Relationship to inventory/stock
    public function stock()
    {
        return $this->belongsTo(Inventory::class, 'stock_id', 'stock_id');
    }

    // Relationship to employee who requested
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    // Employee who performed adjustment
    public function adjustedBy()
    {
        return $this->belongsTo(Employee::class, 'adjusted_by', 'employee_id');
    }

    // Employee who approved
    public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'approved_by', 'employee_id');
    }
}
