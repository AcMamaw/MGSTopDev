<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Employee;
use App\Models\Category;


class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    public $timestamps = true;

    protected $fillable = [
        'customer_id',
        'category_id',
        'order_date',
        'ordered_by',
        'product_type',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'order_date'   => 'date',
        'total_amount' => 'decimal:2',
    ];

 
    public function items()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }

 
    public function orderdetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }


    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id')
                    ->orderBy('payment_id', 'desc');
    }

  
    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id', 'order_id');
    }


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'ordered_by', 'employee_id');
    }

        public function delivery()
    {
        return $this->hasOne(Delivery::class, 'order_id', 'order_id');
    }

        public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

        public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_order', 'order_id', 'employee_id');
    }

}