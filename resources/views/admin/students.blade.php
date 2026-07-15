<head>
    <title>Students - Admin</title>
    @include('layouts.link')
    @include('layouts.style')
    @include('layouts.delete')
    @include('admin.studentcreate')
    @include('admin.studentedit')
</head>

<body>
    @include('layouts.toast')
    <!-- MAIN LAYOUT -->
    <div class="main-wrapper">
        @include('admin.sidebar')
        <div class="main-area">
            @include('admin.navbar')

            <!-- CONTENT -->
            <div class="card shadow-sm border-0 mx-2 my-2 p-4 rounded-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                    <h5 class="fw-semibold mb-0">Student List</h5>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal"
                            data-bs-target="#addStudentModal">
                            New Student
                        </button>
                    </div>
                </div>

                <!-- Semester Filter Buttons -->
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <button class="btn btn-primary btn-sm semester-btn active" data-semester="all">
                        <i class="bi bi-people"></i> &nbsp; All Students
                    </button>
                    @for ($i = 1; $i <= 8; $i++)
                        <button class="btn btn-outline-primary btn-sm semester-btn" data-semester="{{ $i }}">
                            Semester {{ $i }}
                        </button>
                    @endfor
                </div>

                <div class="table-responsive rounded-2">
                    <table class="table table-hover border-3 mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th class="py-3">S.N</th>
                                <th class="py-3">Student Code</th>
                                <th class="py-3">Name</th>
                                <th class="py-3">Roll No</th>
                                <th class="py-3">Semester</th>
                                <th class="py-3">Batch</th>
                                <th class="py-3">Email</th>
                                <th class="py-3">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="student-data">
                            @forelse ($students as $student)
                                <tr class="student-row" data-semester="{{ $student->current_semester }}">
                                    <td>{{ $students->firstItem() + $loop->index }}</td>
                                    <td>{{ $student->student_code }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->roll_no }}</td>
                                    <td>{{ $student->current_semester }}</td>
                                    <td>{{ $student->admission_year }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>
                                        <button class="btn btn-outline-primary fw-semibold btn-sm rounded-3 action-btn"
                                            style="font-size:10px;" data-bs-toggle="modal"
                                            data-bs-target="#editStudentModal" data-id="{{ $student->id }}"
                                            data-name="{{ $student->name }}" data-roll_no="{{ $student->roll_no }}"
                                            data-email="{{ $student->email }}" data-phone="{{ $student->phone }}"
                                            data-gender="{{ $student->gender }}" data-dob="{{ $student->dob }}"
                                            data-address="{{ $student->address }}"
                                            data-current_semester="{{ $student->current_semester }}"
                                            data-admission_year="{{ $student->admission_year }}"
                                            data-student_code="{{ $student->student_code }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button> &nbsp;
                                        <button class="btn btn-outline-danger fw-semibold btn-sm rounded-3 action-btn"
                                            style="font-size:10px;" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-id="{{ $student->id }}"
                                            data-url="{{ route('students.delete', $student->id) }}">
                                            <i class="bi bi-trash"></i> Delete
                                        </button> &nbsp;
                                        <button class="btn btn-outline-secondary fw-semibold btn-sm rounded-3 send-mail"
                                            style="font-size:10px;" data-bs-toggle="modal"
                                            data-id="{{ $student->id }}">
                                            <i class="bi bi-envelope"></i> Send
                                        </button>
                                    </td>
                                </tr>

                            @endforeach
                            <tr id="noStudentRow" style="{{ $students->count() ? 'display:none;' : '' }}">
                                <td colspan="8" class="text-center text-muted py-4">
                                    No students found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="pagination-data">
                    @if ($students->hasPages())
                        @include('layouts.pagination', ['paginator' => $students])
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).on('click', '.send-mail', function() {

            let id = $(this).data('id');

            // 🔵 Loading toast
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Sending mail...',
                showConfirmButton: false,
                timer: 3000,
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

            $.ajax({
                url: "/admin/student/send-email/" + id,
                type: "GET",

                success: function(response) {

                    // Mail Sent Toast Msg
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
                },
                error: function() {
                    // Error toast
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Mail failed',
                        showConfirmButton: false,
                        timer: 1000,
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
                }
            });
        });
    </script>
</body>

{{-- Semester fliter --}}
<script>
    function loadData() {

        let semester = $(".semester-btn.active").data("semester");
        let search = $("#globalSearch").val();

        $.ajax({
            url: "{{ route('admin.students') }}",
            type: "GET",
            data: {
                semester: semester,
                search: search
            },
            success: function(response) {

                $("#student-data").html($(response).find("#student-data").html());
                $("#pagination-data").html($(response).find("#pagination-data").html());

            }
        });

    }

    $(document).on("click", ".semester-btn", function() {

        $(".semester-btn")
            .removeClass("active btn-primary")
            .addClass("btn-outline-primary");

        $(this)
            .removeClass("btn-outline-primary")
            .addClass("btn-primary active");

        loadData();

    });
</script>

<script>
    function loadData() {

        $.ajax({
            url: window.location.pathname,
            data: {
                search: $("#globalSearch").val(),
                semester: $(".semester-btn.active").data("semester")
            },
            success: function(response) {
                $("#student-data").html($(response).find("#student-data").html());
            }
        });

    }

    $(".semester-btn").click(function() {

        $(".semester-btn").removeClass("active");
        $(this).addClass("active");

        loadData();

    });
</script>
