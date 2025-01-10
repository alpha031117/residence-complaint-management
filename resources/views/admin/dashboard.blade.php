@extends('layouts.admin.app')

@section('title', 'Admin Dashboard')

@section('header')
    @include('layouts.admin.navigation', ['user' => $user])
    @section('page-styles')
    <style>
        .card-title{
            font-size: 20px;
            font-weight: 500;
        }

        .card-text{
            font-size: 20px;
            font-weight: bold;
        }

        .recent-complaint-list{
            font-size: 15px;
        }

        .recent-complaint-title {
            font-size: 18px;
            font-weight: bold;
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100%;
            line-height: 1.2;
        }

        .recent-complaint-list {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100%;
            line-height: 1.2;
        }

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

    </style>
    @endsection
@endsection

@section('content')
<div class="d-flex">
    <!-- Main Content -->
    <div class="flex-grow-1" style="margin-left: 250px; padding: 20px; margin-top:30px;">
        <div class="container">
            <div class="row justify-content-start">
                <!-- Total Complaints Card -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Total Complaints</h5>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <p class="card-text" id="totalComplaints">0</p>
                                <span class="badge" id="totalComplaintsChange" style="background-color: #f8d7da; color: #721c24;">
                                    Updated just now
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Resolved Complaints Card -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Resolved Complaints</h5>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <p class="card-text" id="resolvedComplaints" style="color:#22C55E;">0</p>
                                <span class="badge" id="resolvedComplaintsChange" style="background-color: #CEFFE2; color: #22C55E;">
                                    Updated just now
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Complaints Card -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Pending Complaints</h5>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <p class="card-text" id="pendingComplaints" style="color:cornflowerblue;">0</p>
                                <span class="badge" id="pendingComplaintsChange" style="background-color: #CEFAFF; color: cornflowerblue;">
                                    Updated just now
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Graph Integration -->
        <div class="container mt-3">
            <div class="row justify-content-start">
                <div class="col-8">
                    {{-- Overview Card --}}
                    <div class="card" style="height: 500px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <h5 class="card-title">Overview Complaints</h5>
                                <a href="{{ route('admin.complaint.show') }}" class="text-primary text-sm hover:text-black">View All ></a>
                            </div>
                            <canvas id="myChart"></canvas>
                            <small class="text-muted d-block mt-4">Last updated 3 mins ago</small>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card" style="height: 500px;">
                        <div class="card-body">
                            <h5 class="card-title">Recent Complaints</h5>
                            <div class="list-group" id="recentComplaintsList">
                                <!-- Recent Complaints will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script>
    // Fetch complaints data
    document.addEventListener('DOMContentLoaded', function() {
        fetch('{{ route('api.complaint.index') }}') // Assuming the endpoint is '/api/complaints'
            .then(response => response.json())
            .then(data => {
                // Update total complaints
                const totalComplaints = data.length;
                document.getElementById('totalComplaints').textContent = totalComplaints;

                // Filter resolved and pending complaints
                const resolvedComplaints = data.filter(complaint => complaint.complaint_status === 'resolved').length;
                const pendingComplaints = data.filter(complaint => complaint.complaint_status === 'pending').length;
                const inProgressComplaints = data.filter(complaint => complaint.complaint_status === 'in progress').length;

                document.getElementById('resolvedComplaints').textContent = resolvedComplaints;
                document.getElementById('pendingComplaints').textContent = pendingComplaints;

                // Update recent complaints list
                const recentComplaintsList = document.getElementById('recentComplaintsList');
                recentComplaintsList.innerHTML = ''; // Clear previous entries
                data.slice(0, 5).forEach(complaint => {
                    const complaintItem = document.createElement('a');
                    complaintItem.classList.add('list-group-item', 'list-group-item-action', 'border-0');
                    console.log(complaint.id);
                    
                    complaintItem.href = `/admin/complaint/${complaint.id}`;
                    console.log(complaintItem.href);
                    complaintItem.innerHTML = `
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="recent-complaint-title mb-1 text-primary">${truncateTitle(complaint.complaint_title, 25)}</h5>                            <small class="text-muted">${new Date(complaint.created_at).toLocaleDateString()}</small>
                        </div>
                        <p class="recent-complaint-list mb-1">${truncateTitle(complaint.complaint_details, 70)}</p>
                        <small>By ${complaint.issued_by.name}</small>
                    `;
                    recentComplaintsList.appendChild(complaintItem);
                });

                // Update chart (overview)
                const ctx = document.getElementById('myChart');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Resolved', 'Pending', 'In Progress'],
                        datasets: [{
                            label: 'Nums of Complaints',
                            data: [resolvedComplaints, pendingComplaints, inProgressComplaints],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    });

    function truncateTitle(title, wordLimit) {
        const words = title.split(" ");
        if (words.length > wordLimit) {
            return words.slice(0, wordLimit).join(" ") + "...";
        }
        return title;
    }
</script>
@endsection
