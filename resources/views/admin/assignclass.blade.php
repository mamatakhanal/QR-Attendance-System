<head>
    <title>Assign Class - Admin</title>
    @include('layouts.link')
    @include('layouts.style')
    @include('layouts.delete')
    @include('admin.assignclasscreate')
    @include('admin.assignclassedit')
</head>

<body>
    <!-- MAIN LAYOUT -->
    <div class="main-wrapper">
        @include('admin.sidebar')
        <div class="main-area">
            @include('admin.navbar')
            <!-- CONTENT -->
            <div class="card shadow-sm border-0 mx-3 my-2 p-4 rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-semibold mb-0">Assign Class List</h5>
                    <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal"
                        data-bs-target="#addAssignclassModal"> Assign Class </button>
                </div>
                <div class="table-responsive rounded-2">
                    <table class="table table-hover border-3 mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th class="py-3">S.N</th>
                                <th class="py-3">Teacher</th>
                                <th class="py-3">Subject</th>
                                <th class="py-3">Code</th>
                                <th class="py-3">Semester</th>
                                <th class="py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @foreach ($assignclasses as $teacher)
                                @foreach ($teacher->subjects as $subject)
                                    <tr class="assignclass-row">
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $teacher->name }}</td>
                                        <td>{{ $subject->subject_name }}</td>
                                        <td>{{ $subject->subject_code }}</td>
                                        <td>{{ $subject->pivot->semester }}</td>
                                        <td>
                                            {{-- <button class="btn btn-outline-warning fw-semibold btn-sm rounded-3 action-btn"
                                            style="font-size:10px;" data-bs-toggle="modal"
                                            data-bs-target="#editStudentModal" data-id="{{ $student->id }}"
                                            data-name="{{ $student->name }}" data-roll_no="{{ $student->roll_no }}"
                                            data-email="{{ $student->email }}" data-phone="{{ $student->phone }}"
                                            data-gender="{{ $student->gender }}" data-dob="{{ $student->dob }}"
                                            data-address="{{ $student->address }}"
                                            data-current_semester="{{ $student->current_semester }}"
                                            data-admission_year="{{ $student->admission_year }}"
                                            data-student_code="{{ $student->student_code }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button> &nbsp; --}}
                                            <button
                                                class="btn btn-outline-danger fw-semibold btn-sm rounded-3 action-btn"
                                                style="font-size:10px;" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-url="{{ route('assignclass.delete', ['teacher_id' => $teacher->id, 'subject_id' => $subject->id]) }}">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @include('layouts.pagination', ['paginator' => $assignclasses])
            </div>
        </div>
    </div>
</body>
