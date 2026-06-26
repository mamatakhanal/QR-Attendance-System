<head>
    <title>Student - Teacher</title>
    @include('layouts.link')
    @include('layouts.style')
</head>

<body>

    <!-- MAIN LAYOUT -->
    <div class="main-wrapper">
        @include('teacher.sidebar')
        <div class="main-area">
            @include('teacher.navbar')

            <!-- CONTENT -->
            <div class="card shadow-sm border-0 mx-2 my-2 p-4 rounded-4">
                <h5 class="fw-semibold mb-3">
                    Student List
                </h5>
                <!-- Semester Filter -->
                <div class="d-flex gap-2 mb-3 flex-wrap">
                    <button class="btn btn-primary btn-sm semester-btn active" data-semester="all">
                        <i class="bi bi-people"></i>
                        All Students
                    </button>
                    @foreach ($assignedSemesters as $semester)
                        <button class="btn btn-outline-primary btn-sm semester-btn" data-semester="{{ $semester }}">
                            Semester {{ $semester }}
                        </button>
                    @endforeach
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-secondary">
                            <tr>
                                <th>S.N</th>
                                <th>Student Code</th>
                                <th>Name</th>
                                <th>Roll No</th>
                                <th>Semester</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="student-data">
                            @foreach ($students as $student)
                                <tr>
                                    <td> {{ $students->firstItem() + $loop->index }} </td>
                                    <td> {{ $student->student_code }} </td>
                                    <td> {{ $student->name }} </td>
                                    <td> {{ $student->roll_no }} </td>
                                    <td> {{ $student->current_semester }} </td>
                                    <td> {{ $student->email }} </td>
                                    <td>
                                        <button
                                            class="btn btn-sm btn-outline-success fw-semibold rounded-3  view-student"
                                            data-bs-toggle="modal" style="font-size:10px;"
                                            data-bs-target="#studentModal" data-photo="{{ $student->photo }}"
                                            data-name="{{ $student->name }}" data-code="{{ $student->student_code }}"
                                            data-roll="{{ $student->roll_no }}" data-gender="{{ $student->gender }}"
                                            data-dob="{{ $student->dob }}" data-address="{{ $student->address }}"
                                            data-year="{{ $student->admission_year }}"
                                            data-semester="{{ $student->current_semester }}"
                                            data-email="{{ $student->email }}">
                                            <i class="bi bi-eye"></i>
                                            View
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
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


    {{-- Student Details Modal --}}
    <div class="modal fade" id="studentModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 p-3">
                <div class="modal-header">
                    <h4 class="modal-title fw-bold">
                        {{-- <i class="bi bi-person-lines-fill me-2"></i> --}}
                        Student Details
                    </h4>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">

                    <div class="col-md-6">
                        <div class="bg-light rounded-3 p-3 shadow-sm">
                            <small class="text-muted"> Name </small>
                            <h6 class="mb-0 fw-semibold mt-1" id="student-name"> </h6>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-light rounded-3 p-3 shadow-sm">
                            <small class="text-muted"> Roll No </small>
                            <h6 class="mb-0 fw-semibold mt-1" id="student-roll"> </h6>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-light rounded-3 p-3 shadow-sm">
                            <small class="text-muted"> Admission Year </small>
                            <h6 class="mb-0 fw-semibold mt-1" id="student-year"> </h6>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-light rounded-3 p-3 shadow-sm">
                            <small class="text-muted"> Semester </small>
                            <h6 class="mb-0 fw-semibold mt-1" id="student-semester"> </h6>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-light rounded-3 p-3 shadow-sm">
                            <small class="text-muted"> Student Code </small>
                            <h6 class="mb-0 fw-semibold mt-1" id="student-code"> </h6>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-light rounded-3 p-3 shadow-sm">
                            <small class="text-muted"> Email </small>
                            <h6 class="mb-0 fw-semibold mt-1" id="student-email"> </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    $(document).on('click', '.semester-btn', function() {
        let semester = $(this).data('semester');
        $('.semester-btn')
            .removeClass('active');
        $(this).addClass('active');
        $.ajax({
            url: "{{ route('teacher.students') }}",
            type: "GET",
            data: {
                semester: semester
            },
            success: function(response) {
                $('#student-data').html(
                    $(response).find('#student-data').html()
                );
                $('#pagination-data').html(
                    $(response).find('#pagination-data').html()
                );
            }
        });
    });

    // View Student Details
    $(document).on('click', '.view-student', function() {
        $('#student-name').text($(this).data('name'));
        $('#student-code').text($(this).data('code'));
        $('#student-roll').text($(this).data('roll'));
        $('#student-gender').text($(this).data('gender'));
        $('#student-dob').text($(this).data('dob'));
        $('#student-address').text($(this).data('address'));
        $('#student-year').text($(this).data('year'));
        $('#student-semester').text($(this).data('semester'));
        $('#student-email').text($(this).data('email'));

        let photo = $(this).data('photo');
        if (photo) {
            $('#student-photo')
                .attr('src', '/storage/' + photo);
        } else {
            $('#student-photo')
                .attr('src', '/images/default-user.png');
        }
    });
</script>
