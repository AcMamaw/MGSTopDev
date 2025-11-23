<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'role_id',
        'fname',
        'lname',
        'gender',
        'bdate',
        'email',
        'contact_no',
        'status',
        'pictures',
    ];

    // Relationship to Role
   public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class, 'employee_id', 'employee_id');
    }

    public function approvedStockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class, 'approved_by', 'employee_id');
    }

    public function adjustedStockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class, 'adjusted_by', 'employee_id');
    }
    
    // Employee.php
    public function user()
    {
        return $this->hasOne(User::class, 'employee_id', 'employee_id');
    }


}
