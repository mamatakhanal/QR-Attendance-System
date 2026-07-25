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

                                <select @class(['form-select']) id="class_id">
                                    <option value=""> Select Class </option>

                                    @foreach ($assignclasses as $assignclass)
                                        @foreach ($assignclass->subjects as $subject)
                                            <option value="{{ $assignclass->id }}"
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
                                            <strong @class(['text-muted'])> Semester </strong>
                                            <strong id="infoSemester"> - </strong>
                                        </div>

                                        <div @class([
                                            'd-flex',
                                            'justify-content-between',
                                            'align-items-center',
                                            'py-2',
                                            'border-bottom',
                                        ])>
                                            <strong @class(['text-muted'])> Subject </strong>
                                            <strong id="infoSubject"> - </strong>
                                        </div>

                                        <div @class([
                                            'd-flex',
                                            'justify-content-between',
                                            'align-items-center',
                                            'py-2',
                                        ])>
                                            <strong @class(['text-muted'])> Teacher </strong>
                                            <strong> {{ $teacher->name }} </strong>
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
                                            <strong @class(['text-muted'])> Total Students </strong>
                                            <strong id="totalStudents" @class(['text-primary'])> 0 </strong>
                                        </div>

                                        <div @class(['d-flex', 'justify-content-between', 'py-2', 'border-bottom'])>
                                            <strong @class(['text-muted'])> Present </strong>
                                            <strong id="presentCount" @class(['text-success'])>0</strong>
                                        </div>

                                        <div @class(['d-flex', 'justify-content-between', 'py-2'])>
                                            <strong @class(['text-muted'])> Absent </strong>
                                            <strong id="absentCount" @class(['text-danger'])>0</strong>
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

                    <div id="reader" style="width:700px; max-width:700px; margin:auto;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        $(document).ready(function() {

            // QR Scanner Object
            let html5QrCode = new Html5Qrcode("reader");

            // Start Attendance Button
            $('#openScanner').click(function() {

                let classId = $('#class_id').val();

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

                const modal = new bootstrap.Modal(document.getElementById('scannerModal'));
                modal.show();

                $('#scannerModal').one('shown.bs.modal', function() {
                    startScanner();
                });
            });



            // Start Camera
            function startScanner() {

                if (html5QrCode.isScanning) {
                    return;
                }

                Html5Qrcode.getCameras()
                    .then(function(devices) {

                        if (devices.length > 0) {
                            html5QrCode.start(
                                devices[0].id, {
                                    fps: 10,
                                    qrbox: {
                                        width: 250,
                                        height: 250
                                    }
                                },

                                // QR Success
                                function(decodedText) {

                                    html5QrCode.stop().then(() => {
                                        console.log("QR:", decodedText);

                                        $.ajax({
                                            url: "{{ route('teacher.attendance.scan') }}",
                                            type: "POST",

                                            data: {
                                                _token: "{{ csrf_token() }}",
                                                qr_data: decodedText,
                                                assign_class_id: $('#class_id').val()
                                            },


                                            // Attendance Successfully Marked
                                            success: function(response) {
                                                if (response.success) {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Attendance Marked Successfully',
                                                        html: `
                                                                        <div style="border:1px solid #dee2e6;
                                                                                    border-radius:10px;
                                                                                    padding:18px;
                                                                                    background:#f8f9fa;">

                                                                            <table style="width:100%;font-size:16px;line-height:2.2;">
                                                                                <tr>
                                                                                    <td style="width:130px;"><b>Name</b></td>
                                                                                    <td>: ${response.student.name}</td>
                                                                                </tr>

                                                                                <tr>
                                                                                    <td><b>Code</b></td>
                                                                                    <td>: ${response.student.student_code}</td>
                                                                                </tr>

                                                                                <tr>
                                                                                    <td><b>Semester</b></td>
                                                                                    <td>: ${response.student.current_semester}</td>
                                                                                </tr>

                                                                                <tr>
                                                                                    <td><b>Subject</b></td>
                                                                                    <td>: ${response.subject}</td>
                                                                                </tr>

                                                                                <tr>
                                                                                    <td><b>Date</b></td>
                                                                                    <td>: ${response.date}</td>
                                                                                </tr>

                                                                                <tr>
                                                                                    <td><b>Time</b></td>
                                                                                    <td>: ${response.time}</td>
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

                                                    }).then((result) => {

                                                        if (result
                                                            .isConfirmed) {
                                                            html5QrCode.clear();
                                                            startScanner();
                                                        } else {
                                                            bootstrap.Modal
                                                                .getInstance(
                                                                    document
                                                                    .getElementById(
                                                                        'scannerModal'
                                                                    )
                                                                ).hide();
                                                        }
                                                    });

                                                } else {

                                                    let icon = 'error';
                                                    let title = 'Invalid QR Code';
                                                    let showOnlyClose = false;

                                                    if (response.message === 'This student\'s attendance has already been marked for today.') {
                                                        icon = 'warning';
                                                        title = 'Attendance Already Marked';
                                                    } 
                                                    
                                                    if (response.message === 'This student does not belong to the selected class.' ) {
                                                        icon = 'warning';
                                                        title = 'Attendance Denied';
                                                    } 

                                                    if (response.message === 'The Attendance period has ended. <br> <br> Students who did not scan their QR code within the session have been marked <strong> Absent</strong>.' ) {
                                                        icon = 'warning';
                                                        title = 'Attendance Session Closed';
                                                        showOnlyClose = true;
                                                    }

                                                    Swal.fire({
                                                        icon: icon,
                                                        title: title,
                                                        html: `
                                                                <div style="text-align:center;font-size:16px;">
                                                                    ${response.message}
                                                                </div>
                                                                `,
                                                        showConfirmButton: !showOnlyClose,
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
                                                    }).then((result) => {

                                                        if (result
                                                            .isConfirmed) {
                                                            html5QrCode.clear();
                                                            startScanner();
                                                        } else {
                                                            bootstrap.Modal
                                                                .getInstance(
                                                                    document
                                                                    .getElementById(
                                                                        'scannerModal'
                                                                    )
                                                                ).hide();
                                                        }
                                                    });
                                                }
                                            },

                                            error: function(xhr) {

                                                // Swal.fire({
                                                //     icon: 'error',
                                                //     title: 'Server Error',
                                                //     text: 'Something went wrong.'
                                                // });

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
                                                }).then((result) => {

                                                    if (result.isConfirmed) {
                                                        startScanner();
                                                    } else {
                                                        bootstrap.Modal
                                                            .getInstance(
                                                                document
                                                                .getElementById(
                                                                    'scannerModal'
                                                                )
                                                            ).hide();
                                                    }
                                                });

                                            }

                                        });
                                    });
                                },

                                // QR Scan Error
                                function(errorMessage) {}

                            );

                        } else {

                            Swal.fire({
                                icon: 'error',
                                title: 'No camera found.'
                            });

                        }

                    })
                    .catch(function(err) {

                        Swal.fire({
                            icon: 'error',
                            title: 'Camera Error',
                            text: err
                        });

                    });
            }

            // Stop Camera
            $('#scannerModal').on('hidden.bs.modal', function() {

                if (html5QrCode.isScanning) {

                    html5QrCode.stop()
                        .then(() => {
                            html5QrCode.clear();
                        })
                        .catch(err => {
                            console.log(err);
                        });

                }

            });

            // Update Class Information
            $('#class_id').on('change', function() {

                let selected = $(this).find(':selected');

                let semester = selected.data('semester') || '-';
                let subject = selected.data('subject') || '-';
                let students = selected.data('students') || 0;

                $('#infoSemester').text(
                    semester == '-' ? '-' : 'Semester ' + semester
                );

                $('#infoSubject').text(subject);

                $('#totalStudents').text(students);

                $('#presentCount').text(0);
                $('#absentCount').text(0);

                if ($('#scannedCount').length) {
                    $('#scannedCount').text(0);
                }

            });
        });
    </script>
</body>
