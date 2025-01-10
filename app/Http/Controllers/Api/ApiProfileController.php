<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Residence;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ApiProfileController extends Controller
{
    // Fetech staff user data
    public function showStaff(Request $request)
    {
        $staff_user = User::where('role', 'staff')->get();

        Log::info('Staff user data fetched', $staff_user);

        return response()->json([
            'status' => 'success',
            'data' => $staff_user
        ]);
    }

    // Fetch user profile data
    public function show(Request $request)
    {
        $userId = $request->get('user_id');

        // Validate the user_id and get user object
        $user = User::find($userId);

        Log::info('User profile data fetched', ['user_id' => $userId]);

        // Use residence id to fetch residence data
        $residence_id = $user->residence_id;
        $residence = Residence::find($residence_id);

        // Add residence data to user object
        $user->residence = $residence;

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    // Update user profile data
    public function update(Request $request)
    {
        // Validate the user_id and get the user
        $userId = $request->get('user_id');
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
        }
    
        // Validate the request data
        $data = Validator::make($request->all(), [
            'user_first_name' => 'required|string|max:255',
            'user_last_name' => 'required|string|max:255',
            'user_email' => 'nullable|email|unique:users,email,' . $userId,
            'user_phone_num' => 'required|string|max:255',
        ]);
    
        // If validation fails, return the validation errors
        if ($data->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $data->errors()
            ], 422);
        }
    
        // Extract data
        $firstName = $request->input('user_first_name');
        $lastName = $request->input('user_last_name');
        $email = $request->input('user_email');
        $phoneNumber = $request->input('user_phone_num');
    
        // Combine first name and last name
        $name = $firstName . ' ' . $lastName;
    
        // Update user profile data
        $updateData = [
            'name' => $name,
            'email' => $email,
            'phone_number' => $phoneNumber,
        ];
    
        // Update user's profile
        $user->update($updateData);
    
        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully.'
        ]);
    }

    public function updatePhoto(Request $request)
    {
        // Validate the request
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB file size
        ]);

        $userId = $request->get('user_id_photo');
        $user = User::find($userId);

        Log::info('User profile photo update', ['user_id' => $userId]);

        // Handle file upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filePath = $file->store('profile_photos', 'public'); // Save in 'storage/app/public/profile_photos'

            // Delete old photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Update user profile photo path
            $user->profile_photo_path = $filePath;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Profile photo updated successfully.',
                'photo_url' => asset('storage/' . $filePath),
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No photo uploaded.',
        ], 400);
    }
    
    // Delete user profile data
    public function destroy(Request $request)
    {   
        // Validate password for confirmation
        $data = $request->validate([
            'user_id' => 'required|integer',
            'password' => 'required|string'
        ]);
        
        // Get the user
        $user = User::find($data['user_id']);
        
        if (!Hash::check($data['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incorrect password.'
            ], 400);
        }

        // Delete profile photo if exists
        if ($user->profile_photo) {
            Storage::delete('public/profile_photos/' . $user->profile_photo);
        }

        // Delete the user
        $user->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Account deleted successfully.'
        ]);
    }
}
