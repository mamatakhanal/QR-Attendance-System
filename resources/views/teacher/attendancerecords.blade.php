<head>
    <title>Attendance Records - Teacher</title>
    @include('layouts.link')
    @include('layouts.style')
</head>

<body>
    <div class="main-wrapper">
        @include('teacher.sidebar')
        <div class="main-area">
            @include('teacher.navbar')

            <div class="main-content">
                <div class="card shadow-sm border-0 rounded-4 mx-2 my-2 p-4">
                    <h5 class="fw-semibold mb-3">
                        Attendance Records
                    </h5>

                    <!-- Semester Filter -->
                    <div class="d-flex gap-2 mb-3 flex-wrap">
                        <!-- All Classes -->
                        <button
                            class="btn btn-sm semester-btn {{ request('semester') == null || request('semester') == 'all' ? 'btn-primary active' : 'btn-outline-primary' }}"
                            data-semester="all">
                            <i class="bi bi-people"></i> &nbsp; All Classes
                        </button>

                        <!-- Teacher Assigned Semesters -->
                        @foreach ($assignedSemesters as $semester)
                            <button
                                class="btn btn-sm semester-btn {{ request('semester') == $semester ? 'btn-primary active' : 'btn-outline-primary' }}"
                                data-semester="{{ $semester }}">
                                Semester {{ $semester }}
                            </button>
                        @endforeach
                    </div>

                    <div class="table-responsive rounded-2">
                        <table class="table table-hover border-3 mb-0">
                            <thead class="table-secondary">
                                <tr>
                                    <th class="py-3">S.N</th>
                                    <th class="py-3">Date</th>
                                    <th class="py-3">Semester</th>
                                    <th class="py-3">Subject</th>
                                    <th class="py-3">Roll No</th>
                                    <th class="py-3">Student</th>
                                    <th class="py-3">Time</th>
                                    <th class="py-3">Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if ($attendances->count())
                                    @foreach ($attendances as $attendance)
                                        <tr>
                                            <td> {{ $loop->iteration }} </td>
                                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                                            <td> Semester {{ $attendance->semester ?? '-' }} </td>
                                            <td> {{ $attendance->subject->subject_name ?? '-' }} </td>
                                            <td> {{ $attendance->student->roll_no ?? '-' }} </td>
                                            <td> {{ $attendance->student->name ?? '-' }} </td>
                                            <td>
                                                {{ $attendance->time ? \Carbon\Carbon::parse($attendance->time)->format('h:i A') : '-' }}
                                            </td>
                                            <td>
                                                @if ($attendance->status == 'present')
                                                    <button class="btn btn-success btn-sm fw-bold rounded-3 action-btn"
                                                        style="font-size:10px; cursor:default;" disabled>
                                                        <i class="bi bi-check-circle"></i> &nbsp; Present
                                                    </button>
                                                @else
                                                    <button class="btn btn-danger btn-sm fw-bold rounded-3 action-btn"
                                                        style="font-size:10px; cursor:default;" disabled>
                                                        <i class="bi bi-x-circle"></i> &nbsp; Absent
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-3">
                                            No attendance records found.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div id="pagination-data">
                        @if ($attendances->hasPages())
                            @include('layouts.pagination', ['paginator' => $attendances])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

{{-- Semester Fliter --}}
<script>
    $(document).on('click', '.semester-btn', function() {

        $('.semester-btn')
            .removeClass('active btn-primary')
            .addClass('btn-outline-primary');

        $(this)
            .removeClass('btn-outline-primary')
            .addClass('active btn-primary');

        let semester = $(this).data('semester');

        window.location.href = "{{ route('teacher.attendancerecords') }}?semester=" + semester;
    });
</script>
