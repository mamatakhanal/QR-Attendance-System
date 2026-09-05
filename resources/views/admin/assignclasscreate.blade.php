<body>
    <!-- Assign Class to Teacher -->
    <div class="modal fade" id="addAssignclassModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content px-4 pt-4 rounded-4">

                <form id="assignclassForm">
                    @csrf

                    <div class="modal-header">
                        <h3 class="modal-title fw-bold">Assign Subject</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">

                        <div class="col-md-12">
                            <label class="form-label">Teacher</label>
                            <select name="teacher_id" id="teacher" class="form-select" required>
                                <option value="">Select Teacher</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Semester</label>
                            <select name="semester" id="semester" class="form-select" required>
                                <option value="">Select Semester</option>
                                @for ($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}">
                                        Semester {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label for="subject_id" class="form-label">Subject</label>
                            <select name="subject_id" id="subject_id" class="form-select">
                                <option value="">Select Semester First</option>
                            </select>
                            <small id="subject_id_error" class="text-danger"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Start Time</label>
                            <input type="time" name="start_time" id="start_time" class="form-control" required>
                            <small id="start_time_error" class="text-danger"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">End Time</label>
                            <input type="time" name="end_time" id="end_time" class="form-control" required>
                            <small id="end_time_error" class="text-danger"></small>
                        </div>

                    </div>
                    <div class="modal-footer mt-3 mb-0">
                        <button class="btn btn-success">Save </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    $(document).ready(function() {

        // Submit Assign Subject Form

        $('#assignclassForm').submit(function(e) {

            e.preventDefault();

            // Clear previous errors
            $('#subject_id_error').text('');
            $('#start_time_error').text('');
            $('#end_time_error').text('');

            // Check subject selection
            if ($('#subject_id').val() === '') {

                $('#subject_id_error').text(
                    'Please select a subject.'
                );

                return;
            }

            let formData = new FormData(this);

            $.ajax({

                url: "{{ route('assignclass.create') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,

                success: function(response) {

                    if (!response.success) {

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                            customClass: {
                                popup: 'small-toast'
                            }
                        });

                        return;
                    }

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: {
                            popup: 'small-toast'
                        }
                    });

                    // Reset form
                    $('#assignclassForm')[0].reset();

                    // Reset subject dropdown
                    $('#subject_id').html(
                        '<option value="">Select Semester First</option>'
                    );

                    // Hide modal
                    bootstrap.Modal.getInstance(
                        document.getElementById('addAssignclassModal')
                    ).hide();

                    // Reload page
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },

                error: function(xhr) {

                    // Clear previous validation errors
                    $('#subject_id_error').text('');
                    $('#start_time_error').text('');
                    $('#end_time_error').text('');

                    if (xhr.status === 422) {

                        let errors = xhr.responseJSON.errors;

                        $.each(errors, function(key, value) {

                            if (key === 'subject_id') {
                                $('#subject_id_error')
                                    .text(value[0]);
                            }

                            if (key === 'start_time') {
                                $('#start_time_error')
                                    .text(value[0]);
                            }

                            if (key === 'end_time') {
                                $('#end_time_error')
                                    .text(value[0]);
                            }

                        });
                    }
                }
            });
        });


        // Load Subjects According to Semester

        $('#semester').on('change', function() {

            let semester = $(this).val();
            let subjectDropdown = $('#subject_id');

            // Clear previous error
            $('#subject_id_error').text('');

            // Show loading
            subjectDropdown.html(
                '<option value="">Loading...</option>'
            );

            if (!semester) {

                subjectDropdown.html(
                    '<option value="">Select Semester First</option>'
                );

                return;
            }

            $.ajax({

                url: "{{ url('/admin/assignclass/subjects') }}/" + semester,
                type: "GET",

                success: function(data) {

                    subjectDropdown.empty();

                    if (data.length === 0) {

                        subjectDropdown.append(
                            '<option value="">No subjects found</option>'
                        );

                        return;
                    }

                    subjectDropdown.append(
                        '<option value="">Select Subject</option>'
                    );

                    $.each(data, function(key, subject) {

                        subjectDropdown.append(`
                            <option value="${subject.id}">
                                ${subject.subject_name}
                            </option>
                        `);

                    });
                },

                error: function() {

                    subjectDropdown.html(
                        '<option value="">Unable to load subjects</option>'
                    );
                }
            });
        });


        // Clear Modal When Closed

        $('#addAssignclassModal').on('hidden.bs.modal', function() {

            $('#assignclassForm')[0].reset();

            $('#subject_id').html(
                '<option value="">Select Semester First</option>'
            );

            $('#subject_id_error').text('');
            $('#start_time_error').text('');
            $('#end_time_error').text('');

        });

    });
</script>

</body>
