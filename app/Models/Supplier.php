<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $primaryKey = 'supplier_id'; // your PK is supplier_id
    protected $fillable = [
        'supplier_name',
        'contact_person',
        'contact_no',
        'email',
        'address'
    ];
}
