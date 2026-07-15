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
            <div class="container-fluid py-2">
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

            <!-- Quick Actions -->
            <div class="card shadow-sm border-0 rounded-4 mx-2 my-2">
                <div class="card-body">
                    <h5 class="fw-semibold mb-4">
                        <i class="bi bi-lightning-charge-fill text-warning me-2"></i>
                        Quick Actions
                    </h5>

                    <div class="row g-3">

                        <!-- Student Management -->
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.students') }}" class="text-decoration-none">
                                <div class="quick-card h-100">
                                    <i class="bi bi-people-fill quick-icon fs-2"></i>
                                    <h6 class="fw-bold mt-3 mb-2 text-dark"> Student Management </h6>
                                    <p class="text-muted small mb-3"> Add, edit and manage student records. </p>
                                    <span class="quick-link"> Manage Students → </span>
                                </div>
                            </a>
                        </div>

                        <!-- Teacher Management -->
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.teachers') }}" class="text-decoration-none">
                                <div class="quick-card h-100">
                                    <i class="bi bi-person-workspace quick-icon fs-2 text-success"></i>
                                    <h6 class="fw-bold mt-3 mb-2 text-dark"> Teacher Management </h6>
                                    <p class="text-muted small mb-3"> Manage teacher profiles and assignments. </p>
                                    <span class="quick-link"> Manage Teachers → </span>
                                </div>
                            </a>
                        </div>

                        <!-- Subject Management -->
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.subjects') }}" class="text-decoration-none">
                                <div class="quick-card h-100">
                                    <i class="bi bi-book-half quick-icon fs-2 text-danger"></i>
                                    <h6 class="fw-bold mt-3 mb-2 text-dark"> Subject Management </h6>
                                    <p class="text-muted small mb-3"> Manage subjects and their assignments. </p>
                                    <span class="quick-link"> Manage Subjects → </span>
                                </div>
                            </a>
                        </div>

                        <!-- Assign Class -->
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.assignclass') }}" class="text-decoration-none">
                                <div class="quick-card h-100">
                                    <i class="bi bi-diagram-3-fill quick-icon fs-2 text-warning"></i>
                                    <h6 class="fw-bold mt-3 mb-2 text-dark"> Assign Class </h6>
                                    <p class="text-muted small mb-3"> Assign teachers to subjects and semesters. </p>
                                    <span class="quick-link"> Manage Assignments → </span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mx-2 my-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold mb-0">
                            <i class="bi bi-bar-chart-fill text-primary me-2"></i>
                            Students by Semester
                        </h5>

                        <span class="badge bg-light text-muted fs-6 rounded-3 px-3 py-2">
                            Total Students: {{ $studentsCount }}
                        </span>

                    </div>
                    <canvas id="semesterChart" height="80"></canvas>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('semesterChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($semesterLabels),
                datasets: [{
                    label: 'Students',
                    data: @json($semesterData),
                    borderWidth: 1,
                    borderRadius: 8,
                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#dc3545',
                        '#ffc107',
                        '#6f42c1',
                        '#20c997',
                        '#fd7e14',
                        '#4F46E5'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        title: {
                            display: true,
                            text: 'Number of Students',
                            color: '#212529',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Semester',
                            color: '#212529',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
