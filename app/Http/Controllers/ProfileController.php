<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Residence;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function showProfile(Request $request): View
    {
        // Return the residence
        $residences = Residence::all();

        return view('residence/profile/myprofile', [
            'user' => $request->user(),
            'residences' => $residences
        ]);
        
    }
    
}
