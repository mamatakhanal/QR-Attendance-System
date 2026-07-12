<head>
    <title>Attendance - Admin</title>
    @include('layouts.link')
    @include('layouts.style')
</head>

<body>
    <!-- MAIN LAYOUT -->
    <div class="main-wrapper">
        @include('admin.sidebar')
        <div class="main-area">
            @include('admin.navbar')

            <!-- CONTENT -->
            <div class="main-content">
                <div class="card shadow-sm border-0 rounded-4 mx-2 my-2 p-4">
                    <h5 class="fw-semibold mb-3">
                        Attendance List
                    </h5>

                    {{-- Fliter --}}
                    <form method="GET">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <input type="text" name="search" class="form-control" placeholder="Search Student..."
                                    value="{{ request('search') }}">
                            </div>

                            <div class="col-md-2">
                                <select name="teacher_id" class="form-select">
                                    <option value="">All Teachers</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}"
                                            {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>

                                            {{ $teacher->name }}

                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="semester" class="form-select">
                                    <option value="">All Semester</option>
                                    @for ($i = 1; $i <= 8; $i++)
                                        <option value="{{ $i }}"
                                            {{ request('semester') == $i ? 'selected' : '' }}>
                                            Semester {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-2">
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>

                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                </select>
                            </div>

                            <div class="col-md-1 d-grid">
                                <button class="btn btn-primary">
                                    Search
                                </button>
                            </div>
                            <div class="col-md-1 d-grid">
                                <a href="{{ route('admin.attendance') }}" class="btn btn-outline-secondary">
                                    Reset
                                </a>
                            </div>
                        </div>

                    </form>

                    <div class="table-responsive rounded-2">
                        <table class="table table-hover border-3 mb-0">
                            <thead class="table-secondary">
                                <tr>
                                    <th class="py-3">S.N</th>
                                    <th class="py-3">Date</th>
                                    <th class="py-3">Teacher</th>
                                    <th class="py-3">Semester</th>
                                    <th class="py-3">Subject</th>
                                    <th class="py-3">Roll No</th>
                                    <th class="py-3">Student</th>
                                    <th class="py-3">Time</th>
                                    <th class="py-3">Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($attendances as $attendance)
                                    <tr>
                                        <td>{{ $attendances->firstItem() + $loop->index }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}
                                        </td>
                                        <td>
                                            {{ $attendance->teacher->name ?? '-' }}
                                        </td>
                                        <td>
                                            Semester {{ $attendance->semester }}
                                        </td>
                                        <td>
                                            {{ $attendance->subject->subject_name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $attendance->student->roll_no ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $attendance->student->name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $attendance->time ? \Carbon\Carbon::parse($attendance->time)->format('h:i A') : '-' }}
                                        </td>
                                        <td>
                                            @if ($attendance->status == 'present')
                                                <button class="btn btn-success btn-sm fw-bold rounded-3"
                                                    style="font-size:10px;" disabled>
                                                    <i class="bi bi-check-circle"></i>
                                                    Present
                                                </button>
                                            @else
                                                <button class="btn btn-danger btn-sm fw-bold rounded-3"
                                                    style="font-size:10px;" disabled>
                                                    <i class="bi bi-x-circle"></i>
                                                    Absent
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            No attendance records found.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        @if ($attendances->hasPages())
                            @include('layouts.pagination', ['paginator' => $attendances])
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
