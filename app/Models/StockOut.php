<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    use HasFactory;

    protected $casts = [
    'date_out' => 'datetime', // or 'date' if you only need Y-m-d
];
    protected $table = 'stockout';
    protected $primaryKey = 'stockout_id';

    protected $fillable = [
        'stock_id',
        'employee_id',
        'quantity_out',
        'date_out',
        'reason',
        'size',
        'status',
        'approved_by'
    ];

    // Relationships
    public function stock()
    {
        return $this->belongsTo(\App\Models\Inventory::class, 'stock_id', 'stock_id');
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'employee_id');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'approved_by', 'employee_id');
    }
}
