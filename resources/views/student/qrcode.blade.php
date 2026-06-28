<head>
    <title>MY QR Code - Student</title>
    @include('layouts.link')
    @include('layouts.style')
</head>

<body>
    <div class="main-wrapper">
        @include('student.sidebar')
        <div class="main-area">
            @include('student.navbar')
            <div class="main-content">
                <div class="card shadow-sm border-0 rounded-4 mx-2 my-2">
                    <div class="card-body text-center p-5 pb-4">

                        <h4 class="fw-bold mb-4">
                           My QR Code
                        </h4>

                        <div class="bg-light rounded-4 p-4 d-inline-block">
                            {!! $qr !!}
                        </div>

                        <h5 class="mt-4 fw-semibold">
                            {{ $student->name }}
                        </h5>

                        <p class="text-muted mb-1">
                            Student Code:
                            {{ $student->student_code }}
                        </p>

                        <p class="text-muted">
                            Semester:
                            {{ $student->current_semester }}
                        </p>

                        <p class="small text-muted mt-4">
                            Show this QR code to teacher while taking attendance
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
