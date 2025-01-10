<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaints;
use App\Models\ComplaintAttachment;
use App\Models\Residence;
use Illuminate\Support\Facades\Storage;

class FileComplaintController extends Controller
{
    public function showFileComplaintForm(Request $request)
    {
        // Return the residence
        $residences = Residence::all();

        return view('residence/complaint/fileComplaint', compact('residences'));
    }

    public function show($id)
    {
        // Find the complaint by ID
        $complaint = Complaints::with('residence', 'attachment')->findOrFail($id);
        
        // Return view with complaint details
        return view('residence/complaint/complaintDetails', compact('complaint'));
    }

}
