<body>
    <!-- Edit Teacher -->
    <div class="modal fade" id="editAssignclassModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content px-4 pt-4 rounded-4">

                <form id="editAssignclassForm">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_id">

                    <div class="modal-header">
                        <h3 class="modal-title fw-bold">Edit Assign Subject - <span id="teacher_name_title"></span>
                        </h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">

                        <div class="col-md-12">
                            <label class="form-label">Teacher</label>
                            <select name="teacher_id" id="edit_teacher" class="form-select" required>
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
                            <select name="semester" id="edit_semester" class="form-select" required>
                                <option value="">Select Semester</option>
                                @for ($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}">
                                        Semester {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Subjects</label>
                            <select name="subject_id" id="edit_subject" class="form-select" required>

                                <option value="">
                                    Select Semester First
                                </option>

                            </select>
                            <small id="edit_subject_id_error" class="text-danger"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Start Time</label>
                            <input type="time" name="start_time" id="edit_start_time" class="form-control"
                                min="10:00" max="17:00" required>
                            <small id="edit_start_time_error" class="text-danger"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">End Time</label>
                            <input type="time" name="end_time" id="edit_end_time" class="form-control" min="10:00"
                                max="17:00" required>
                            <small id="edit_end_time_error" class="text-danger"></small>
                        </div>
                    </div>

                    <div class="modal-footer mt-2 mb-0">
                        <button type="submit" class="btn btn-primary"> Update </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

<script>
    // Open Edit Modal
    $(document).on('click', '.edit-btn', function() {

        let id = $(this).data('id');
        let teacher = $(this).data('teacher');
        let semester = $(this).data('semester');
        let teacherName = $(this).data('teacher-name');
        let subjectId = $(this).data('subject-id');
        let subjectName = $(this).data('subject-name');

        // Get existing time
        let startTime = $(this).data('start-time');
        let endTime = $(this).data('end-time');


        // Set existing values
        $('#edit_id').val(id);
        $('#edit_teacher').val(teacher);
        $('#edit_semester').val(semester);
        $('#teacher_name_title').text(teacherName);

        // Set existing time
        $('#edit_start_time').val(
            startTime ? String(startTime).substring(0, 5) : ''
        );

        $('#edit_end_time').val(
            endTime ? String(endTime).substring(0, 5) : ''
        );

        // Load subjects for selected semester
        loadSubjects(semester, subjectId, subjectName);
    });


    // Load Subjects Function
    function loadSubjects(semester, selectedSubjectId = '', selectedSubjectName = '') {

        let dropdown = $('#edit_subject');

        // Show existing subject immediately
        if (selectedSubjectId && selectedSubjectName) {
            dropdown.html(
                '<option value="' + selectedSubjectId + '" selected>' +
                selectedSubjectName +
                '</option>'
            );
        } else {
            dropdown.html('<option value="">Loading...</option>');
        }

        if (!semester) {
            dropdown.html(
                '<option value="">Select Semester First</option>'
            );
            return;
        }

        $.ajax({
            url: "{{ url('/admin/assignclass/subjects') }}/" + semester,
            type: "GET",

            success: function(data) {

                dropdown.empty();

                if (data.length === 0) {
                    dropdown.html(
                        '<option value="">No subjects found</option>'
                    );
                    return;
                }

                dropdown.append(
                    '<option value="">Select Subject</option>'
                );

                $.each(data, function(key, subject) {

                    let selected =
                        Number(subject.id) === Number(selectedSubjectId) ?
                        'selected' :
                        '';

                    dropdown.append(`
                    <option value="${subject.id}" ${selected}>
                        ${subject.subject_name}
                    </option>
                `);
                });
            },

            error: function() {

                // Keep existing selected subject if AJAX fails
                if (selectedSubjectId && selectedSubjectName) {
                    dropdown.html(
                        '<option value="' + selectedSubjectId + '" selected>' +
                        selectedSubjectName +
                        '</option>'
                    );
                } else {
                    dropdown.html(
                        '<option value="">Unable to load subjects</option>'
                    );
                }
            }
        });
    }

    // Semester Change
    $(document).on('change', '#edit_semester', function() {

        let semester = $(this).val();

        $('#edit_subject_id_error').text('');

        if (semester) {

            loadSubjects(semester);

        } else {

            $('#edit_subject').html(
                '<option value="">Select Semester First</option>'
            );

        }

    });


    // Reset modal
    $('#editAssignclassModal').on('hidden.bs.modal', function() {

        $('#editAssignclassForm')[0].reset();

        $('#edit_id').val('');

        $('#teacher_name_title').text('');

        $('#edit_subject').html(
            '<option value="">Select Semester First</option>'
        );

        $('#edit_subject_id_error').text('');

        $('#edit_start_time_error').text('');

        $('#edit_end_time_error').text('');
    });


    // Update AJAX
    $('#editAssignclassForm').submit(function(e) {

        e.preventDefault();

        $('#edit_subject_id_error').text('');




        $.ajax({

            url: '/admin/assignclass/update/' + $('#edit_id').val(),

            type: 'POST',

            data: {

                _token: '{{ csrf_token() }}',

                _method: 'PUT',

                teacher_id: $('#edit_teacher').val(),

                semester: $('#edit_semester').val(),

                subject_id: $('#edit_subject').val(),

                start_time: $('#edit_start_time').val(),

                end_time: $('#edit_end_time').val()
            },

            success: function(response) {

                if (!response.success) {

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
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

                    return;
                }


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


                bootstrap.Modal.getInstance(
                    document.getElementById('editAssignclassModal')
                ).hide();


                setTimeout(function() {
                    location.reload();
                }, 1500);
            },


            error: function(xhr) {

                $('#editAssignclassForm .text-danger').text('');

                if (xhr.status === 422) {

                    let errors = xhr.responseJSON.errors;

                    $.each(errors, function(key, value) {

                        if (key === 'subject_id') {
                            $('#edit_subject_id_error').text(value[0]);
                        } else {
                            $('#edit_' + key + '_error').text(value[0]);
                        }
                    });
                }
            }
        });
    });
</script>
