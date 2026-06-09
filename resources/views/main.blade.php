<head>
    <title>Dashboard - Admin</title>

    @include('layouts.link')
    @include('layouts.style')

    <script src="{{ asset('js/arrow.js') }}"></script>
</head>

<body>

    <!-- MAIN LAYOUT -->
    <div class="main-wrapper">

        <!-- SIDEBAR -->
        <div class="sidebar d-flex flex-column border-end shadow-lg border-2 p-2 rounded-end-4">
            <!-- LOGO -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-2 px-2">
                <a href="{{ url('/admin/dashboard') }}" class="text-decoration-none d-flex align-items-center">
                    <img src="{{ asset('storage/images/logo.png') }}" alt="logo" height="30" class="me-1">
                    <h4 class="fw-bold mt-2">
                        <span class="text-success">QR</span>
                        <span class="text-primary">Attendance</span>
                    </h4>
                </a>
                <button class="sidebar-close-btn" id="closeSidebar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- MENU -->
            <ul class="nav flex-column ">
                <!-- DASHBOARD -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active-sidebar' : '' }}">
                        <i class="bi bi-house-door-fill me-3"></i> Dashboard
                    </a>
                </li>

                <!-- USERS -->
                <li class="nav-item my-2">
                    <div class="menu-toggle d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" data-bs-target="#userMenu" aria-expanded="true"
                        style="cursor:pointer; font-size:14px; padding:0.5rem 1rem;">
                        <span>Users</span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </div>
                    <ul class="collapse show list-unstyled" id="userMenu">
                        <li>
                            <a href="{{ route('admin.teachers') }}"
                                class="nav-link {{ request()->routeIs('admin.teachers') ? 'active-sidebar' : '' }}">
                                <i class="bi bi-person-workspace me-3"></i> Teachers
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.students') }}"
                                class="nav-link {{ request()->routeIs('admin.students') ? 'active-sidebar' : '' }}">
                                <i class="bi bi-mortarboard-fill me-3"></i> Students
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- CLASS SUBJECT -->
                <li class="nav-item my-2">
                    <div class="menu-toggle d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" data-bs-target="#classMenu" aria-expanded="true"
                        style="cursor:pointer; font-size:14px; padding:0.5rem 1rem;">
                        <span>Class & Subject</span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </div>
                    <ul class="collapse show list-unstyled" id="classMenu">
                        <li>
                            <a href="{{ route('admin.classes') }}"
                                class="nav-link {{ request()->routeIs('admin.classes') ? 'active-sidebar' : '' }}">
                                <i class="bi bi-building me-3"></i> Classes
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.subjects') }}"
                                class="nav-link {{ request()->routeIs('admin.subjects') ? 'active-sidebar' : '' }}">
                                <i class="bi bi-book me-3"></i> Subjects
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.assignclass') }}"
                                class="nav-link {{ request()->routeIs('admin.assignclass') ? 'active-sidebar' : '' }}">
                                <i class="bi bi-people me-3"></i> Assign Class
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- ATTENDANCE -->
                <li class="nav-item">
                    <a href="{{ route('admin.attendance') }}"
                        class="nav-link {{ request()->routeIs('admin.attendance') ? 'active-sidebar' : '' }}">
                        <i class="bi bi-clipboard-check me-3"></i> Attendance Reports
                    </a>
                </li>

                <!-- REPORT -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-bar-chart me-3"></i> Reports
                    </a>
                </li>

            </ul>
        </div>

        <div class="main-area">
            <!-- NAVBAR -->
            <nav class="navbar navbar-expand-lg shadow-lg rounded-4 m-2 pe-2">
                <div class="container-fluid">

                    <!-- LEFT -->
                    <div class="d-flex align-items-center">
                        <!-- TOGGLE -->
                        <button class="btn" id="toggleSidebar">
                            <i class="bi bi-list fs-3"></i>
                        </button>
                        <span class="fw-bold fs-4"> Admin Panel </span>
                    </div>

                    <!-- RIGHT -->
                    <div class="d-flex align-items-center gap-4">
                        <!-- SEARCH -->
                        <div class="search-box position-relative" style="max-width:300px;">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 small text-muted"></i>
                            <input type="text" class="form-control form-control-sm rounded-3 ps-4 py-2" placeholder="Search">
                        </div>

                        <!-- PROFILE -->
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" role="button" aria-expanded="false" data-bs-toggle="dropdown">
                                <div class="bg-dark text-white rounded-circle d-flex justify-content-center align-items-center"
                                    style="width:32px; height:32px; font-size:14px;"> A </div>
                                <span class="ms-2 fw-semibold text-dark">Admin</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end rounded-3 shadow-lg" style="min-width:200px;">
                                <li> <a class="dropdown-item text-dark small" href="{{ route('admin.profile') }}"> <i class="bi bi-person-circle me-2"></i> Profile </a> </li>
                                <li>
                                    <hr class="dropdown-divider my-1">
                                </li>
                                <li> <a class="dropdown-item small text-danger" href="{{ route('admin.login') }}"> <i class="bi bi-box-arrow-right me-2"></i> Sign out </a> </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- CONTENT -->
            <div class="main-content">

                <h1 class="fw-semibold mb-4">
                    Dashboard
                </h1>
                
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="card dashboard-card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted">
                                    Total Students
                                </h6>
                                <h1 class="fw-bold">
                                    250
                                </h1>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card dashboard-card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted">
                                    Total Teachers
                                </h6>
                                <h1 class="fw-bold">
                                    25
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TOGGLE SCRIPT -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const toggleBtn = document.getElementById("toggleSidebar");
            const closeBtn = document.getElementById("closeSidebar");
            const wrapper = document.querySelector(".main-wrapper");

            // open/close from navbar
            toggleBtn.addEventListener("click", function() {
                wrapper.classList.toggle("sidebar-collapsed");
            });

            // close from X button
            closeBtn.addEventListener("click", function() {
                wrapper.classList.add("sidebar-collapsed");
            });

        });
    </script>

</body>

<style>
    body {
        font-family: "Inter", "Roboto", "Poppins", sans-serif;
        background: white;
        margin: 0;
    }

    /* MAIN */
    .main-wrapper {
        display: flex;
        min-height: 100vh;
    }

    /* SIDEBAR */
    .sidebar {
        width: 270px;
        min-width: 270px;
        height: 100vh;
        background: whitesmoke;
        position: sticky;
        top: 0;
        overflow-y: auto;
        transition: all 0.3s ease;
    }

    .sidebar-hide {
        margin-left: -270px;
    }

    .sidebar-collapsed .sidebar {
        margin-left: -270px;
    }

    .sidebar-collapsed .main-area {
        width: 100%;
    }

    .main-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        width: 100%;
        transition: all 0.3s ease;
    }

    .navbar {
        height: 54px;
        background: #ffffff;
        border-bottom: 1px solid #ececec;
    }

    .nav-link {
        color: #111827;
        padding: 8px 12px;
        border-radius: 10px;
        margin: 2px 2px;
        font-size: 15px;
        display: flex;
        align-items: center;
        transition: 0.2s ease;
        text-decoration: none;
    }

    .active-sidebar {
        background: #cfd9ff;
        color: #2246d1 !important;
        font-weight: 500;
    }

    .nav-link:hover,
    .nav-link.active {
        background-color: #bbcdf0;
        border-radius: 10px;
        color: #072c91 !important;
        font-weight: 500;
    }

    .dropdown-item:focus,
    .dropdown-item:active {
        background-color: #c8d2e7;
    }

    .arrow {
        transition: transform 0.3s ease;
    }

    .menu-toggle {
        padding: 12px 14px;
        cursor: pointer;
        border-radius: 12px;
        font-size: 15px;
    }

    .menu-toggle:hover {
        background: #edf2ff;
    }

    .menu-toggle[aria-expanded="true"] .arrow {
        transform: rotate(180deg);
    }

    .sidebar-close-btn {
        background: transparent;
        border: none;
        font-size: 18px;
        cursor: pointer;
        display: none;
        color: #333;
    }

    /* MOBILE */
    @media(max-width:768px) {
        .sidebar {
            position: fixed;
            z-index: 1050;
        }
        .search-box {
            display: none;
        }
        .navbar {
            padding: 0 14px;
        }
        .main-content {
            padding: 20px 14px;
        }
    }

    @media(max-width:768px) {
        .sidebar-close-btn {
            display: block;
        }
    }

    .main-content {
        padding: 30px 24px;
    }

    .dashboard-card {
        border-radius: 18px;
    }

    .dashboard-card .card-body {
        padding: 22px;
    }
</style>