<head>
    <title>Attendance - Student</title>
    @include('layouts.link')
    @include('layouts.style')
</head>

<body>

    <!-- MAIN LAYOUT -->
    <div class="main-wrapper">
        @include('student.sidebar')
        <div class="main-area">
            @include('student.navbar')

            <!-- CONTENT -->
            <div class="main-content">
                <div class="card shadow-sm border-0 rounded-4 mx-2 my-2 p-4">
                    <h5 class="fw-semibold mb-3">
                        My Attendance
                    </h5>

                    <form method="GET" action="{{ route('student.attendance') }}">
                        <div class="row g-3">

                            <div class="col-md-3">
                                <select class="form-select" name="teacher_id">
                                    <option value="">All Teachers</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}"
                                            {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select class="form-select" name="subject_id">
                                    <option value="">All Subjects</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}"
                                            {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->subject_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                            </div>

                            <div class="col-md-2">
                                <select class="form-select" name="status">
                                    <option value="all">All Status</option>

                                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>
                                        Present
                                    </option>

                                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>
                                        Absent
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-1 d-grid">
                                <button class="btn btn-primary">
                                    Search
                                </button>
                            </div>

                            <div class="col-md-1 d-grid">
                                <a href="{{ route('student.attendance') }}" class="btn btn-outline-secondary">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>


                    <div class="table-responsive rounded-3">
                        <table class="table table-hover mb-0">
                            <thead class="table-secondary">
                                <tr>
                                    <th class="py-3">S.N</th>
                                    <th class="py-3">Date</th>
                                    <th class="py-3">Subject</th>
                                    <th class="py-3">Teacher</th>
                                    <th class="py-3">Time</th>
                                    <th class="py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                    <tr>
                                        <td> {{ $attendances->firstItem() + $loop->index }} </td>
                                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                                        <td> {{ $attendance->subject->subject_name ?? '-' }} </td>
                                        <td> {{ $attendance->teacher->name ?? '-' }} </td>
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
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No attendance records found for this subject.
                                        </td>
                                    </tr>
                                @endforelse
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
    </div>
</body>


{{-- Basanta Sir 
INSERT INTO `attendance` (`id`, `semester`, `student_id`, `subject_id`, `teacher_id`, `date`, 
`time`, `status`, `created_at`, `updated_at`) VALUES ('1', '4', '11', '41', '4', '2026/7/16', '01:00',
 'present', current_timestamp(), current_timestamp()); --}}
