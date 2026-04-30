<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Task extends Model
{
    //
    protected $table = 'tasks';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'project_id',
        'assigned_to',
        'due_date',
        'priority',
        'status',
        'description',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // ✅ Relationships
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }
}
