<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Residence;
use App\Models\User;
use App\Http\Controllers\Controller;

class ApiResidenceController extends Controller
{
    // Store a new residence
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'residence_name' => 'required|string|max:255',
            'block_no' => 'required|string|max:255',
            'unit_no' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            // Create the residence
            $residence = Residence::create($validatedData);

            // Update user with the created residence
            $user = User::findOrFail($validatedData['user_id']);
            $user->residence_id = $residence->id;
            $user->save();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Residence successfully registered. Please refresh the page.',
                'data' => $residence
            ], 201);
        } catch (\Exception $e) {
            // Return failure response
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create residence. Please try again.'
            ], 500);
        }
    }

    // Update an existing residence
    public function edit(Request $request, $id)
    {
        // Find the residence by ID
        $residence = Residence::findOrFail($id);

        // Validate the request data
        $validatedData = $request->validate([
            'block_no' => 'required|string|max:255',
            'unit_no' => 'required|string|max:255',
        ]);

        // Update the residence data
        $residence->update($validatedData);

        // Return a response with the updated residence data
        return response()->json($residence, 200);
    }

    // Delete a residence
    public function destroy($id)
    {
        // Find the residence by ID
        $residence = Residence::findOrFail($id);

        // Delete the residence
        $residence->delete();

        // Return a response indicating the residence was deleted
        return response()->json(null, 204);
    }
}
