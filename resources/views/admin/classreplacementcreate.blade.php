<div class="modal fade" id="createClassReplacementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content px-4 pt-4 rounded-4">

            <form id="createClassReplacementForm" action="{{ route('admin.classreplacement.store') }}" method="POST">

                @csrf

                <div class="modal-header">
                    <h3 class="modal-title fw-bold">Create Class Replacement</h3>

                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body row g-3">

                    {{-- Replacement Teacher --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Teacher
                        </label>

                        <select name="replacement_teacher_id" id="replacementTeacher" class="form-select" required>

                            <option value="">
                                Select Teacher
                            </option>

                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">
                                    {{ $teacher->name }}
                                </option>
                            @endforeach

                        </select>

                        <small id="replacement_teacher_id_error" class="text-danger">
                        </small>
                    </div>

                    {{-- Date --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Date
                        </label>

                        <input type="date" name="date" id="replacementDate" class="form-control" required>

                        <small id="date_error" class="text-danger">
                        </small>
                    </div>


                    {{-- Semester --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Semester
                        </label>

                        <select name="semester" id="replacementSemester" class="form-select" required>

                            <option value="">
                                Select Semester
                            </option>

                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}">
                                    Semester {{ $i }}
                                </option>
                            @endfor

                        </select>

                        <small id="semester_error" class="text-danger">
                        </small>
                    </div>


                    <div class="col-md-6">
                        <label class="form-label">
                            Subject
                        </label>

                        <select name="subject_id" id="replacementAssignClass" class="form-select" required>

                            <option value="">
                                Select Semester First
                            </option>

                        </select>

                        <small id="subject_id_error" class="text-danger"></small>
                    </div>


                    {{-- Start Time --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Start Time
                        </label>

                        <input type="time" name="start_time" id="replacementStartTime" class="form-control"
                            min="10:00" max="17:00" required>

                        <small id="start_time_error" class="text-danger">
                        </small>

                    </div>


                    {{-- End Time --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            End Time
                        </label>

                        <input type="time" name="end_time" id="replacementEndTime" class="form-control"
                            min="10:00" max="17:00" required>

                        <small id="end_time_error" class="text-danger">
                        </small>

                    </div>

                </div>


                <div class="modal-footer mt-3 mb-0">

                    <button type="submit" class="btn btn-success">

                        Save

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
        | Today's date
        |--------------------------------------------------------------------------
        */

        function getToday() {

            let today = new Date();

            let year = today.getFullYear();
            let month = String(today.getMonth() + 1).padStart(2, '0');
            let day = String(today.getDate()).padStart(2, '0');

            return year + '-' + month + '-' + day;
        }

        let today = getToday();

        $('#replacementDate').attr('min', today);


        /*
        |--------------------------------------------------------------------------
        | Load subjects when semester changes
        |--------------------------------------------------------------------------
        */

        $('#replacementSemester').on('change', function() {

            let semester = $(this).val();

            let subjectDropdown = $('#replacementAssignClass');

            subjectDropdown.html(
                '<option value="">Loading subjects...</option>'
            );

            $('#subject_id_error').text('');

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

                    subjectDropdown.append(
                        '<option value="">Select Subject</option>'
                    );

                    $.each(data, function(index, item) {

                        subjectDropdown.append(
                            '<option value="' +
                            item.subject_id +
                            '">' +
                            item.subject_name +
                            '</option>'
                        );

                    });

                },

                error: function() {

                    subjectDropdown.html(
                        '<option value="">Unable to load subjects</option>'
                    );

                }

            });

        });


        /*
        |--------------------------------------------------------------------------
        | Clear subject error
        |--------------------------------------------------------------------------
        */

        $('#replacementAssignClass').on('change', function() {

            $('#subject_id_error').text('');

        });


        /*
        |--------------------------------------------------------------------------
        | Date validation
        |--------------------------------------------------------------------------
        */

        $('#replacementDate').on('change', function() {

            let selectedDate = $(this).val();

            $('#date_error').text('');

            if (!selectedDate) {
                return;
            }

            if (selectedDate < getToday()) {

                $('#date_error').text(
                    'Replacement date cannot be before today.'
                );

                $(this).val('');

            }

        });


        /*
        |--------------------------------------------------------------------------
        | Start time validation
        |--------------------------------------------------------------------------
        */

        $('#replacementStartTime').on('change', function() {

            let startTime = $(this).val();

            $('#start_time_error').text('');

            if (!startTime) {
                return;
            }

            /*
            | Time must be between 10:00 AM and 5:00 PM
            */

            if (
                startTime < '10:00' ||
                startTime > '17:00'
            ) {

                $('#start_time_error').text(
                    'Start time must be between 10:00 AM and 5:00 PM.'
                );

                $(this).val('');

                return;
            }


            let endTime = $('#replacementEndTime').val();

            if (
                endTime &&
                endTime <= startTime
            ) {

                $('#end_time_error').text(
                    'End time must be after start time.'
                );

                $('#replacementEndTime').val('');

            }

        });


        /*
        |--------------------------------------------------------------------------
        | End time validation
        |--------------------------------------------------------------------------
        */

        $('#replacementEndTime').on('change', function() {

            let endTime = $(this).val();

            let startTime =
                $('#replacementStartTime').val();

            $('#end_time_error').text('');

            if (!endTime) {
                return;
            }


            /*
            | Time must be between 10:00 AM and 5:00 PM
            */

            if (
                endTime < '10:00' ||
                endTime > '17:00'
            ) {

                $('#end_time_error').text(
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

                $('#end_time_error').text(
                    'End time must be after start time.'
                );

                $(this).val('');

            }

        });


        /*
        |--------------------------------------------------------------------------
        | Submit Create Class Replacement
        |--------------------------------------------------------------------------
        */

        $('#createClassReplacementForm').on('submit', function(e) {

            e.preventDefault();

            let form = $(this);


            /*
            |--------------------------------------------------------------------------
            | Clear previous errors
            |--------------------------------------------------------------------------
            */

            $('#replacement_teacher_id_error').text('');
            $('#semester_error').text('');
            $('#subject_id_error').text('');
            $('#date_error').text('');
            $('#start_time_error').text('');
            $('#end_time_error').text('');


            /*
            |--------------------------------------------------------------------------
            | Get values
            |--------------------------------------------------------------------------
            */

            let teacher =
                $('#replacementTeacher').val();

            let semester =
                $('#replacementSemester').val();

            let subjectId =
                $('#replacementAssignClass').val();

            let date =
                $('#replacementDate').val();

            let startTime =
                $('#replacementStartTime').val();

            let endTime =
                $('#replacementEndTime').val();


            /*
            |--------------------------------------------------------------------------
            | Teacher
            |--------------------------------------------------------------------------
            */

            if (!teacher) {

                $('#replacement_teacher_id_error').text(
                    'Please select a replacement teacher.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Semester
            |--------------------------------------------------------------------------
            */

            if (!semester) {

                $('#semester_error').text(
                    'Please select a semester.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Subject
            |--------------------------------------------------------------------------
            */

            if (!subjectId) {

                $('#subject_id_error').text(
                    'Please select a subject.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Date
            |--------------------------------------------------------------------------
            */

            if (!date) {

                $('#date_error').text(
                    'Please select replacement date.'
                );

                return;
            }


            if (date < getToday()) {

                $('#date_error').text(
                    'Replacement date cannot be before today.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Start time
            |--------------------------------------------------------------------------
            */

            if (!startTime) {

                $('#start_time_error').text(
                    'Please select start time.'
                );

                return;
            }


            if (
                startTime < '10:00' ||
                startTime > '17:00'
            ) {

                $('#start_time_error').text(
                    'Start time must be between 10:00 AM and 5:00 PM.'
                );

                return;
            }

            if (!endTime) {

                $('#end_time_error').text(
                    'Please select end time.'
                );

                return;
            }


            if (
                endTime < '10:00' ||
                endTime > '17:00'
            ) {

                $('#end_time_error').text(
                    'End time must be between 10:00 AM and 5:00 PM.'
                );

                return;
            }


            if (endTime <= startTime) {

                $('#end_time_error').text(
                    'End time must be after start time.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Submit AJAX
            |--------------------------------------------------------------------------
            */

            let formData = new FormData(this);

            let submitButton =
                form.find('button[type="submit"]');

            submitButton.prop('disabled', true);


            $.ajax({

                url: form.attr('action'),

                type: 'POST',

                data: formData,

                processData: false,

                contentType: false,


                /*
                |--------------------------------------------------------------------------
                | Success
                |--------------------------------------------------------------------------
                */

                success: function(response) {

                    if (!response.success) {

                        Swal.fire({

                            toast: true,

                            position: 'top-end',

                            icon: 'error',

                            title: response.message ||
                                'Unable to create class replacement.',

                            showConfirmButton: false,

                            timer: 2500,

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


                    /*
                    | Reset form
                    */

                    $('#createClassReplacementForm')[0].reset();

                    $('#replacementAssignClass').html(
                        '<option value="">Select Semester First</option>'
                    );


                    /*
                    | Clear errors
                    */

                    $('#replacement_teacher_id_error').text('');
                    $('#semester_error').text('');
                    $('#subject_id_error').text('');
                    $('#date_error').text('');
                    $('#start_time_error').text('');
                    $('#end_time_error').text('');


                    /*
                    | Close modal
                    */

                    let modalElement =
                        document.getElementById(
                            'createClassReplacementModal'
                        );

                    let modal =
                        bootstrap.Modal.getInstance(
                            modalElement
                        );

                    if (modal) {
                        modal.hide();
                    }


                    /*
                    | Reload page
                    */

                    setTimeout(function() {

                        location.reload();

                    }, 1500);

                },


                /*
                |--------------------------------------------------------------------------
                | Validation / Server Error
                |--------------------------------------------------------------------------
                */

                error: function(xhr) {

                    console.log('STATUS:', xhr.status);

                    console.log(
                        'RESPONSE:',
                        xhr.responseText
                    );

                    console.log(
                        'JSON:',
                        xhr.responseJSON
                    );


                    /*
                    | Clear errors
                    */

                    $('#replacement_teacher_id_error').text('');
                    $('#semester_error').text('');
                    $('#subject_id_error').text('');
                    $('#date_error').text('');
                    $('#start_time_error').text('');
                    $('#end_time_error').text('');


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

                                    if (
                                        key ===
                                        'replacement_teacher_id'
                                    ) {

                                        $(
                                            '#replacement_teacher_id_error'
                                        ).text(value[0]);

                                    }


                                    if (
                                        key === 'semester'
                                    ) {

                                        $(
                                            '#semester_error'
                                        ).text(value[0]);

                                    }


                                    if (
                                        key === 'subject_id'
                                    ) {

                                        $(
                                            '#subject_id_error'
                                        ).text(value[0]);

                                    }


                                    if (
                                        key === 'date'
                                    ) {

                                        $(
                                            '#date_error'
                                        ).text(value[0]);

                                    }


                                    if (
                                        key === 'start_time'
                                    ) {

                                        $(
                                            '#start_time_error'
                                        ).text(value[0]);

                                    }


                                    if (
                                        key === 'end_time'
                                    ) {

                                        $(
                                            '#end_time_error'
                                        ).text(value[0]);

                                    }

                                }
                            );

                        }


                        /*
                        | Server custom message
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
                    | 500 / other errors
                    */

                    let serverMessage =
                        xhr.responseJSON?.message;


                    Swal.fire({

                        toast: true,

                        position: 'top-end',

                        icon: 'error',

                        title: serverMessage ||
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
                | Complete
                |--------------------------------------------------------------------------
                */

                complete: function() {

                    submitButton.prop(
                        'disabled',
                        false
                    );

                }

            });

        });


        /*
        |--------------------------------------------------------------------------
        | Reset when modal closes
        |--------------------------------------------------------------------------
        */

        $('#createClassReplacementModal').on(
            'hidden.bs.modal',
            function() {

                $('#createClassReplacementForm')[0].reset();

                $('#replacementAssignClass').html(
                    '<option value="">Select Semester First</option>'
                );


                $('#replacement_teacher_id_error').text('');
                $('#semester_error').text('');
                $('#subject_id_error').text('');
                $('#date_error').text('');
                $('#start_time_error').text('');
                $('#end_time_error').text('');

            }
        );

    });
</script>
