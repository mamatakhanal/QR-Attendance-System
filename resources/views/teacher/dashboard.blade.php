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
        </div>
    </div>
</body>
