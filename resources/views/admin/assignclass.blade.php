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
                                <th class="py-3">Semester</th>
                                <th class="py-3">Subject</th>
                                <th class="py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assignclasses as $assignclass)
                                <tr class="assignclass-row">
                                     <td>{{ $assignclasses->firstItem() + $loop->index }}</td>
                                    <td>{{ $assignclass->teacher->name ?? 'No Teacher' }}</td>
                                    <td>Semester {{ $assignclass->semester }}</td>
                                    <td>
                                        @php
                                            $subjectNames = $assignclass->subjects->pluck('subject_name')->toArray();
                                        @endphp
                                        {{ implode(', ', $subjectNames) }}
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-primary fw-semibold btn-sm rounded-3 edit-btn"
                                            style="font-size:10px;" data-bs-toggle="modal"
                                            data-bs-target="#editAssignclassModal" data-id="{{ $assignclass->id }}"
                                            data-teacher="{{ $assignclass->teacher_id }}"
                                            data-teacher-name="{{ $assignclass->teacher->name }}"
                                            data-semester="{{ $assignclass->semester }}"
                                            data-subjects='@json($assignclass->subjects->pluck('id'))'>
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button> &nbsp;
                                        <button class="btn btn-outline-danger fw-semibold btn-sm rounded-3 action-btn"
                                            style="font-size:10px;" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-id="{{ $assignclass->id }}"
                                            data-url="{{ route('assignclass.delete', $assignclass->id) }}">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @include('layouts.pagination', ['paginator' => $assignclasses])
            </div>
        </div>
    </div>
</body>
