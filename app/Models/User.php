<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{ protected $primaryKey = 'user_id';

    protected $fillable = [
        'employee_id',
        'username',
        'password',
    ];
    public $timestamps = true;

    // Use "username" instead of email for auth
    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

        // Relationship to Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

}
