<head>
    <title>Classes - Student</title>
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
                <div class="card shadow-sm border-0 rounded-4 mx-2 my-2">

                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold mb-0">
                                My Classes
                            </h5>

                            <span class="bg-light fw-semibold text-dark rounded-3 px-3 py-2">
                                Semester {{ $student->current_semester }}
                            </span>
                        </div>

                        <div class="table-responsive rounded-2">
                            <table class="table table-hover mb-0">
                                <thead class="table-secondary">
                                    <tr>
                                        <th class="py-3">S.N</th>
                                        <th class="py-3">Subject Code</th>
                                        <th class="py-3">Subject</th>
                                        <th class="py-3">Teacher</th>
                                        <th class="py-3">Contact</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        $sn = 1;
                                    @endphp
                                    @foreach ($classes as $class)
                                        @foreach ($class->subjects as $subject)
                                            <tr>
                                                <td class="pt-3"> {{ $sn++ }} </td>
                                                <td class="pt-3"> {{ $subject->subject_code }} </td>
                                                <td class="pt-3"> {{ $subject->subject_name }} </td>
                                                <td class="pt-3 fw-semibold"> {{ $class->teacher->name ?? 'N/A' }} </td>
                                                <td>
                                                    <div>
                                                        <i class="bi bi-envelope me-1 text-muted"></i>
                                                        {{ $class->teacher->email ?? 'N/A' }}
                                                    </div>
                                                    <div class="mt-2">
                                                        <i class="bi bi-telephone me-1 text-muted"></i>
                                                        {{ $class->teacher->phone ?? 'N/A' }}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach

                                    @if ($classes->count() == 0)
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-3">
                                                No classes assigned yet
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
