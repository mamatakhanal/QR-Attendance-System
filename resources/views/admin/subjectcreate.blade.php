<body>
    <!-- Create Subject -->
    <div class="modal fade" id="addSubjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content px-4 pt-4 rounded-4">

                <form id="subjectForm" action="javascript:void(0);">
                    @csrf

                    <div class="modal-header">
                        <h3 class="modal-title fw-bold">Add New Subject</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">

                        <div class="col-md-12">
                            <label class="form-label">Name</label>
                            <input type="text" name="subject_name" class="form-control" required>
                            <small class="text-danger" id="subject_name_error"></small>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Code</label>
                            <input type="text" name="subject_code" class="form-control" required>
                            <small class="text-danger" id="subject_code_error"></small>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select" required>
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
                            <small class="text-danger" id="semester_error"></small>
                        </div>
                    </div>

                    <div class="modal-footer mt-3 mb-0">
                        <button type="submit" class="btn btn-success">Save Subject</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>


<script>
    $('#subjectForm').submit(function(e) {
        e.preventDefault();
        $('.text-danger').text('');

        // AJAX Request - Add New Subject Without Reloading Page
        $.ajax({
            url: "{{ route('subjects.create') }}",
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

                $('#subjectForm')[0].reset();

                const modal = bootstrap.Modal.getInstance(
                    document.getElementById('addSubjectModal')
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
    $('#addSubjectModal').on('hidden.bs.modal', function() {

        // Reset form
        $('#subjectForm')[0].reset();

        // Clear validation messages
        $('.text-danger').text('');

    });
</script>
