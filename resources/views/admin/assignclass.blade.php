<head>
    <title>Assign Classes - Admin</title>
    @include('layouts.link')
    @include('layouts.style')
    @include('layouts.delete')
    @include('admin.assignclasscreate')
    @include('admin.assignclassedit')
</head>

<body>
    @include('layouts.toast')
    <!-- MAIN LAYOUT -->
    <div class="main-wrapper">
        @include('admin.sidebar')
        <div class="main-area">
            @include('admin.navbar')

            <!-- CONTENT -->
            <div class="card shadow-sm border-0 mx-2 my-2 p-4 rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0">Assign Classes List</h5>
                     <div class="d-flex align-items-center">
                    <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal"
                        data-bs-target="#addAssignclassModal"> Assign Class
                    </button>
                     </div>
                </div>

                <!-- Semester Filter Buttons -->
                <div class="d-flex flex-wrap align-items-center mb-3">
                    <button class="btn btn-primary btn-sm semester-btn active" data-semester="all">
                        <i class="bi bi-people"></i> &nbsp; All Classes
                    </button>
                    @for ($i = 1; $i <= 8; $i++)
                        <button class="btn btn-outline-primary btn-sm semester-btn" data-semester="{{ $i }}">
                            Semester {{ $i }}
                        </button>
                    @endfor
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


                        <tbody id="assignclass-data">
                            @forelse ($assignclasses as $assignclass)
                                <tr class="assignclass-row" data-semester="{{ $assignclass->semester }}">
                                    <td>{{ $assignclasses->firstItem() + $loop->index }}</td>
                                    <td>{{ $assignclass->teacher->name ?? 'No Teacher' }}</td>
                                    <td>Semester {{ $assignclass->semester }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($assignclass->subjects as $subject)
                                                <div class="px-2 py-1 border rounded-3 shadow-sm bg-light">
                                                    {{ $subject->subject_name }}
                                                </div>
                                            @endforeach
                                        </div>
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
                                @empty
                                    <tr id="noAssignClassRow">
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No classes found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div id="pagination-data">
                        @if ($assignclasses->hasPages())
                            @include('layouts.pagination', ['paginator' => $assignclasses])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </body>

    <script>
        function loadData() {

            $.ajax({
                url: window.location.pathname,
                type: "GET",
                data: {
                    search: $("#globalSearch").val(),
                    semester: $(".semester-btn.active").data("semester")
                },
                success: function(response) {

                    $("#assignclass-data").html(
                        $(response).find("#assignclass-data").html()
                    );

                    $("#pagination-data").html(
                        $(response).find("#pagination-data").html()
                    );
                }
            });

        }

        $(document).on("click", ".semester-btn", function() {

            $(".semester-btn")
                .removeClass("active btn-primary")
                .addClass("btn-outline-primary");

            $(this)
                .removeClass("btn-outline-primary")
                .addClass("btn-primary active");

            loadData();

        });
    </script>
