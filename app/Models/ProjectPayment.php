<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Project;
class ProjectPayment extends Model
{
    //
    use HasFactory;
    protected $table = 'project_payments';

    protected $fillable = [
        'project_id',
        'amount',
        'note',
        'payment_date'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2'
    ];
    public $timestamps = false;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
