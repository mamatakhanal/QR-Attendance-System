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
                            <div class="dropdown">
                                <button id="subjectBtnEdit"
                                    class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button"
                                    data-bs-toggle="dropdown"> Select Subjects </button>
                                <ul class="dropdown-menu w-100 p-3" id="editsubjectDropdown"
                                    style="max-height:250px; overflow-y:auto;">
                                    <li>Select semester first</li>
                                </ul>
                            </div>
                            {{-- Selected subject ids --}}
                            <div id="selectedSubjects"></div>
                            <small id="edit_subject_ids_error" class="text-danger"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Start Time</label>
                            <input type="time" name="start_time" id="edit_start_time" class="form-control" required>
                            <small id="edit_start_time_error" class="text-danger"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">End Time</label>
                            <input type="time" name="end_time" id="edit_end_time" class="form-control" required>
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
    let selectedSubjects = [];

    // Open Edit Modal
    $(document).on('click', '.edit-btn', function() {

        let id = $(this).data('id');
        let teacher = $(this).data('teacher');
        let semester = $(this).data('semester');
        let teacherName = $(this).data('teacher-name');
        let subjects = $(this).attr('data-subjects');

        // Get existing time
        let startTime = $(this).data('start-time');
        let endTime = $(this).data('end-time');

        // Convert subject IDs to array
        try {
            selectedSubjects = JSON.parse(subjects).map(Number);
        } catch (e) {
            selectedSubjects = [];
        }

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
        loadSubjects(semester);
    });


    // Load Subjects Function
    function loadSubjects(semester) {

        let dropdown = $('#editsubjectDropdown');

        dropdown.html('<li class="text-muted">Loading...</li>');

        if (!semester) {
            dropdown.html('<li>Select semester first</li>');
            return;
        }

        $.ajax({
            url: "{{ url('/admin/assignclass/subjects') }}/" + semester,
            type: "GET",

            success: function(data) {

                dropdown.empty();

                if (data.length === 0) {
                    dropdown.html('<li class="text-muted">No subjects found</li>');
                    updateSelectedCount();
                    return;
                }

                data.forEach(function(sub) {

                    let subjectId = Number(sub.id);

                    let checked = selectedSubjects.includes(subjectId) ?
                        'checked' :
                        '';

                    dropdown.append(`
                        <li>
                            <div class="form-check">
                                <input
                                    class="form-check-input edit-subject-check"
                                    type="checkbox"
                                    name="subject_ids[]"
                                    value="${subjectId}"
                                    id="edit_subject_${subjectId}"
                                    ${checked}>

                                <label
                                    class="form-check-label"
                                    for="edit_subject_${subjectId}">
                                    ${sub.subject_name}
                                </label>
                            </div>
                        </li>
                    `);
                });

                updateSelectedCount();
            },

            error: function() {
                dropdown.html(
                    '<li class="text-danger">Unable to load subjects</li>'
                );
            }
        });
    }


    // Semester Change
    $(document).on('change', '#edit_semester', function() {

        let semester = $(this).val();

        // Clear old subject
        selectedSubjects = [];

        $('#editsubjectDropdown').html(
            '<li>Selecting subjects...</li>'
        );

        if (semester) {
            loadSubjects(semester);
        } else {
            $('#editsubjectDropdown').html(
                '<li>Select semester first</li>'
            );
        }

        updateSelectedCount();
    });


    // Subject checkbox change
    $(document).on('change', '.edit-subject-check', function() {

        let checkedSubjects = $('.edit-subject-check:checked')
            .map(function() {
                return Number($(this).val());
            })
            .get();

        selectedSubjects = checkedSubjects;

        updateSelectedCount();

        $('#edit_subject_ids_error').text('');
    });


    // Update selected subject button
    function updateSelectedCount() {

        let checked = $('.edit-subject-check:checked');

        if (checked.length > 0) {

            let names = [];

            checked.each(function() {
                names.push(
                    $(this).closest('.form-check')
                    .find('label')
                    .text()
                    .trim()
                );
            });

            $('#subjectBtnEdit').text(names.join(', '));

        } else {

            $('#subjectBtnEdit').text('Select Subject');
        }
    }


    // Reset modal
    $('#editAssignclassModal').on('hidden.bs.modal', function() {

        $('#editAssignclassForm')[0].reset();

        $('#edit_id').val('');

        $('#teacher_name_title').text('');

        $('#editsubjectDropdown').html(
            '<li>Select semester first</li>'
        );

        $('#subjectBtnEdit').text('Select Subject');

        $('#edit_subject_ids_error').text('');

        selectedSubjects = [];
    });


    // Update AJAX
    $('#editAssignclassForm').submit(function(e) {

        e.preventDefault();

        $('#edit_subject_ids_error').text('');

        let checkedSubjects = $('.edit-subject-check:checked')
            .map(function() {
                return $(this).val();
            })
            .get();


        // No subject
        if (checkedSubjects.length === 0) {

            $('#edit_subject_ids_error')
                .text('Please select one subject.');

            return;
        }


        // More than one subject
        if (checkedSubjects.length > 1) {

            $('#edit_subject_ids_error')
                .text('Please select only one subject.');

            return;
        }


        $.ajax({

            url: '/admin/assignclass/update/' + $('#edit_id').val(),

            type: 'POST',

            data: {

                _token: '{{ csrf_token() }}',

                _method: 'PUT',

                teacher_id: $('#edit_teacher').val(),

                semester: $('#edit_semester').val(),

                subject_ids: checkedSubjects,

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

                    return;
                }


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

                        if (key === 'subject_ids') {

                            $('#edit_subject_ids_error')
                                .text(value[0]);

                        } else {

                            $('#edit_' + key + '_error')
                                .text(value[0]);
                        }
                    });
                }
            }
        });
    });
</script>
