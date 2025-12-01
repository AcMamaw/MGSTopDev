<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';
    protected $primaryKey = 'stock_id';
    public $timestamps = true;

    protected $fillable = [
        'deliverydetails_id',
        'product_id',
        'total_stock',
        'current_stock',
        'unit_cost',
        'date_received',
        'received_by',
        'size',
        'product_type',
        'stockin_id',
        'last_updated',
        'remarks'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'received_by', 'employee_id');
    }

    public function stockouts()
    {
        return $this->hasMany(StockOut::class, 'stock_id', 'stock_id');
    }

    public function adjustments()
    {
        return $this->hasMany(StockAdjustment::class, 'stock_id', 'stock_id');
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class, 'stock_id', 'stock_id');
    }

    public function stockin()
    {
        return $this->belongsTo(StockIn::class, 'stockin_id', 'stockin_id');
    }

    // ✅ Add this relationship
    public function deliveryDetail()
    {
        return $this->belongsTo(DeliveryDetail::class, 'deliverydetails_id', 'deliverydetails_id');
    }
}
