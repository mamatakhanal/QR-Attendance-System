<body>
    <!-- Create Student -->
    <div @class(['modal', 'fade']) id="addStudentModal" tabindex="-1" aria-hidden="true">
        <div @class(['modal-dialog', 'modal-lg', 'modal-dialog-centered'])>

            <div @class(['modal-content', 'px-4', 'pt-4', 'rounded-4'])>

                <form id="studentForm">
                    @csrf

                    <div @class(['modal-header'])>
                        <h3 @class(['modal-title', 'fw-bold'])>Add New Student</h3>
                        <button type="button" @class(['btn-close']) data-bs-dismiss="modal"></button>
                    </div>

                    <div @class(['modal-body', 'row', 'g-3'])>

                        <div @class(['col-md-6'])>
                            <label @class(['form-label'])>Name</label>
                            <input type="text" name="name" @class(['form-control']) required>
                            <small @class(['text-danger']) id="name_error"></small>

                        </div>

                        <div @class(['col-md-6'])>
                            <label @class(['form-label'])>Roll No</label>
                            <input type="number" name="roll_no" @class(['form-control']) min="1" required>
                            <small @class(['text-danger']) id="roll_no_error"></small>
                        </div>

                        <div @class(['col-md-6'])>
                            <label @class(['form-label'])>Email</label>
                            <input type="email" name="email" @class(['form-control']) required>
                            <small @class(['text-danger']) id="email_error"></small>
                        </div>

                        <div @class(['col-md-6'])>
                            <label @class(['form-label'])>Password</label>
                            <div @class(['input-group'])>
                                <input type="password" name="password" @class(['form-control', 'create-password']) required>
                                <span @class(['input-group-text', 'toggle-password']) style="cursor:pointer;">
                                    <i @class(['ri-eye-off-line'])></i>
                                </span>
                            </div>
                            <small @class(['text-danger']) id="password_error"></small>
                        </div>

                        <div @class(['col-md-6'])>
                            <label @class(['form-label'])>Batch</label>
                            <input type="number" id="admission_year" name="admission_year" @class(['form-control']) min="2010" max="{{ date('Y') }}" required>
                            <small @class(['text-danger']) id="admission_year_error"></small>
                        </div>

                        <div @class(['col-md-6'])>
                            <label @class(['form-label'])>Semester</label>
                            <select id="current_semester" @class(['form-select']) disabled>
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
                            <small @class(['text-danger']) id="current_semester_error"></small>
                        </div>
                    </div>

                    <div @class(['modal-footer', 'mt-3', 'mb-0'])>
                        <button type="submit" @class(['btn', 'btn-success'])>Save Student</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $('#studentForm').submit(function(e) {

        e.preventDefault();

            $('.text-danger').text('');

            // AJAX Request - Add New Student Without Reloading Page
            $.ajax({
                url: "{{ route('students.create') }}",
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
                        },
                    });

                    $('#studentForm')[0].reset();

                const modal = bootstrap.Modal.getInstance(
                    document.getElementById('addStudentModal')
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
        $('#addStudentModal').on('hidden.bs.modal', function() {

            // Reset form
            $('#studentForm')[0].reset();

            // Clear validation messages
            $('.text-danger').text('');

            // Reset semester dropdown
            $('#current_semester').val('');

        });
    </script>
</body>


<!-- JS-Password-Eye -->
<script>
    document.querySelectorAll(".toggle-password").forEach(function(toggle) {

        toggle.addEventListener("click", function() {

            let input = this.parentElement.querySelector(".create-password");
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

<!-- Automatically Semester Select -->
<script>
    document.getElementById('admission_year').addEventListener('input', function() {

        let admissionYear = parseInt(this.value);
        let currentYear = new Date().getFullYear();

        if (!admissionYear) return;

        let semester = (currentYear - admissionYear) + 1;

        let semesterSelect = document.getElementById('current_semester');

        if (admissionYear <= 2018) {
            semesterSelect.value = '';
        } else {
            semesterSelect.value = semester;
        }
    });
</script>