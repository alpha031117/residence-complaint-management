<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Residence;
use App\Models\Complaints;
use App\Models\ComplaintAttachment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ApiComplaintController extends Controller
{
    public function index()
    {
        // Fetch all complaints with related data
        $complaints = Complaints::with(['issuedBy', 'residence', 'attachment'])->get();

        Log::info('Complaints: ' . $complaints);

        // Return the complaints as a JSON response
        return response()->json($complaints);
    }
    
    public function displayComplaint(Request $request)
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

    // Store the complaint (with file upload support)
    public function store(Request $request)
    {
        // Validate the form fields
        $validatedData = $request->validate([
            'complaint_title' => 'required|string|max:255', // Title of the complaint
            'complaint_details' => 'required|string|max:250', // Complaint description
            'residence_id' => 'required|string|exists:residence,id', // Ensure the residence exists
            'file_attachment' => 'nullable|mimes:jpg,jpeg,png,pdf,docx|max:2048', // Optional file upload validation
        ]);

        Log::info('residence_id: ' . $request->residence_id);

        $residence = Residence::find($request->residence_id);

        if (!$residence) {
            return response()->json(['error' => 'Residence not found'], 400); // or handle it as needed
        }

        // Create a new complaint instance
        $complaint = Complaints::create([
            'complaint_title' => $request->complaint_title,
            'complaint_details' => $request->complaint_details,
            'residence_id' => $residence->id,
            'issued_by' => $request->user_id, // Get authenticated user's ID
        ]);
        
        // Handle file attachment if exists
        if ($request->hasFile('file_attachment')) {
            $file = $request->file('file_attachment');
            $filePath = $file->store('complaints_files', 'public'); // Store the file

            // Create an attachment record
            $attachment = ComplaintAttachment::create([
                'file_path' => $filePath,
                'file_type' => $file->getMimeType(),
            ]);

            // Associate the file with the complaint
            $complaint->file_attachment = $attachment->id;
            $complaint->save();
        }

        // Return the created complaint in response
        return response()->json([
            'message' => 'Complaint successfully filled.',
            'complaint' => $complaint
        ], 201);
    }

    // Get complaint details by ID (for API)
    public function show($id)
    {
        $complaint = Complaints::with('residence', 'attachment', 'issuedBy', 'assignedTo')->findOrFail($id);

        // attachment, issuedBy are relationships defined in Complaints model
        $attachment_id = $complaint->file_attachment;

        // Get the attachment record
        $attachment = ComplaintAttachment::find($attachment_id);
        Log::info('Attachment: ' . $attachment);


        // Return the complaint and attachment in response
        return response()->json([
            'complaint' => $complaint,
            'attachment' => $attachment
        ]);
    }    

    public function edit(Request $request)
    {
        // Validate complaint ID
        $complaintId = $request->get('complaint_id');
        $complaint = Complaints::find($complaintId);

        if($request->has('user_id')){
            $userId = $request->get('user_id');
        }else{
            $userId = null;
        }
    
        if (!$complaint) {
            return response()->json(['status' => 'error', 'message' => 'Complaint not found.'], 404);
        }
    
        Log::info('data', $request->all());
    
        // Validate the request data
        if ($request->has('complaint_title')){
            $data = Validator::make($request->all(), [
                'complaint_title' => 'required|string|max:255', // Validate complaint title
                'complaint_details' => 'nullable|string', // Optional complaint details
                'file_attachment' => 'nullable|string',  // Accept base64 string or file upload
                'assign_to' => 'nullable|exists:users,id', // Assign the complaint to a staff member
                'complaint_status' => 'nullable|string', // Update complaint status
                'complaint_feedback' => 'nullable|string', // Optional feedback
            ]);
        }else{
            $data = Validator::make($request->all(), [
                'complaint_status' => 'required|string', // Update complaint status
            ]);
        }

    
        // If validation fails, return the validation errors
        if ($data->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $data->errors()
            ], 422);
        }

        // Handle file attachment: Regular file upload or base64 string
        if ($request->has('file_attachment')) {
            $fileAttachment = $request->input('file_attachment');
            if($fileAttachment !== null) {
                $filePath = null;
                            // If a file is uploaded (not base64)
                if ($request->hasFile('file_attachment')) {
                    $file = $request->file('file_attachment');
                    $filePath = $file->store('complaints_files', 'public'); // Store file in public storage
        
                    // Delete old file if it exists
                    if ($complaint->file_attachment) {
                        $oldFile = ComplaintAttachment::find($complaint->file_attachment);
                        if ($oldFile) {
                            Storage::disk('public')->delete($oldFile->file_path); // Delete old file from storage
                        }
                    }
        
                } elseif ($fileAttachment) {
                    // Handle base64 file (this part is for base64 encoded files)
                    $fileData = base64_decode($fileAttachment);
                    $fileName = uniqid() . '.pdf'; // You can adjust the file extension dynamically based on the content type
                    $filePath = 'complaints_files/' . $fileName;
        
                    // Save the base64 file to storage
                    Storage::disk('public')->put($filePath, $fileData);
        
                    // Delete old file if it exists
                    if ($complaint->file_attachment) {
                        $oldFile = ComplaintAttachment::find($complaint->file_attachment);
                        if ($oldFile) {
                            Storage::disk('public')->delete($oldFile->file_path); // Delete old file from storage
                        }
                    }
                }
        
                Log::info('File Path: ' . $filePath);
        
                // Create a new attachment record
                $attachment = ComplaintAttachment::create([
                    'file_path' => $filePath,
                    'file_type' => $request->hasFile('file_attachment') ? $file->getMimeType() : 'application/pdf', // Default mime type if base64
                ]);
        
                // Update the complaint with the new file attachment ID
                $complaint->file_attachment = $attachment->id;
            }
        }
    
        // Extract other data
        $complaint_title = $request->input('complaint_title');
        $complaint_details = $request->input('complaint_details');
        $complaint_status = $request->input('complaint_status') ?? $complaint->complaint_status;

        if($userId){
            $assign_to_id = $request->input('assign_to') ?? $complaint->assigned_to;
            $assign_to = User::find($assign_to_id);
            $feedback = $request->input('complaint_feedback') ?? $complaint->complaint_feedback;

            Log::info('user_id: ' . $userId);

            if($complaint_status == 'resolved') {
                $complaint->resolved_at = now();
                $complaint->resolution_time = round($complaint->created_at->diffInDays(now()));
    
                Log::info('Created At: ' . $complaint->created_at);
                Log::info('Resolved At: ' . now());
                Log::info('Resolution Time (days): ' . $complaint->created_at->diffInDays(now()));            
            }
    
            // Update complaint data
            $updateComplaint = [
                'complaint_title' => $complaint_title,
                'complaint_details' => $complaint_details,
                'assigned_to' => $assign_to->id,
                'complaint_status' => $complaint_status,
                'updated_by' => $userId,
                'complaint_feedback' => $feedback,
            ];
        }else{
            if ($complaint_title){
                $updateComplaint = [
                    'complaint_title' => $complaint_title,
                    'complaint_details' => $complaint_details,
                ];
            }else{
                $updateComplaint = [
                    'complaint_status' => $complaint_status,
                ];
            }
        }

        Log::info('Update Complaint: ' . json_encode($updateComplaint, JSON_PRETTY_PRINT));
    
        // Update the complaint in the database
        $complaint->update($updateComplaint);
        $complaint->save();

        // Log the query
        Log::info(DB::getQueryLog());
    
        return response()->json([
            'status' => 'success',
            'message' => 'Complaint updated successfully.'
        ]);
    }
    
    // Delete complaint (with attachment deletion)
    public function destroy(Request $request)
    {
        // Validate the form fields
        $validatedData = $request->validate([
            'complaint_id' => 'required|exists:complaints,id', // Complaint ID to delete
        ]);

        $id = $request->complaint_id;
        
        $complaint = Complaints::findOrFail($id);
        $file_attachment = ComplaintAttachment::find($complaint->file_attachment);
        
        // If the complaint has an attachment, delete it
        if ($file_attachment) {
            // Delete the file from storage
            Storage::disk('public')->delete($file_attachment->file_path);
            
            // Delete the attachment record
            $file_attachment->delete();
        }

        // Delete the complaint
        $complaint->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Complaint deleted successfully.'
        ]);
    }
}
