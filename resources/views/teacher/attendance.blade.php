<head>
    <title>Attendance - Teacher</title>
    @include('layouts.link')
    @include('layouts.style')
</head>

<body>

    <div @class(['main-wrapper'])>
        @include('teacher.sidebar')
        <div @class(['main-area'])>
            @include('teacher.navbar')
            <div @class(['main-content'])>
                <div @class([
                    'card',
                    'shadow-sm',
                    'border-0',
                    'rounded-4',
                    'mx-2',
                    'my-2',
                    'p-2',
                ])>

                    <h5 @class(['fw-semibold', 'px-3', 'pt-3'])>
                        Take Attendance
                    </h5>

                    <div @class(['card-body'])>

                        <div @class(['row', 'align-items-end', 'g-3'])>

                            <!-- Select Class -->
                            <div @class(['col-md-9'])>

                                <label @class(['form-label', 'fw-semibold'])>
                                    Select Class
                                </label>

                                <select class="form-select" id="class_id">

                                    <option value="">
                                        Select Class
                                    </option>


                                    @foreach ($assignclasses as $assignclass)
                                        @foreach ($assignclass->subjects as $subject)
                                            <option value="{{ $assignclass->id }}"
                                                {{ isset($selectedClass) && $selectedClass == $assignclass->id ? 'selected' : '' }}
                                                data-semester="{{ $assignclass->semester }}"
                                                data-subject="{{ $subject->subject_name }}"
                                                data-students="{{ $assignclass->student_count }}">

                                                Semester {{ $assignclass->semester }}
                                                -
                                                {{ $subject->subject_name }}

                                            </option>
                                        @endforeach
                                    @endforeach

                                </select>


                                @if ($currentClass)
                                    <script>
                                        document.addEventListener(
                                            "DOMContentLoaded",
                                            function() {

                                                document
                                                    .getElementById(
                                                        'infoSemester'
                                                    )
                                                    .innerText =
                                                    "Semester {{ $currentClass->semester }}";


                                                document
                                                    .getElementById(
                                                        'infoSubject'
                                                    )
                                                    .innerText =
                                                    "{{ $currentClass->subjects->first()->subject_name ?? '-' }}";


                                                document
                                                    .getElementById(
                                                        'totalStudents'
                                                    )
                                                    .innerText =
                                                    "{{ $currentClass->student_count ?? 0 }}";

                                            }
                                        );
                                    </script>
                                @endif

                            </div>


                            <!-- Button -->
                            <div @class(['col-md-3'])>

                                <button id="openScanner" @class(['btn', 'btn-primary', 'w-100', 'rounded-3', 'py-2'])>

                                    <i @class(['bi', 'bi-qr-code-scan', 'me-2'])></i>

                                    Start Attendance

                                </button>

                            </div>

                        </div>


                        <!-- Instruction -->
                        <p @class(['text-muted', 'small', 'mt-3'])>
                            Select class before starting attendance
                        </p>


                        <div @class(['row', 'mt-4', 'justify-content-between'])>


                            <!-- Class Information -->
                            <div @class(['col-lg-5', 'mb-3'])>

                                <div @class(['card', 'border-2', 'shadow-sm', 'rounded-4', 'h-100'])>


                                    <div @class([
                                        'card-header',
                                        'bg-light',
                                        'border-bottom',
                                        'rounded-top-4',
                                        'py-3',
                                    ])>

                                        <h6 @class(['mb-0', 'fw-semibold', 'text-dark'])>

                                            <i @class(['bi', 'bi-journal-bookmark-fill', 'text-primary', 'me-2'])></i>

                                            Current Class

                                        </h6>

                                    </div>


                                    <div @class(['card-body'])>


                                        <div @class([
                                            'd-flex',
                                            'justify-content-between',
                                            'align-items-center',
                                            'py-2',
                                            'border-bottom',
                                        ])>

                                            <strong @class(['text-muted'])>
                                                Semester
                                            </strong>

                                            <strong id="infoSemester">
                                                -
                                            </strong>

                                        </div>


                                        <div @class([
                                            'd-flex',
                                            'justify-content-between',
                                            'align-items-center',
                                            'py-2',
                                            'border-bottom',
                                        ])>

                                            <strong @class(['text-muted'])>
                                                Subject
                                            </strong>

                                            <strong id="infoSubject">
                                                -
                                            </strong>

                                        </div>


                                        <div @class([
                                            'd-flex',
                                            'justify-content-between',
                                            'align-items-center',
                                            'py-2',
                                        ])>

                                            <strong @class(['text-muted'])>
                                                Teacher
                                            </strong>

                                            <strong>
                                                {{ $teacher->name }}
                                            </strong>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- Today's Attendance -->
                            <div @class(['col-lg-5', 'mb-3'])>

                                <div @class(['card', 'border-2', 'shadow-sm', 'rounded-4', 'h-100'])>


                                    <div @class([
                                        'card-header',
                                        'bg-light',
                                        'border-bottom',
                                        'rounded-top-4',
                                        'py-3',
                                    ])>

                                        <h6 @class(['mb-0', 'fw-semibold', 'text-dark'])>

                                            <i @class(['bi', 'bi-bar-chart-fill', 'text-success', 'me-2'])></i>

                                            Today's Attendance

                                        </h6>

                                    </div>


                                    <div @class(['card-body'])>


                                        <div @class(['d-flex', 'justify-content-between', 'py-2', 'border-bottom'])>

                                            <strong @class(['text-muted'])>
                                                Total Students
                                            </strong>

                                            <strong id="totalStudents" @class(['text-primary'])>
                                                0
                                            </strong>

                                        </div>


                                        <div @class(['d-flex', 'justify-content-between', 'py-2', 'border-bottom'])>

                                            <strong @class(['text-muted'])>
                                                Present
                                            </strong>

                                            <strong id="presentCount" @class(['text-success'])>
                                                0
                                            </strong>

                                        </div>


                                        <div @class(['d-flex', 'justify-content-between', 'py-2'])>

                                            <strong @class(['text-muted'])>
                                                Absent
                                            </strong>

                                            <strong id="absentCount" @class(['text-danger'])>
                                                0
                                            </strong>

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


    {{-- Open Scanner --}}
    <div @class(['modal', 'fade']) id="scannerModal" tabindex="-1" aria-hidden="true">

        <div @class(['modal-dialog', 'modal-lg', 'modal-dialog-centered'])>

            <div @class(['modal-content', 'px-4', 'pt-3', 'rounded-4'])>


                <div @class(['modal-header'])>

                    <h3 @class(['modal-title', 'fw-bold'])>

                        <i @class(['bi', 'bi-qr-code-scan', 'me-2'])></i>

                        Scan Student QR Code

                    </h3>


                    <button type="button" @class(['btn-close']) data-bs-dismiss="modal"></button>

                </div>


                <div @class(['modal-body'])>

                    <div @class(['alert', 'alert-info', 'py-2', 'mb-3'])>

                        Hold the student's QR code inside the camera frame.

                    </div>


                    <div id="reader" class="w-100 mx-auto"></div>

                </div>

            </div>

        </div>

    </div>


    <!-- QR Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>


    <!-- Attendance Count -->
    <script>
        function loadAttendanceCount() {

            let classId =
                $('#class_id').val();


            if (!classId) {

                $('#totalStudents').text(0);

                $('#presentCount').text(0);

                $('#absentCount').text(0);

                return;
            }


            $.ajax({

                url: "{{ route('teacher.attendance.count') }}",

                type: "POST",

                data: {

                    _token: "{{ csrf_token() }}",

                    assign_class_id: classId

                },


                success: function(res) {

                    if (
                        res.success === false
                    ) {
                        return;
                    }


                    $('#totalStudents')
                        .text(res.total);

                    $('#presentCount')
                        .text(res.present);

                    $('#absentCount')
                        .text(res.absent);

                },


                error: function(xhr) {

                    console.log(
                        'Unable to load attendance count.'
                    );

                    console.log(
                        xhr.responseText
                    );

                }

            });

        }


        $('#class_id').on(
            'change',
            function() {

                loadAttendanceCount();


                let selected =
                    $(this)
                    .find(':selected');


                let semester =
                    selected.data('semester') ||
                    '-';


                let subject =
                    selected.data('subject') ||
                    '-';


                let students =
                    selected.data('students') ||
                    0;


                $('#infoSemester').text(

                    semester == '-' ?

                    '-' :

                    'Semester ' +
                    semester

                );


                $('#infoSubject')
                    .text(subject);


                $('#totalStudents')
                    .text(students);

            }
        );


        // Load attendance when page loads
        if (
            $('#class_id').val() != ""
        ) {

            loadAttendanceCount();

        }
    </script>


    <!-- QR Attendance Scanner -->
    <script>
        $(document).ready(function() {

            let html5QrCode = null;
            let scannerStarting = false;
            let scannerProcessing = false;


            // Initialize Scanner
            function initializeScanner() {

                if (!html5QrCode) {
                    $('#reader').html('');
                    html5QrCode = new Html5Qrcode("reader");
                }

            }


            // Completely reset scanner
            function resetScanner() {

                return new Promise(function(resolve) {

                    scannerStarting = false;
                    scannerProcessing = false;

                    if (!html5QrCode) {

                        $('#reader').html('');
                        resolve();
                        return;
                    }


                    if (html5QrCode.isScanning) {

                        html5QrCode.stop()

                            .catch(function(error) {

                                console.log("Stop scanner error:", error);

                            })

                            .finally(function() {

                                html5QrCode.clear()

                                    .catch(function(error) {

                                        console.log("Clear scanner error:", error);

                                    })

                                    .finally(function() {

                                        html5QrCode = null;

                                        $('#reader').html('');

                                        resolve();

                                    });

                            });

                    } else {

                        html5QrCode.clear()

                            .catch(function(error) {

                                console.log("Clear scanner error:", error);

                            })

                            .finally(function() {

                                html5QrCode = null;

                                $('#reader').html('');

                                resolve();

                            });

                    }

                });

            }


            // Scan Another
            function resetAndStartScanner() {

                scannerProcessing = false;

                resetScanner()

                    .then(function() {

                        // Small delay so camera is fully released
                        setTimeout(function() {

                            startScanner();

                        }, 300);

                    });

            }


            // Open Scanner Modal
            function openScannerModal() {

                const scannerModal =
                    document.getElementById('scannerModal');


                const modal =
                    bootstrap.Modal.getOrCreateInstance(
                        scannerModal
                    );


                $('#scannerModal')
                    .off('shown.bs.modal');


                $('#scannerModal')
                    .one('shown.bs.modal', function() {

                        setTimeout(function() {

                            startScanner();

                        }, 300);

                    });


                modal.show();

            }


            // Start Camera
            function startScanner() {

                if (scannerStarting) {
                    return;
                }


                if (
                    html5QrCode &&
                    html5QrCode.isScanning
                ) {
                    return;
                }


                scannerStarting = true;


                initializeScanner();


                Html5Qrcode.getCameras()

                    .then(function(devices) {

                        if (
                            !devices ||
                            devices.length === 0
                        ) {

                            scannerStarting = false;

                            Swal.fire({

                                icon: 'error',

                                title: 'No Camera Found',

                                text: 'No camera was detected on this device.'

                            });

                            return;

                        }


                        let cameraId =
                            devices[0].id;


                        const backCamera =
                            devices.find(function(device) {

                                const label =
                                    device.label
                                    .toLowerCase();

                                return (
                                    label.includes('back') ||
                                    label.includes('rear') ||
                                    label.includes('environment')
                                );

                            });


                        if (backCamera) {

                            cameraId =
                                backCamera.id;

                        }


                        html5QrCode.start(

                                cameraId,

                                {

                                    fps: 10,

                                    qrbox: {

                                        width: 250,

                                        height: 250

                                    }

                                },


                                // QR Successfully Scanned
                                function(decodedText) {

                                    // Prevent same QR callback multiple times
                                    if (scannerProcessing) {
                                        return;
                                    }


                                    scannerProcessing = true;


                                    console.log(
                                        "QR:",
                                        decodedText
                                    );


                                    // Stop camera immediately
                                    html5QrCode.stop()

                                        .then(function() {

                                            scannerStarting =
                                                false;


                                            sendAttendance(
                                                decodedText
                                            );

                                        })

                                        .catch(function(error) {

                                            console.log(
                                                "Stop scanner error:",
                                                error
                                            );


                                            scannerStarting =
                                                false;


                                            sendAttendance(
                                                decodedText
                                            );

                                        });

                                },


                                // Ignore scanning errors
                                function(errorMessage) {}

                            )

                            .catch(function(error) {

                                console.error(
                                    "Start scanner error:",
                                    error
                                );


                                scannerStarting = false;


                                Swal.fire({

                                    icon: 'error',

                                    title: 'Camera Error',

                                    text: error.toString()

                                });

                            });

                    })

                    .catch(function(err) {

                        console.error(
                            "Camera error:",
                            err
                        );


                        scannerStarting = false;


                        Swal.fire({

                            icon: 'error',

                            title: 'Camera Error',

                            text: err.toString()

                        });

                    });

            }


            // Send Attendance
            function sendAttendance(decodedText) {

                $.ajax({

                    url: "{{ route('teacher.attendance.scan') }}",

                    type: "POST",

                    data: {

                        _token: "{{ csrf_token() }}",

                        qr_data: decodedText,

                        assign_class_id: $('#class_id').val()

                    },


                    success: function(response) {

                        if (response.success) {

                            loadAttendanceCount();


                            Swal.fire({

                                    icon: 'success',

                                    title: 'Attendance Marked Successfully',


                                    html: `

                                <div style="
                                    border:1px solid #dee2e6;
                                    border-radius:10px;
                                    padding:18px;
                                    background:#f8f9fa;
                                ">

                                    <table style="
                                        width:100%;
                                        font-size:16px;
                                        line-height:2.2;
                                    ">

                                        <tr>

                                            <td style="width:130px;">

                                                <b>Name</b>

                                            </td>

                                            <td>
                                                : ${response.student.name}
                                            </td>

                                        </tr>


                                        <tr>

                                            <td>

                                                <b>Code</b>

                                            </td>

                                            <td>

                                                : ${response.student.student_code}

                                            </td>

                                        </tr>


                                        <tr>

                                            <td>

                                                <b>Semester</b>

                                            </td>

                                            <td>

                                                : ${response.student.current_semester}

                                            </td>

                                        </tr>


                                        <tr>

                                            <td>

                                                <b>Subject</b>

                                            </td>

                                            <td>

                                                : ${response.subject}

                                            </td>

                                        </tr>


                                        <tr>

                                            <td>

                                                <b>Date</b>

                                            </td>

                                            <td>

                                                : ${response.date}

                                            </td>

                                        </tr>


                                        <tr>

                                            <td>

                                                <b>Time</b>

                                            </td>

                                            <td>

                                                : ${response.time}

                                            </td>

                                        </tr>

                                    </table>

                                </div>

                            `,


                                    showCancelButton: true,


                                    confirmButtonText: 'Scan Another',


                                    cancelButtonText: 'Close',


                                    customClass: {

                                        confirmButton: 'btn btn-success me-3',

                                        cancelButton: 'btn btn-secondary ms-3',

                                    },


                                    buttonsStyling: true,


                                    confirmButtonColor: '#198754',


                                    cancelButtonColor: '#6c757d',


                                    width: 550

                                })

                                .then(function(result) {


                                    // Scan Another
                                    if (result.isConfirmed) {

                                        resetAndStartScanner();

                                    }


                                    // Close
                                    else {

                                        const modal =
                                            bootstrap.Modal
                                            .getInstance(
                                                document
                                                .getElementById(
                                                    'scannerModal'
                                                )
                                            );


                                        if (modal) {

                                            modal.hide();

                                        }

                                    }

                                });

                        }


                        // Attendance Error
                        else {

                            let icon =
                                'error';


                            let title =
                                'Invalid QR Code';


                            let showOnlyClose =
                                false;


                            if (

                                response.message ===
                                'This student\'s attendance has already been marked for today.'

                            ) {

                                icon =
                                    'warning';


                                title =
                                    'Attendance Already Marked';

                            }


                            if (

                                response.message ===
                                'This student does not belong to the selected class.'

                            ) {

                                icon =
                                    'warning';


                                title =
                                    'Attendance Denied';

                            }


                            if (

                                response.message ===
                                'The Attendance period has ended. <br> <br> Students who did not scan their QR code within the session have been marked <strong> Absent</strong>.'

                            ) {

                                icon =
                                    'warning';


                                title =
                                    'Attendance Session Closed';


                                showOnlyClose =
                                    true;

                            }


                            Swal.fire({

                                    icon: icon,


                                    title: title,


                                    html: `

                                <div style="
                                    text-align:center;
                                    font-size:16px;
                                ">

                                    ${response.message}

                                </div>

                            `,


                                    showConfirmButton:
                                        !showOnlyClose,


                                    showCancelButton: true,


                                    confirmButtonText: 'Scan Another',


                                    cancelButtonText: 'Close',


                                    customClass: {

                                        confirmButton: 'btn btn-success me-3',

                                        cancelButton: 'btn btn-secondary ms-3',

                                    },


                                    buttonsStyling: true,


                                    confirmButtonColor: '#198754',


                                    cancelButtonColor: '#6c757d',


                                    width: 550

                                })

                                .then(function(result) {


                                    if (
                                        result.isConfirmed &&
                                        !showOnlyClose
                                    ) {

                                        resetAndStartScanner();

                                    } else {

                                        const modal =
                                            bootstrap.Modal
                                            .getInstance(
                                                document
                                                .getElementById(
                                                    'scannerModal'
                                                )
                                            );


                                        if (modal) {

                                            modal.hide();

                                        }

                                    }

                                });

                        }

                    },


                    error: function(xhr) {

                        console.log(
                            xhr.responseText
                        );


                        Swal.fire({

                                icon: 'error',


                                title: 'Server Error',


                                text: 'Something went wrong.',


                                showCancelButton: true,


                                confirmButtonText: 'Scan Again',


                                confirmButtonColor: '#198754',


                                cancelButtonText: 'Close',


                                cancelButtonColor: '#6c757d',


                                width: 550

                            })

                            .then(function(result) {


                                if (
                                    result.isConfirmed
                                ) {

                                    resetAndStartScanner();

                                } else {

                                    const modal =
                                        bootstrap.Modal
                                        .getInstance(
                                            document
                                            .getElementById(
                                                'scannerModal'
                                            )
                                        );


                                    if (modal) {

                                        modal.hide();

                                    }

                                }

                            });

                    }

                });

            }


            // Start Attendance Button
            $('#openScanner').click(function() {

                let classId =
                    $('#class_id').val();


                if (classId == "") {

                    Swal.fire({

                        toast: true,


                        position: 'top-end',


                        icon: 'warning',


                        title: 'Please select a class first.',


                        showConfirmButton: false,


                        timer: 1500,


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


                $.ajax({

                    url: "{{ route('teacher.attendance.startSession') }}",


                    type: "POST",


                    data: {

                        _token: "{{ csrf_token() }}",

                        assign_class_id: classId

                    },


                    success: function(response) {


                        if (
                            response.type ===
                            'completed'
                        ) {

                            Swal.fire({

                                icon: 'warning',


                                title: 'Attendance Already Taken',


                                html: `

                                <div style="
                                    text-align:center;
                                    font-size:16px;
                                ">

                                    ${response.message}

                                </div>

                            `,


                                confirmButtonText: 'Close',


                                confirmButtonColor: '#6c757d',


                                width: 500

                            });


                            return;

                        }


                        // Attendance already closed
                        if (
                            response.type ===
                            'closed'
                        ) {

                            Swal.fire({

                                icon: 'warning',


                                title: 'Attendance Closed',


                                html: `

                                <p>
                                    The attendance session for this class has ended.
                                </p>

                                <p>
                                    Students who did not scan their QR code within the attendance period have been marked <b>Absent</b>.
                                </p>

                            `,


                                confirmButtonColor: '#198754',


                                width: 500

                            });


                            loadAttendanceCount();


                            return;

                        }


                        // Attendance already running
                        if (
                            response.type ===
                            'open'
                        ) {

                            openScannerModal();


                            return;

                        }


                        // New Attendance Session
                        if (
                            response.type ===
                            'new'
                        ) {

                            let selected =
                                $('#class_id option:selected');


                            let subject_name =
                                selected.data('subject');


                            Swal.fire({

                                    icon: 'question',


                                    title: 'Start Attendance ?',


                                    html: `

                                <strong>
                                    Subject:
                                </strong>

                                ${subject_name}

                                <br><br>

                                <p>
                                    Attendance will remain <b>Open for 40 Minutes</b>.
                                <p>

                                <p>
                                    Students who do not scan their QR code within this time will be marked <b>Absent</b> automatically.
                                </p>

                            `,


                                    showCancelButton: true,


                                    confirmButtonText: 'Scan Attendance',


                                    cancelButtonText: 'Cancel',


                                    customClass: {

                                        confirmButton: 'btn btn-success me-3',

                                        cancelButton: 'btn btn-secondary ms-3',

                                    },


                                    buttonsStyling: true,


                                    confirmButtonColor: '#198754',


                                    cancelButtonColor: '#6c757d',


                                    width: 550

                                })

                                .then(function(result) {


                                    if (
                                        result.isConfirmed
                                    ) {

                                        $.ajax({

                                            url: "{{ route('teacher.attendance.createSession') }}",


                                            type: "POST",


                                            data: {

                                                _token: "{{ csrf_token() }}",

                                                assign_class_id: classId

                                            },


                                            success: function(res) {


                                                if (
                                                    res.success
                                                ) {

                                                    openScannerModal();

                                                } else {

                                                    Swal.fire({

                                                        icon: 'error',


                                                        title: 'Unable to Start Attendance',


                                                        text: res
                                                            .message ||
                                                            'Something went wrong.'

                                                    });

                                                }

                                            },


                                            error: function() {

                                                Swal.fire({

                                                    icon: 'error',


                                                    title: 'Server Error',


                                                    text: 'Unable to create attendance session.'

                                                });

                                            }

                                        });

                                    }

                                });

                        }

                    },


                    error: function(xhr) {

                        console.log(
                            xhr.responseText
                        );


                        Swal.fire({

                            icon: 'error',


                            title: 'Server Error',


                            text: 'Unable to check attendance session.'

                        });

                    }

                });

            });


            // Stop Camera when modal closes
            $('#scannerModal').on(
                'hidden.bs.modal',
                function() {

                    scannerStarting =
                        false;


                    scannerProcessing =
                        false;


                    resetScanner();

                }
            );

        });
    </script>

</body>
