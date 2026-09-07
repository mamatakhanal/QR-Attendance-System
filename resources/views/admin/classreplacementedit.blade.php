<!-- Edit Class Replacement Modal -->
<div class="modal fade" id="editClassReplacementModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content px-4 pt-4 rounded-4">

            <form id="editClassReplacementForm">

                @csrf
                @method('PUT')

                <input type="hidden" name="id" id="editReplacementId">

                <!-- Header -->
                <div class="modal-header">

                    <h3 class="modal-title fw-bold">
                        Edit Class Replacement
                    </h3>

                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>

                </div>


                <!-- Body -->
                <div class="modal-body row g-3">


                    {{-- Replacement Teacher --}}
                    <div class="col-md-12">

                        <label class="form-label">
                            Replacement Teacher
                        </label>

                        <select name="replacement_teacher_id" id="editReplacementTeacher" class="form-select" required>

                            <option value="">
                                Select Teacher
                            </option>

                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">
                                    {{ $teacher->name }}
                                </option>
                            @endforeach

                        </select>

                        <small id="edit_replacement_teacher_id_error" class="text-danger">
                        </small>

                    </div>


                    {{-- Date --}}
                    <div class="col-md-12">

                        <label class="form-label">
                            Date
                        </label>

                        <input type="date" name="date" id="editReplacementDate" class="form-control" required>

                        <small id="edit_date_error" class="text-danger">
                        </small>

                    </div>


                    {{-- Semester --}}
                    <div class="col-md-12">

                        <label class="form-label">
                            Semester
                        </label>

                        <select name="semester" id="editReplacementSemester" class="form-select" required>

                            <option value="">
                                Select Semester
                            </option>

                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}">
                                    Semester {{ $i }}
                                </option>
                            @endfor

                        </select>

                        <small id="edit_semester_error" class="text-danger">
                        </small>

                    </div>


                    {{-- Subject --}}
                    <div class="col-md-12">

                        <label class="form-label">
                            Subject
                        </label>

                        <select name="subject_id" id="editReplacementSubject" class="form-select" required>

                            <option value="">
                                Select Subject
                            </option>

                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" data-semester="{{ $subject->semester }}">
                                    {{ $subject->subject_name }}
                                </option>
                            @endforeach

                        </select>

                        <small id="edit_subject_id_error" class="text-danger">
                        </small>

                    </div>


                    {{-- Start Time --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Start Time
                        </label>

                        <input type="time" name="start_time" id="editReplacementStartTime" class="form-control"
                            min="10:00" max="17:00" required>

                        <small id="edit_start_time_error" class="text-danger">
                        </small>

                    </div>


                    {{-- End Time --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            End Time
                        </label>

                        <input type="time" name="end_time" id="editReplacementEndTime" class="form-control"
                            min="10:00" max="17:00" required>

                        <small id="edit_end_time_error" class="text-danger">
                        </small>

                    </div>

                </div>


                <!-- Footer -->
                <div class="modal-footer mt-2 mb-0">

                    <button type="submit" class="btn btn-primary">
                        Update
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>
    $(document).ready(function() {


        /*
        |--------------------------------------------------------------------------
        | GET TODAY
        |--------------------------------------------------------------------------
        */

        function getToday() {

            let today = new Date();

            let year =
                today.getFullYear();

            let month =
                String(today.getMonth() + 1)
                .padStart(2, '0');

            let day =
                String(today.getDate())
                .padStart(2, '0');

            return year + '-' + month + '-' + day;
        }


        /*
        |--------------------------------------------------------------------------
        | SET MINIMUM DATE
        |--------------------------------------------------------------------------
        */

        $('#editReplacementDate').attr(
            'min',
            getToday()
        );


        /*
        |--------------------------------------------------------------------------
        | CACHE ALL SUBJECTS
        |
        | We already loaded all subjects from Blade.
        | Therefore, we don't need AJAX when semester changes.
        |--------------------------------------------------------------------------
        */

        const allEditSubjectOptions =
            $('#editReplacementSubject option[data-semester]')
            .clone();


        /*
        |--------------------------------------------------------------------------
        | LOAD SUBJECTS BY SEMESTER
        |--------------------------------------------------------------------------
        */

        function loadEditSubjects(
            semester,
            selectedSubjectId = ''
        ) {

            let subjectDropdown =
                $('#editReplacementSubject');


            /*
            | Clear dropdown
            */

            subjectDropdown.empty();


            /*
            | No semester selected
            */

            if (!semester) {

                subjectDropdown.append(
                    '<option value="">Select Semester First</option>'
                );

                return;
            }


            /*
            | Default option
            */

            subjectDropdown.append(
                '<option value="">Select Subject</option>'
            );


            /*
            | Add subjects belonging to selected semester
            */

            allEditSubjectOptions
                .filter(function() {

                    return String(
                        $(this).data('semester')
                    ) === String(semester);

                })
                .each(function() {

                    subjectDropdown.append(
                        $(this).clone()
                    );

                });


            /*
            | Select existing subject while editing
            */

            if (selectedSubjectId) {

                subjectDropdown.val(
                    String(selectedSubjectId)
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | EDIT BUTTON
        |
        | No AJAX request.
        | Data comes directly from data-* attributes.
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'click',
            '.edit-btn',
            function() {

                let id =
                    $(this).data('id');

                let teacher =
                    $(this).data('teacher');

                let date =
                    $(this).data('date');

                let semester =
                    $(this).data('semester');

                let subjectId =
                    $(this).data('subject-id');

                let startTime =
                    $(this).data('start-time');

                let endTime =
                    $(this).data('end-time');


                /*
                |--------------------------------------------------------------------------
                | CLEAR OLD ERRORS
                |--------------------------------------------------------------------------
                */

                $('#edit_replacement_teacher_id_error').text('');

                $('#edit_semester_error').text('');

                $('#edit_subject_id_error').text('');

                $('#edit_date_error').text('');

                $('#edit_start_time_error').text('');

                $('#edit_end_time_error').text('');


                /*
                |--------------------------------------------------------------------------
                | SET ID
                |--------------------------------------------------------------------------
                */

                $('#editReplacementId').val(id);


                /*
                |--------------------------------------------------------------------------
                | SET TEACHER
                |--------------------------------------------------------------------------
                */

                $('#editReplacementTeacher').val(
                    String(teacher)
                );


                /*
                |--------------------------------------------------------------------------
                | SET DATE
                |--------------------------------------------------------------------------
                */

                $('#editReplacementDate').val(
                    date
                );


                /*
                |--------------------------------------------------------------------------
                | SET SEMESTER
                |--------------------------------------------------------------------------
                */

                $('#editReplacementSemester').val(
                    String(semester)
                );


                /*
                |--------------------------------------------------------------------------
                | SET START TIME
                |--------------------------------------------------------------------------
                |
                | Converts:
                | 10:00:00
                |
                | to:
                | 10:00
                |--------------------------------------------------------------------------
                */

                $('#editReplacementStartTime').val(

                    startTime ?
                    String(startTime).substring(0, 5) :
                    ''

                );


                /*
                |--------------------------------------------------------------------------
                | SET END TIME
                |--------------------------------------------------------------------------
                */

                $('#editReplacementEndTime').val(

                    endTime ?
                    String(endTime).substring(0, 5) :
                    ''

                );


                /*
                |--------------------------------------------------------------------------
                | LOAD EXISTING SUBJECT
                |--------------------------------------------------------------------------
                */

                loadEditSubjects(
                    semester,
                    subjectId
                );


                /*
                |--------------------------------------------------------------------------
                | SET UPDATE FORM ACTION
                |--------------------------------------------------------------------------
                */

                $('#editClassReplacementForm').attr(
                    'action',
                    "{{ url('/admin/classreplacement/update') }}/" + id
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | SEMESTER CHANGE
        |
        | No AJAX.
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'change',
            '#editReplacementSemester',
            function() {

                let semester =
                    $(this).val();


                /*
                | Clear subject error
                */

                $('#edit_subject_id_error').text('');


                /*
                | Reload subjects instantly
                */

                loadEditSubjects(
                    semester
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | SUBJECT CHANGE
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'change',
            '#editReplacementSubject',
            function() {

                $('#edit_subject_id_error').text('');

            }
        );


        /*
        |--------------------------------------------------------------------------
        | DATE VALIDATION
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'change',
            '#editReplacementDate',
            function() {

                let selectedDate =
                    $(this).val();


                $('#edit_date_error').text('');


                if (!selectedDate) {
                    return;
                }


                if (selectedDate < getToday()) {

                    $('#edit_date_error').text(
                        'Replacement date cannot be before today.'
                    );

                    $(this).val('');

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | START TIME VALIDATION
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'change',
            '#editReplacementStartTime',
            function() {

                let startTime =
                    $(this).val();


                $('#edit_start_time_error').text('');


                if (!startTime) {
                    return;
                }


                /*
                | Start time must be between 10 AM and 5 PM
                */

                if (
                    startTime < '10:00' ||
                    startTime > '17:00'
                ) {

                    $('#edit_start_time_error').text(
                        'Start time must be between 10:00 AM and 5:00 PM.'
                    );

                    $(this).val('');

                    return;
                }


                /*
                | Today cannot use past time
                */

                let selectedDate =
                    $('#editReplacementDate').val();


                if (selectedDate === getToday()) {

                    let now =
                        new Date();


                    let currentHour =
                        String(
                            now.getHours()
                        ).padStart(2, '0');


                    let currentMinute =
                        String(
                            now.getMinutes()
                        ).padStart(2, '0');


                    let currentTime =
                        currentHour + ':' + currentMinute;


                    if (startTime < currentTime) {

                        $('#edit_start_time_error').text(
                            'For today, start time cannot be earlier than the current time.'
                        );

                        $(this).val('');

                        return;
                    }

                }


                /*
                | Check existing end time
                */

                let endTime =
                    $('#editReplacementEndTime').val();


                if (
                    endTime &&
                    endTime <= startTime
                ) {

                    $('#edit_end_time_error').text(
                        'End time must be after start time.'
                    );

                    $('#editReplacementEndTime').val('');

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | END TIME VALIDATION
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'change',
            '#editReplacementEndTime',
            function() {

                let endTime =
                    $(this).val();


                let startTime =
                    $('#editReplacementStartTime').val();


                $('#edit_end_time_error').text('');


                if (!endTime) {
                    return;
                }


                /*
                | End time must be between 10 AM and 5 PM
                */

                if (
                    endTime < '10:00' ||
                    endTime > '17:00'
                ) {

                    $('#edit_end_time_error').text(
                        'End time must be between 10:00 AM and 5:00 PM.'
                    );

                    $(this).val('');

                    return;
                }


                /*
                | End time must be after start time
                */

                if (
                    startTime &&
                    endTime <= startTime
                ) {

                    $('#edit_end_time_error').text(
                        'End time must be after start time.'
                    );

                    $(this).val('');

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | SUBMIT EDIT FORM
        |--------------------------------------------------------------------------
        */

        $('#editClassReplacementForm').on(
            'submit',
            function(e) {

                e.preventDefault();


                let form =
                    $(this);


                /*
                |--------------------------------------------------------------------------
                | CLEAR ERRORS
                |--------------------------------------------------------------------------
                */

                $('#edit_replacement_teacher_id_error').text('');

                $('#edit_semester_error').text('');

                $('#edit_subject_id_error').text('');

                $('#edit_date_error').text('');

                $('#edit_start_time_error').text('');

                $('#edit_end_time_error').text('');


                /*
                |--------------------------------------------------------------------------
                | GET FORM VALUES
                |--------------------------------------------------------------------------
                */

                let teacher =
                    $('#editReplacementTeacher').val();

                let semester =
                    $('#editReplacementSemester').val();

                let subjectId =
                    $('#editReplacementSubject').val();

                let date =
                    $('#editReplacementDate').val();

                let startTime =
                    $('#editReplacementStartTime').val();

                let endTime =
                    $('#editReplacementEndTime').val();


                /*
                |--------------------------------------------------------------------------
                | TEACHER VALIDATION
                |--------------------------------------------------------------------------
                */

                if (!teacher) {

                    $('#edit_replacement_teacher_id_error').text(
                        'Please select a replacement teacher.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | SEMESTER VALIDATION
                |--------------------------------------------------------------------------
                */

                if (!semester) {

                    $('#edit_semester_error').text(
                        'Please select a semester.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | SUBJECT VALIDATION
                |--------------------------------------------------------------------------
                */

                if (!subjectId) {

                    $('#edit_subject_id_error').text(
                        'Please select a subject.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | DATE VALIDATION
                |--------------------------------------------------------------------------
                */

                if (!date) {

                    $('#edit_date_error').text(
                        'Please select replacement date.'
                    );

                    return;
                }


                if (date < getToday()) {

                    $('#edit_date_error').text(
                        'Replacement date cannot be before today.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | START TIME VALIDATION
                |--------------------------------------------------------------------------
                */

                if (!startTime) {

                    $('#edit_start_time_error').text(
                        'Please select start time.'
                    );

                    return;
                }


                if (
                    startTime < '10:00' ||
                    startTime > '17:00'
                ) {

                    $('#edit_start_time_error').text(
                        'Start time must be between 10:00 AM and 5:00 PM.'
                    );

                    return;
                }


                /*
                | Today cannot use past start time
                */

                if (date === getToday()) {

                    let now =
                        new Date();


                    let currentHour =
                        String(
                            now.getHours()
                        ).padStart(2, '0');


                    let currentMinute =
                        String(
                            now.getMinutes()
                        ).padStart(2, '0');


                    let currentTime =
                        currentHour + ':' + currentMinute;


                    if (startTime < currentTime) {

                        $('#edit_start_time_error').text(
                            'For today, start time cannot be earlier than the current time.'
                        );

                        return;
                    }

                }


                /*
                |--------------------------------------------------------------------------
                | END TIME VALIDATION
                |--------------------------------------------------------------------------
                */

                if (!endTime) {

                    $('#edit_end_time_error').text(
                        'Please select end time.'
                    );

                    return;
                }


                if (
                    endTime < '10:00' ||
                    endTime > '17:00'
                ) {

                    $('#edit_end_time_error').text(
                        'End time must be between 10:00 AM and 5:00 PM.'
                    );

                    return;
                }


                if (endTime <= startTime) {

                    $('#edit_end_time_error').text(
                        'End time must be after start time.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | FORM DATA
                |--------------------------------------------------------------------------
                */

                let formData =
                    new FormData(this);


                /*
                |--------------------------------------------------------------------------
                | SUBMIT BUTTON
                |--------------------------------------------------------------------------
                */

                let submitButton =
                    form.find(
                        'button[type="submit"]'
                    );


                submitButton.prop(
                    'disabled',
                    true
                );


                /*
                |--------------------------------------------------------------------------
                | AJAX UPDATE
                |--------------------------------------------------------------------------
                */

                $.ajax({

                    url: form.attr('action'),

                    type: 'POST',

                    data: formData,

                    processData: false,

                    contentType: false,


                    /*
                    |--------------------------------------------------------------------------
                    | SUCCESS
                    |--------------------------------------------------------------------------
                    */

                    success: function(response) {

                        if (!response.success) {

                            Swal.fire({

                                toast: true,

                                position: 'top-end',

                                icon: 'error',

                                title: response.message ||
                                    'Unable to update class replacement.',

                                showConfirmButton: false,

                                timer: 3000,

                                customClass: {
                                    popup: 'small-toast'
                                }

                            });

                            return;
                        }


                        /*
                        | Success message
                        */

                        Swal.fire({

                            toast: true,

                            position: 'top-end',

                            icon: 'success',

                            title: response.message ||
                                'Class replacement updated successfully.',

                            showConfirmButton: false,

                            timer: 1500,

                            customClass: {
                                popup: 'small-toast'
                            }

                        });


                        /*
                        | Close modal
                        */

                        let modalElement =
                            document.getElementById(
                                'editClassReplacementModal'
                            );


                        let modal =
                            bootstrap.Modal.getInstance(
                                modalElement
                            );


                        if (modal) {

                            modal.hide();

                        }


                        /*
                        | Reload table/page
                        */

                        setTimeout(
                            function() {

                                location.reload();

                            },
                            1500
                        );

                    },


                    /*
                    |--------------------------------------------------------------------------
                    | ERROR
                    |--------------------------------------------------------------------------
                    */

                    error: function(xhr) {

                        console.log(
                            'STATUS:',
                            xhr.status
                        );

                        console.log(
                            'RESPONSE:',
                            xhr.responseText
                        );


                        /*
                        | Laravel validation error
                        */

                        if (xhr.status === 422) {

                            let errors =
                                xhr.responseJSON?.errors;


                            if (errors) {

                                $.each(
                                    errors,
                                    function(key, value) {


                                        /*
                                        | Teacher
                                        */

                                        if (
                                            key ===
                                            'replacement_teacher_id'
                                        ) {

                                            $(
                                                '#edit_replacement_teacher_id_error'
                                            ).text(
                                                value[0]
                                            );

                                        }


                                        /*
                                        | Semester
                                        */

                                        if (
                                            key ===
                                            'semester'
                                        ) {

                                            $(
                                                '#edit_semester_error'
                                            ).text(
                                                value[0]
                                            );

                                        }


                                        /*
                                        | Subject
                                        */

                                        if (
                                            key ===
                                            'subject_id'
                                        ) {

                                            $(
                                                '#edit_subject_id_error'
                                            ).text(
                                                value[0]
                                            );

                                        }


                                        /*
                                        | Date
                                        */

                                        if (
                                            key ===
                                            'date'
                                        ) {

                                            $(
                                                '#edit_date_error'
                                            ).text(
                                                value[0]
                                            );

                                        }


                                        /*
                                        | Start Time
                                        */

                                        if (
                                            key ===
                                            'start_time'
                                        ) {

                                            $(
                                                '#edit_start_time_error'
                                            ).text(
                                                value[0]
                                            );

                                        }


                                        /*
                                        | End Time
                                        */

                                        if (
                                            key ===
                                            'end_time'
                                        ) {

                                            $(
                                                '#edit_end_time_error'
                                            ).text(
                                                value[0]
                                            );

                                        }

                                    }
                                );

                            }


                            /*
                            | General 422 message
                            */

                            let message =
                                xhr.responseJSON?.message;


                            if (message) {

                                Swal.fire({

                                    toast: true,

                                    position: 'top-end',

                                    icon: 'error',

                                    title: message,

                                    showConfirmButton: false,

                                    timer: 3000,

                                    customClass: {
                                        popup: 'small-toast'
                                    }

                                });

                            }


                            return;
                        }


                        /*
                        | Other errors
                        */

                        Swal.fire({

                            toast: true,

                            position: 'top-end',

                            icon: 'error',

                            title: xhr.responseJSON?.message ||
                                'Something went wrong.',

                            showConfirmButton: false,

                            timer: 3000,

                            customClass: {
                                popup: 'small-toast'
                            }

                        });

                    },


                    /*
                    |--------------------------------------------------------------------------
                    | COMPLETE
                    |--------------------------------------------------------------------------
                    */

                    complete: function() {

                        submitButton.prop(
                            'disabled',
                            false
                        );

                    }

                });

            }
        );


        /*
        |--------------------------------------------------------------------------
        | RESET MODAL AFTER CLOSE
        |--------------------------------------------------------------------------
        */

        $('#editClassReplacementModal').on(
            'hidden.bs.modal',
            function() {

                /*
                | Reset form
                */

                $('#editClassReplacementForm')[0].reset();


                /*
                | Reset subject dropdown
                */

                $('#editReplacementSubject').html(
                    '<option value="">Select Semester First</option>'
                );


                /*
                | Clear errors
                */

                $('#edit_replacement_teacher_id_error').text('');

                $('#edit_semester_error').text('');

                $('#edit_subject_id_error').text('');

                $('#edit_date_error').text('');

                $('#edit_start_time_error').text('');

                $('#edit_end_time_error').text('');

            }
        );


    });
</script>
