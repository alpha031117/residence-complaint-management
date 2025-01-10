@extends('layouts.admin.app')

@section('title', 'Complaints Dashboard')

@section('header')
    @include('layouts.staff.navigation', ['user' => $user])
    @section('page-styles')
    <style>
    /* Cards Section */
    .card {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 10px;
        color: #333;
    }

    .card-text {
        font-size: 24px;
        font-weight: bold;
        margin: 0;
        color: #333;
    }

    /* Set the table background as transparent */
    #myTable {
        background-color: transparent; /* Makes the table transparent */
    }

    /* Optional: Style the table header */
    #myTable thead th {
        background-color: transparent; /* Light background for header with transparency */
        color: #000; /* Dark text color */
    }

    /* Optional: Style the table rows */
    #myTable tbody tr {
        background-color: transparent; /* Light row background */
        border-bottom: 1px solid #dee2e6; /* Row border */
    }

    #myTable tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.05); /* Slightly darker background on hover */
    }
    /* Button Styling */
    .btn {
        border-radius: 5px;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
        }
    }
</style>
    @endsection
@endsection

@section('content')
<div class="d-flex">
    <!-- Main Content -->
    <div class="flex-grow-1" style="margin-left: 250px; padding: 30px;">
        <div id="status-message" class="alert" style="display: none;"></div>
        
        <!-- Cards Section with Same Width as Table -->
        <div class="container-fluid">
            <div class="row g-3 mb-4">
                <!-- Total Complaints Card -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Total Complaints</h5>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <p class="card-text" id="totalComplaints">{{ $complaintsCount }}</p>
                                <span class="badge" id="totalComplaintsChange" style="background-color: #f8d7da; color: #721c24;">
                                    Updated just now
                                    {{-- {{ $complaintsIncreaseRate >= 0 ? '+' : '' }}{{ $complaintsIncreaseRate }}% from last week --}}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Resolved Complaints Card -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Resolved Complaints</h5>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <p class="card-text" id="resolvedComplaints" style="color:#22C55E;">{{ $resolvedComplaints }}</p>
                                <span class="badge" id="resolvedComplaintsChange" style="background-color: #CEFFE2; color: #22C55E;">
                                    {{-- {{ $resolvedIncreaseRate >= 0 ? '+' : '' }}{{ $resolvedIncreaseRate }}% from last week --}}
                                    Updated just now
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Complaints Card -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Pending Complaints</h5>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <p class="card-text" id="pendingComplaints" style="color:cornflowerblue;">{{ $pendingComplaints }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="container-fluid">
            <h2 class="h4 mb-3">List of Complaints</h2>
            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Issued By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignedComplaints as $complaint)
                        <tr>
                            <td>{{ $complaint->complaint_title }}</td>
                            <td>{{ Str::limit($complaint->complaint_details, 80, '...') }}</td>
                            <td>
                                @php
                                    $badgeClass = match(strtolower($complaint->complaint_status)) {
                                        'pending' => 'badge bg-warning text-dark',
                                        'resolved' => 'badge bg-success',
                                        'rejected' => 'badge bg-danger',
                                        default => 'badge bg-secondary',
                                    };
                                @endphp
                                <span class="{{ $badgeClass }}">{{ $complaint->complaint_status }}</span>
                            </td>
                            <td>{{ $complaint->IssuedBy->name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('staff.complaint.details', $complaint->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <button type="button" class="btn btn-sm btn-warning edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-complaint-id="{{ $complaint->id }}" data-complaint-status="{{ $complaint->complaint_status }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Modal for editing complaint -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Complaint</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" enctype="multipart/form-data">
                <form id="editForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="complaint_id" id="complaintIdInput" value="">
                    <div class="mb-3">
                        <label for="complaintStatus" class="form-label">Complaint Status</label>
                        <select class="form-select" id="complaintStatus" name="complaint_status">
                            <option value="pending">Pending</option>
                            <option value="resolved">Resolved</option>
                            <option value="in progress">In Progress</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.edit-btn');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const complaintId = this.getAttribute('data-complaint-id');
                const complaintStatus = this.getAttribute('data-complaint-status');
                document.getElementById('complaintIdInput').value = complaintId;

                // Pre-select the current status in the dropdown
                const statusDropdown = document.getElementById('complaintStatus');
                Array.from(statusDropdown.options).forEach(option => {
                    option.selected = option.value.toLowerCase() === complaintStatus.toLowerCase();
                });
            });
        });

        document.getElementById('editForm').addEventListener('submit', function (event) {
            event.preventDefault();

            const complaintId = document.getElementById('complaintIdInput').value;
            const status = document.getElementById('complaintStatus').value;

            let formData = {
                complaint_id: complaintId,
                complaint_status: status
            };

            // Fetch CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`{{ route('api.complaint.edit') }}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
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
                    const statusMessage = document.getElementById('status-message');
                    statusMessage.innerText = "Complaint updated successfully!";
                    statusMessage.classList.remove('alert-danger');
                    statusMessage.classList.add('alert-success');
                    statusMessage.style.display = 'block';

                    // Refresh the updated table row
                    refreshTableRow(complaintId);
                    refreshCards();

                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                    modal.hide();

                    // Hide the status message after a delay
                    setTimeout(() => {
                        statusMessage.style.display = 'none';
                    }, 5000);
                } else {
                    alert('Failed to update complaint.');
                }
            })
            .catch(error => {
                console.error('Error updating complaint:', error);
                alert('Something went wrong.');
            });
        });

        // Function to refresh the specific table row
        function refreshTableRow(complaintId) {
            fetch(`/api/complaints/${complaintId}`)
                .then(response => response.json())
                .then(data => {
                    const complaint = data.complaint;
                    const row = document.querySelector(`button[data-complaint-id="${complaintId}"]`).closest('tr');
                    
                    // Update the row content
                    row.querySelector('td:nth-child(1)').innerText = complaint.complaint_title;
                    row.querySelector('td:nth-child(2)').innerText = truncateText(complaint.complaint_details, 80);

                    const badgeClass = {
                        pending: 'badge bg-warning text-dark',
                        resolved: 'badge bg-success',
                        rejected: 'badge bg-danger'
                    }[complaint.complaint_status.toLowerCase()] || 'badge bg-secondary';

                    row.querySelector('td:nth-child(3)').innerHTML = `<span class="${badgeClass}">${complaint.complaint_status}</span>`;
                })
                .catch(error => {
                    console.error('Error refreshing table row:', error);
                });
        }

        // Function to refresh the cards
        function refreshCards() {
            fetch('{{ route('api.complaint.index') }}')
                .then(response => response.json())
                .then(data => {
                    // Calculate totals
                    const totalComplaints = data.length;
                    const resolvedComplaints = data.filter(complaint => complaint.complaint_status === 'resolved').length;
                    const pendingComplaints = data.filter(complaint => complaint.complaint_status === 'pending').length;

                    // // Calculate percentage increases (dummy logic, replace as needed)
                    // const lastWeekTotal = totalComplaints - 5; // Replace with actual data
                    // const lastWeekResolved = resolvedComplaints - 3; // Replace with actual data

                    // const totalIncreaseRate = lastWeekTotal > 0 ? (((totalComplaints - lastWeekTotal) / lastWeekTotal) * 100).toFixed(2) : 0;
                    // const resolvedIncreaseRate = lastWeekResolved > 0 ? (((resolvedComplaints - lastWeekResolved) / lastWeekResolved) * 100).toFixed(2) : 0;

                    // Update card values
                    document.getElementById('totalComplaints').textContent = totalComplaints;
                    document.getElementById('resolvedComplaints').textContent = resolvedComplaints;
                    document.getElementById('pendingComplaints').textContent = pendingComplaints;

                    // Update percentage badges
                    // document.getElementById('totalComplaintsChange').textContent = `${totalIncreaseRate >= 0 ? '+' : ''}${totalIncreaseRate}% from last week`;
                    // document.getElementById('resolvedComplaintsChange').textContent = `${resolvedIncreaseRate >= 0 ? '+' : ''}${resolvedIncreaseRate}% from last week`;
                })
                .catch(error => console.error('Error fetching card data:', error));
            }

        });

        // Helper function to truncate text
        function truncateText(text, maxLength, suffix = '...') {
            if (text.length > maxLength) {
                return text.substring(0, maxLength) + suffix;
            }
            return text;
        }
</script>
