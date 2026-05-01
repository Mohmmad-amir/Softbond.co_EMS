<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    //

    protected $table = 'expenses';
    public $timestamps = false;

    protected $fillable = [
        'description',
        'category',
        'amount',
        'date',
        'project_id',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date'   => 'date',
    ];

    //  Relationship
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
