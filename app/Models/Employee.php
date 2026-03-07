<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $employee_id
 * @property int $role_id
 * @property string $fname
 * @property string $lname
 * @property string $gender
 * @property string $bdate
 * @property string $email
 * @property string|null $alt_email
 * @property string $contact_no
 * @property string|null $status
 * @property string|null $pictures
 * @property string|null $archive
 */
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
        'alt_email',
        'contact_no',
        'status',
        'pictures',
        'archive',
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
