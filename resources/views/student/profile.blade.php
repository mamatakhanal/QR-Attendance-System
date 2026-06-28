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
            <div class="card shadow-sm border-0 mx-2 my-2 px-4 rounded-4">
                <form id="editStudentProfile" class="p-4 pb-1">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="profile_id" value="{{ $student->id }}">

                    <div class="modal-header mb-3">
                        <h3 class="modal-title fw-bold"> Profile </h3>
                    </div>

                    <div class="modal-body row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control"
                                value="{{ $student->name }}">
                            <small class="text-danger" id="edit_name_error"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Roll No</label>
                            <input type="number" name="roll_no" id="edit_roll_no" class="form-control" min="1"
                                value="{{ $student->roll_no }}">
                            <small class="text-danger" id="edit_roll_no_error"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control"
                                value="{{ $student->phone }}">
                            <small class="text-danger" id="edit_phone_error"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" id="edit_gender" class="form-select">
                                <option value="">Select Gender</option>
                                <option value="Male" @if ($student->gender == 'Male') selected @endif>Male</option>
                                <option value="Female" @if ($student->gender == 'Female') selected @endif>Female</option>
                                <option value="Other" @if ($student->gender == 'Other') selected @endif>Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" id="edit_dob" class="form-control"
                                value="{{ $student->dob }}">
                            <small class="text-danger" id="edit_dob_error"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <textarea name="address" id="edit_address" class="form-control" rows="1">{{ $student->address }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control"
                                value="{{ $student->email }}">
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
                                <label class="form-label">Batch</label>
                                <input type="number" id="edit_admission_year" name="admission_year"
                                    class="form-control" min="2010" max="{{ date('Y') }}"
                                    value="{{ $student->admission_year }}" readonly>
                                <small class="text-danger" id="edit_admission_year_error"></small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Semester</label>
                                <input type="text" class="form-control" value="{{ $student->current_semester }}"
                                    readonly>
                            </div>
                    </div>

                    <div class="modal-footer mt-4">
                        <button type="submit" class="btn btn-primary">Save Profile</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            $(document).ready(function() {

                $('#editStudentProfile').on('submit', function(e) {
                    e.preventDefault();

                    let id = $('#profile_id').val();
                    $('.text-danger').text('');

                    $.ajax({
                        url: '/student/profile/update/' + id,
                        type: 'POST',
                        data: $(this).serialize() + '&_method=PUT',

                        success: function(response) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1000,
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

                            setTimeout(() => location.reload(), 2000);
                        },

                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    $('#edit_' + key + '_error').text(value[0]);
                                });
                            }
                        }
                    });
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
