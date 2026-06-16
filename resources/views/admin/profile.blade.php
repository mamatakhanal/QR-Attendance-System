<head>
    <title>Profile - Admin</title>
    @include('layouts.link')
    @include('layouts.style')
</head>

<body>
    <!-- MAIN LAYOUT -->
    <div class="main-wrapper">
        @include('admin.sidebar')
        <div class="main-area">
            @include('admin.navbar')

            <!-- CONTENT -->
            <div class="card shadow-sm border-0 mx-3 my-2 px-4 rounded-4">
                <form id="editAdminProfile" class=" p-4">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="profile_id" value="{{ $admin->id }}">

                    <div class="modal-header mb-3">
                        <h3 class="modal-title fw-bold"> Profile </h3>
                    </div>

                    <div class="modal-body row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control"
                                value="{{ $admin->name }}">
                            <small class="text-danger" id="edit_name_error"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control"
                                value="{{ $admin->email }}">
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
                    </div>

                    <div class="modal-footer mt-3">
                        <button type="submit" class="btn btn-primary">Save Profile</button>
                    </div>
                </form>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $(document).ready(function() {

                $('#editAdminProfile').on('submit', function(e) {

                    e.preventDefault();

                    let id = $('#profile_id').val();
                    $('.text-danger').text('');

                    $.ajax({
                        url: '/admin/profile/update/' + id,
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

                            setTimeout(function() {
                                location.reload();
                            }, 2000);

                        },

                        error: function(xhr) {
                            if (xhr.status == 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    $('#edit_' + key + '_error')
                                        .text(value[0]);
                                });
                            }
                        }
                    });
                });
            });
        </script>


        {{-- JS-Password-Eye --}}
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
