<head>
    <title>Teachers - Admin</title>
    @include('layouts.link')
    @include('layouts.style')
    @include('layouts.delete')
    @include('admin.teachercreate')
    @include('admin.teacheredit')
</head>

<body>
    @include('layouts.toast')
    <!-- MAIN LAYOUT -->
    <div class="main-wrapper">
        @include('admin.sidebar')
        <div class="main-area">
            @include('admin.navbar')

            <!-- CONTENT -->
            <div class="card shadow-sm border-0 mx-3 my-2 p-4 rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-semibold mb-0">Teacher List</h5>
                    <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal"
                        data-bs-target="#addTeacherModal">
                        New Teacher
                    </button>
                </div>
                <div class="table-responsive rounded-2">
                    <table class="table table-hover border-3 mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th class="py-3">S.N</th>
                                <th class="py-3">Name</th>
                                <th class="py-3">Phone</th>
                                <th class="py-3">Email</th>
                                <th class="py-3">Assignment</th>
                                <th class="py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teachers as $teacher)
                                <tr class="teacher-row">
                                    <td>{{ $teachers->firstItem() + $loop->index }}</td>
                                    <td>{{ $teacher->name }}</td>
                                    <td>{{ $teacher->phone }}</td>
                                    <td>{{ $teacher->email }}</td>
                                    <td>
                                        <button
                                            class="btn btn-outline-success fw-semibold btn-sm rounded-3 view-assignment"
                                            data-bs-toggle="modal" style="font-size:10px;"
                                            data-bs-target="#viewAssignmentModal" data-id="{{ $teacher->id }}">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-primary fw-semibold btn-sm rounded-3 action-btn"
                                            style="font-size:10px;" data-bs-toggle="modal"
                                            data-bs-target="#editTeacherModal" data-id="{{ $teacher->id }}"
                                            data-name="{{ $teacher->name }}" data-email="{{ $teacher->email }}"
                                            data-phone="{{ $teacher->phone }}" data-gender="{{ $teacher->gender }}"
                                            data-address="{{ $teacher->address }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button> &nbsp;
                                        <button class="btn btn-outline-danger fw-semibold btn-sm rounded-3 action-btn"
                                            style="font-size:10px;" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-id="{{ $teacher->id }}" data-name="{{ $teacher->name }}"
                                            data-url="{{ route('teachers.delete', $teacher->id) }}">
                                            <i class="bi bi-trash"></i> Delete
                                        </button> &nbsp;
                                        <button class="btn btn-outline-secondary fw-semibold btn-sm rounded-3 send-mail"
                                            style="font-size:10px;" data-bs-toggle="modal"
                                            data-id="{{ $teacher->id }}">
                                            <i class="bi bi-envelope"></i> Send
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @include('layouts.pagination', ['paginator' => $teachers])
            </div>
        </div>
    </div>
    <script>
        $(document).on('click', '.send-mail', function() {
            let id = $(this).data('id');
            // Loading toast
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
                }
            });

            $.ajax({
                url: "/admin/teacher/send-email/" + id,
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
                    //  Error toast
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Mail failed',
                        showConfirmButton: false,
                        timer: 1000
                    });

                }
            });
        });
    </script>
</body>

<div class="modal fade" id="viewAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-4 rounded-4">

            <div class="modal-header">
                <h3 class="modal-title fw-bold">Subject Assign - <span id="view_teacher"></span> </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="assignmentList">
                    <!-- dynamic content -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.view-assignment', function() {
        let id = $(this).data('id');
        console.log("Teacher ID:", id);
        $('#view_teacher').text('');
        $('#assignmentList').html('Loading...');
        $.ajax({
            url: "/admin/teacher/assignment/" + id,
            type: "GET",
            success: function(res) {
                console.log(res);
                // Teacher name
                $('#view_teacher').text(res.teacher.name);
                let html = "";
                if (res.assignments.length == 0) {
                    html = `
                    <h5 class="text-muted">
                        No subjects are assigned to this teacher
                    </h5>`;
                } else {
                    // Sort semester 1,2,3....8
                    res.assignments.sort(function(a, b) {
                        return a.semester - b.semester;
                    });
                    $.each(res.assignments, function(index, item) {
                        let subjectList = "";
                        $.each(item.subjects, function(i, subject) {
                            subjectList += `
                           <li>${subject.subject_name}</li>`;
                        });
                        html += `
                    <div class="card mb-2 shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="fw-bold"> Semester ${item.semester} </h6>
                             <ol class="mb-0"> ${subjectList} </ol>
                        </div>
                    </div> `;
                    });
                }
                $('#assignmentList').html(html);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                $('#assignmentList').html(
                    '<p class="text-danger">Failed to load data</p>'
                );
            }
        });
    });
</script>
