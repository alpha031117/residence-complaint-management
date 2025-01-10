@extends('layouts.app')

@section('title', 'My Profile')

@section('header')
@include('layouts.navigation')
@endsection

@section('content')
    {{-- If residence_id hasn't been registered --}}
    @if(!auth()->user()->residence)
        <div class="container mt-5">
            <div class="alert alert-warning">
                <h4 class="alert-heading">Residence Not Registered</h4>
                <p>
                    You need to register your residence before you can file a complaint.
                </p>
                <form id="residenceForm" method="POST">
                    @csrf
                    <div class="mb-3 mt-3">
                        <label for="residence_name" class="form-label">Choose Your Residence Name</label>
                        <select class="form-select" id="residence_name" name="residence_name" required style="border-radius: 10px; padding: 10px; border-color: #ced4da;">
                            <option value="">Select Residence Name</option>
                            @foreach($residences->unique('residence_name') as $residence)
                                <option value="{{ $residence->residence_name }}">{{ $residence->residence_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="block_no" class="form-label">Residence Block No</label>
                            <input type="text" class="form-control" id="block_no" name="block_no" style="border-radius: 10px; padding: 10px; border-color: #ced4da;" required>
                        </div>
                        <div class="col-md-6">
                            <label for="unit_no" class="form-label">Residence Unit No</label>
                            <input type="text" class="form-control" id="unit_no" name="unit_no" style="border-radius: 10px; padding: 10px; border-color: #ced4da;" required>
                        </div>      
                    </div>

                    {{-- Hidden Input --}}
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    <button type="submit" class="btn btn-primary">Register Residence</button>
                </form>

                <div id="responseMessage" class="mt-3"></div> <!-- This div will hold the response message -->
            </div>
        </div>
    @else
        <div class="container mt-3 mb-3">
            <div id="status-message" class="alert" style="display: none;"></div>
            <div class="row">
                <div class="col">
                    <div class="card bg-white border-0 rounded">
                        <div class="card-header bg-white border-0">
                            <h3 class="mt-2 mb-2" style="font-size: 24px; font-weight:bolder;">Personal Information</h3>
                        </div>
                        <hr style="width:50px; margin-left: auto; margin-right: auto; border-top: 1px solid #ccc;">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-2 d-flex flex-column align-items-center text-center">
                                    <form id="profilePhotoForm" enctype="multipart/form-data">
                                        @csrf
                                        <img 
                                            id="profile-image" 
                                            src="{{ asset('images/user-icon.jpg') }}" 
                                            class="rounded-circle mb-3" 
                                            style="width: 160px; height: 150px; border: 2px solid lightgrey;" 
                                            alt="Profile Picture"
                                            {{-- onclick="document.getElementById('file-input').click();" --}}
                                        />
                                        
                                        <!-- Hidden file input to select a new image -->
                                        <input type="file" id="file-input" name="photo" style="display: none;" accept="image/*" onchange="previewImage(event)" />
                                        <input type="hidden" id="user_id_photo" name="user_id_photo" value="{{ auth()->user()->id }}">

                                        {{-- <!-- Submit Button (Submit the form to update the profile photo) -->
                                        <div>
                                            <button type="submit" class="btn btn-primary">Save Profile Photo</button>
                                        </div> --}}
                                    </form>
                                    <i class="fas fa-camera position-absolute bottom-0 mb-2" style="font-size: 24px; color: white; cursor: pointer;" onclick="document.getElementById('file-input').click();"></i>
                                </div>
                                <div class="col-md-10">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="first-name" style="border-radius: 10px;" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="last-name" style="border-radius: 10px;" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="email" style="border-radius: 10px;" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone-number" style="border-radius: 10px;" readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Residence</label>
                                        <input type="text" class="form-control" id="residence-name" style="border-radius: 10px;" readonly>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Block No.</label>
                                            <input type="text" class="form-control" id="block-no" style="border-radius: 10px;" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Unit No.</label>
                                            <input type="text" class="form-control" id="unit-no" style="border-radius: 10px;" readonly>
                                        </div>
                                    </div>                                        
                                    <div class="mb-3">
                                        <label class="form-label">Account Password</label>
                                        <input type="password" class="form-control" value="password" style="border-radius: 10px;" readonly>
                                    </div>
                                    <!-- Button to tigger edit account modal -->
                                    <button type="button" class="btn btn-primary btn-sm" id="editProfileButton" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                        <i class="bi bi-pencil me-1"></i>Edit Profile
                                    </button>

                                    <!-- Modal Edit Account -->
                                    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="editProfileForm" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label for="user-first-name" class="form-label">First Name</label>
                                                            <input type="text" class="form-control" id="user_first_name" name="user_first_name" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="user-last-name" class="form-label">Last Name</label>
                                                            <input type="text" class="form-control" id="user_last_name" name="user_last_name" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="user-email" class="form-label">Email Address</label>
                                                            <input type="email" class="form-control" id="user_email" name="user_email" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="user-phone-num" class="form-label">Phone Number</label>
                                                            <input type="text" class="form-control" id="user_phone_num" name="user_phone_num" required>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mt-4 mb-3 border-0" style="background-color: #F3F7F8; border-radius: 20px;">
                                        <div class="card-header border-0" style="background-color: #F3F7F8;">
                                            <h3 class="mt-3" style="font-size: 18px; font-weight:bolder;">Delete Account</h3>
                                        </div>
                                        <div class="card-body" style="color: #7c828a;">
                                            <div class="mb-3 bg-white p-2" style="border-radius: 10px;">
                                                <p>&nbsp; <i class='fas fa-exclamation-triangle' style="color: grey;"></i> &nbsp; After making a deletion request, you will have <b style="color:black;">6 months</b> to maintain this account.</p>
                                            </div>
                                            <p>To permanently erase your whole residence account, click the button below. This implies that you won't have access to your account's data.</p>
                                            <br/>
                                            <p>There is no reversing this action.</p>
                                            <!-- Button to trigger the delete account modal -->
                                            <button type="button" class="btn btn-danger mt-2 btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                                <i class="fas fa-trash-alt"></i> &nbsp; Delete Account
                                            </button>

                                            <!-- Modal for Delete Account -->
                                            <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteAccountModalLabel">Confirm Account Deletion</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form id="deleteProfileForm" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('DELETE')

                                                                <!-- Password Input -->
                                                                <div class="mb-3">
                                                                    <label for="password" class="form-label">Please enter your password to confirm deletion:</label>
                                                                    <input type="password" class="form-control" id="password" name="password" required>
                                                                </div>

                                                                <div class="d-flex justify-content-end">
                                                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-danger ms-2">Delete Account</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card border-0">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Membership Since:</strong>
                                                    <p>{{ \Carbon\Carbon::parse(auth()->user()->created_at)->format('F j, Y') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // function previewImage(event) {
            //     const file = event.target.files[0];
            //     if (file) {
            //         const reader = new FileReader();
            //         reader.onload = function(e) {
            //             document.getElementById('profile-image').src = e.target.result;
            //         };
            //         reader.readAsDataURL(file);
            //     }
            // }
        </script>
    @endif
@endsection

@section('page-scripts')
<script>
    // Fetch user profile data when the page loads
    document.addEventListener('DOMContentLoaded', function () {
        // console.log({{ $user->id }});
        fetchProfileData({{ $user->id }});
        editProfile({{ $user->id }});
        deleteProfile({{ $user->id }});
    });

    // Function to fetch user profile data
    function fetchProfileData(userId) {
        // Fetch user data using Profile API
        fetch("{{ route('api.profile.show') }}?user_id=" + userId, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            // If the API returns data successfully, update the profile info
            if (data.status === 'success') {
                const profileData = data.data;
                console.log(data);

                // Split the full name into first and last names
                const fullName = profileData.name.split(' ');
                const firstName = fullName[0];
                const lastName = fullName.slice(1).join(' ');

                // Update the profile section with the fetched data
                // document.getElementById('profile-image').src = profileData.profile_photo_path || 'https://mdbootstrap.com/img/new/avatars/2.jpg';
                document.getElementById('first-name').value = firstName;
                document.getElementById('last-name').value = lastName || '-';
                document.getElementById('email').value = profileData.email;
                document.getElementById('phone-number').value = profileData.phone_number || '-';
                document.getElementById('residence-name').value = profileData.residence.residence_name;
                document.getElementById('block-no').value = profileData.residence.block_no;
                document.getElementById('unit-no').value = profileData.residence.unit_no;

                // Populate the modal with existing data
                document.getElementById('editProfileButton').addEventListener('click', function() {
                    document.getElementById('user_first_name').placeholder = document.getElementById('first-name').value;
                    document.getElementById('user_last_name').placeholder = document.getElementById('last-name').value;
                    // console.log(document.getElementById('email').value);
                    document.getElementById('user_email').placeholder = document.getElementById('email').value;
                    document.getElementById('user_phone_num').placeholder = document.getElementById('phone-number').value;
                });

            } else {
                // Handle any errors returned from the API
                alert('Failed to load profile data');
            }
        })
        .catch(error => {   
            console.error('Error fetching profile data:', error);
            // alert('An error occurred while fetching profile data.');
        });
    }

    // Function to handle the form submission for updating profile
    function editProfile(userId){
        document.getElementById('editProfileForm').addEventListener('submit', function(event) {
            event.preventDefault();  // Prevent default form submission

            // Create a JSON object to hold the form data
            let formData = {
                user_id: userId,  // Include the user_id if necessary
                user_first_name: document.getElementById('user_first_name').value,
                user_last_name: document.getElementById('user_last_name').value,
                user_email: document.getElementById('user_email').value,
                user_phone_num: document.getElementById('user_phone_num').value
            };

            // Fetch token for CSRF protection
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Send request with PUT method
            fetch("{{ route('api.profile.update') }}", {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',  // Set Content-Type to application/json
                    'X-CSRF-TOKEN': csrfToken  // Send CSRF token in the header
                },
                body: JSON.stringify(formData) 
            })
            .then(response => 
            {
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
                    const statusMessage = document.getElementById('status-message');
                    statusMessage.innerText = "Profile updated successfully!";
                    statusMessage.classList.remove('alert-danger');
                    statusMessage.classList.add('alert-success');
                    statusMessage.style.display = 'block';

                    // Close modal after successful update
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
                    modal.hide();

                    // Optionally refresh user profile data on the page
                    fetchProfileData(userId);

                    setTimeout(() => {
                        statusMessage.style.display = 'none';
                    }, 5000);
                } else {
                    alert('Failed to update profile.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating profile.');
            });
        });
    }

    // Funtion to delete user account
    function deleteProfile(userId){
        document.getElementById('deleteProfileForm').addEventListener('submit', function(event) {
            event.preventDefault();  // Prevent default form submission

            // Create a JSON object to hold the user ID (assumed it's required for deletion)
            let formData = {
                user_id: userId, 
                password: document.getElementById('password').value,
            };

            // Fetch token for CSRF protection
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Send request with DELETE method
            fetch("{{ route('api.profile.destroy') }}", {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',  // Set Content-Type to application/json
                    'X-CSRF-TOKEN': csrfToken  // Send CSRF token in the header
                },
                body: JSON.stringify(formData)  // Send user_id as part of the request
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
                    statusMessage.innerText = "Profile deleted successfully!";
                    statusMessage.classList.remove('alert-danger');
                    statusMessage.classList.add('alert-success');
                    statusMessage.style.display = 'block';

                    // Navigate to 'welcome' route after successful deletion
                    window.location.href = "{{ route('main') }}";

                    setTimeout(() => {
                        statusMessage.style.display = 'none';
                    }, 5000);
                } else {
                    alert('Failed to delete profile.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting profile.');
            });
        });
    }

</script>

@endsection