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
                    <table class="table table-hover border-3 mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th class="py-3">S.N</th>
                                <th class="py-3">Semester</th>
                                <th class="py-3">Subjects</th>
                                <th class="py-3">Students</th>
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
                                        {{ $assignclass->student_count }} Students
                                    </td>
                                    <td>
                                        <a href="{{ route('teacher.attendance', $assignclass->id) }}"
                                            class="btn btn-sm btn-primary rounded-3">
                                            <i class="bi bi-qr-code-scan"></i> 
                                            Attendance
                                        </a>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
