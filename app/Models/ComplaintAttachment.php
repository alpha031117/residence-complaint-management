<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintAttachment extends Model
{
    use HasFactory;

    // Define the table name if it doesn't follow the default 'complaint_attachments'
    protected $table = 'complaint_attachments';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'id', // ID of the complaint this attachment belongs to
        'file_path',    // Path to the file attachment
        'file_type',    // Type of file (e.g., image, pdf, etc.)
    ];

    // Inverse of the relationship to Complaints
    public function complaint()
    {
        return $this->hasOne(Complaints::class, 'id');
    }
}

