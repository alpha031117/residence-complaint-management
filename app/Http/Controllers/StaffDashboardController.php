<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Models\Complaints;
use App\Models\CompaintAttachment;
use App\Models\ComplaintAttachment;
use Illuminate\Support\Facades\Log;

class StaffDashboardController extends Controller
{
    public function showAssignedCase(Request $request): View
    {
        // Get the sort and order query parameters (default to 'issued_at' and 'desc')
        $sort = $request->get('sort', 'created_at'); 
        $order = $request->get('order', 'desc'); 
    
        // Define allowed sort fields for security and prevent SQL injection
        $allowedSortFields = ['id', 'complaint_title', 'created_at'];
    
        // If the sort field is not valid, default to 'issued_at'
        if (!in_array($sort, $allowedSortFields)) {
            $sort = 'created_at';
        }
    
        // Fetch all assigned complaints
        $assignedComplaints = Complaints::with(['issuedBy', 'residence', 'attachment'])
            ->where('assigned_to', $request->user()->id)
            ->orderBy($sort, $order)
            ->get();  // No limit, fetch all complaints
    
        // Count the total number of assigned complaints
        $complaintsCount = Complaints::where('assigned_to', $request->user()->id)->count();
    
        // Count the number of in progress assigned complaints
        $inProgressComplaints = Complaints::where('assigned_to', $request->user()->id)
            ->where('complaint_status', 'In Progress')
            ->count();

        // Count the number of pending assigned complaints
        $pendingComplaints = Complaints::where('assigned_to', $request->user()->id)
            ->where('complaint_status', 'Pending')
            ->count();
    
        // Count the number of resolved assigned complaints
        $resolvedComplaints = Complaints::where('assigned_to', $request->user()->id)
            ->where('complaint_status', 'Resolved')
            ->count();
        
        // Count rate of resolved complaints/total complaints
        // Count previous period totals (e.g., last 7 days for demonstration)
        $previousComplaintsCount = Complaints::where('assigned_to', $request->user()->id)
        ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
        ->count();

        Log::info('Previous Complaints Count: ' . $previousComplaintsCount);

        $previousResolvedComplaints = Complaints::where('assigned_to', $request->user()->id)
            ->where('complaint_status', 'Resolved')
            ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();
        
        Log::info('Previous Resolved Complaints Count: ' . $previousResolvedComplaints);

        // Calculate percentage increase for total complaints
        $complaintsIncreaseRate = $previousComplaintsCount > 0 
            ? (($complaintsCount - $previousComplaintsCount) / $previousComplaintsCount) * 100
            : 0;

        // Calculate percentage increase for resolved complaints
        $resolvedIncreaseRate = $previousResolvedComplaints > 0 
            ? (($resolvedComplaints - $previousResolvedComplaints) / $previousResolvedComplaints) * 100
            : 0;
        
        // Pass the necessary data to the view
        return view('staff/dashboard', [
            'user' => $request->user(),
            'complaintsCount' => $complaintsCount,
            'assignedComplaints' => $assignedComplaints,
            'inProgressComplaints' => $inProgressComplaints,
            'resolvedComplaints' => $resolvedComplaints,
            'pendingComplaints' => $pendingComplaints,
            'complaintsIncreaseRate' => round($complaintsIncreaseRate, 2),
            'resolvedIncreaseRate' => round($resolvedIncreaseRate, 2),
            'sort' => $sort,
            'order' => $order,
        ]);
    } 

    // Show complaint details
    public function showComplaintDetails(Request $request, $id): View
    {
        // Fetch the complaint details
        $complaint = Complaints::with(['issuedBy', 'residence', 'attachment'])
            ->where('id', $id)
            ->firstOrFail();

        // Fetch attachments
        $attachment_id = $complaint->file_attachment;
        $attachment = ComplaintAttachment::where('id', $attachment_id)->first();
    
        // Pass the necessary data to the view
        return view('staff/complaint_details', [
            'user' => $request->user(),
            'complaint' => $complaint,
            'attachment' => $attachment,
        ]);
    }

}
