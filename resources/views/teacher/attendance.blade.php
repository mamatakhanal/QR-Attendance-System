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
                    Attendance
                </h5>

                <div class="card-body">
                    <div class="row align-items-end g-3">

                        <!-- Select Class -->
                        <div class="col-md-9">
                            <label class="form-label fw-semibold">
                                Select Class
                            </label>

                            <select class="form-select" id="class_id">
                                <option value="">
                                    Select Class
                                </option>

                                @foreach ($assignclasses as $assignclass)
                                    @foreach ($assignclass->subjects as $subject)
                                        <option value="{{ $assignclass->id }}">
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
                    </div>

                    <!-- Instruction -->
                    <p class="text-muted small mt-1 mb-4">
                        Select class before starting attendance
                    </p>


                    <!-- Attendance List -->
                    <h5 class="fw-semibold mb-3">
                        Today's Attendance
                    </h5>

                    <div class="table-responsive rounded-3">

                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>S.N</th>
                                    <th>Roll No</th>
                                    <th>Name</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody id="attendance-data">

                                <!-- scanned students will appear here -->
                                <tr>

                                    <td colspan="5" class="text-center text-muted py-3">
                                        No attendance taken yet
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

</body>
