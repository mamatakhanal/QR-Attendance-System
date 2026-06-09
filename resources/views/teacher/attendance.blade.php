<head>
    <title>Attendance - Teacher</title>
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
            <div class="main-content">

                <h1 class="fw-semibold mb-4">
                    Attendance
                </h1>


                <div class="container-fluid">

                    <!-- PAGE TITLE -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-semibold">Take Attendance</h3>
                    </div>

                    <!-- QR ATTENDANCE CARD -->
                    <div class="card shadow-sm border-0 rounded-4">

                        <div class="card-body">

                            <div class="row align-items-center">

                                <!-- LEFT SIDE -->
                                <div class="col-md-4 text-center border-end">

                                    <h5 class="fw-semibold mb-3">
                                        QR Attendance Scanner
                                    </h5>

                                    <button id="startScanner"
                                        class="btn btn-primary px-4 py-2 rounded-pill">
                                        Open Camera
                                    </button>

                                    <button id="stopScanner"
                                        class="btn btn-danger px-4 py-2 rounded-pill mt-2">
                                        Stop Camera
                                    </button>

                                </div>

                                <!-- RIGHT SIDE -->
                                <div class="col-md-8">

                                    <!-- CAMERA -->
                                    <div id="reader"
                                        style="width:100%; max-width:500px;">
                                    </div>

                                    <!-- RESULT -->
                                    <div class="mt-3">

                                        <h6>Scan Result:</h6>

                                        <div id="result"
                                            class="alert alert-success d-none">
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>