<!-- Edit Class Replacement Modal -->
<div class="modal fade"
     id="editClassReplacementModal"
     tabindex="-1"
     aria-labelledby="editClassReplacementModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <div class="modal-header">
                <h5 class="modal-title fw-bold"
                    id="editClassReplacementModalLabel">
                    Edit Class Replacement
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>
            </div>

            <form id="editClassReplacementForm"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="modal-body">

                    <input type="hidden"
                           id="editReplacementId">

                    <input type="hidden"
                           id="editOriginalTeacherId"
                           name="original_teacher_id">

                    <!-- Class -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Select Class
                        </label>

                        <select name="assign_class_id"
                                id="editReplacementAssignClass"
                                class="form-select"
                                required>

                            <option value="">
                                Select Class
                            </option>

                            @foreach ($assignClasses as $assignClass)

                                <option value="{{ $assignClass->id }}"
                                        data-semester="{{ $assignClass->semester }}"
                                        data-subject="{{ $assignClass->subjects->first()?->subject_name ?? '-' }}"
                                        data-teacher="{{ $assignClass->teacher_id }}"
                                        data-teacher-name="{{ $assignClass->teacher->name ?? '-' }}"
                                        data-start="{{ \Carbon\Carbon::parse($assignClass->start_time)->format('H:i') }}"
                                        data-end="{{ \Carbon\Carbon::parse($assignClass->end_time)->format('H:i') }}">

                                    Semester {{ $assignClass->semester }}
                                    -
                                    {{ $assignClass->subjects->first()?->subject_name ?? '-' }}
                                    -
                                    {{ $assignClass->teacher->name ?? '-' }}

                                </option>

                            @endforeach

                        </select>
                    </div>


                    <!-- Semester -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Semester
                        </label>

                        <input type="text"
                               id="editReplacementSemester"
                               class="form-control"
                               readonly>
                    </div>


                    <!-- Subject -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Subject
                        </label>

                        <input type="text"
                               id="editReplacementSubject"
                               class="form-control"
                               readonly>
                    </div>


                    <!-- Original Teacher -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Original Teacher
                        </label>

                        <input type="text"
                               id="editOriginalTeacherName"
                               class="form-control"
                               readonly>
                    </div>


                    <!-- Replacement Teacher -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Replacement Teacher
                        </label>

                        <select name="replacement_teacher_id"
                                id="editReplacementTeacher"
                                class="form-select"
                                required>

                            <option value="">
                                Select Replacement Teacher
                            </option>

                            @foreach ($teachers as $teacher)

                                <option value="{{ $teacher->id }}">
                                    {{ $teacher->name }}
                                </option>

                            @endforeach

                        </select>
                    </div>


                    <!-- Date -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Replacement Date
                        </label>

                        <input type="date"
                               name="date"
                               id="editReplacementDate"
                               class="form-control"
                               required>
                    </div>


                    <!-- Start Time -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Start Time
                        </label>

                        <input type="time"
                               name="start_time"
                               id="editReplacementStartTime"
                               class="form-control"
                               readonly
                               required>
                    </div>


                    <!-- End Time -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            End Time
                        </label>

                        <input type="time"
                               name="end_time"
                               id="editReplacementEndTime"
                               class="form-control"
                               readonly
                               required>
                    </div>


                    <!-- Reason -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Reason
                        </label>

                        <textarea name="reason"
                                  id="editReplacementReason"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Enter reason (optional)"></textarea>
                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Update Replacement
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>


<script>

$(document).ready(function () {

    /*
    |--------------------------------------------------------------------------
    | Open Edit Modal
    |--------------------------------------------------------------------------
    */

    $(document).on("click", ".edit-btn", function () {

        const id = $(this).data("id");

        $.ajax({

            url: "{{ url('/admin/classreplacement') }}/" + id + "/edit",

            type: "GET",

            success: function (response) {

                $("#editReplacementAssignClass")
                    .val(response.assign_class_id)
                    .trigger("change");

                $("#editReplacementSemester").val(
                    "Semester " + response.semester
                );

                $("#editReplacementSubject").val(
                    response.subject
                );

                $("#editOriginalTeacherName").val(
                    response.original_teacher_name
                );

                $("#editOriginalTeacherId").val(
                    response.original_teacher_id
                );

                $("#editReplacementTeacher").val(
                    response.replacement_teacher_id
                );

                $("#editReplacementDate").val(
                    response.date
                );

                $("#editReplacementStartTime").val(
                    response.start_time
                );

                $("#editReplacementEndTime").val(
                    response.end_time
                );

                $("#editReplacementReason").val(
                    response.reason
                );

                $("#editClassReplacementModal").modal("show");

                $("#editClassReplacementForm").attr(
                    "action",
                    "{{ url('/admin/classreplacement/update') }}/" + id
                );

                /*
                |--------------------------------------------------------------------------
                | Disable Original Teacher
                |--------------------------------------------------------------------------
                */

                $("#editReplacementTeacher option")
                    .prop("disabled", false);

                $("#editReplacementTeacher option[value='" +
                    response.original_teacher_id +
                "']").prop("disabled", true);

            },

            error: function (xhr) {

                console.log(xhr.responseText);

                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Unable to load class replacement."
                });

            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Class Changed
    |--------------------------------------------------------------------------
    */

    $("#editReplacementAssignClass").on("change", function () {

        const selected = $(this).find(":selected");

        if (!selected.val()) {

            $("#editReplacementSemester").val("");
            $("#editReplacementSubject").val("");
            $("#editOriginalTeacherName").val("");
            $("#editOriginalTeacherId").val("");
            $("#editReplacementStartTime").val("");
            $("#editReplacementEndTime").val("");

            return;
        }

        $("#editReplacementSemester").val(
            "Semester " + selected.data("semester")
        );

        $("#editReplacementSubject").val(
            selected.data("subject")
        );

        $("#editOriginalTeacherName").val(
            selected.data("teacher-name")
        );

        $("#editOriginalTeacherId").val(
            selected.data("teacher")
        );

        $("#editReplacementStartTime").val(
            selected.data("start")
        );

        $("#editReplacementEndTime").val(
            selected.data("end")
        );

        /*
        |--------------------------------------------------------------------------
        | Disable Original Teacher
        |--------------------------------------------------------------------------
        */

        $("#editReplacementTeacher option")
            .prop("disabled", false);

        $("#editReplacementTeacher option[value='" +
            selected.data("teacher") +
        "']").prop("disabled", true);

    });


    /*
    |--------------------------------------------------------------------------
    | Prevent Same Teacher
    |--------------------------------------------------------------------------
    */

    $("#editClassReplacementForm").on("submit", function (e) {

        const originalTeacher =
            $("#editOriginalTeacherId").val();

        const replacementTeacher =
            $("#editReplacementTeacher").val();

        if (originalTeacher === replacementTeacher) {

            e.preventDefault();

            Swal.fire({
                icon: "warning",
                title: "Invalid Teacher",
                text: "Replacement teacher cannot be the original teacher.",
                confirmButtonText: "OK"
            });

            return false;
        }

    });

});

</script>