<head>
    <title>Subjects - Admin</title>
    @include('layouts.link')
    @include('layouts.style')
    @include('layouts.delete')
    @include('admin.subjectcreate')
    @include('admin.subjectedit')
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
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-semibold mb-0">Subject List</h5>
                    <button class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal"
                        data-bs-target="#addSubjectModal"> New Subject
                    </button>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <button class="btn btn-primary btn-sm semester-btn active" data-semester="all">
                        <i class="bi bi-book"></i> &nbsp; All Subjects
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
                                <th class="py-3">Code</th>
                                <th class="py-3">Name</th>
                                <th class="py-3">Semester</th>
                                <th class="py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="subject-data">
                            @foreach ($subjects as $subject)
                                <tr class="subject-row">
                                    <td>{{ $subjects->firstItem() + $loop->index }}</td>
                                    <td>{{ $subject->subject_code }}</td>
                                    <td>{{ $subject->subject_name }}</td>
                                    <td>Semester {{ $subject->semester }}</td>
                                    <td>
                                        <button class="btn btn-outline-primary fw-semibold btn-sm rounded-3 action-btn"
                                            style="font-size:10px;" data-bs-toggle="modal"
                                            data-bs-target="#editSubjectModal" data-id="{{ $subject->id }}"
                                            data-subject_name="{{ $subject->subject_name }}"
                                            data-subject_code="{{ $subject->subject_code }}"
                                            data-semester="{{ $subject->semester }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button> &nbsp;
                                        <button class="btn btn-outline-danger fw-semibold btn-sm rounded-3 action-btn"
                                            style="font-size:10px;" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-id="{{ $subject->id }}"
                                            data-subject_name="{{ $subject->subject_name }}"
                                            data-url="{{ route('subjects.delete', $subject->id) }}">
                                            <i class="bi bi-trash"></i> Delete
                                        </button> &nbsp;
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="pagination-data">
                    @if ($subjects->hasPages())
                        @include('layouts.pagination', ['paginator' => $subjects])
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>

<script>

$(document).on('click','.semester-btn',function(){
    let semester = $(this).data('semester');

    $('.semester-btn')
        .removeClass('active btn-primary')
        .addClass('btn-outline-primary');

    $(this)
        .removeClass('btn-outline-primary')
        .addClass('btn-primary active');

    $.ajax({
        url:"{{ route('admin.subjects') }}",
        type:"GET",
        data:{
            semester:semester
        },

        success:function(response){

            let table = $(response)
                .find('#subject-data')
                .html();
            let pagination = $(response)
                .find('#pagination-data')
                .html();
            $('#subject-data').html(table);
            $('#pagination-data').html(pagination);
        }
    });
});
</script>