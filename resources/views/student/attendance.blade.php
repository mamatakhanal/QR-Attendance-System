<head>
    <title>Attendance - Student</title>
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
            <div class="main-content">
                <div class="card shadow-sm border-0 rounded-4 mx-2 my-2">

                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold mb-0">
                                My Attendance
                            </h5>
                        </div>

                        <div class="table-responsive rounded-2">
                            <table class="table table-hover mb-0">
                                <thead class="table-secondary">
                                    <tr>
                                        <th class="py-3">S.N</th>
                                        <th class="py-3">Subject</th>
                                        <th class="py-3">Date</th>
                                        <th class="py-3">Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {{-- @foreach ($classes as $class) --}}
                                    <tr>
                                        <td>1</td>
                                        <td>DBMS</td>
                                        <td>2-20</td>
                                        <td>
                                            <span class="badge bg-success px-3">
                                                Present
                                            </span>
                                        </td>
                                    </tr>
                                    {{-- @endforeach --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
