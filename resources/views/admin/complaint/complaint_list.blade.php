@extends('layouts.admin.app')

@section('title', 'Complaints Dashboard')

@section('header')
    @include('layouts.admin.navigation', ['user' => $user])
    @section('page-styles')
    <style>
        /* Set the table background as transparent */
        #myTable {
            background-color: transparent; /* Makes the table transparent */
        }

        /* Set the wrapper to ensure alignment */
        #myTable_wrapper {
            background-color: transparent; /* Makes the DataTables wrapper transparent */
            margin-top: 30px; /* Optional padding for better spacing */
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

        #myTable_length select,
        #myTable_filter input {
            vertical-align: middle;
            height: 35px;
            padding: 0px 20px;
            font-size: 14px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background-color: #f8f9fa;
            color: #495057;
        }

        #myTable_filter input {
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
        <h2 class="h4 mb-3">List of Complaints</h2>
        <table id="myTable" class="display">
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
                <!-- Data will be dynamically populated by DataTables -->
            </tbody>
        </table>
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
                    <!-- Hidden input for complaint_id -->
                    <input type="hidden" name="complaint_id" id="complaintIdInput" value="">
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
        const table = new DataTable('#myTable', {
            ajax: {
                url: "{{ route('api.complaint.index') }}", // API endpoint
                type: 'GET',
                dataSrc: '',
            },
            columns: [
                { data: 'complaint_title' },
                { 
                    data: 'complaint_details',
                    render: function (data) {
                        const maxLength = 50; // Maximum number of characters
                        if (data && data.length > maxLength) {
                            return data.substring(0, maxLength) + '...';
                        }
                        return data;
                    }, 
                },
                {
                    data: 'complaint_status',
                    render: function (data) {
                        let badgeClass;
                        switch (data.toLowerCase()) {
                            case 'pending':
                                badgeClass = 'badge bg-warning text-dark';
                                break;
                            case 'resolved':
                                badgeClass = 'badge bg-success';
                                break;
                            case 'rejected':
                                badgeClass = 'badge bg-danger';
                                break;
                            default:
                                badgeClass = 'badge bg-secondary';
                        }
                        return `<span class="${badgeClass}">${data}</span>`;
                    },
                },
                { data: 'issued_by.name', defaultContent: 'N/A' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `
                            <a href="{{ route('admin.complaint.details', '') }}/${row.id}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-complaint-id="${row.id}" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        `;
                    },
                    orderable: false,
                },
            ],
        });

        // Add event listener for delete form submission
        document.querySelector('#deleteForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const complaintId = document.getElementById('complaintIdInput').value;

            // Fetch token for CSRF protection
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("{{ route('api.complaint.destroy') }}", {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ complaint_id: complaintId })
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

        // Event delegation for delete buttons
        document.querySelector('#myTable').addEventListener('click', function (e) {
            if (e.target.closest('.delete-btn')) {
                const complaintId = e.target.closest('.delete-btn').getAttribute('data-complaint-id');
                document.getElementById('complaintIdInput').value = complaintId;
            }
        });
    });

</script>
@endsection
