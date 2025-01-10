@extends('layouts.app')

@section('title', 'Complaint Case Details')

@section('header')
    @include('layouts.navigation')
    @section('page-styles')
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

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
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .grid .section {
            margin-bottom: 20px;
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

        .sidebar {
            border-left: 1px solid #ddd;
            padding-left: 20px;
        }

        .sidebar .section {
            margin-bottom: 15px;
        }

        .remarks {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #007bff;
            font-size: 14px;
            margin-top: 110px; /* Push remarks section lower */
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
<div class="container">
    <div id="status-message" class="alert" style="display: none;"></div>
    <div class="header">
        <h1 id="complaint-title">Case Title: Loading...</h1>
    </div>

    <div class="grid">
        <div>
            <div class="section two-column" style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3>Submitted By:</h3>
                    <p id="submitted-by">Loading...</p>
                </div>

                <div>
                    <h3>Issued On:</h3>
                    <p id="issued-on">Loading...</p>
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
                <span class="badge" id="complaint-status">Loading...</span>
            </div>

            <div class="section two-column" style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center; margin-bottom:0px;">
                <div>
                    <h3>Resolution Time:</h3>
                    <p id="resolution-time">Loading...</p>
                </div>

                <div>
                    <h3>Status Updated:</h3>
                    <p id="status-updated">Loading...</p>
                </div>
            </div>
        </div>

        <div class="sidebar">
            <div class="section">
                <h3>Assigned To:</h3>
                <p id="assigned-to">Loading...</p>
            </div>

            {{-- <div class="section" style="margin-top: 30px;">
                <h3>Complaint Report:</h3>
                <p><i class="far fa-file-pdf"></i> <a href="#" id="report-link">View Report</a></p>
            </div> --}}

            <div class="remarks">
                <h3>Remarks:</h3>
                <p><strong id="remarks">Loading...</strong></p>
            </div>
        </div>
    </div>
    <!-- Buttons container -->
    <div class="mt-3 d-flex justify-content-end align-items-center gap-2">
        <a href="#" class="btn btn-primary btn-sm" id="edit-btn">Edit Complaint</a>
        <button type="button" class="btn btn-danger btn-sm" id="delete-btn">Delete Complaint</button>
    </div>
</div>

<!-- Modal -->
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
                {{-- Hidden Input --}}
                <input type="hidden" name="delete_complaint_id" id="delete_complaint_id">
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
                        <input type="text" class="form-control" id="complaintTitle" name="complaint_title" required>
                    </div>
                    <!-- Complaint Details -->
                    <div class="mb-3">
                        <label for="complaintDetails" class="form-label">Complaint Details</label>
                        <textarea class="form-control" id="complaintDetails" name="complaint_details" rows="4" style="height: 150px; resize: none;" required></textarea>
                    </div>
                    <!-- Attach File -->
                    <div class="mb-3">
                        <label for="file_attachment" class="form-label">Attach File</label>
                        <input type="file" class="form-control custom-file-input" id="file_attachment" name="file_attachment" accept="image/*,application/pdf" />
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
                document.getElementById('remarks').innerText = complaint.complaint_feedback || 'No remarks provided';

                // Pre-fill form fields with the fetched complaint data
                document.getElementById('complaintTitle').value = complaint.complaint_title;
                document.getElementById('complaintDetails').value = complaint.complaint_details;

                document.getElementById('delete_complaint_id').value = complaint.id;

                // Handling attachments
                const attachmentContainer = document.getElementById('attachments');
                console.log('Attachment:', attachment);
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
                    document.getElementById('file_attachment').setAttribute('data-existing-file', attachment);
                    console.log('Existing Attachment:', attachment);
                } else {
                    attachmentContainer.innerHTML = '<p>No attachments available</p>';
                }
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
        const fileAttachment = document.getElementById('file_attachment').files[0] || null;

        // Handle case when no file is selected (fileAttachment will be null)
        let fileBase64 = null;

        if (fileAttachment) {
            // Convert the file to base64 (asynchronously)
            const reader = new FileReader();
            reader.readAsDataURL(fileAttachment);
            
            reader.onload = function() {
                fileBase64 = reader.result.split(',')[1];  // Remove the data URL prefix (e.g., "data:image/png;base64,")
                console.log('File Base64:', fileBase64);

                // After the file is encoded, send the AJAX request
                sendData(fileBase64);
            };
        } else {
            // If no file is attached, send the request without the file
            sendData(null);
        }

        function sendData(fileBase64) {
            // Create a JSON object to hold the form data
            let formData = {
                complaint_id: complaintId,
                complaint_title: complaintTitle,
                complaint_details: complaintDetails,
                file_attachment: fileBase64,
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
                window.location.href = "{{ route('dashboard') }}";

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