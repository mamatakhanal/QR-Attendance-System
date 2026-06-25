<head>
    <title>Dashboard - Admin</title>
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
            <div class="container-fluid px-4 py-1">
                <div class="row g-4">
                    <!-- Teachers -->
                    <div class="col-md-4 col-sm-6">
                        <a href="{{ route('admin.teachers') }}" class="text-decoration-none">
                            <div class="card text-white bg-primary shadow-sm dashboard-card py-2 px-3">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5>Total Teachers</h5>
                                        <h2 class="fw-bold mb-0">{{ $teachersCount }}</h2>
                                    </div>
                                    <i class="bi bi-person-workspace fs-1"></i>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Students -->
                    <div class="col-md-4 col-sm-6">
                        <a href="{{ route('admin.students') }}" class="text-decoration-none">
                            <div class="card text-white bg-success shadow-sm dashboard-card py-2 px-3">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5>Total Students</h5>
                                        <h2 class="fw-bold mb-0">{{ $studentsCount }}</h2>
                                    </div>
                                    <i class="bi bi-people-fill fs-1"></i>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Subjects -->
                    <div class="col-md-4 col-sm-6">
                        <a href="{{ route('admin.subjects') }}" class="text-decoration-none">
                            <div class="card text-white bg-danger shadow-sm dashboard-card py-2 px-3">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5>Total Subjects</h5>
                                        <h2 class="fw-bold mb-0">{{ $subjectsCount }}</h2>
                                    </div>
                                    <i class="bi bi-book-half fs-1"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Students By Semester -->
            {{-- <div class="card shadow-sm border-0 rounded-4 mx-4 p-2 my-2 pb-0">
                <div class="card-body">
                    <h5 class="fw-semibold mb-4">
                        <i class="bi bi-bar-chart-fill"></i>
                        Students By Semester
                    </h5>
                    @foreach ($studentsBySemester as $semester)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>
                                    Semester {{ $semester->current_semester }}
                                </span>
                                <span class="fw-bold">
                                    {{ $semester->total }}
                                </span>
                            </div>
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar"
                                    style="width: {{ ($semester->total / $studentsBySemester->max('total')) * 100 }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div> --}}

            <!-- Attendance Overview -->
            <div class="card shadow-sm border-0 rounded-4 mx-4 p-1 my-2 pb-0">
                 <div class="card-body">
                    <h5 class="fw-semibold mb-4">
                        <i class="bi bi-calendar-check"></i>
                        Attendance Overview
                    </h5>

                    @foreach ($attendanceBySemester as $attendance)
                        @php
                            $total = $attendance->present + $attendance->absent;
                            $percentage = $total > 0 ? ($attendance->present / $total) * 100 : 0;
                        @endphp

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>
                                    Semester {{ $attendance->semester }}
                                </span>

                                <span class="fw-bold">
                                    Present: {{ $attendance->present }}
                                    &nbsp;
                                    Absent: {{ $attendance->absent }}
                                </span>
                            </div>
                            <div class="progress mt-1" style="height:4px;">
                                <div class="progress-bar bg-success" style="width: {{ $percentage }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</body>
