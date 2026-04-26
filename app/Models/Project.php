<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    //
    use HasFactory;

    protected $table = 'projects';

    public $timestamps = false; // ✅ Add this line

    protected $fillable = [
        'name',
        'client',
        'type',
        'budget',
        'received',
        'start_date',
        'end_date',
        'status',
        'description',
        'progress',
    ];

    protected $casts = [
        'budget'     => 'decimal:2',
        'received'   => 'decimal:2',
        'progress'   => 'integer',
        'start_date' => 'date',
        'end_date'   => 'date',
    ];
}
