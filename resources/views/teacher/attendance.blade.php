<head>
    <title>Attendance - Teacher</title>
    @include('layouts.link')
    @include('layouts.style')
</head>


<body>

    <div class="main-wrapper">
        @include('teacher.sidebar')
        <div class="main-area">
            @include('teacher.navbar')

            <div class="main-content">
                <div class="card shadow-sm border-0 rounded-4 mx-2 my-2 p-2">
                    <h5 class="fw-semibold px-3 pt-3">
                        Take Attendance
                    </h5>

                    <div class="card-body">
                        <div class="row align-items-end g-3">

                            <!-- Select Class -->
                            <div class="col-md-9">
                                <label class="form-label fw-semibold">
                                    Select Class
                                </label>

                                <select class="form-select" id="class_id">
                                    <option value=""> Select Class </option>

                                    @foreach ($assignclasses as $assignclass)
                                        @foreach ($assignclass->subjects as $subject)
                                            <option value="{{ $assignclass->id }}"
                                                data-semester="{{ $assignclass->semester }}">
                                                Semester {{ $assignclass->semester }}
                                                -
                                                {{ $subject->subject_name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>

                            <!-- Button -->
                            <div class="col-md-3">
                                <button id="startScanner" class="btn btn-primary w-100 rounded-3 py-2">
                                    <i class="bi bi-qr-code-scan me-2"></i>
                                    Start Attendance
                                </button>
                            </div>
                            <div id="reader" class="mt-4" style="width:400px; display:none;"></div>
                        </div>

                        <!-- Instruction -->
                        <p class="text-muted small mt-1">
                            Select class before starting attendance
                        </p>

                    </div>
                </div>
            </div>
        </div>
        <script src="https://unpkg.com/html5-qrcode"></script>

        <script>
            $(document).ready(function() {
                let html5QrCode;

                $('#startScanner').click(function() {

                    let classId = $('#class_id').val();

                    if (classId == "") {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Please select class first.',
                            showConfirmButton: false,
                            timer: 1000,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'small-toast'
                            },
                            showClass: {
                                popup: 'animate__animated animate__fadeInRight'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutRight'
                            }
                        });
                        return;
                    }

                    // Start QR Scanner
                    $('#reader').show();
                    html5QrCode = new Html5Qrcode("reader");
                    Html5Qrcode.getCameras().then(devices => {
                        if (devices && devices.length) {
                            html5QrCode.start(
                                devices[0].id, {
                                    fps: 10,
                                    qrbox: 250
                                },
                                function(decodedText) {
                                    console.log(decodedText);
                                }
                            );
                        }
                    });
                });
            });
        </script>

</body>
