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
                        Attendance Records List
                    </h5>

                    </form>
                    {{-- Filter --}}
                    <form method="GET" action="{{ route('teacher.attendancerecords') }}">
                        <div class="row g-3">

                            {{-- Search --}}
                            <div class="col-md-2">
                                <input type="text" name="search" class="form-control" placeholder="Search Student..."
                                    value="{{ request('search') }}">
                            </div>

                            {{-- Semester --}}
                            <div class="col-md-2">
                                <select name="semester" class="form-select">
                                    <option value="">All Semester</option>

                                    @foreach ($assignedSemesters as $semester)
                                        <option value="{{ $semester }}"
                                            {{ request('semester') == $semester ? 'selected' : '' }}>
                                            Semester {{ $semester }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Subject --}}
                            <div class="col-md-2">
                                <select name="subject_id" class="form-select">
                                    <option value="">All Subjects</option>

                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}"
                                            {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->subject_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Date --}}
                            <div class="col-md-2">
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>

                            {{-- Status --}}
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>

                                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>
                                        Present
                                    </option>

                                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>
                                        Absent
                                    </option>
                                </select>
                            </div>

                            {{-- Search Button --}}
                            <div class="col-md-1 d-grid">
                                <button class="btn btn-primary">
                                    Search
                                </button>
                            </div>

                            {{-- Reset Button --}}
                            <div class="col-md-1 d-grid">
                                <a href="{{ route('teacher.attendancerecords') }}" class="btn btn-outline-secondary">
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
                                            No attendance records found for this semester.
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

