<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Employee extends Model
{
    //
    use HasFactory;

    protected $table = 'employees';

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'department',
        'designation',
        'salary',
        'join_date',
        'nid',
        'address',
        'status',
        'payment_method',
        'bank_name',
        'bank_account',
        'mobile_banking_number',
        'photo',
    ];

    protected $casts = [
        'salary'    => 'decimal:2',
        'join_date' => 'date',
    ];
}
