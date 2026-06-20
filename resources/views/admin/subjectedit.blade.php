<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <!-- Edit Subject -->
    <div class="modal fade" id="editSubjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content px-4 pt-4 rounded-4">

                <form id="editSubjectForm">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_id">

                    <div class="modal-header">
                        <h3 class="modal-title fw-bold">Edit Subject - <span id="subject_name_title"></span> </h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">

                        <div class="col-md-12">
                            <label class="form-label">Name</label>
                            <input type="text" name="subject_name" id="edit_subject_name" class="form-control"
                                required>
                            <small class="text-danger" id="edit_subject_name_error"></small>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Code</label>
                            <input type="text" name="subject_code" id="edit_subject_code" class="form-control"
                                required>
                            <small class="text-danger" id="edit_subject_code_error"></small>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Semester</label>
                            <select name="semester" id="edit_semester" class="form-select" required>
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
                            <small class="text-danger" id="edit_semester_error"></small>
                        </div>
                    </div>

                    <div class="modal-footer mt-3 mb-0">
                        <button type="submit" class="btn btn-primary">Update Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $('#editSubjectForm').submit(function(e) {

            e.preventDefault();

            let id = $('#edit_id').val();
            // Clear old validation errors
            $('.text-danger').text('');

            // Send Subject Update Data to Server Without Reloading Page
            $.ajax({
                url: '/admin/subjects/update/' + id,
                type: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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

                    // Close modal only after successful update
                    bootstrap.Modal.getInstance(
                        document.getElementById('editSubjectModal')
                    ).hide();

                    // Reload page after 3 seconds
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },

                // Validation Error Response
                error: function(xhr) {
                    if (xhr.status == 422) {
                        $('.text-danger').text('');
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


<script>
    $('#editSubjectModal').on('show.bs.modal', function(event) {

        let button = $(event.relatedTarget);

        // Clear old validation errors
        $('#edit_subject_name_error').text('');
        $('#edit_subject_code_error').text('');
        $('#edit_semester_error').text('');

        $('#subject_name_title').text(button.data('subject_name'));
        // Form Fields
        $('#edit_id').val(button.data('id'));
        $('#edit_subject_name').val(button.data('subject_name'));
        $('#edit_subject_code').val(button.data('subject_code'));
        $('#edit_semester').val(button.data('semester'));
        $('#subject_name_title').text(button.data('subject_name'));
    });
</script>
