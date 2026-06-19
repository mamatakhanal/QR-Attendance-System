<body>
    <!-- Create Teacher -->
    <div class="modal fade" id="addTeacherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content px-4 pt-4 rounded-4">

                <form id="teacherForm" action="javascript:void(0);">
                    @csrf

                    <div class="modal-header">
                        <h3 class="modal-title fw-bold">Add New Teacher</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                            <small class="text-danger" id="name_error"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                            <small class="text-danger" id="phone_error"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                            <small class="text-danger" id="email_error"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control teacher-password" required>
                                <span class="input-group-text toggle-password" style="cursor:pointer;">
                                    <i class="ri-eye-off-line"></i>
                                </span>
                            </div>
                            <small class="text-danger" id="password_error"></small>
                        </div>
                    </div>

                    <div class="modal-footer mt-3 mb-0">
                        <button type="submit" class="btn btn-success">Save Teacher</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>


<script>
    $('#teacherForm').submit(function(e) {

        e.preventDefault();

        $('.text-danger').text('');

        // AJAX Request - Add New Teacher Without Reloading Page
        $.ajax({
            url: "{{ route('teachers.create') }}",
            type: "POST",
            data: $(this).serialize(),

            success: function(response) {

                // Show Success Toast Message
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

                $('#teacherForm')[0].reset();

                const modal = bootstrap.Modal.getInstance(
                    document.getElementById('addTeacherModal')
                );

                if (modal) {
                    modal.hide();
                }

                // Reload page after 3 seconds
                setTimeout(function() {
                    location.reload();
                }, 2000);
            },

            // Validation Error Response
            error: function(xhr) {

                if (xhr.status === 422) {

                    // Get validation errors
                    let errors = xhr.responseJSON.errors;

                    $.each(errors, function(key, value) {
                        $('#' + key + '_error').text(value[0]);
                    });
                }
            }
        });
    });
</script>


<script>
    $('#addTeacherModal').on('hidden.bs.modal', function() {

        // Reset form
        $('#teacherForm')[0].reset();

        // Clear validation messages
        $('.text-danger').text('');

    });
</script>



<!-- JS-Password-Eye -->
<script>
    document.querySelectorAll(".toggle-password").forEach(function(toggle) {

        toggle.addEventListener("click", function() {

            let input = this.parentElement.querySelector(".teacher-password");
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