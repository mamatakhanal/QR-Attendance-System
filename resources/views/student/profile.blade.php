<head>
    <title>Profile - Student</title>
    @include('layouts.link')
    @include('layouts.style')
</head>

<body>

    <!-- MAIN LAYOUT -->
    <div class="main-wrapper">
        @include('student.sidebar')
        <div class="main-area">
            @include('student.navbar')

            <!-- CONTENT -->
            <div class="card shadow-sm border-0 m-3 px-4 rounded-4">
                <form id="editStudentProfile" class=" p-4">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_id">

                    <div class="modal-header mb-3">
                        <h3 class="modal-title fw-bold"> Profile </h3>
                    </div>

                    <div class="modal-body row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control">
                            <small class="text-danger" id="edit_name_error"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Roll No</label>
                            <input type="number" name="roll_no" id="edit_roll_no" class="form-control" min="1">
                            <small class="text-danger" id="edit_roll_no_error"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                            <small class="text-danger" id="edit_phone_error"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" id="edit_gender" class="form-select">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" id="edit_dob" class="form-control">
                            <small class="text-danger" id="edit_dob_error"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <textarea name="address" id="edit_address" class="form-control" rows="1"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                            <small class="text-danger" id="edit_email_error"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control edit-password">
                                <span class="input-group-text toggle-password">
                                    <i class="ri-eye-off-line"></i>
                                </span>
                            </div>
                            <small class="text-muted"> Leave blank to keep current password </small> <br>
                            <small class="text-danger" id="edit_password_error"></small>
                        </div>

                        <div class="col-md-6">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Batch</label>
                                <input type="number" id="edit_admission_year" name="admission_year" class="form-control" min="2010" max="{{ date('Y') }}" readonly>
                                <small class="text-danger" id="edit_admission_year_error"></small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Semester</label>
                                <select id="edit_current_semester" class="form-select" disabled>
                                    <option value="">Select Semester</option>
                                    <option value="1">Semester 1</option>
                                    <option value="2">Semester 2</option>
                                    <option value="3">Semester 3</option>
                                    <option value="4">Semester 4</option>
                                    <option value="5">Semester 5</option>
                                    <option value="6">Semester 6</option>
                                    <option value="7">Semester 7</option>
                                    <option value="8">Semester 8</option>
                                </select>
                                <input type="hidden" name="current_semester" id="current_semester_hidden">
                                <small class="text-danger" id="edit_current_semester_error"></small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Student Code</label>
                                <input type="text" name="student_code" id="edit_student_code" class="form-control" readonly>
                            </div>
                        </div>

                        <!-- QR Code -->
                        <div class="col-md-6 d-flex flex-column align-items-center">
                            <label class="form-label fw-semibold">QR Code</label>
                            <img id="edit_qr_image"
                                src=""
                                class="img-thumbnail shadow-sm p-2"
                                style="width:210px;height:210px;object-fit:contain;">
                        </div>
                    </div>

                    <div class="modal-footer mt-3">
                        <button type="submit" class="btn btn-primary">Save Profile</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
            $('#editStudentProfile').submit(function(e) {

                e.preventDefault();

                let id = $('#edit_id').val();
                // Clear old validation errors
                $('.text-danger').text('');

                // Send Student Profile Update Data to Server Without Reloading Page
                $.ajax({
                    url: '/student/profile/update/' + id,
                    type: 'POST',
                    data: $(this).serialize() + '&_method=PUT',

                    success: function(response) {

                        // Show Success Toast Message
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'small-toast'
                            },
                            showClass: {
                                popup: 'animate__animated animate__fadeInRight'
                            },

                            hideClass: {
                                popup: 'animate__animated animate__fadeOutRight'
                            }
                        });

                        // Reload page after 3 seconds
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    },

                    // Validation Error Response
                    error: function(xhr) {
                        if (xhr.status == 422) {

                            // Get validation errors
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#edit_' + key + '_error').text(value[0]);
                            });
                        }
                    }
                });

            });
        </script>
</body>

<!-- JS-Password-Eye -->
<script>
    document.querySelectorAll(".toggle-password").forEach(function(toggle) {

        toggle.addEventListener("click", function() {

            let input = this.parentElement.querySelector(".edit-password");
            let icon = this.querySelector("i");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("ri-eye-off-line");
                icon.classList.add("ri-eye-line");
            } else {
                input.type = "password";
                icon.classList.remove("ri-eye-line");
                icon.classList.add("ri-eye-off-line");
            }
        });
    });
</script>