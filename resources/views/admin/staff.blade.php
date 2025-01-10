@extends('layouts.admin.app')

@section('title', 'Staff Management')

@section('header')
    @include('layouts.admin.navigation', ['user' => $user])
    @section('page-styles')
    <style>
        /* Set the table background as transparent */
        #staffTable {
            background-color: transparent; /* Makes the table transparent */
        }

        /* Set the wrapper to ensure alignment */
        #staffTable_wrapper {
            background-color: transparent; /* Makes the DataTables wrapper transparent */
            margin-top: 30px; /* Optional padding for better spacing */
        }

        /* Optional: Style the table header */
        #staffTable thead th {
            background-color: transparent; /* Light background for header with transparency */
            color: #000; /* Dark text color */
        }

        /* Optional: Style the table rows */
        #staffTable tbody tr {
            background-color: transparent; /* Light row background */
            border-bottom: 1px solid #dee2e6; /* Row border */
        }

        #staffTable tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.05); /* Slightly darker background on hover */
        }

        #staffTable_length select,
        #staffTable_filter input {
            vertical-align: middle;
            height: 35px;
            padding: 0px 20px;
            font-size: 14px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background-color: #f8f9fa;
            color: #495057;
        }

        #staffTable_filter input {
            padding: 6px 20px;
        }
    </style>
    @endsection
@endsection

@section('content')
<div class="d-flex">
    <!-- Main Content -->
    <div class="flex-grow-1" style="margin-left: 250px; padding: 30px;">
        <div id="status-message" class="alert" style="display: none;"></div>
        <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
            <h2 class="h4 mb-0">List of Staff</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus"></i> Create Staff
            </button>
        </div>        
        <table id="staffTable" class="display">
            <thead>
                <tr>
                    <th>Staff Name</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be dynamically populated by DataTables -->
            </tbody>
        </table>
    </div>
</div>

{{-- Modal for creating staff --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Create Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal for edting staff --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="editPhoneNumber" name="phone_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="editPassword" name="password" value="password">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="editRole" name="role" required>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    {{-- Hidden Input --}}
                    <input type="hidden" name="staff_id" id="staffIdUpdate" value="">
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for deleting staff -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this staff?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" action="" style="display: inline;" enctype="multipart/form-data">
                    @csrf
                    @method('DELETE')
                    <!-- Hidden input for complaint_id -->
                    <input type="hidden" name="staff_id" id="staffIdInput" value="">
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
        // Initialize DataTable with AJAX data source
        const table = new DataTable('#staffTable', {
            ajax: {
                url: "{{ route('api.staff.show') }}", // API endpoint
                type: 'GET',
                dataSrc: '',
            },
            columns: [
                { data: 'name' },
                { 
                    data: 'phone_number', 
                    defaultContent: 'N/A',
                },
                { data: 'email', defaultContent: 'N/A' },
                {  
                    data: 'created_at',
                    render: function(data, type, row) {
                        return new Date(data).toLocaleDateString(); // Converts the date to a local string format
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `
                            <button type="button" class="btn btn-primary btn-sm edit-btn" data-staff-id="${row.id}" data-bs-toggle="modal" data-bs-target="#editModal">
                                <i class="bi bi-eye"></i> Edit
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-staff-id="${row.id}" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        `;
                    },
                    orderable: false,
                },
            ],
        });

        // Fetch existing staff data in form for editing
        document.querySelector('#staffTable').addEventListener('click', function (e) {
            if (e.target.closest('.edit-btn')) {
                const staffId = e.target.closest('.edit-btn').getAttribute('data-staff-id');
                fetch(`{{ route('api.staff.details', ':id') }}`.replace(':id', staffId), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editName').value = data.name;
                    document.getElementById('editPhoneNumber').value = data.phone_number;
                    document.getElementById('editEmail').value = data.email;
                    document.getElementById('editRole').value = data.role;
                })
                .catch(error => {
                    console.error('Error fetching staff:', error);
                    alert('Failed to fetch staff.');
                });
            }
        });

        // Add event listener for edit form submission
        document.querySelector('#editForm').addEventListener('submit', function(event) {
            event.preventDefault();

            // Fetch token for CSRF protection
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Get form data
            let formData = new FormData(this);
            // formData.append('_token', csrfToken); // Manually append CSRF token to FormData
            formData.append('_method', 'PUT'); // Method spoofing for Laravel

            formData.forEach((value, key) => {
                console.log(`${key}: ${value}`);
            });

            fetch("{{ route('api.staff.update') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken  // Send CSRF token in the header
                },  
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const statusMessage = document.getElementById('status-message');
                    statusMessage.innerText = data.message;
                    statusMessage.classList.remove('alert-danger');
                    statusMessage.classList.add('alert-success');
                    statusMessage.style.display = 'block';

                    // Close modal and reload table data
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                    modal.hide();
                    table.ajax.reload();
                    setTimeout(() => statusMessage.style.display = 'none', 5000);
                }
            })
            .catch(error => {
                console.error('Error updating staff:', error);
                alert('Failed to update staff.');
            });
        });

        // Add event listener for create form submission
        document.querySelector('#createForm').addEventListener('submit', function(event) {
            event.preventDefault();

            // Fetch token for CSRF protection
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Get form data
            let formData = new FormData(this);
            formData.append('_token', csrfToken); // Manually append CSRF token to FormData

            formData.forEach((value, key) => {
                console.log(`${key}: ${value}`);
            });

            fetch("{{ route('api.staff.store') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const statusMessage = document.getElementById('status-message');
                    statusMessage.innerText = data.message;
                    statusMessage.classList.remove('alert-danger');
                    statusMessage.classList.add('alert-success');
                    statusMessage.style.display = 'block';

                    // Close modal and reload table data
                    const modal = bootstrap.Modal.getInstance(document.getElementById('createModal'));
                    modal.hide();
                    table.ajax.reload();
                    setTimeout(() => statusMessage.style.display = 'none', 5000);
                }
            })
            .catch(error => {
                console.error('Error creating staff:', error);
                alert('Failed to create staff.');
            });
        });

        // Add event listener for delete form submission
        document.querySelector('#deleteForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const staffId = document.getElementById('staffIdInput').value;

            // Fetch token for CSRF protection
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("{{ route('api.staff.destroy') }}", {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ staffId: staffId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const statusMessage = document.getElementById('status-message');
                    statusMessage.innerText = data.message;
                    statusMessage.classList.remove('alert-danger');
                    statusMessage.classList.add('alert-success');
                    statusMessage.style.display = 'block';

                    // Close modal and reload table data
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    modal.hide();
                    table.ajax.reload();
                    setTimeout(() => statusMessage.style.display = 'none', 5000);
                }
            })
            .catch(error => {
                console.error('Error deleting complaint:', error);
                alert('Failed to delete complaint.');
            });
        });

        // Event delegation for buttons
        document.querySelector('#staffTable').addEventListener('click', function (e) {
            if (e.target.closest('.delete-btn')) {
                const staffId = e.target.closest('.delete-btn').getAttribute('data-staff-id');
                document.getElementById('staffIdInput').value = staffId;
            }
            if (e.target.closest('.edit-btn')) {
                const staffId = e.target.closest('.edit-btn').getAttribute('data-staff-id');
                document.getElementById('staffIdUpdate').value = staffId;
            }
        });
    });

</script>
@endsection
