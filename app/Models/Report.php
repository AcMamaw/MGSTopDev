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
        'inventory_id',
        'generated_by',
        'category',
        'report_type',
        'date_created',
    ];

    public function payment()
    {
        return $this->belongsTo(\App\Models\Payment::class, 'payment_id', 'payment_id');
    }

    public function inventory()
    {
        return $this->belongsTo(\App\Models\Inventory::class, 'inventory_id', 'stock_id');
    }

    public function generatedBy()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'generated_by', 'employee_id');
    }
}
