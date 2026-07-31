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

                <!-- Today's Classes -->
                <div class="card shadow-sm border-0 rounded-4 my-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center my-1 mb-2">
                            <h5 class="fw-semibold">
                                <i class="bi bi-calendar-check text-primary me-2"></i>
                                Today's Classes
                            </h5>
                            <span class="badge bg-light text-dark fs-6 px-3 py-2">
                                {{ now()->format('d M Y') }}
                            </span>
                        </div>


                        @if (count($todayClasses) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Subject</th>
                                            <th>Teacher</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($todayClasses as $class)
                                            <tr>
                                                <td>
                                                    <span class="fw-semibold">
                                                        {{ $class['subject'] }}
                                                    </span>
                                                </td>

                                                <td>
                                                    {{ $class['teacher'] }}
                                                </td>

                                                <td>
                                                    @if ($class['time'] != '-')
                                                        <i class="bi bi-clock me-1 text-muted"></i>
                                                        {{ $class['time'] }}
                                                    @else
                                                        <span class="text-muted">
                                                            -
                                                        </span>
                                                    @endif
                                                </td>

                                                <td>
                                                    @if ($class['status'] === 'Taken')
                                                        <span class="badge bg-success rounded-3 px-3 py-2">
                                                            <i class="bi bi-check-circle-fill me-1"></i>
                                                            Taken
                                                        </span>
                                                    @elseif ($class['status'] === 'Open')
                                                        <span class="badge bg-warning text-dark rounded-3 px-3 py-2">
                                                            <i class="bi bi-hourglass-split me-1"></i>
                                                            Attendance Open
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary rounded-3 px-3 py-2">
                                                            <i class="bi bi-dash-circle me-1"></i>
                                                            Not Taken
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                <p class="mb-0">
                                    No classes available for today.
                                </p>
                            </div>
                        @endif
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
                {{-- <div class="card shadow-sm border-0 rounded-4 my-2 mb-0">
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
                </div> --}}

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
