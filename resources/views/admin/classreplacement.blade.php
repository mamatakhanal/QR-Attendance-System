<head>
    <title>Class Replacement - Admin</title>

    @include('layouts.link')
    @include('layouts.style')
    @include('layouts.delete')
    @include('admin.classreplacementcreate')
    @include('admin.classreplacementedit')
</head>

<body>
    @include('layouts.toast')
    <div class="main-wrapper">
        @include('admin.sidebar')
        <div class="main-area">
            @include('admin.navbar')
            <div class="card shadow-sm border-0 mx-2 my-2 p-4 rounded-4">

                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h5 class="fw-semibold mb-0">
                        Class Replacement List
                    </h5>

                    <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal"
                        data-bs-target="#createClassReplacementModal">
                        + New Class
                    </button>
                </div>


                {{-- Filter --}}
                {{-- <form id="replacementFilterForm" method="GET" action="{{ route('admin.classreplacement') }}">

                    <div class="row g-2 align-items-end">

                        <div class="col" style="flex: 0 0 19%; max-width: 19%;">
                            <select name="teacher_id" class="form-select form-select-sm">
                                <option value="">All Teachers</option>

                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}"
                                        {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col" style="flex: 0 0 18%; max-width: 18%;">
                            <select name="semester" class="form-select form-select-sm">
                                <option value="">All Semester</option>

                                @for ($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}"
                                        {{ request('semester') == $i ? 'selected' : '' }}>
                                        Semester {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col" style="flex: 0 0 18%; max-width: 18%;">
                            <select name="subject_id" class="form-select form-select-sm">
                                <option value="">All Subjects</option>

                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->subject_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col" style="flex: 0 0 14%; max-width: 14%;">
                            <input type="time" name="time" id="time" class="form-control form-control-sm"
                                value="{{ request('time') }}">
                        </div>

                        <div class="col" style="flex: 0 0 14%; max-width: 14%;">
                            <input type="date" name="from_date" id="from_date" class="form-control form-control-sm"
                                value="{{ request('from_date') }}">
                        </div>

                        <div class="col-md-1 d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">
                                Search
                            </button>
                        </div>

                        <div class="col-md-1 d-grid">
                            <a href="{{ route('admin.classreplacement') }}" class="btn btn-outline-secondary btn-sm">
                                Reset
                            </a>
                        </div>

                    </div>
                </form> --}}



                {{-- Semester Filter --}}
                <div class="d-flex flex-wrap align-items-center mb-3">
                    <button class="btn btn-primary btn-sm semester-btn active" data-semester="all">
                        <i class="bi bi-people"></i>
                        &nbsp; All Classes
                    </button>

                    @for ($i = 1; $i <= 8; $i++)
                        <button class="btn btn-outline-primary btn-sm semester-btn" data-semester="{{ $i }}">
                            Semester {{ $i }}
                        </button>
                    @endfor
                </div>


                {{-- Table --}}
                <div class="table-responsive rounded-2">

                    <table class="table table-hover border-3 mb-0 align-middle">

                        <thead class="table-secondary">

                            <tr>

                                <th class="py-3">S.N</th>
                                <th class="py-3">Date</th>
                                <th class="py-3">Semester</th>
                                <th class="py-3">Subject</th>
                                <th class="py-3">Teacher</th>
                                <th class="py-3">Time</th>
                                {{-- <th class="py-3">Status</th> --}}
                                <th class="py-3">Actions</th>
                            </tr>
                        </thead>


                        <tbody id="classreplacement-data">

                            @forelse ($replacements as $replacement)
                                <tr class="replacement-row" data-semester="{{ $replacement->subject?->semester }}">
                                    <td>
                                        {{ $replacements->firstItem() + $loop->index }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($replacement->date)->format('d M Y') }}
                                    </td>
                                    <td>
                                        Semester {{ $replacement->subject?->semester }}
                                    </td>
                                    <td>
                                        {{ $replacement->subject?->subject_name ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $replacement->replacementTeacher?->name ?? '-' }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($replacement->start_time)->format('h:i A') }}
                                        -
                                        {{ \Carbon\Carbon::parse($replacement->end_time)->format('h:i A') }}
                                    </td>
                                    {{-- <td>
                                        @if ($replacement->attendance_status === 'Attendance Done')
                                            <button type="button"
                                                class="btn btn-success fw-semibold btn-sm rounded-3 edit-btn"
                                                style="font-size:10px;">
                                                <i class="bi bi-check-circle"></i>
                                                Attendance Done
                                            </button>
                                        @elseif ($replacement->attendance_status === 'Attendance In Progress')
                                            <button type="button"
                                                class="btn btn-warning text-dark fw-semibold btn-sm rounded-3 edit-btn"
                                                style="font-size:10px;">
                                                <i class="bi bi-clock"></i>
                                                Attendance In Progress
                                            </button>
                                        @elseif ($replacement->attendance_status === 'Not Taken')
                                            <button type="button"
                                                class="btn btn-danger fw-semibold btn-sm rounded-3 edit-btn"
                                                style="font-size:10px;">
                                                <i class="bi bi-x-circle"></i>
                                                Not Taken
                                            </button>
                                        @elseif ($replacement->attendance_status === 'Time Expired')
                                            <button type="button"
                                                class="btn btn-danger fw-semibold btn-sm rounded-3 edit-btn"
                                                style="font-size:10px;">
                                                <i class="bi bi-clock-history"></i>
                                                Time Expired
                                            </button>
                                        @else
                                            <button type="button"
                                                class="btn btn-secondary fw-semibold btn-sm rounded-3 edit-btn"
                                                style="font-size:10px;">
                                                <i class="bi bi-calendar-check"></i>
                                                Scheduled
                                            </button>
                                        @endif
                                    </td> --}}
                                    <td>
                                        @if ($replacement->attendance_status === 'Attendance Done')
                                            <button type="button" class="btn btn-success fw-semibold btn-sm rounded-3"
                                                style="font-size:12px;">
                                                <i class="bi bi-check-circle"></i>
                                                Taken
                                            </button>
                                        @elseif ($replacement->attendance_status === 'Attendance In Progress')
                                            <button type="button"
                                                class="btn btn-warning fw-semibold btn-sm rounded-3 text-dark"
                                                style="font-size:12px;">
                                                <i class="bi bi-clock"></i>
                                                In Progress
                                            </button>
                                        @elseif ($replacement->attendance_status === 'Time Expired')
                                            <button type="button" class="btn btn-danger fw-semibold btn-sm rounded-3"
                                                style="font-size:12px;">
                                                <i class="bi bi-clock-history"></i>
                                                Not Taken
                                            </button>
                                        @else
                                            {{-- Scheduled / Not Taken --}}
                                            @if ($replacement->can_edit)
                                                <button type="button"
                                                    class="btn btn-outline-primary fw-semibold btn-sm rounded-3 edit-btn"
                                                    style="font-size:12px;" data-bs-toggle="modal"
                                                    data-bs-target="#editClassReplacementModal"
                                                    data-id="{{ $replacement->id }}"
                                                    data-teacher="{{ $replacement->replacement_teacher_id }}"
                                                    data-date="{{ \Carbon\Carbon::parse($replacement->date)->format('Y-m-d') }}"
                                                    data-semester="{{ $replacement->subject ? $replacement->subject->semester : '' }}"
                                                    data-subject-id="{{ $replacement->subject_id }}"
                                                    data-start-time="{{ $replacement->start_time }}"
                                                    data-end-time="{{ $replacement->end_time }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                    Edit
                                                </button>
                                            @endif

                                            @if ($replacement->can_edit && $replacement->can_delete)
                                                &nbsp;
                                            @endif

                                            @if ($replacement->can_delete)
                                                <button type="button"
                                                    class="btn btn-outline-danger fw-semibold btn-sm rounded-3 action-btn"
                                                    style="font-size:12px;" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal" data-id="{{ $replacement->id }}"
                                                    data-url="{{ route('admin.classreplacement.delete', $replacement->id) }}">
                                                    <i class="bi bi-trash"></i>
                                                    Delete
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No class replacements found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}
                <div id="pagination-data">

                    @if ($replacements->hasPages())
                        @include('layouts.pagination', [
                            'paginator' => $replacements,
                        ])
                    @endif

                </div>

            </div>

        </div>

    </div>


    <script>
        function loadData() {

            let semester = $(".semester-btn.active").data("semester");

            $.ajax({
                url: "{{ route('admin.classreplacement') }}",
                type: "GET",

                data: {
                    teacher_id: $("select[name='teacher_id']").val(),
                    subject_id: $("select[name='subject_id']").val(),
                    from_date: $("input[name='from_date']").val(),
                    to_date: $("input[name='to_date']").val(),

                    // Semester button
                    semester: semester === "all" ? "" : semester
                },

                success: function(response) {

                    $("#classreplacement-data").html(
                        $(response).find("#classreplacement-data").html()
                    );

                    $("#pagination-data").html(
                        $(response).find("#pagination-data").html()
                    );
                },

                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }


        // Semester buttons
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

    <script>
        $(document).on('submit', '#deleteForm', function(e) {
            e.preventDefault();

            let form = $(this);
            let url = form.attr('action');

            $.ajax({
                url: url,
                type: 'POST',
                data: form.serialize(),

                success: function(response) {

                    $('#deleteModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message || 'Class replacement deleted successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {

                        // Stay on Class Replacement page
                        window.location.href = "{{ route('admin.classreplacement') }}";

                    });
                },

                error: function(xhr) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Unable to delete class replacement.'
                    });

                }
            });
        });
    </script>

    {{-- Replacement validation error popup --}}
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Replacement Not Allowed',
                text: @json(session('error')),
                confirmButtonText: 'OK'
            });
        </script>
    @endif

</body>
