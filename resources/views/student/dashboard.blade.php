<head>
    <title>Dashboard - Student</title>
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
            <div class="container-fluid py-2">
                <div class="row g-4">
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('student.classes') }}" class="text-decoration-none">
                            <div class="card text-white bg-primary shadow-sm dashboard-card py-2 px-3">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5> Total Classes </h5>
                                        <h2 class="fw-bold mb-0"> {{ $totalSubjects }} </h2>
                                    </div>
                                    <i class="bi bi-building fs-1"></i>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('student.attendance') }}" class="text-decoration-none">
                            <div class="card text-white bg-success shadow-sm dashboard-card py-2 px-3">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5> Present </h5>
                                        <h2 class="fw-bold mb-0"> {{ $present }} </h2>
                                    </div>
                                    <i class="bi bi-check-circle-fill fs-1"></i>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('student.attendance') }}" class="text-decoration-none">
                            <div class="card text-white bg-danger shadow-sm dashboard-card py-2 px-3">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5> Absent </h5>
                                        <h2 class="fw-bold mb-0"> {{ $absent }} </h2>
                                    </div>
                                    <i class="bi bi-x-circle-fill fs-1"></i>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('student.attendance') }}" class="text-decoration-none">
                            <div class="card text-white bg-info shadow-sm dashboard-card py-2 px-3">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5> Attendance % </h5>
                                        <h2 class="fw-bold mb-0"> {{ $percentage }}% </h2>
                                    </div>
                                    <i class="bi bi-qr-code-scan fs-1"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
