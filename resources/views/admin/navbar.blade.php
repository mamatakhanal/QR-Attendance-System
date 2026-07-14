<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg shadow-sm rounded-4 m-2 pe-2">
        <div class="container-fluid">

            <!-- LEFT -->
            <div class="d-flex align-items-center">
                <!-- TOGGLE -->
                <button class="btn" id="toggleSidebar">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h5 class="fw-bold mb-0"> {{ $pageTitle ?? '' }}</h5>
            </div>

            <!-- RIGHT -->
            <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end navbar-right">

                {{-- Search --}}
                @if (isset($pageTitle) && $pageTitle != 'Dashboard')
                    @php
                        $placeholders = [
                            'Students' => 'Search by Students Name, Code or Email...',
                            'Teachers' => 'Search by Teachers Name, Email, or Phone...',
                            'Subjects' => 'Search by Subjects Name or Code...',
                            'Assign Classes' => 'Search by Teacher Name or Subject Name...',
                        ];
                    @endphp

                    @if (array_key_exists($pageTitle, $placeholders))
                        <div class="position-relative search-box">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 small text-muted"></i>
                            <input type="text" id="globalSearch" class="form-control form-control-sm ps-5 py-2 rounded-3"
                                placeholder="{{ $placeholders[$pageTitle] }}">
                        </div>
                    @endif
                @endif


                <!-- PROFILE -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                        role="button" aria-expanded="false" data-bs-toggle="dropdown">
                        <div class="bg-dark text-white rounded-circle d-flex justify-content-center align-items-center"
                            style="width:32px; height:32px; font-size:14px;"> A </div>
                        <span class="ms-2 fw-semibold text-dark">
                            {{ \Illuminate\Support\Str::of(session('admin_name') ?? 'Admin')->before(' ') }} </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end rounded-3 shadow-lg" style="min-width:200px;">
                        <li> <a class="dropdown-item text-dark small" href="{{ route('admin.profile') }}"> <i
                                    class="bi bi-person-circle me-2"></i> Profile </a> </li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li>
                            <form action="{{ route('admin.logout') }}" method="POST" class="m-0 p-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger small">
                                    <i class="bi bi-box-arrow-right me-2"></i> Sign out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</body>

<!-- TOGGLE SCRIPT -->
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const toggleBtn = document.getElementById("toggleSidebar");
        const closeBtn = document.getElementById("closeSidebar");
        const wrapper = document.querySelector(".main-wrapper");

        function checkScreen() {
            if (window.innerWidth <= 768) {
                wrapper.classList.add("sidebar-collapsed"); // hide on mobile
            } else {
                wrapper.classList.remove("sidebar-collapsed"); // show on desktop
            }
        }
        checkScreen();

        window.addEventListener("resize", checkScreen);

        // toggle button
        toggleBtn.addEventListener("click", function() {
            wrapper.classList.toggle("sidebar-collapsed");
        });

        // close button
        closeBtn.addEventListener("click", function() {
            wrapper.classList.add("sidebar-collapsed");
        });

    });
</script>

{{-- Search --}}
<script>
    $(document).ready(function() {
        $("#globalSearch").on("keyup", function() {
            if (typeof loadData === "function") {
                loadData();
            }
        });
    });
</script>
