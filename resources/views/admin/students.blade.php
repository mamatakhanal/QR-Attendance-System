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
            <div class="card shadow-sm border-0 m-3 p-4 rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-semibold mb-0">Student List</h5>
                    <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        New Student
                    </button>
                </div>
                <div class="table-responsive rounded-2">
                    <table class="table table-hover border-3 mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th class="py-3">SN</th>
                                <th class="py-3">Student Code</th>
                                <th class="py-3">Name</th>
                                <th class="py-3">Roll No</th>
                                <th class="py-3">Semester</th>
                                <th class="py-3">Batch</th>
                                <th class="py-3">Email</th>
                                <th class="py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)

                            <tr class="student-row">
                                <td>{{ $students->firstItem() + $loop->index }}</td>
                                <td>{{ $student->student_code }}</td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->roll_no }}</td>
                                <td>{{ $student->current_semester }}</td>
                                <td>{{ $student->admission_year }}</td>
                                <td>{{ $student->email }}</td>
                                <td>
                                    <button
                                        class="btn btn-outline-warning fw-semibold btn-sm rounded-3 action-btn"
                                        style="font-size:10px;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editStudentModal"

                                        data-id="{{ $student->id }}"
                                        data-name="{{ $student->name }}"
                                        data-roll_no="{{ $student->roll_no }}"
                                        data-email="{{ $student->email }}"
                                        data-phone="{{ $student->phone }}"
                                        data-gender="{{ $student->gender }}"
                                        data-dob="{{ $student->dob }}"
                                        data-address="{{ $student->address }}"
                                        data-current_semester="{{ $student->current_semester }}"
                                        data-admission_year="{{ $student->admission_year }}"
                                        data-student_code="{{ $student->student_code }}">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button> &nbsp;
                                    <button
                                        class="btn btn-outline-danger fw-semibold btn-sm rounded-3 action-btn" style="font-size:10px;" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal" data-id="{{ $student->id }}" data-url="{{ route('students.delete', $student->id) }}">
                                        <i class="bi bi-trash"></i> Delete
                                    </button> &nbsp;
                                    <button
                                        class="btn btn-outline-primary fw-semibold btn-sm rounded-3 send-mail" style="font-size:10px;" data-bs-toggle="modal"
                                        data-id="{{ $student->id }}">
                                        <i class="bi bi-envelope"></i> Send
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @include('layouts.pagination', ['paginator' => $students])
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
                timer: 2000,
                timerProgressBar: true,
                customClass: {
                    popup: 'small-toast'
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
                        timer: 2000
                    });
                }
            });
        });
    </script>
</body>

<script>
    $('#editStudentModal').on('show.bs.modal', function(event) {

        let button = $(event.relatedTarget);

        // Clear old validation errors
        $('.text-danger').text('');

        // Title
        $('#student_name_title').text(button.data('name'));

        // Form Fields
        $('#edit_id').val(button.data('id'));
        $('#edit_name').val(button.data('name'));
        $('#edit_roll_no').val(button.data('roll_no'));
        $('#edit_email').val(button.data('email'));
        $('#edit_phone').val(button.data('phone'));
        $('#edit_gender').val(button.data('gender'));
        $('#edit_dob').val(button.data('dob'));
        $('#edit_address').val(button.data('address'));
        $('#edit_current_semester').val(button.data('current_semester'));
        $('#edit_admission_year').val(button.data('admission_year'));
        $('#edit_student_code').val(button.data('student_code'));

        // QR Code
        let studentCode = button.data('student_code');
        $('#edit_qr_image').attr(
            'src',
            '/admin/student-qr/' + encodeURIComponent(studentCode)
        );
    });
</script>