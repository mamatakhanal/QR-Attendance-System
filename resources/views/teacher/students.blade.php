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
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="fw-semibold mb-3">
                        Student List
                    </h5>
                    <form method="GET" action="{{ url()->current() }}" id="searchForm">
                        <input type="hidden" name="semester" id="selectedSemester"
                            value="{{ request('semester', 'all') }}">
                        <div class="search-box position-relative">
                            <i
                                class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 small text-muted"></i>
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                                class="form-control form-control-sm rounded-3 ps-5 py-2"
                                placeholder="Search by Name, Roll No or Student Code...">
                        </div>
                    </form>
                </div>

                <!-- Semester   Filter -->
                <div class="d-flex gap-2 mb-4 flex-wrap">
                    <button
                        class="btn btn-primary btn-sm semester-btn active {{ request('semester', 'all') == 'all' ? 'btn-primary active' : 'btn-outline-primary' }}"
                        data-semester="all">
                        <i class="bi bi-people"></i> &nbsp; All Students
                    </button>
                    @foreach ($assignedSemesters as $semester)
                        <button
                            class="btn btn-outline-primary btn-sm semester-btn {{ request('semester') == $semester ? 'btn-primary active' : 'btn-outline-primary' }}"
                            data-semester="{{ $semester }}">
                            Semester {{ $semester }}
                        </button>
                    @endforeach
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-secondary">
                            <tr>
                                <th class="py-3">S.N</th>
                                <th class="py-3">Student Code</th>
                                <th class="py-3">Name</th>
                                <th class="py-3">Roll No</th>
                                <th class="py-3">Semester</th>
                                <th class="py-3">Email</th>
                                <th class="py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody id="student-data">
                            @foreach ($students as $student)
                                <tr class="student-row">
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
                            <tr id="noStudentRow" style="{{ $students->count() ? 'display:none;' : '' }}">
                                <td colspan="7" class="text-center text-muted py-4">
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

        $('#selectedSemester').val(semester);

        $('.semester-btn')
            .removeClass('active btn-primary')
            .addClass('btn-outline-primary');

        $(this)
            .removeClass('btn-outline-primary')
            .addClass('btn-primary active');

        $.ajax({
            url: "{{ route('teacher.students') }}",
            type: "GET",
            data: {
                semester: semester,
                search: $('#searchInput').val()
            },
            success: function(response) {
                $('#student-data').html(
                    $(response).find('#student-data').html()
                );
                $('#pagination-data').html(
                    $(response).find('#pagination-data').html()
                );
                checkNoStudent();
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

    function checkNoStudent() {

        let visibleRows = $('#student-data .student-row:visible').length;

        if (visibleRows === 0) {
            $('#noStudentRow').show();
        } else {
            $('#noStudentRow').hide();
        }

    }
</script>


<!-- Search  -->
<script>
    let searchTimer;

    $('#searchInput').on('keyup', function() {

        clearTimeout(searchTimer);

        searchTimer = setTimeout(function() {

            $.ajax({
                url: "{{ route('teacher.students') }}",
                type: "GET",
                data: {
                    search: $('#searchInput').val(),
                    semester: $('#selectedSemester').val()
                },
                success: function(response) {

                    $('#student-data').html(
                        $(response).find('#student-data').html()
                    );

                    $('#pagination-data').html(
                        $(response).find('#pagination-data').html()
                    );

                    checkNoStudent();
                }
            });

        }, 300);

    });
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
    });
</script>
