<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    use HasFactory;

    protected $table = 'stockins';
    protected $primaryKey = 'stockin_id';

    protected $fillable = [
        'employee_id',
        'product_id',
        'product_type',
        'size',
        'quantity_product',
        'unit_cost',
        'total',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'stockin_id', 'stockin_id');
    }
}