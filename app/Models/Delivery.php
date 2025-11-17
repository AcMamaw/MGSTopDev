<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'status'
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function details()
    {
        return $this->hasMany(DeliveryDetail::class, 'delivery_id', 'delivery_id');
    }

        public function receiver()
    {
        return $this->belongsTo(Employee::class, 'received_by', 'employee_id');
    }

}
