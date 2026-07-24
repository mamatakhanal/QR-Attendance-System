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


                <!-- Attendance Summary -->
                <div class="card shadow-sm border-0 rounded-4 my-3">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">
                            <i class="bi bi-bar-chart-fill text-primary me-2"></i>
                            Attendance by Subject
                        </h5>
                        <canvas id="attendanceChart" height="80"></canvas>
                    </div>
                </div>


                <!-- Subject Attendance Status -->
                <div class="card shadow-sm border-0 rounded-4 my-3 mb-0">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-award-fill text-success me-2"></i>
                            Subject Attendance Status
                        </h5>

                        @foreach ($subjectStatuses as $status)
                            <div
                                class="alert alert-{{ $status['class'] }} d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold">
                                        <i class="bi {{ $status['icon'] }} me-2"></i>
                                        {{ $status['subject'] }}
                                    </h6>
                                    <small>
                                        <strong>{{ $status['title'] }}</strong> -
                                        {{ $status['message'] }}
                                    </small>
                                </div>
                                <span class="badge bg-dark fs-6">
                                    {{ $status['percentage'] }}%
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('attendanceChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($subjectLabels),
                datasets: [{
                    label: 'Attendance %',
                    data: @json($subjectPercentages),
                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#ffc107'
                    ],
                    borderRadius: 8,
                    barThickness: 40
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                

                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + "%";
                            }
                        }
                    }
                },

                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + "%";
                            }
                        },
                        title: {
                            display: true,
                            text: "Attendance Percentage",
                            color: '#54595e',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },

                    y: {
                        title: {
                            display: true,
                            text: "Subjects",
                            color: '#54595e',
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
