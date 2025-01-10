@extends('layouts.app')

@section('title', 'File Complaint')

@section('header')
@include('layouts.navigation')
@section('page-styles')
<style>
.custom-file-upload {
        border: 2px dashed #ddd;
        background: white;
        padding: 15px;
        text-align: center;
        position: relative;
        border-radius: 10px;
    }

    .upload-label {
        cursor: pointer;
        font-size: 16px;
        color: #007bff;
        margin-top: 10px; 
    }

    .file-preview-container {
        margin-top: 10px;
        text-align: left;
    }

    .file-name {
        font-size: 14px;
        color: #333;
        font-weight: bold;
    }

    .preview-image {
        width: 100px;
        height: 100px;
        margin-top: 10px;
    }

    .preview-icon {
        width: 30px;
        height: 30px;
    }

    #cancel-button {
        margin-top: 10px;
    }

    .no-resize {
        resize: none; /* Disable resizing */
        border-radius: 10px;
        border-color: lightgrey;
    }

    /* Style for the PDF preview wrapper */
    .pdf-preview-wrapper {
        display: inline-flex;
        align-items: center; /* Vertically align the items */
    }

    /* Style for the PDF icon */
    .preview-icon {
        width: 17px;
        height: 20px;
        margin-right: 8px; /* Space between the icon and the text */
    }

    /* Style for the PDF link */
    .pdf-preview-wrapper a {
        font-size: 14px;
        color: black; /* Blue color for the link */
        font-weight: bold;
        text-decoration: none; /* Remove underline */
    }

    .pdf-preview-wrapper a:hover {
        text-decoration: underline; /* Underline the link when hovered */
    }
</style>
@endsection
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

        <script>
            // Handle form submission with AJAX
            document.getElementById('residenceForm').addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent the default form submission
                
                let form = this;
                let formData = new FormData(form);
        
                // Send an AJAX request
                fetch("{{ route('api.residence') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Handle success or error based on response
                    const responseMessage = document.getElementById('responseMessage');
                    if (data.status === 'success') {
                        responseMessage.innerHTML = `
                            <div class="alert alert-success">${data.message}</div>
                        `;
                    } else {
                        responseMessage.innerHTML = `
                            <div class="alert alert-danger">${data.message}</div>
                        `;
                    }
                })
                .catch(error => {
                    // Handle any network errors
                    document.getElementById('responseMessage').innerHTML = `
                        <div class="alert alert-danger">An error occurred. Please try again later.</div>
                    `;
                });
            });
        </script>

    @else
        <div class="container mb-5">
            <h3 class="mb-3" style="font-size: 24px; font-weight:bolder;">File A Complaint</h3>
            <form id="complaint-form" enctype="multipart/form-data">
                @csrf <!-- Include CSRF token for security -->
                
                <div class="mb-3">
                    <label class="form-label">Block Residence</label>
                    <input type="text" class="form-control" name="residence_name" id="residence_name" value="{{ auth()->user()->residence->residence_name }}" style="border-radius: 10px; border-color:lightgrey;" readonly>
                    
                    {{-- Hidden input --}}
                    <input type="hidden" name="residence_id" id="residence_id" value="{{ auth()->user()->residence->id }}">
                    <input type="hidden" name="user_id" id="user_id" value="{{ auth()->user()->id }}">

                    <!-- Description for the Residence field -->
                    <small class="form-text text-muted">
                        <b>This field shows the name of the residence associated with your account. It is read-only and cannot be edited.</b>
                    </small>
                </div>
            
                <div class="mb-3">
                    <label class="form-label" for="complaint_title">Complaint Title</label>
                    <input type="text" class="form-control" id="complaint_title" name="complaint_title" placeholder="eg, Noise in the building" style="border-radius: 10px; border-color:lightgrey;" required>
                    
                    <!-- Description for the Complaint Title -->
                    <small class="form-text text-muted">
                        <b>Provide a brief title for your complaint.</b>
                    </small>
                </div>
            
                <div class="mb-3">
                    <label class="form-label" for="complaint_details">Complaint Details</label>
                    <textarea class="form-control no-resize" id="complaint_details" name="complaint_details" rows="4" style="border-radius: 10px; border-color:lightgrey; resize: none;" maxlength="250" required></textarea> 
            
                    <!-- Description for the Complaint Details -->
                    <small class="form-text text-muted">
                        <b>Describe the details of the complaint. Include relevant information like dates, times, and any other specifics. <span id="char-count">250</span> characters remaining.</b>
                    </small>
                </div>
            
                <div class="mb-3">
                    <label class="form-label" for="file_attachment">File Attachment</label>
                
                    <!-- Custom File Upload Box -->
                    <div class="custom-file-upload" id="upload-box">
                        <label for="file_attachment" class="upload-label">
                            <i class="fas fa-cloud-upload-alt"></i> &nbsp; Upload a file
                        </label>
                        <input class="form-control-file" type="file" id="file_attachment" name="file_attachment" style="display: none;" onchange="previewFile()" />
                
                        <!-- Preview area inside the box -->
                        <div id="file-preview" class="file-preview-container">
                            <!-- The file name and preview will be added here dynamically -->
                        </div>
                    </div>
                
                    <!-- Description for the file attachment -->
                    <small class="form-text text-muted">
                        <b>You can attach any relevant documents or images to support your complaint (optional).</b>
                    </small>
        
                    <!-- Cancel button to remove the uploaded file -->
                    <div class="d-flex justify-content-between mt-2">
                        <div></div> <!-- Empty div to align the cancel button to the right -->
                        <button type="button" id="cancel-button" class="btn btn-outline-danger btn-sm" style="display: none;" onclick="cancelFile()">Cancel</button>
                    </div>
                </div>

                {{-- Submit button --}}
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        Submit Complaint
                    </button>
                </div>
        
                <!-- Display Success or Error Message -->
                <div id="message-container" class="container mt-5" style="display: none;">
                    <div id="message" class="alert" role="alert"></div>
                </div>
            </form>
        
            <script>
               // Handle the form submission via AJAX
                document.getElementById('complaint-form').addEventListener('submit', function (e) {
                    e.preventDefault();

                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Get form data
                    let formData = new FormData(this);
                    formData.append('_token', csrfToken); // Manually append CSRF token to FormData

                    // Send the form data via AJAX
                    fetch("{{ route('api.complaint.store') }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',  // Ensure the server knows you're expecting a JSON response
                        },
                        body: formData,
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
                        // Handle success (201)
                        if (data.message && data.complaint) {
                            document.getElementById('message-container').style.display = 'block';
                            document.getElementById('message').classList.remove('alert-danger');
                            document.getElementById('message').classList.add('alert-success');
                            document.getElementById('message').innerHTML = data.message;

                            // Optionally, clear the form
                            document.getElementById('complaint-form').reset();

                            // Clear file preview if any
                            cancelFile();

                            // Redirect to success page
                            window.location.href = "{{ route('file_complaint.success') }}";
                        }
                    })
                    .catch(error => {
                        // Handle error
                        document.getElementById('message-container').style.display = 'block';
                        document.getElementById('message').classList.remove('alert-success');
                        document.getElementById('message').classList.add('alert-danger');
                        document.getElementById('message').innerHTML = error.message || 'Something went wrong. Please try again.';
                    });
                });


                // Update character count for textarea
                const textarea = document.getElementById('complaint_details');
                const charCount = document.getElementById('char-count');

                textarea.addEventListener('input', function () {
                    const remaining = 250 - textarea.value.length;
                    charCount.textContent = remaining; // Update the character count
                });

                // Function to preview the uploaded file
                function previewFile() {
                    const fileInput = document.getElementById('file_attachment');
                    const filePreview = document.getElementById('file-preview');
                    const cancelButton = document.getElementById('cancel-button');
                    const file = fileInput.files[0];

                    // Clear previous preview
                    filePreview.innerHTML = '';

                    if (file) {
                        const fileName = file.name;
                        const fileType = file.type.split('/')[0]; // image, pdf, etc.

                        // If the file is an image, show a thumbnail preview
                        if (fileType === 'image') {
                            const img = document.createElement('img');
                            img.src = URL.createObjectURL(file);
                            img.classList.add('preview-image');
                            filePreview.appendChild(img);
                        }

                        // If the file is a PDF, show a PDF icon and a clickable link to view the file
                        else if (fileType === 'application' && file.name.endsWith('.pdf')) {
                            // Create the PDF icon
                            const pdfIcon = document.createElement('img');
                            pdfIcon.src = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQq1aZw8V35IO876xr_qje7N-8QqCxXRdWOSw&s'; // PDF icon image
                            pdfIcon.classList.add('preview-icon');

                            // Create the link to view the PDF
                            const pdfLink = document.createElement('a');
                            pdfLink.href = URL.createObjectURL(file); // Use the URL.createObjectURL for local preview
                            pdfLink.textContent = fileName;
                            pdfLink.target = '_blank'; // Open in a new tab

                            // Create a wrapper to hold the icon and the link together
                            const pdfPreviewWrapper = document.createElement('div');
                            pdfPreviewWrapper.classList.add('pdf-preview-wrapper');

                            // Append the icon and link to the wrapper
                            pdfPreviewWrapper.appendChild(pdfIcon);
                            pdfPreviewWrapper.appendChild(pdfLink);

                            // Append the wrapper to the file preview container
                            filePreview.appendChild(pdfPreviewWrapper);
                        }

                        // Show the cancel button to remove the file
                        cancelButton.style.display = 'inline-block';
                    }
                }

                // Function to cancel the file and remove preview
                function cancelFile() {
                    const fileInput = document.getElementById('file_attachment');
                    const filePreview = document.getElementById('file-preview');
                    const cancelButton = document.getElementById('cancel-button');

                    // Clear the file input and preview
                    fileInput.value = '';
                    filePreview.innerHTML = '';

                    // Hide the cancel button
                    cancelButton.style.display = 'none';
                }

            </script>
        </div>
    @endif
@endsection

@section('page-scripts')
@endsection
