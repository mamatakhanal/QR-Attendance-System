<body>
    <!-- Assign Class to Teacher -->
    <div class="modal fade" id="addAssignclassModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content px-4 pt-4 rounded-4">

                <form id="assignclassForm" action="javascript:void(0);">
                    @csrf

                    <div class="modal-header">
                        <h3 class="modal-title fw-bold">Assign Subject</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">

                        <div class="col-md-12">
                            <label class="form-label">Teacher</label>
                            <select name="teacher_id" id="teacher" class="form-select">
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
                            <select name="semester" id="semester" class="form-select">
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
                                <button id="subjectBtnCreate"
                                    class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button"
                                    data-bs-toggle="dropdown"> Select Subjects </button>
                                <ul class="dropdown-menu w-100 p-3" id="subjectDropdown"
                                    style="max-height:250px; overflow-y:auto;">
                                    <li>Select semester first</li>
                                </ul>
                            </div>
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
        $('#assignclassForm').submit(function(e) {
            e.preventDefault();
            $('.text-danger').text('');
            $.ajax({
                url: "{{ route('assignclass.create') }}",
                type: "POST",
                data: $(this).serialize(),
                beforeSend: function() {
                    console.log($(this).serialize());
                },

                success: function(response) {

                    // Show Success Toast Message
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: response.success ? 'success' : 'error',
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

                    $('#assignclassForm')[0].reset();

                    $('#subjectDropdown').html(
                        '<li>Select semester first</li>'
                    );
                    $('#selectedSubjects').empty();
                    $('#subjectBtn').text('Select Subjects');

                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById('addAssignclassModal')
                    );

                    if (modal) {
                        modal.hide();
                    }
                    // Reload page after 3 seconds
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                },

                // Validation Error Response
                error: function(xhr) {
                    if (xhr.status === 422) {
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
        $('#addAssignclassModal').on('hidden.bs.modal', function() {

            $('#assignclassForm')[0].reset();
            $('#subjectDropdown').html(
                '<li>Select semester first</li>'
            );

            $('#selectedSubjects').empty();
            $('#subjectBtn').text('Select Subjects');
            $('.text-danger').text('');
        });
    </script>
</body>

<script>
    $(document).on('change', '.subject-check', function() {

        let selectedNames = [];
        $('.subject-check:checked').each(function() {
            let name = $(this).next('label').text();
            selectedNames.push(name);
        });

        if (selectedNames.length > 0) {
            $('#subjectBtnCreate').text(
                selectedNames.length + ' Subjects Selected'
            );
        } else {
            $('#subjectBtnCreate').text(
                'Select Subjects'
            );
        }
    });
</script>

<script>
    $('#semester').on('change', function() {
        let semester = $(this).val();
        let dropdown = $('#subjectDropdown');
        dropdown.html('<li>Loading...</li>');
        if (semester) {
            $.ajax({
                url: "{{ url('/admin/assignclass') }}/" + semester,
                type: "GET",
                success: function(data) {
                    dropdown.empty();
                    if (data.length > 0) {
                        $.each(data, function(key, subject) {
                            dropdown.append(`
                            <li>
                                <div class="form-check">
                                    <input 
                                    class="form-check-input subject-check"
                                    type="checkbox" 
                                    name="subject_ids[]" 
                                    value="${subject.id}" 
                                    id="subject_${subject.id}">
                                    <label class="form-check-label" for="subject_${subject.id}">
                                        ${subject.subject_name}
                                    </label>
                                </div>
                            </li>
                        `);
                        });
                    } else {
                        dropdown.html(
                            '<li>No subjects found</li>'
                        );
                    }
                }
            });
        } else {
            dropdown.html(
                '<li>Select semester first</li>'
            );
        }
    });
</script>
