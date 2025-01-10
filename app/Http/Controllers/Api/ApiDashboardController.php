<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Complaints;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ApiDashboardController extends Controller
{
    public function show(Request $request)
    {
        // Get the user_id from the request
        $userId = $request->get('id');

        Log::info('User ID: ' . $userId);

        // Validate the user_id
        if (!$userId) {
            return response()->json(['error' => 'User ID is required'], 400);
        }

        // Get the sort and order query parameters (default to 'issued_at' and 'desc')
        $sort = $request->get('sort', 'issued_at'); 
        $order = $request->get('order', 'desc'); 
    
        // Define allowed sort fields for security and prevent SQL injection
        $allowedSortFields = ['id', 'complaint_title', 'created_at'];
    
        // If the sort field is not valid, default to 'created_at'
        if (!in_array($sort, $allowedSortFields)) {
            $sort = 'created_at';
        }
    
        // Fetch the complaints issued by the user, sorted based on the selected field and order
        $recentComplaints = Complaints::where('issued_by', $userId)
            ->orderBy($sort, $order) // Apply sorting
            ->get(); // No limit, fetch all complaints for the user
    
        // Count the total number of complaints
        $complaintsCount = Complaints::where('issued_by', $userId)->count();
    
        // Count the number of pending complaints
        $inProgressComplaints = Complaints::where('complaint_status', 'In Progress')
            ->where('issued_by', $userId)
            ->count();
    
        // Count the number of resolved complaints
        $resolvedComplaints = Complaints::where('complaint_status', 'Resolved')
            ->where('issued_by', $userId)
            ->count();
    
        // Return the data as a JSON response
        return response()->json([
            'complaintsCount' => $complaintsCount,
            'recentComplaints' => $recentComplaints,
            'inProgressComplaints' => $inProgressComplaints,
            'resolvedComplaints' => $resolvedComplaints,
            'sort' => $sort,
            'order' => $order,
        ]);
    }
}
