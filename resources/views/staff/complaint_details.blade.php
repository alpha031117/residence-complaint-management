@extends('layouts.staff.app')

@section('title', 'Complaint Case Details')

@section('header')
    @include('layouts.staff.navigation', ['user' => $user])
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
                <h1 id="complaint-title">Case Title: {{ $complaint->complaint_title }}</h1>
            </div>

            <div class="grid">
                <div>
                    <div class="section two-column" style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3>Submitted By:</h3>
                            <p id="submitted-by">{{ $complaint->IssuedBy->name ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <h3>Issued On:</h3>
                            <p id="issued-on">{{ $complaint->created_at->format('d M Y') }}</p>
                        </div>
                    </div>

                    <div class="section">
                        <h3>Complaint Details:</h3>
                        <p id="complaint-details">{{ $complaint->complaint_details }}</p>
                    </div>

                    <div class="section">
                        <h3>Attachments:</h3>
                        <div class="attachments" id="attachments">
                            @if($attachment)
                                <div class="attachment">
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank">
                                        {{ basename($attachment->file_path) }}
                                    </a>
                                </div>
                            @else
                                <p>No attachments available</p>
                            @endif
                        </div>
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
                            <p id="resolution-time">
                                {{ isset($complaint->resolution_time) ? $complaint->resolution_time . ' days' : 'Not Provided' }}
                            </p>
                        </div>                        

                        <div>
                            <h3>Status Updated:</h3>
                            <p id="status-updated">{{ $complaint->updated_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="section remarks mt-5">
                        <h3>Remarks:</h3>
                        <p><strong id="remarks">{{ $complaint->complaint_feedback }}</strong></p>
                    </div>
                    <!-- Buttons container -->
                    {{-- <div class="mt-3 d-flex justify-content-end align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editModal" data-complaint-id="{{ $complaint->id }}" data-complaint-status="{{ $complaint->complaint_status }}">
                            Edit Complaint
                        </button>
                    </div> --}}
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Modal for editing complaint -->
{{-- <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Complaint</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" action="#">
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
</div> --}}

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
    });
</script>