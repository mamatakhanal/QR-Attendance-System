<head>
    <title>Assign Classes - Teacher</title>
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
                <h5 class="fw-semibold mb-4">
                    Assigned Classes List
                </h5>
                <div class="table-responsive rounded-2">
                    <table class="table table-hover border-3 mb-0 align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th class="py-3">S.N</th>
                                <th class="py-3">Semester</th>
                                <th class="py-3">Subjects</th>
                                <th class="py-3">Code</th>
                                <th class="py-3">Class Time</th>
                                {{-- Show Replacement column only if replacement exists --}}
                                @if ($replacements->isNotEmpty())
                                    <th class="py-3">Replacement Time</th>
                                @endif
                                {{-- <th class="py-3">Students</th> --}}
                                <th class="py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignclasses as $assignclass)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>
                                    <td>
                                        Semester {{ $assignclass->semester }}
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($assignclass->subjects as $subject)
                                                <div class="px-2 py-1 border rounded-3 shadow-sm bg-light">
                                                    {{ $subject->subject_name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        {{ $assignclass->subjects->first()->subject_code ?? '-' }}
                                    </td>
                                    {{-- Normal Time --}}
                                    <td>
                                        @if ($assignclass->start_time && $assignclass->end_time)
                                            {{ \Carbon\Carbon::parse($assignclass->start_time)->format('h:i A') }}
                                            -
                                            {{ \Carbon\Carbon::parse($assignclass->end_time)->format('h:i A') }}
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </td>

                                    {{-- Replacement Time --}}
                                    @if ($replacements->isNotEmpty())
                                        <td>

                                            @if ($assignclass->is_replacement_today)
                                                {{ \Carbon\Carbon::parse($assignclass->replacement_start_time)->format('h:i A') }}
                                                -
                                                {{ \Carbon\Carbon::parse($assignclass->replacement_end_time)->format('h:i A') }}
                                            @else
                                                <span class="text-muted">
                                                    —
                                                </span>
                                            @endif
                                        </td>
                                    @endif
                                    {{-- <td>
                                        <a href="{{ route('teacher.students', ['semester' => $assignclass->semester]) }}"
                                            class="text-decoration-none text-dark fw-semibold">
                                            {{ $assignclass->student_count }} Students
                                        </a>
                                    </td> --}}

                                    <td>
                                        @if ($assignclass->attendance_status === 'Blocked')
                                            <span class="badge bg-danger rounded-3 px-3 py-2" style="font-size:12px;">
                                                <i class="bi bi-slash-circle me-1"></i>
                                                Blocked
                                            </span>
                                        @elseif ($assignclass->attendance_status === 'Taken')
                                            <span class="badge bg-success rounded-3 px-3 py-2" style="font-size:12px;">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Taken
                                            </span>
                                        @elseif ($assignclass->attendance_status === 'In Progress')
                                            <span class="badge bg-warning text-dark rounded-3 px-3 py-2"
                                                style="font-size:12px;">
                                                <i class="bi bi-hourglass-split me-1"></i>
                                                In Progress
                                            </span>
                                        @else
                                            <span class="badge bg-secondary rounded-3 px-3 py-2"
                                                style="font-size:12px;">
                                                <i class="bi bi-dash-circle me-1"></i>
                                                Not Taken
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="pagination-data">
                    @if ($assignclasses->hasPages())
                        @include('layouts.pagination', ['paginator' => $assignclasses])
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
