<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    // Specify the correct table name
    protected $table = 'inventory';

    // Specify the primary key
    protected $primaryKey = 'stock_id';

    // Disable timestamps if your table doesn't have created_at and updated_at
    public $timestamps = true; // change to false if your table doesn't have these columns

    // Fillable columns for mass assignment
    protected $fillable = [
        'deliverydetails_id',
        'product_id',
        'total_stock',
        'current_stock',
        'unit_cost',
        'date_received',
        'received_by',
        'stockin_id', // <--- new foreign key
        'last_updated',
        'remarks'
    ];

    /**
     * Relationship: Inventory belongs to a Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }


    /**
     * Relationship: Inventory received by an Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'received_by', 'employee_id');
    }

    /**
     * Relationship: Inventory has many StockOut records
     */
    public function stockouts()
    {
        return $this->hasMany(StockOut::class, 'stock_id', 'stock_id');
    }

    /**
     * Relationship: Inventory has many StockAdjustments
     */
    public function adjustments()
    {
        return $this->hasMany(StockAdjustment::class, 'stock_id', 'stock_id');
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class, 'stock_id', 'stock_id');
    }

        /**
     * Inventory belongs to a Stockin
     */
   public function stockin()
    {
        return $this->belongsTo(StockIn::class, 'stockin_id', 'stockin_id');
    }

}
