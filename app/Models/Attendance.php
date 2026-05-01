<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Attendance extends Model
{
    //
    protected $table = 'attendance';

    public $timestamps = false;

    protected $fillable = [
        'employee_id',
        'date',
        'status',
        'check_in',
        'check_out',
    ];

    protected $casts = [
        'att_date' => 'date',
    ];

    // ✅ Relationship
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
