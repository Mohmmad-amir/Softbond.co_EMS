<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeDocument extends Model
{
    //

    use HasFactory;

    protected $table = 'employee_documents';

    public $timestamps = false; // ✅ uploaded_at আছে created_at/updated_at নেই

    const CREATED_AT = 'uploaded_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'employee_id',
        'doc_name',
        'doc_type',
        'file_path',
        'uploaded_at',
    ];

    // ✅ Relationship
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

}
