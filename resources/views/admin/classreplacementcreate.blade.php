
<!-- Create Class Replacement Modal -->
<div class="modal fade" id="createClassReplacementModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content px-4 pt-4 rounded-4">

            <form id="createClassReplacementForm"
                action="{{ route('admin.classreplacement.store') }}"
                method="POST">

                @csrf

                <div class="modal-header">
                    <h3 class="modal-title fw-bold">
                        Create Class Replacement
                    </h3>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>


                <div class="modal-body row g-3">

                    <!-- Teacher -->
                    <div class="col-md-12">

                        <label class="form-label">
                            Teacher 
                        </label>

                        <select name="teacher_id"
                            id="replacementTeacher"
                            class="form-select"
                            required>

                            <option value="">
                                Select Teacher
                            </option>

                            @foreach ($teachers as $teacher)

                                <option value="{{ $teacher->id }}">
                                    {{ $teacher->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <!-- Semester -->
                    <div class="col-md-12">

                        <label class="form-label">
                            Semester 
                        </label>

                        <select name="semester"
                            id="replacementSemester"
                            class="form-select"
                            required>

                            <option value="">
                                Select Semester
                            </option>

                            @for ($i = 1; $i <= 8; $i++)

                                <option value="{{ $i }}">
                                    Semester {{ $i }}
                                </option>

                            @endfor

                        </select>

                    </div>


                    <!-- Subject -->
                    <div class="col-md-12">

                        <label class="form-label">
                            Subject 
                        </label>

                        <select name="subject_id"
                            id="replacementSubject"
                            class="form-select"
                            required>

                            <option value="">
                                Select Semester First
                            </option>

                        </select>

                        <small id="subject_id_error"
                            class="text-danger">
                        </small>

                    </div>


                    <!-- Start Time -->
                    <div class="col-md-6">

                        <label class="form-label">
                            Start Time 
                        </label>

                        <input type="time"
                            name="start_time"
                            id="replacementStartTime"
                            class="form-control"
                            min="10:00"
                            max="17:00"
                            required>

                        <small id="start_time_error"
                            class="text-danger">
                        </small>

                    </div>


                    <!-- End Time -->
                    <div class="col-md-6">

                        <label class="form-label">
                            End Time 
                        </label>

                        <input type="time"
                            name="end_time"
                            id="replacementEndTime"
                            class="form-control"
                            min="10:00"
                            max="17:00"
                            required>

                        <small id="end_time_error"
                            class="text-danger">
                        </small>

                    </div>

                </div>


                <div class="modal-footer mt-3 mb-0">
                    <button type="submit"
                        class="btn btn-success">
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
        | Load Subjects According To Semester
        |--------------------------------------------------------------------------
        */

        $('#replacementSemester').on('change', function() {

            let semester = $(this).val();

            let subjectDropdown = $('#replacementSubject');


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


                    if (data.length > 0) {

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

                    } else {

                        subjectDropdown.html(
                            '<option value="">No subjects found</option>'
                        );

                    }

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
        | Start Time Validation
        |--------------------------------------------------------------------------
        */

        $('#replacementStartTime').on('change', function() {

            let startTime = $(this).val();

            $('#start_time_error').text('');

            if (startTime && startTime < '10:00') {

                $('#start_time_error').text(
                    'Start time must be between 10:00 AM and 5:00 PM.'
                );

                $(this).val('');

            }

        });


        /*
        |--------------------------------------------------------------------------
        | End Time Validation
        |--------------------------------------------------------------------------
        */

        $('#replacementEndTime').on('change', function() {

            let endTime = $(this).val();

            let startTime = $('#replacementStartTime').val();

            $('#end_time_error').text('');


            if (endTime && endTime > '17:00') {

                $('#end_time_error').text(
                    'End time must be between 10:00 AM and 5:00 PM.'
                );

                $(this).val('');

                return;

            }


            if (startTime && endTime && endTime <= startTime) {

                $('#end_time_error').text(
                    'End time must be after start time.'
                );

                $(this).val('');

            }

        });


        /*
        |--------------------------------------------------------------------------
        | Submit Form Validation
        |--------------------------------------------------------------------------
        */

        $('#createClassReplacementForm').submit(function(e) {

            e.preventDefault();


            $('#subject_id_error').text('');
            $('#start_time_error').text('');
            $('#end_time_error');


            let teacher = $('#replacementTeacher').val();

            let semester = $('#replacementSemester').val();

            let subject = $('#replacementSubject').val();

            let startTime = $('#replacementStartTime').val();

            let endTime = $('#replacementEndTime').val();


            /*
            | Teacher
            */

            if (!teacher) {

                return;

            }


            /*
            | Semester
            */

            if (!semester) {

                return;

            }


            /*
            | Subject
            */

            if (!subject) {

                $('#subject_id_error').text(
                    'Please select a subject.'
                );

                return;

            }


            /*
            | Start Time
            */

            if (!startTime) {

                $('#start_time_error').text(
                    'Please select start time.'
                );

                return;

            }


            if (startTime < '10:00' || startTime > '17:00') {

                $('#start_time_error').text(
                    'Start time must be between 10:00 AM and 5:00 PM.'
                );

                return;

            }


            /*
            | End Time
            */

            if (!endTime) {

                $('#end_time_error').text(
                    'Please select end time.'
                );

                return;

            }


            if (endTime < '10:00' || endTime > '17:00') {

                $('#end_time_error').text(
                    'End time must be between 10:00 AM and 5:00 PM.'
                );

                return;

            }


            /*
            | End time must be after start time
            */

            if (endTime <= startTime) {

                $('#end_time_error').text(
                    'End time must be after start time.'
                );

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Submit
            |--------------------------------------------------------------------------
            */

            let formData = new FormData(this);


            $.ajax({

                url: $(this).attr('action'),

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


                    $('#createClassReplacementForm')[0].reset();


                    $('#replacementSubject').html(
                        '<option value="">Select Semester First</option>'
                    );


                    $('#subject_id_error').text('');

                    $('#start_time_error').text('');

                    $('#end_time_error').text('');


                    bootstrap.Modal.getInstance(
                        document.getElementById(
                            'createClassReplacementModal'
                        )
                    ).hide();


                    setTimeout(function() {

                        location.reload();

                    }, 1500);

                },


                error: function(xhr) {

                    $('#subject_id_error').text('');

                    $('#start_time_error').text('');

                    $('#end_time_error').text('');


                    if (xhr.status === 422) {

                        let errors = xhr.responseJSON.errors;


                        $.each(errors, function(key, value) {

                            if (key === 'subject_id') {

                                $('#subject_id_error').text(
                                    value[0]
                                );

                            }


                            if (key === 'start_time') {

                                $('#start_time_error').text(
                                    value[0]
                                );

                            }


                            if (key === 'end_time') {

                                $('#end_time_error').text(
                                    value[0]
                                );

                            }

                        });

                    }

                }

            });

        });


        /*
        |--------------------------------------------------------------------------
        | Clear Modal When Closed
        |--------------------------------------------------------------------------
        */

        $('#createClassReplacementModal').on(
            'hidden.bs.modal',
            function() {

                $('#createClassReplacementForm')[0].reset();

                $('#replacementSubject').html(
                    '<option value="">Select Semester First</option>'
                );

                $('#subject_id_error').text('');

                $('#start_time_error').text('');

                $('#end_time_error').text('');

            }
        );

    });

</script>

