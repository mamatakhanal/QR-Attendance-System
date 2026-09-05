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
                                <th class="py-3">Actions</th>
                            </tr>
                        </thead>


                        <tbody id="classreplacement-data">

                            @forelse ($replacements as $replacement)
                                <tr class="replacement-row" data-semester="{{ $replacement->assignclass->semester }}">

                                    <td>
                                        {{ $replacements->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($replacement->date)->format('d M Y') }}
                                    </td>

                                    <td>
                                        Semester {{ $replacement->assignclass->semester }}
                                    </td>

                                    <td>
                                        {{ $replacement->assignclass->subjects->first()?->subject_name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $replacement->replacementTeacher->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($replacement->start_time)->format('h:i A') }}
                                        -
                                        {{ \Carbon\Carbon::parse($replacement->end_time)->format('h:i A') }}
                                    </td>

                                    <td>
                                        <button class="btn btn-outline-primary fw-semibold btn-sm rounded-3 edit-btn"
                                            data-bs-toggle="modal" data-bs-target="#editClassReplacementModal"
                                            data-id="{{ $replacement->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                            Edit
                                        </button>

                                        &nbsp;

                                        <button class="btn btn-outline-danger fw-semibold btn-sm rounded-3 action-btn"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-id="{{ $replacement->id }}"
                                            data-url="{{ route('classreplacement.delete', $replacement->id) }}">

                                            <i class="bi bi-trash"></i>
                                            Delete

                                        </button>
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

            $.ajax({

                url: window.location.pathname,

                type: "GET",

                data: {

                    search: $("#globalSearch").val(),

                    semester: $(".semester-btn.active").data("semester")

                },

                success: function(response) {

                    $("#classreplacement-data").html(
                        $(response)
                        .find("#classreplacement-data")
                        .html()
                    );

                    $("#pagination-data").html(
                        $(response)
                        .find("#pagination-data")
                        .html()
                    );

                },

                error: function(xhr) {

                    console.log(xhr.responseText);

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

</body>
