@extends('layouts.admin.app')

@section('title', 'Complaint Case Details')

@section('header')
    @include('layouts.admin.navigation', ['user' => $user])
    @section('page-styles')
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #eaeaea;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: #333;
        }

        .header div {
            font-size: 14px;
            color: #555;
        }

        .grid {
            display: grid;
            grid-template-columns: 2fr 0fr;
            gap: 20px;
        }

        .grid .section {
            margin-bottom: 15px;
        }

        .grid .section h3 {
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 2px;
            color: #333;
        }

        .grid .section p {
            font-size: 14px;
            margin: 0;
            line-height: 1.6;
            color: #555;
        }

        .badge {
            font-size: 12px;
            color: #fff;
            background-color: #28a745;
            padding: 5px 10px;
            border-radius: 15px;
        }

        .attachments {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .attachment {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            background-color: #f8f9fa;
            padding: 5px 10px;
            border-radius: 5px;
            color: #555;
            border: 1px solid #ddd;
            margin-top: 10px;
        }   

        .remarks {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #007bff;
            font-size: 14px;
            margin-top: 10px; /* Push remarks section lower */
        }

        .remarks p {
            margin: 0;
            line-height: 1.6;
            font-weight: 500;
            color: #333;
        }

        .complaint-status {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .complaint-status span {
            font-size: 14px;
        }

        .status-label {
            font-weight: bold;
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Responsive layout for smaller screens */
        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .sidebar {
                border-left: none;
                padding-left: 0;
            }

            .two-column {
                grid-template-columns: 1fr;
            }
        }

        textarea {
            height: 150px; /* Set the desired height */
            width: 100%;   /* Make it take full width */
        }

        /* Custom class to adjust the height of file input */
        .custom-file-input {
            border: 1px solid lightgrey;
            padding: 10px;
        }

    </style>
    @endsection
@endsection

@section('content')
<div class="d-flex">
    <!-- Main Content -->
    <div class="flex-grow-1" style="margin-left: 250px; padding: 30px;">
        <div id="status-message" class="alert" style="display: none;"></div>
            <div class="header">
                <h1 id="complaint-title">Case Title: Loading...</h1>
            </div>

            <div class="grid">
                <div>
                    <div class="section three-column" style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3>Submitted By:</h3>
                            <p id="submitted-by">Loading...</p>
                        </div>

                        <div>
                            <h3>Issued On:</h3>
                            <p id="issued-on">Loading...</p>
                        </div>
                        <div>
                            <h3>Assigned To:</h3>
                            <p id="assigned-to">Loading...</p>
                        </div>
                    </div>

                    <div class="section">
                        <h3>Complaint Details:</h3>
                        <p id="complaint-details">Loading...</p>
                    </div>

                    <div class="section">
                        <h3>Attachments:</h3>
                        <div class="attachments" id="attachments">Loading...</div>
                    </div>
                    <hr/>
                    <div class="section complaint-status mt-3" style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;">
                        <h3>Complaint Status</h3>
                        @php
                            $badgeClass = match(strtolower($complaint->complaint_status)) {
                                'pending' => 'badge bg-warning text-dark',
                                'resolved' => 'badge bg-success',
                                'rejected' => 'badge bg-danger',
                                default => 'badge bg-secondary',
                            };
                        @endphp
                        <span class="{{ $badgeClass }}" id="complaint-status">{{ $complaint->complaint_status }}</span>
                    </div>

                    <div class="section two-column" style="display: flex; justify-content: space-between; align-items: center; margin-bottom:0px;">
                        <div>
                            <h3>Resolution Time:</h3>
                            <p id="resolution-time">Loading...</p>
                        </div>

                        <div>
                            <h3>Status Updated:</h3>
                            <p id="status-updated">Loading...</p>
                        </div>
                    </div>
                    <div class="section remarks mt-5">
                        <h3>Remarks:</h3>
                        <p><strong id="remarks">Loading...</strong></p>
                    </div>
                    <!-- Buttons container -->
                    <div class="mt-3 d-flex justify-content-end align-items-center gap-2">
                        <a href="#" class="btn btn-primary btn-sm" id="edit-btn">Edit Complaint</a>
                        <button type="button" class="btn btn-danger btn-sm" id="delete-btn">Delete Complaint</button>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="delete-form" >
                @csrf
                <div class="modal-body">
                    Are you sure you want to delete this complaint? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Editing Complaint -->
<div class="modal fade" id="editComplaintModal" tabindex="-1" aria-labelledby="editComplaintModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editComplaintModalLabel">Edit Complaint</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editComplaintForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!-- Complaint Title -->
                    <div class="mb-3">
                        <label for="complaintTitle" class="form-label">Complaint Title</label>
                        <input type="text" class="form-control" id="complaintTitle" name="complaint_title" readonly>
                    </div>
                    <!-- Complaint Details -->
                    <div class="mb-3">
                        <label for="complaintDetails" class="form-label">Complaint Details</label>
                        <textarea class="form-control" id="complaintDetails" name="complaint_details" rows="4" style="height: 150px; resize: none;" readonly></textarea>
                    </div>
                    {{-- Feedback --}}
                    <div class="mb-3">
                        <label for="complaintFeedback" class="form-label">Feedback</label>
                        <textarea class="form-control" id="complaintFeedback" name="complaint_feedback" rows="4" style="height: 150px; resize: none;"></textarea>
                    </div>
                    <!-- Status-->
                    <div class="mb-3">
                        <label for="complaintStatus" class="form-label">Complaint Status</label>
                        <select class="form-select" id="complaintStatus" name="complaint_status">
                            <option value="pending">Pending</option>
                            <option value="resolved">Resolved</option>
                            <option value="in progress">In Progress</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    {{-- Assign to --}}
                    <div class="mb-3">
                        <label for="assignTo" class="form-label">Assign To</label>
                        <select class="form-select" id="assignTo" name="assign_to">
                            {{-- loop using admin --}}
                            @foreach($staff as $staff_user)
                                <option value="{{ $staff_user->id }}">{{ $staff_user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Submit Button -->
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
<script>
    // Fetch complaint details on page load
    document.addEventListener('DOMContentLoaded', fetchComplaintDetails);

    // Fetch complaint details using API
    async function fetchComplaintDetails() {
        const complaintId = {{ $complaint->id }};

        try {
            const response = await fetch(`/api/complaints/${complaintId}`);
            const complaint_response = await response.json();
            const complaint = complaint_response.complaint;
            const attachment = complaint_response.attachment;

            // console.log(complaint);

            if (response.ok) {
                document.getElementById('complaint-title').innerText = `Case Title: ${complaint.complaint_title}`;
                document.getElementById('submitted-by').innerText = complaint.issued_by ? complaint.issued_by.name : 'Unknown';
                document.getElementById('issued-on').innerText = new Date(complaint.created_at).toLocaleDateString();
                document.getElementById('complaint-details').innerText = complaint.complaint_details;
                document.getElementById('complaint-status').innerText = complaint.complaint_status;
                document.getElementById('resolution-time').innerText = 
                complaint.resolution_time !== null && complaint.resolution_time !== undefined 
                    ? `${complaint.resolution_time} days`
                    : 'Not Provided';
                document.getElementById('status-updated').innerText = new Date(complaint.updated_at).toLocaleDateString();
                if(complaint.assigned_to == null){
                    document.getElementById('assigned-to').innerText = 'Not Assigned';
                }else{
                    document.getElementById('assigned-to').innerText = complaint.assigned_to.name;
                }
                document.getElementById('complaintFeedback').innerText = complaint.complaint_feedback || 'Not Provided';
                console.log(complaint.assigned_to);  // Check its structure

                document.getElementById('remarks').innerText = complaint.complaint_feedback || 'No remarks provided';

                // Pre-fill form fields with the fetched complaint data
                document.getElementById('complaintTitle').value = complaint.complaint_title;
                document.getElementById('complaintDetails').value = complaint.complaint_details;

                // Handling attachments
                const attachmentContainer = document.getElementById('attachments');
                // console.log('Attachment:', attachment);
                if (attachment) {
                    const fileName = attachment.file_path.split('/').pop();
                    attachmentContainer.innerHTML = `
                        <div class="attachment">
                            ${attachment.file_type === 'pdf' ? '<i class="far fa-file-pdf"></i>' : '<i class="far fa-image"></i>'}
                            <a href="/storage/${attachment.file_path}" target="_blank">${fileName}</a>
                        </div>
                    `;

                    // If an attachment exists, you can display it or do something with it.
                    // For example, you could display the file name in the modal or show a preview if it's an image.
                } else {
                    attachmentContainer.innerHTML = '<p>No attachments available</p>';
                }

                // // Handling report link (if applicable)
                // const reportLink = document.getElementById('report-link');
                // if (complaint.report_path) {
                //     reportLink.href = `/storage/${complaint.report_path}`;
                //     reportLink.innerText = 'Download Report';
                // } else {
                //     reportLink.innerText = 'No Report Available';
                //     reportLink.href = '#';
                // }
            } else {
                alert('Failed to load complaint data.');
            }
        } catch (error) {
            console.error('Error fetching complaint data:', error);
            alert('An error occurred while fetching complaint details.');
        }
    }

    // Handle Edit Button Click
    document.getElementById('edit-btn').addEventListener('click', function () {
        // Show the modal
        new bootstrap.Modal(document.getElementById('editComplaintModal')).show();
    });

    document.getElementById('editComplaintForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        const complaintId = window.location.pathname.split('/').pop();
        const complaintTitle = document.getElementById('complaintTitle').value;
        const complaintDetails = document.getElementById('complaintDetails').value;
        const complaintStatus = document.getElementById('complaintStatus').value;
        const assignTo = document.getElementById('assignTo').value;
        const complaint_feedback = document.getElementById('complaintFeedback').value;
        const user_id = {{ $user->id }};

        // Handle case when no file is selected (fileAttachment will be null)
        let fileBase64 = null;

        sendData(fileBase64);

        function sendData(fileBase64) {
            // Create a JSON object to hold the form data
            let formData = {
                complaint_id: complaintId,
                complaint_title: complaintTitle,
                complaint_details: complaintDetails,
                file_attachment: fileBase64,
                complaint_status: complaintStatus,
                complaint_feedback: complaint_feedback,
                assign_to: assignTo,
                user_id: user_id
            };

            console.log('Form Data:', formData);

            // Fetch token for CSRF protection
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Send data using fetch (POST or PUT request based on your API)
            fetch(`{{ route('api.complaint.edit') }}`, {
                method: 'PUT',  // Adjust method as necessary
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',  // Set Content-Type to application/json
                    'X-CSRF-TOKEN': csrfToken  // Send CSRF token in the header
                },
                body: JSON.stringify(formData)  // Send the form data as JSON
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Something went wrong.');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    // Handle success response
                    const statusMessage = document.getElementById('status-message');
                    statusMessage.innerText = "Complaint updated successfully!";
                    statusMessage.classList.remove('alert-danger');
                    statusMessage.classList.add('alert-success');
                    statusMessage.style.display = 'block';

                    // Close modal after successful update
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editComplaintModal'));
                    modal.hide();

                    // Optionally fetch updated complaint details here
                    fetchComplaintDetails();

                    // Hide the status message after a short delay
                    setTimeout(() => {
                        statusMessage.style.display = 'none';
                    }, 5000);
                } else {
                    alert('Failed to update complaint.');
                }
            })
            .catch(error => {
                console.error('Error updating complaint:', error);
            });
        }
    });

    // Handle Delete Button Click
    document.getElementById('delete-btn').addEventListener('click', function() {
        // Show the modal
        new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
    });

    // Prevent form submission if canceled (not required as form submission only happens after modal confirmation)
    document.getElementById('delete-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const complaintId = window.location.pathname.split('/').pop();

        
        let formData = {
            complaint_id: complaintId 
        };

        // Fetch token for CSRF protection
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Send a DELETE request to the API
        fetch(`{{ route('api.complaint.destroy') }}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
                if (!response.ok) {
                    // If response is not OK, throw an error for the catch block
                    return response.json().then(err => {
                        throw new Error(err.message || 'Something went wrong.');
                    });
                }
                return response.json();
            })
        .then(data => {
            if (data.status === 'success') {
                // Handle success response, display success message
                const statusMessage = document.getElementById('status-message');
                statusMessage.innerText = "Complaint deleted successfully!";
                statusMessage.classList.remove('alert-danger');
                statusMessage.classList.add('alert-success');
                statusMessage.style.display = 'block';

                // Navigate to 'welcome' route after successful deletion
                window.location.href = "{{ route('admin.complaint.show') }}";

                setTimeout(() => {
                    statusMessage.style.display = 'none';
                }, 5000);
            } else {
                alert('Failed to delete complaint.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting complaint.');
        });
    });

</script>
@endsection