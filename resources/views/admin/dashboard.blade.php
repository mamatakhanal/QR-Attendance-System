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
            <div class="container-fluid px-4 py-3">
                <div class="row g-4">
                    <!-- Teachers -->
                    <div class="col-md-4 col-sm-6">
                        <div class="card text-white bg-primary shadow-sm dashboard-card py-2 px-3">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h5>Total Teachers</h5>
                                    <h2 class="fw-bold mb-0">{{ $teachersCount }}</h2>
                                </div>
                                <i class="bi bi-person-workspace fs-1"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Students -->
                    <div class="col-md-4 col-sm-6">
                        <div class="card text-white bg-success shadow-sm dashboard-card py-2 px-3">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h5>Total Students</h5>
                                    <h2 class="fw-bold mb-0">{{ $studentsCount }}</h2>
                                </div>
                                <i class="bi bi-people-fill fs-1"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Subjects -->
                    <div class="col-md-4 col-sm-6">
                        <div class="card text-white bg-danger shadow-sm dashboard-card py-2 px-3">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h5>Total Subjects</h5>
                                    <h2 class="fw-bold mb-0">{{ $subjectsCount }}</h2>
                                </div>
                                <i class="bi bi-book-half fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
