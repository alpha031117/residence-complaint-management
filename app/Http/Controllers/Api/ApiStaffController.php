<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class ApiStaffController extends Controller
{
    // Fetech staff user data
    public function showStaff()
    {
        $staff_user = User::where('role', 'staff')->get();

        Log::info('Staff User: ' . $staff_user);

        return response()->json($staff_user);
    }

    // Add staff user
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'role' => 'required|string|max:255'
        ]);

        try {
            // Create the staff user
            $staff_user = User::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make($request->password),
            ]);

            $staff_user->save();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Staff user successfully registered.',
                'data' => $staff_user
            ], 201);
        } catch (\Exception $e) {
            // Return failure response
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create staff user. Please try again.'
            ], 500);
        }
    }

    // Show one staff user
    public function showDetails($id)
    {
        $staff_user = User::where('role', 'staff')->findOrFail($id);

        return response()->json($staff_user);
    }

    // Update staff user
    public function update(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'staff_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
            'role' => 'required|string|max:255'
        ]);

        // Log::info('Full Request Data:', $request->all());
        // Log::info('Searching for user with ID: ' . $request->staff_id);

        try {
            // Find the staff user
            $staff_user = User::where('role', 'staff')
                              ->where('id', $request->staff_id)
                              ->firstOrFail();
        
            // Update the staff user
            $staff_user->name = $request->name;
            $staff_user->phone_number = $request->phone_number;
            $staff_user->email = $request->email;
            $staff_user->role = $request->role;
        
            if ($request->filled('password')) {
                $staff_user->password = Hash::make($request->password);
            }
        
            $staff_user->save();
        
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Staff user successfully updated.',
                'data' => $staff_user
            ], 200);
        
        } catch (\Exception $e) {
            Log::error('Failed to update staff user: ' . $e->getMessage());
        
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update staff user. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
        
    }

    // Delete staff user
    public function destroy(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'staffId' => 'required|integer'
        ]);

        try {
            // Find the staff user
            $staff_user = User::where('role', 'staff')
                              ->where('id', $request->staffId)
                              ->firstOrFail();
        
            // Delete the staff user
            $staff_user->delete();
        
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Staff user successfully deleted.'
            ], 200);
        
        } catch (\Exception $e) {
            Log::error('Failed to delete staff user: ' . $e->getMessage());
        
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete staff user. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
