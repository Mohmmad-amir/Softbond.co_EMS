<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryRequest extends Model
{
    protected $table = 'salary_requests';

    public $timestamps = false;

    protected $fillable = [
        'employee_id',
        'amount',
        'month',
        'status',
        'note',
        'requested_at',
        'actioned_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'actioned_at'  => 'datetime',
    ];

    //  Relationship
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
