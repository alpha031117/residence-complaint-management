@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
@include('layouts.navigation')
@endsection

@section('content')
<div class="container mt-5 mb-5">
    <div id="status-message" class="alert" style="display: none;"></div>
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    Total Complaints
                </div>
                <div class="card-body">
                    <h2 id="complaintsCount" class="text-center">Loading...</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    In Progress Complaints
                </div>
                <div class="card-body">
                    <h2 id="inProgressComplaints" class="text-center">Loading...</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    Resolved Complaints
                </div>
                <div class="card-body">
                    <h2 id="resolvedComplaints" class="text-center">Loading...</h2>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-5 mb-2" style="font-size: 22px; font-weight: 500; display: flex; justify-content: space-between; align-items: center;">
        <span>Recent Complaints</span>
        <div style="display: flex; gap: 10px;">
            <div class="dropdown">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 5px 15px;">
                    <i class='fas fa-filter'></i> &nbsp; Filter
                </button>
                <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                    <li><a class="dropdown-item" href="#" onclick="sortComplaints('id')">Sort by ID</a></li>
                    <li><a class="dropdown-item" href="#" onclick="sortComplaints('complaint_title')">Sort by Title</a></li>
                    <li><a class="dropdown-item" href="#" onclick="sortComplaints('created_at')">Sort by Date</a></li>
                </ul>                
            </div>
            <a href="{{ route('file_complaint') }}">
                <button class="btn btn-primary btn-sm" style="padding: 5px 15px;">
                    <i class='fas fa-plus'></i> &nbsp; File Complaint
                </button>
            </a>
        </div>
    </h3>    

    <div class="card">
        <div class="card-body">
            <table class="table" id="complaintsTable">
                <thead>
                    <tr>
                        <th>Complaint ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Complaints will be inserted here dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for deleting complaint -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Complaint</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this complaint?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" action="" style="display: inline;" enctype="multipart/form-data">
                    @csrf
                    @method('DELETE')
                    {{-- Hidden Complaint ID --}}
                    <input type="hidden" name="complaint_id" id="complaint_id">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
<script>
   document.addEventListener('DOMContentLoaded', function() {
        let currentSort = 'created_at'; // Default sorting field
        let currentOrder = 'desc'; // Default order (descending)

        // Initial load of complaints
        loadComplaints(currentSort, currentOrder);

        function loadComplaints(sort = 'created_at', order = 'desc') {
            const userId = {{ auth()->user()->id }}; // Make sure the user ID is dynamically set
            const url = `/api/dashboard?sort=${sort}&order=${order}&id=${userId}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Update total complaints
                    document.getElementById('complaintsCount').innerText = data.complaintsCount;

                    // Update in progress complaints
                    document.getElementById('inProgressComplaints').innerText = data.inProgressComplaints > 0 ? data.inProgressComplaints : 'No in progress complaints';

                    // Update resolved complaints
                    document.getElementById('resolvedComplaints').innerText = data.resolvedComplaints > 0 ? data.resolvedComplaints : 'No resolved complaints';

                    // Clear previous complaints
                    const complaintsTableBody = document.querySelector('#complaintsTable tbody');
                    complaintsTableBody.innerHTML = '';

                    // Insert new complaints into the table
                    data.recentComplaints.forEach((complaint, index) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${complaint.complaint_title}</td>
                            <td>
                                <span class="badge ${
                                    complaint.complaint_status === 'resolved' ? 'bg-success' :
                                    complaint.complaint_status === 'pending' ? 'bg-warning text-dark' :
                                    complaint.complaint_status === 'rejected' ? 'bg-danger' :
                                    complaint.complaint_status === 'in progress' ? 'bg-primary' :
                                    'bg-secondary'
                                }">
                                    ${complaint.complaint_status}
                                </span>
                            </td>
                            <td>${new Date(complaint.created_at).toLocaleDateString()}</td>
                            <td style="text-align: center;">
                                <a href="/complaint/${complaint.id}" class="btn btn-sm btn-info" style="color:white;">View</a>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</button>
                            </td>
                        `;
                        complaintsTableBody.appendChild(row);

                        // Set the complaint ID in the delete form
                        const deleteForm = document.querySelector('#deleteModal form');

                        // Take hidden input
                        deleteForm.querySelector('input[id="complaint_id"]').value = complaint.id;
                    });
                })
                .catch(error => {
                    console.error('Error fetching complaints:', error);
                    alert('Failed to load complaints.');
                });
        }

        // Update the sort and load complaints when a new sort option is selected
        window.sortComplaints = function(field) {
            // Toggle order
            if (currentSort === field) {
                currentOrder = currentOrder === 'asc' ? 'desc' : 'asc'; // Toggle between ascending and descending
            } else {
                currentSort = field;
                currentOrder = 'asc'; // Reset to ascending when a new field is chosen
            }

            // Reload complaints with the new sort field and order
            loadComplaints(currentSort, currentOrder);
        };

        // Add an event listener for the delete form submission
        document.querySelector('#deleteForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const deleteForm = document.querySelector('#deleteModal form');

            // Take hidden input
            const complaintIdInput = deleteForm.querySelector('input[name="complaint_id"]');

            console.log('Deleting complaint:', complaintIdInput.value);

            // Fetch token for CSRF protection
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let formData = {
                complaint_id: complaintIdInput.value
            };
            fetch("{{ route('api.complaint.destroy') }}", {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',  // Set Content-Type to application/json
                    'X-CSRF-TOKEN': csrfToken  // Send CSRF token in the header
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Display success message
                    const statusMessage = document.getElementById('status-message');
                    statusMessage.innerText = data.message;
                    statusMessage.classList.remove('alert-danger');
                    statusMessage.classList.add('alert-success');
                    statusMessage.style.display = 'block';

                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    modal.hide();

                    // Reload the complaints list after deletion
                    loadComplaints();

                    // Optionally hide the message after a few seconds
                    setTimeout(() => {
                        statusMessage.style.display = 'none';
                    }, 5000);
                }
            })
            .catch(error => {
                console.error('Error deleting complaint:', error);
                alert('Failed to delete complaint.');
            });
        });
    });
</script>
@endsection
