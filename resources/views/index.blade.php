<head>
    <script src="{{ asset('js/arrow.js') }}"></script>
</head>


<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top px-2 ">
        <div class="container-fluid">

        
            <!-- LOGO -->
            <a href="{{ url('/admin/dashboard') }}" class="text-decoration-none d-flex align-items-center">
                <img src="{{ asset('storage/images/logo.png') }}" height="40" style="margin-right:-7px;">
                <h2 class="fs-4 fw-bold mt-3 ms-1">
                    <span class="text-success">skill</span><span class="text-primary">career</span>
                </h2>
            </a>

            <!-- PROFILE -->
            <div class="ms-auto d-flex align-items-center gap-3">

                <!-- SEARCH -->
                <div class="position-relative flex-grow-1" style="max-width:300px;">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 small text-muted"></i>
                    <input type="text" class="form-control form-control-sm rounded-3 ps-4" placeholder="Search">
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                        <div class="bg-dark text-white rounded-circle d-flex justify-content-center align-items-center"
                            style="width:32px; height:32px; font-size:13px;"> A
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end rounded-3 shadow-lg" style="min-width:200px;">
                        <li> <a class="dropdown-item text-dark small" href="#"> <i class="bi bi-person me-2"></i> admin </a> </li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li> <a class="dropdown-item small text-danger" href="#"> <i class="bi bi-box-arrow-right me-2"></i> Sign out </a> </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- SIDEBAR -->
    <div class="sidebar border-end p-3" style="width:250px;">
        <ul class="nav flex-column">
            <!-- DASHBOARD -->
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ url('/admin/dashboard') }}">
                    <i class="bi bi-grid me-3"></i>Dashboard
                </a>
            </li>

            <!-- USER -->
            <li class="nav-item">
                <a class="nav-link text-dark d-flex align-items-center" href="#">
                    <i class="bi bi-people me-3"></i>Users
                </a>
            </li>

            <!-- SKILLS -->
            <li class="nav-item my-2">
                <div class="text-dark d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse" href="#skillsMenu" role="button" onclick="toggleArrow(this)"
                    style="cursor: pointer; font-size: 12px; padding: 0.5rem 1rem;">
                    <span class="">SKILLS</span>
                    <span>
                        <i class="bi bi-chevron-down arrow-down"></i>
                        <i class="bi bi-chevron-up arrow-up d-none"></i>
                    </span>
                </div>
                <ul class="collapse list-unstyled" id="skillsMenu">
                    <li><a class="nav-link text-dark" href="#"><i class="bi bi-tags me-3"></i> Categories </a></li>
                    <li><a class="nav-link text-dark" href="#"><i class="bi bi-lightbulb me-3"></i> Skills </a></li>
                    <li><a class="nav-link text-dark" href="#"><i class="bi bi-diagram-3 me-3"></i> Skill Mapping </a></li>
                </ul>
            </li>

            <!-- ASSESSMENT -->
            <li class="nav-item my-2">
                <div class="text-dark d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse" href="#skillsAssessment" role="button" onclick="toggleArrow(this)"
                    style="cursor: pointer; font-size: 12px; padding: 0.5rem 1rem;">
                    <span class="">ASSESSMENT</span>
                    <span>
                        <i class="bi bi-chevron-down arrow-down"></i>
                        <i class="bi bi-chevron-up arrow-up d-none"></i>
                    </span>
                </div>
                <ul class="collapse list-unstyled" id="skillsAssessment">
                    <li><a class="nav-link text-dark" href="#"><i class="bi bi-tags me-3"></i> Create Test </a></li>
                    <li><a class="nav-link text-dark" href="#"><i class="bi bi-lightning-charge me-3"></i> Assign Test </a></li>
                    <li><a class="nav-link text-dark" href="#"><i class="bi bi-person-check me-3"></i> Results </a></li>
                </ul>
            </li>

             <!-- REPORT -->
            <li class="nav-item">
                <a class="nav-link text-dark d-flex align-items-center" href="#">
                    <i class="bi bi-bar-chart me-3"></i>Reports
                </a>
            </li>
        </ul>
    </div>
</body>