<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function showRecentComplaint(Request $request): View
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
        return view('residence/dashboard', [
            'sort' => $sort,
            'order' => $order,
        ]);
    }
}