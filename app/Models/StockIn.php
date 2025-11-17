<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    use HasFactory;

    protected $table = 'stockins';  // table name
    protected $primaryKey = 'stockin_id';
    public $timestamps = true;      // has created_at and updated_at

    protected $fillable = [
        'employee_id',
        'product_id',
        'quantity_product',
        'unit_cost',
        'total',
    ];

    // Relationship: StockIn belongs to Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    // Relationship: StockIn received by Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
