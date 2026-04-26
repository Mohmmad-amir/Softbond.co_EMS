<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class AdminProfile extends Model
{
    use HasFactory;

// Laravel will look for the table name 'admin_profiles' by default,
// so you need to specify your table name here.
    protected $table = 'admin_profile';

// If your table doesn't have 'created_at' and 'updated_at' columns, set this to false
public $timestamps = false;

// Columns that you can save in the database (Mass Assignment)
    protected $fillable = [
        'user_id',
        'company_name',
        'phone',
        'address',
    ];

    /**
     * Relationship to User model (BelongsTo)
     * A profile belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
