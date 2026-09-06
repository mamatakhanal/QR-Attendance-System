<!-- Edit Class Replacement Modal -->
<div class="modal fade" id="editClassReplacementModal" tabindex="-1" aria-labelledby="editClassReplacementModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content px-4 pt-4 rounded-4">

            <form id="editClassReplacementForm" method="POST">

                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h3 class="modal-title fw-bold" id="editClassReplacementModalLabel">
                        Edit Class Replacement
                    </h3>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>


                <div class="modal-body row g-3">

                    <!-- Replacement Teacher -->
                    <div class="col-md-6">

                        <label class="form-label">
                            Teacher
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


                    <!-- Date -->
                    <div class="col-md-6">

                        <label class="form-label">
                            Date
                        </label>

                        <input type="date" name="date" id="editReplacementDate" class="form-control" required>

                        <small id="edit_date_error" class="text-danger">
                        </small>

                    </div>


                    <!-- Semester -->
                    <div class="col-md-6">

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


                    <!-- Subject -->
                    <div class="col-md-6">

                        <label class="form-label">
                            Subject
                        </label>

                        <select name="subject_id" id="editReplacementSubject" class="form-select" required>

                            <option value="">
                                Select Semester First
                            </option>

                        </select>

                        <small id="edit_subject_id_error" class="text-danger">
                        </small>

                    </div>


                    <!-- Start Time -->
                    <div class="col-md-6">

                        <label class="form-label">
                            Start Time
                        </label>

                        <input type="time" name="start_time" id="editReplacementStartTime" class="form-control"
                            min="10:00" max="17:00" required>

                        <small id="edit_start_time_error" class="text-danger">
                        </small>

                    </div>


                    <!-- End Time -->
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


                <div class="modal-footer mt-3 mb-0">
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
        | Get Today
        |--------------------------------------------------------------------------
        */

        function getToday() {

            let today = new Date();

            let year = today.getFullYear();

            let month = String(
                today.getMonth() + 1
            ).padStart(2, '0');

            let day = String(
                today.getDate()
            ).padStart(2, '0');

            return year + '-' + month + '-' + day;
        }


        /*
        |--------------------------------------------------------------------------
        | Set Minimum Date
        |--------------------------------------------------------------------------
        */

        $('#editReplacementDate').attr(
            'min',
            getToday()
        );


        /*
        |--------------------------------------------------------------------------
        | Load Subjects According To Semester
        |--------------------------------------------------------------------------
        */

        $('#editReplacementSemester').on(
            'change',
            function() {

                let semester = $(this).val();

                let subjectDropdown =
                    $('#editReplacementSubject');


                subjectDropdown.html(
                    '<option value="">Loading subjects...</option>'
                );


                $('#edit_subject_id_error').text('');


                if (!semester) {

                    subjectDropdown.html(
                        '<option value="">Select Semester First</option>'
                    );

                    return;
                }


                $.ajax({

                    url: "{{ url('/admin/assignclass/subjects') }}/" +
                        semester,

                    type: "GET",

                    success: function(data) {

                        subjectDropdown.empty();

                        subjectDropdown.append(
                            '<option value="">Select Subject</option>'
                        );


                        $.each(
                            data,
                            function(index, item) {

                                subjectDropdown.append(

                                    '<option value="' +
                                    item.subject_id +
                                    '">' +
                                    item.subject_name +
                                    '</option>'

                                );

                            }
                        );

                    },

                    error: function() {

                        subjectDropdown.html(
                            '<option value="">Unable to load subjects</option>'
                        );

                    }

                });

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Clear Subject Error
        |--------------------------------------------------------------------------
        */

        $('#editReplacementSubject').on(
            'change',
            function() {

                $('#edit_subject_id_error').text('');

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Date Validation
        |--------------------------------------------------------------------------
        */

        $('#editReplacementDate').on(
            'change',
            function() {

                let selectedDate = $(this).val();

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
        | Start Time Validation
        |--------------------------------------------------------------------------
        */

        $('#editReplacementStartTime').on(
            'change',
            function() {

                let startTime = $(this).val();

                $('#edit_start_time_error').text('');


                if (!startTime) {

                    return;

                }


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


                let selectedDate =
                    $('#editReplacementDate').val();


                /*
                |--------------------------------------------------------------
                | Today Cannot Use Past Time
                |--------------------------------------------------------------
                */

                if (selectedDate === getToday()) {

                    let now = new Date();

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
                |--------------------------------------------------------------
                | Check End Time
                |--------------------------------------------------------------
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
        | End Time Validation
        |--------------------------------------------------------------------------
        */

        $('#editReplacementEndTime').on(
            'change',
            function() {

                let endTime = $(this).val();

                let startTime =
                    $('#editReplacementStartTime').val();


                $('#edit_end_time_error').text('');


                if (!endTime) {

                    return;

                }


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
        | Open Edit Modal
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'click',
            '.edit-btn',
            function() {

                const id = $(this).data('id');


                /*
                |--------------------------------------------------------------
                | Clear Previous Errors
                |--------------------------------------------------------------
                */

                $('#edit_replacement_teacher_id_error').text('');
                $('#edit_semester_error').text('');
                $('#edit_subject_id_error').text('');
                $('#edit_date_error').text('');
                $('#edit_start_time_error').text('');
                $('#edit_end_time_error').text('');


                /*
                |--------------------------------------------------------------
                | Load Replacement Data
                |--------------------------------------------------------------
                */

                $.ajax({

                    url: "{{ url('/admin/classreplacement') }}/" +
                        id +
                        "/edit",

                    type: 'GET',

                    success: function(response) {


                        /*
                        |------------------------------------------------------
                        | Teacher
                        |------------------------------------------------------
                        */

                        $('#editReplacementTeacher').val(
                            response.replacement_teacher_id
                        );


                        /*
                        |------------------------------------------------------
                        | Date
                        |------------------------------------------------------
                        */

                        $('#editReplacementDate').val(
                            response.date
                        );


                        /*
                        |------------------------------------------------------
                        | Semester
                        |------------------------------------------------------
                        */

                        $('#editReplacementSemester').val(
                            response.semester
                        );


                        /*
                        |------------------------------------------------------
                        | Load Subjects
                        |------------------------------------------------------
                        */

                        let semester =
                            response.semester;


                        let subjectDropdown =
                            $('#editReplacementSubject');


                        subjectDropdown.html(
                            '<option value="">Loading subjects...</option>'
                        );


                        $.ajax({

                            url: "{{ url('/admin/assignclass/subjects') }}/" +
                                semester,

                            type: 'GET',

                            success: function(data) {

                                subjectDropdown.empty();

                                subjectDropdown.append(
                                    '<option value="">Select Subject</option>'
                                );


                                $.each(
                                    data,
                                    function(index, item) {

                                        subjectDropdown.append(

                                            '<option value="' +
                                            item.subject_id +
                                            '">' +
                                            item.subject_name +
                                            '</option>'

                                        );

                                    }
                                );


                                /*
                                |----------------------------------------------
                                | Select Existing Subject
                                |----------------------------------------------
                                */

                                subjectDropdown.val(
                                    response.subject_id
                                );

                            },

                            error: function() {

                                subjectDropdown.html(
                                    '<option value="">Unable to load subjects</option>'
                                );

                            }

                        });


                        /*
                        |------------------------------------------------------
                        | Start Time
                        |------------------------------------------------------
                        */

                        $('#editReplacementStartTime').val(
                            response.start_time
                        );


                        /*
                        |------------------------------------------------------
                        | End Time
                        |------------------------------------------------------
                        */

                        $('#editReplacementEndTime').val(
                            response.end_time
                        );


                        /*
                        |------------------------------------------------------
                        | Form Action
                        |------------------------------------------------------
                        */

                        $('#editClassReplacementForm').attr(

                            'action',

                            "{{ url('/admin/classreplacement/update') }}/" +
                            id

                        );


                        /*
                        |------------------------------------------------------
                        | Open Modal
                        |------------------------------------------------------
                        */

                        let modalElement =
                            document.getElementById(
                                'editClassReplacementModal'
                            );


                        let modal =
                            bootstrap.Modal.getOrCreateInstance(
                                modalElement
                            );


                        modal.show();

                    },


                    error: function(xhr) {

                        console.log(
                            xhr.responseText
                        );


                        Swal.fire({

                            icon: 'error',

                            title: 'Error',

                            text: 'Unable to load class replacement.'

                        });

                    }

                });

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Submit Edit Form
        |--------------------------------------------------------------------------
        */

        $('#editClassReplacementForm').on(
            'submit',
            function(e) {

                e.preventDefault();


                let form = $(this);


                /*
                |--------------------------------------------------------------
                | Clear Errors
                |--------------------------------------------------------------
                */

                $('#edit_replacement_teacher_id_error').text('');
                $('#edit_semester_error').text('');
                $('#edit_subject_id_error').text('');
                $('#edit_date_error').text('');
                $('#edit_start_time_error').text('');
                $('#edit_end_time_error').text('');


                /*
                |--------------------------------------------------------------
                | Get Values
                |--------------------------------------------------------------
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
                |--------------------------------------------------------------
                | Teacher
                |--------------------------------------------------------------
                */

                if (!teacher) {

                    $('#edit_replacement_teacher_id_error').text(
                        'Please select a replacement teacher.'
                    );

                    return;

                }


                /*
                |--------------------------------------------------------------
                | Semester
                |--------------------------------------------------------------
                */

                if (!semester) {

                    $('#edit_semester_error').text(
                        'Please select a semester.'
                    );

                    return;

                }


                /*
                |--------------------------------------------------------------
                | Subject
                |--------------------------------------------------------------
                */

                if (!subjectId) {

                    $('#edit_subject_id_error').text(
                        'Please select a subject.'
                    );

                    return;

                }


                /*
                |--------------------------------------------------------------
                | Date
                |--------------------------------------------------------------
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
                |--------------------------------------------------------------
                | Start Time
                |--------------------------------------------------------------
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
                |--------------------------------------------------------------
                | Today Current Time Validation
                |--------------------------------------------------------------
                */

                if (date === getToday()) {

                    let now = new Date();

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
                |--------------------------------------------------------------
                | End Time
                |--------------------------------------------------------------
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
                |--------------------------------------------------------------
                | Submit
                |--------------------------------------------------------------
                */

                let formData =
                    new FormData(this);


                let submitButton =
                    form.find(
                        'button[type="submit"]'
                    );


                submitButton.prop(
                    'disabled',
                    true
                );


                $.ajax({

                    url: form.attr('action'),

                    type: 'POST',

                    data: formData,

                    processData: false,

                    contentType: false,


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
                        |------------------------------------------------------
                        | Close Modal
                        |------------------------------------------------------
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
                        |------------------------------------------------------
                        | Reload Table
                        |------------------------------------------------------
                        */

                        setTimeout(
                            function() {

                                location.reload();

                            },
                            1500
                        );

                    },


                    error: function(xhr) {

                        console.log(
                            'STATUS:',
                            xhr.status
                        );

                        console.log(
                            'RESPONSE:',
                            xhr.responseText
                        );

                        console.log(
                            'JSON:',
                            xhr.responseJSON
                        );


                        if (xhr.status === 422) {

                            let errors =
                                xhr.responseJSON?.errors;


                            if (errors) {

                                $.each(
                                    errors,
                                    function(key, value) {


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


                                        if (
                                            key === 'semester'
                                        ) {

                                            $(
                                                '#edit_semester_error'
                                            ).text(
                                                value[0]
                                            );

                                        }


                                        if (
                                            key === 'subject_id'
                                        ) {

                                            $(
                                                '#edit_subject_id_error'
                                            ).text(
                                                value[0]
                                            );

                                        }


                                        if (
                                            key === 'date'
                                        ) {

                                            $(
                                                '#edit_date_error'
                                            ).text(
                                                value[0]
                                            );

                                        }


                                        if (
                                            key === 'start_time'
                                        ) {

                                            $(
                                                '#edit_start_time_error'
                                            ).text(
                                                value[0]
                                            );

                                        }


                                        if (
                                            key === 'end_time'
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
        | Reset Edit Modal
        |--------------------------------------------------------------------------
        */

        $('#editClassReplacementModal').on(
            'hidden.bs.modal',
            function() {

                $('#editClassReplacementForm')[0].reset();

                $('#editReplacementSubject').html(
                    '<option value="">Select Semester First</option>'
                );


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
