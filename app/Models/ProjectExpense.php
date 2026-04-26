<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectExpense extends Model
{
    //
    use HasFactory;

//     protected $table = 'expenses';

    protected $fillable = [
        'project_id',
        'description',
        'category',
        'amount',
        'expense_date',
        'note'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public $timestamps = false;
}
