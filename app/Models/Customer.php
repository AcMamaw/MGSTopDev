<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'customer_id';

    public $timestamps = false; // IMPORTANT FIX

    protected $fillable = [
        'fname',
        'mname',
        'lname',
        'contact_no',
        'address',
        'archive',
    ];
}
