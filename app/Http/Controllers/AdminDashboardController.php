<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Models\Complaints;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        // Pass the necessary data to the view
        return view('admin/dashboard', [
            'user' => $request->user(),
        ]);
    } 

    public function showDetails(Request $request, $id)
    {
        // Find the complaint by ID
        $complaint = Complaints::with('residence', 'attachment')->findOrFail($id);

        // Retrieve staff
        $staff = User::where('role', 'staff')->get();

        // Return view with complaint details
        return view('admin/complaint/complaint_detail', [
            'user' => $request->user(),
            'complaint' => $complaint,
            'staff' => $staff,
        ]);
    }

    // Show the complaint page
    public function show(Request $request): View
    {
        // Get the sort and order query parameters (default to 'issued_at' and 'desc')
        $sort = $request->get('sort', 'issued_at'); 
        $order = $request->get('order', 'desc'); 
    
        // Define allowed sort fields for security and prevent SQL injection
        $allowedSortFields = ['id', 'complaint_title', 'created_at'];
    
        // If the sort field is not valid, default to 'created_at'
        if (!in_array($sort, $allowedSortFields)) {
            $sort = 'created_at';
        }

        // Return the view with the complaints data
        return view('admin/complaint/complaint_list', [
            'user' => $request->user(),
            'sort' => $sort,
            'order' => $order,
        ]);
    }

    // Show staff management page
    public function staffManagement(Request $request): View
    {
        // Get the sort and order query parameters (default to 'issued_at' and 'desc')
        $sort = $request->get('sort', 'issued_at'); 
        $order = $request->get('order', 'desc'); 
    
        // Define allowed sort fields for security and prevent SQL injection
        $allowedSortFields = ['id', 'complaint_title', 'created_at'];
    
        // If the sort field is not valid, default to 'created_at'
        if (!in_array($sort, $allowedSortFields)) {
            $sort = 'created_at';
        }

        // Return the view with the complaints data
        return view('admin/staff', [
            'user' => $request->user(),
            'sort' => $sort,
            'order' => $order,
        ]);
    }
}
