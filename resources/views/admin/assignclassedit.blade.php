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
        let subjects = $(this).data('subjects');

        if (typeof subjects === 'string') {
            subjects = JSON.parse(subjects);
        }

        selectedSubjects = subjects.map(Number);

        $('#edit_id').val(id);
        $('#edit_teacher').val(teacher);
        $('#edit_semester').val(semester);
        $('#teacher_name_title').text(teacherName);

        loadSubjects(semester);
    });

    // Load Subjects Function
    function loadSubjects(semester) {

        $('#editsubjectDropdown').html('<li>Loading...</li>');

        $.ajax({
            url: "/admin/assignclass/subjects/" + semester,
            type: "GET",
            success: function(data) {

                $('#editsubjectDropdown').empty();

                data.forEach(function(sub) {

                    let checked = selectedSubjects.includes(Number(sub.id)) ? 'checked' : '';

                    $('#editsubjectDropdown').append(`
                    <li>
                        <div class="form-check">
                            <input class="form-check-input edit-subject-check"
                                type="checkbox"
                                name="subject_ids[]"
                                value="${sub.id}"
                                ${checked}
                                id="sub_${sub.id}">
                            <label class="form-check-label">
                                ${sub.subject_name}
                            </label>
                        </div>
                    </li>
                `);
                });

                updateSelectedCount();
            }
        });
    }

    // Semester Change
    $(document).on('change', '#edit_semester', function() {
        let semester = $(this).val();
        selectedSubjects = [];
        loadSubjects(semester);
    });

    // Count Selected Subjects

    $(document).on('change', '.edit-subject-check', function() {
        updateSelectedCount();
    });

    function updateSelectedCount() {
        let count = $('.edit-subject-check:checked').length;

        $('#subjectBtnEdit').text(
            count > 0 ? count + ' Subjects Selected' : 'Select Subjects'
        );
    }

    // Reset Modal
    $('#editAssignclassModal').on('hidden.bs.modal', function() {

        $('#editAssignclassForm')[0].reset();
        $('#editsubjectDropdown').html('<li>Select semester first</li>');
        $('#subjectBtnEdit').text('Select Subjects');
        selectedSubjects = [];
        // / Clear validation message
        $('#edit_subject_ids_error').text('');
    });

    // Update AJAX
    $('#editAssignclassForm').submit(function(e) {
        e.preventDefault();

        // Clear old error
        $('#edit_subject_ids_error').text('');

        // No subject selected
        if ($('.edit-subject-check:checked').length === 0) {
            $('#edit_subject_ids_error').text('Please select one subject.');
            return;
        }

        // More than one subject selected
        if ($('.edit-subject-check:checked').length > 1) {
            $('#edit_subject_ids_error').text('Please select only one subject.');
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
                subject_ids: $('.edit-subject-check:checked').map(function() {
                    return $(this).val();
                }).get()
            },

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

                bootstrap.Modal.getInstance(
                    document.getElementById('editAssignclassModal')
                ).hide();

                setTimeout(() => location.reload(), 2000);
            },
            error: function(xhr) {

                $('#edit_subject_ids_error').text('');

                if (xhr.status === 422) {

                    let errors = xhr.responseJSON.errors;

                    if (errors.subject_ids) {
                        $('#edit_subject_ids_error').text(errors.subject_ids[0]);
                    }
                }
            }
        });
    });
</script>
