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
                        </div>
                    </div>

                    <div class="modal-footer mt-2 mb-0">
                        <button type="submit" class="btn btn-primary"> Update </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).on('click', '.edit-btn', function() {

            let id = $(this).data('id');
            let teacher = $(this).data('teacher');
            let teacherName = $(this).data('teacher-name');
            let semester = $(this).data('semester');
            let subject = $(this).data('subject');

            $('#edit_id').val(id);
            $('#edit_teacher').val(teacher);
            $('#edit_semester').val(semester);
            $('#teacher_name_title').text(teacherName);

            $('#editsubjectDropdown').html('<li>Loading...</li>');

            $.ajax({
                url: "/admin/assignclass/" + semester,
                type: "GET",

                success: function(data) {

                    $('#editsubjectDropdown').empty();

                    data.forEach(function(sub) {

                        let checked = sub.id == subject ? 'checked' : '';

                        $('#editsubjectDropdown').append(`

                                <li>
                                <div class="form-check">

                                    <input 
                                    class="form-check-input subject-check"
                                    type="checkbox" name="subject_ids[]"
                                    value="${sub.id}" ${checked} id="sub_${sub.id}">

                                    <label class="form-check-label">
                                    ${sub.subject_name}
                                    </label>

                                </div>
                                </li>
                            `);
                    });
                }
            });
        });
    </script>

    {{-- Get Subject List --}}
    <script>
        $(document).on('change', '.subject-check', function() {

            let count = $('.subject-check:checked').length;
            if (count > 0) {
                $('#subjectBtnEdit').text(count + ' Subjects Selected');
            } else {
                $('#subjectBtnEdit').text('Select Subjects');
            }
        });
    </script>

    <script>
        $('#editAssignclassForm').submit(function(e) {

            e.preventDefault();

            let id = $('#edit_id').val();
            // Clear old validation errors
            $('.text-danger').text('');
            $('#teacher_name_title').text($(this).data('teacher_name'));

            $.ajax({
                url: '/admin/assignclass/update/' + id,
                type: 'POST',
                data: $(this).serialize() + '&_method=PUT',

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
                        document.getElementById('editAssignclassModal')
                    ).hide();

                    // Reload page after 3 seconds
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },

                // Validation Error Response
                error: function(xhr) {
                    if (xhr.status == 422) {

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
