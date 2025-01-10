<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaints extends Model
{
    use HasFactory;

    // Define the table name (if not using the default 'complaints')
    protected $table = 'complaints';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'issued_by', 
        'residence_id',
        'complaint_title', 
        'complaint_details', 
        'complaint_feedback', 
        'complaint_status', 
        'file_attachment', 
        'resolved_at', 
        'resolution_time', 
        'updated_by',
        'assigned_to',
    ];

    // Relationship to the Residence model
    public function residence()
    {
        return $this->belongsTo(Residence::class, 'residence_id');
    }

    // Relationship: A complaint is issued by a user
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    // Relationship: A complaint is assigned to a user
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // In Complaints model
    public function attachment()
    {
        return $this->hasOne(ComplaintAttachment::class, 'id'); // Assuming 'complaint_id' is the foreign key in the attachments table
    }

    // Relationship: A complaint is updated by a user
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
