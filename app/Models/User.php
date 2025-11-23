<?php


namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'employee_id',
        'username',
        'password',
        'plain_password',
    ];

    protected $hidden = [
        'password',
    ];

    public $timestamps = true;

  

    // Relationship to Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
