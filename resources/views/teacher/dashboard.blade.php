<head>
    <title>Dashboard - Teacher</title>
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
            <div class="container-fluid py-2">
                <div class="row g-4">
                    <!-- Total Classes -->
                    <div class="col-md-4 col-sm-6">
                        <a href="{{ route('teacher.assignclass') }}" class="text-decoration-none">
                            <div class="card text-white bg-primary shadow-sm dashboard-card py-2 px-3">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5>Total Semester</h5>
                                        <h2 class="fw-bold mb-0">{{ $totalClasses }} </h2>
                                    </div>
                                    <i class="bi bi-building fs-1"></i>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Total Subjects -->
                    <div class="col-md-4 col-sm-6 ">
                        <a href="{{ route('teacher.assignclass') }}" class="text-decoration-none">
                            <div class="card text-white bg-success shadow-sm dashboard-card py-2 px-3">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5>Total Subjects</h5>
                                        <h2 class="fw-bold mb-0"> {{ $totalSubjects }} </h2>
                                    </div>
                                    <i class="bi bi-book fs-1"></i>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Total Students -->
                    <div class="col-md-4 col-sm-6">
                        <a href="{{ route('teacher.students') }}" class="text-decoration-none">
                            <div class="card text-white bg-warning shadow-sm dashboard-card py-2 px-3">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5>Total Students</h5>
                                        <h2 class="fw-bold mb-0"> {{ $totalStudents }} </h2>
                                    </div>
                                    <i class="bi bi-people-fill fs-1"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>


            <!-- Quick Actions -->
            <div class="card shadow-sm border-0 rounded-4 mx-2 my-2">
                <div class="card-body">
                    <h5 class="fw-semibold mb-4">
                        <i class="bi bi-lightning-charge-fill text-warning"></i>
                        Quick Actions
                    </h5>

                    <div class="row g-3">

                        <!-- My Assignments -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('teacher.assignclass') }}" class="text-decoration-none">
                                <div class="quick-card text-center">
                                    <i class="bi bi-journal-bookmark quick-icon fs-2 text-warning"></i>
                                    <h6 class="fw-semibold mt-3 mb-2 text-dark">My Assignments</h6>
                                    <p class="text-muted small mb-3">
                                        View your assigned semesters and subjects.
                                    </p>
                                    <span class="quick-link">
                                        Open <i class="bi bi-arrow-right"></i>
                                    </span>
                                </div>
                            </a>
                        </div>

                        <!-- View Students -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('teacher.students') }}" class="text-decoration-none">
                                <div class="quick-card text-center">
                                    <i class="bi bi-people-fill quick-icon fs-2 text-success"></i>
                                    <h6 class="fw-semibold mt-3 mb-2 text-dark">View Students</h6>
                                    <p class="text-muted small mb-3">
                                        Browse students assigned to your semesters.
                                    </p>
                                    <span class="quick-link">
                                        Open <i class="bi bi-arrow-right"></i>
                                    </span>
                                </div>
                            </a>
                        </div>

                        <!-- Take Attendance -->
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('teacher.attendance') }}" class="text-decoration-none">
                                <div class="quick-card text-center">
                                    <i class="bi bi-qr-code-scan quick-icon fs-2 text-info"></i>
                                    <h6 class="fw-bold mt-3 mb-2 text-dark">Take Attendance</h6>
                                    <p class="text-muted small mb-3">
                                        Scan student QR codes and record attendance.
                                    </p>
                                    <span class="quick-link">
                                        Open <i class="bi bi-arrow-right"></i>
                                    </span>
                                </div>
                            </a>
                        </div>

                        <!-- Attendance Records -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('teacher.attendancerecords') }}" class="text-decoration-none">
                                <div class="quick-card text-center">
                                    <i class="bi bi-calendar-check quick-icon fs-2 text-danger"></i>
                                    <h6 class="fw-semibold mt-3 mb-2 text-dark">Attendance Records</h6>
                                    <p class="text-muted small mb-3">
                                        View attendance history and records.
                                    </p>
                                    <span class="quick-link">
                                        Open <i class="bi bi-arrow-right"></i>
                                    </span>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
</body>
