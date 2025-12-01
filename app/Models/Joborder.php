<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Joborder extends Model
{
    use HasFactory;

    protected $table = 'joborders';
    protected $primaryKey = 'joborder_id';

    protected $fillable = [
        'orderdetails_id',
        'joborder_created',
        'joborder_end',
        'estimated_time',
        'status',
        'made_by',
    ];

    // Relationship to OrderDetail
    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class, 'orderdetails_id', 'orderdetails_id');
    }

    // Relationship to Customer through OrderDetail
    public function customer()
    {
        return $this->hasOneThrough(
            \App\Models\Customer::class,
            \App\Models\OrderDetail::class,
            'orderdetails_id', // FK on OrderDetail
            'customer_id',     // PK on Customer
            'orderdetails_id', // Local key on Joborder
            'customer_id'      // Local key on OrderDetail
        );
    }

    // Relationship to Employee who made the job order
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'made_by', 'employee_id');
    }
}
