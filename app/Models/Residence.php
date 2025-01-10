<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Residence extends Model
{
    use HasFactory;

    // Define the table name if it's not the default 'residences'
    protected $table = 'residence'; // In case your table name is 'residence', adjust if needed

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'residence_name',
        'block_no',
        'unit_no',
    ];

    // Alternatively, you can use $guarded to prevent mass-assignment vulnerabilities
    // protected $guarded = [];

    // Define relationships (if needed)
    // Example: if a Residence has many Users
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relationship back to the complaints
    public function complaints()
    {
        return $this->hasMany(Complaints::class, 'id');
    }
}

