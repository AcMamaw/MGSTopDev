<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDetail; 
use App\Models\Customer;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';

     protected $fillable = [
        'customer_id',
        'category_id', 
        'order_date',
        'ordered_by', 
        'total_amount', 
        'status'
    ];

    public function items()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id')
                    ->orderBy('payment_id', 'desc');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'ordered_by'); 
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

}
