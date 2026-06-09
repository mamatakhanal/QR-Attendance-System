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
        <div class="sidebar shadow-sm">
            <button class="sidebar-close-btn" id="closeSidebar">
    <i class="bi bi-x-lg"></i>
</button>

            <!-- LOGO -->
            <div class="logo-section">
                <a href="{{ route('admin.dashboard') }}"
                    class="text-decoration-none d-flex align-items-center">

                    <img src="{{ asset('storage/images/logo.png') }}"
                        alt="logo"
                        height="30"
                        class="me-2">

                    <h3 class="fw-bold m-0">
                        <span class="text-success">QR</span>
                        <span class="text-primary">Attendance</span>
                    </h3>
                </a>
            </div>

            <!-- MENU -->
            <ul class="nav flex-column px-2">

                <!-- DASHBOARD -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active-sidebar' : '' }}">

                        <i class="bi bi-house-door-fill me-3"></i>
                        Dashboard
                    </a>
                </li>

                <!-- USERS -->
                <li class="nav-item mt-3">

                    <div class="menu-toggle d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse"
                        data-bs-target="#userMenu"
                        aria-expanded="true">

                        <span>Users</span>

                        <i class="bi bi-chevron-down arrow"></i>
                    </div>

                    <ul class="collapse show list-unstyled" id="userMenu">

                        <li>
                            <a href="{{ route('admin.teachers') }}"
                                class="nav-link {{ request()->routeIs('admin.teachers') ? 'active-sidebar' : '' }}">

                                <i class="bi bi-person-workspace me-3"></i>
                                Teachers
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.students') }}"
                                class="nav-link {{ request()->routeIs('admin.students') ? 'active-sidebar' : '' }}">

                                <i class="bi bi-mortarboard-fill me-3"></i>
                                Students
                            </a>
                        </li>

                    </ul>
                </li>

                <!-- CLASS SUBJECT -->
                <li class="nav-item mt-3">

                    <div class="menu-toggle d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse"
                        data-bs-target="#classMenu"
                        aria-expanded="true">

                        <span>Class & Subject</span>

                        <i class="bi bi-chevron-down arrow"></i>
                    </div>

                    <ul class="collapse show list-unstyled" id="classMenu">

                        <li>
                            <a href="{{ route('admin.classes') }}"
                                class="nav-link {{ request()->routeIs('admin.classes') ? 'active-sidebar' : '' }}">

                                <i class="bi bi-building me-3"></i>
                                Classes
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.subjects') }}"
                                class="nav-link {{ request()->routeIs('admin.subjects') ? 'active-sidebar' : '' }}">

                                <i class="bi bi-book me-3"></i>
                                Subjects
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.assignclass') }}"
                                class="nav-link {{ request()->routeIs('admin.assignclass') ? 'active-sidebar' : '' }}">

                                <i class="bi bi-people me-3"></i>
                                Assign Class
                            </a>
                        </li>

                    </ul>
                </li>

                <!-- ATTENDANCE -->
                <li class="nav-item mt-2">

                    <a href="{{ route('admin.attendance') }}"
                        class="nav-link {{ request()->routeIs('admin.attendance') ? 'active-sidebar' : '' }}">

                        <i class="bi bi-clipboard-check me-3"></i>
                        Attendance Reports
                    </a>
                </li>

                <!-- REPORT -->
                <li class="nav-item">

                    <a href="#"
                        class="nav-link">

                        <i class="bi bi-bar-chart me-3"></i>
                        Reports
                    </a>
                </li>

            </ul>
        </div>

        <!-- RIGHT SIDE -->
        <div class="main-area">

            <!-- NAVBAR -->
            <nav class="navbar navbar-expand-lg shadow-sm">

                <div class="container-fluid">

                    <!-- LEFT -->
                    <div class="d-flex align-items-center gap-2">

                        <!-- TOGGLE -->
                        <button class="btn border-0 shadow-none p-0"
                            id="toggleSidebar">

                            <i class="bi bi-list fs-3"></i>
                        </button>

                        <h4 class="fw-bold m-0">
                            Admin Panel
                        </h4>

                    </div>

                    <!-- RIGHT -->
                    <div class="d-flex align-items-center gap-4">

                        <!-- SEARCH -->
                        <div class="position-relative search-box">

                            <i class="bi bi-search search-icon"></i>

                            <input type="text"
                                class="form-control search-input"
                                placeholder="Search">

                        </div>

                        <!-- PROFILE -->
                        <div class="dropdown">

                            <a href="#"
                                class="d-flex align-items-center text-decoration-none dropdown-toggle"
                                data-bs-toggle="dropdown">

                                <div class="profile-circle">
                                    A
                                </div>

                                <span class="fw-semibold text-dark ms-2">
                                    Admin
                                </span>

                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4">

                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('admin.profile') }}">

                                        <i class="bi bi-person-circle me-2"></i>
                                        Profile
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>


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
document.addEventListener("DOMContentLoaded", function () {

    const toggleBtn = document.getElementById("toggleSidebar");
    const closeBtn = document.getElementById("closeSidebar");
    const wrapper = document.querySelector(".main-wrapper");

    // open/close from navbar
    toggleBtn.addEventListener("click", function () {
        wrapper.classList.toggle("sidebar-collapsed");
    });

    // close from X button
    closeBtn.addEventListener("click", function () {
        wrapper.classList.add("sidebar-collapsed");
    });

});
    </script>

</body>

<style>
    body {
        margin: 0;
        font-family: "Inter", "Poppins", sans-serif;
        background: #f5f6fa;
        overflow-x: hidden;
    }

    /* MAIN */
    .main-wrapper {
        display: flex;
        min-height: 100vh;
    }

    /* SIDEBAR */
    .sidebar {
        width: 250px;
        min-width: 250px;
        height: 100vh;
        background: #ffffff;
        border-right: 1px solid #e5e7eb;
        position: sticky;
        top: 0;
        overflow-y: auto;
        transition: all 0.3s ease;
    }

    /* HIDE SIDEBAR */
    .sidebar-hide {
        margin-left: -250px;
    }

    /* LOGO */
    .logo-section {
        padding: 22px 20px;
        border-bottom: 1px solid #f1f1f1;
    }

    /* RIGHT AREA */
    .main-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        width: 100%;
         transition: all 0.3s ease;
    }

    .sidebar-collapsed .sidebar {
    margin-left: -250px;
}

.sidebar-collapsed .main-area {
    width: 100%;
}

    /* NAVBAR */
    .navbar {
        height: 70px;
        background: #ffffff;
        border-bottom: 1px solid #ececec;
        padding: 0 24px;
    }

    /* CONTENT */
    .main-content {
        padding: 30px 24px;
    }

    /* LINKS */
    .nav-link {
        color: #111827;
        padding: 12px 14px;
        border-radius: 12px;
        margin: 4px 0;
        font-size: 15px;
        display: flex;
        align-items: center;
        transition: 0.2s;
        text-decoration: none;
    }

    .nav-link:hover {
        background: #edf2ff;
        color: #2246d1;
    }

    /* ACTIVE */
    .active-sidebar {
        background: #cfd9ff;
        color: #2246d1 !important;
        font-weight: 600;
    }

    /* MENU TOGGLE */
    .menu-toggle {
        padding: 12px 14px;
        cursor: pointer;
        border-radius: 12px;
        font-size: 15px;
    }

    .menu-toggle:hover {
        background: #edf2ff;
    }

    /* ARROW */
    .arrow {
        transition: 0.3s;
    }

    .menu-toggle[aria-expanded="true"] .arrow {
        transform: rotate(180deg);
    }

    /* SEARCH */
    .search-box {
        width: 250px;
    }

    .search-input {
        border-radius: 10px;
        padding-left: 38px;
        height: 38px;
    }

    .search-icon {
        position: absolute;
        top: 11px;
        left: 12px;
        color: gray;
    }

    /* PROFILE */
    .profile-circle {
        width: 35px;
        height: 35px;
        background: #111827;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
    }

    /* CARD */
    .dashboard-card {
        border-radius: 18px;
    }

    .dashboard-card .card-body {
        padding: 22px;
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
    .sidebar-close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: transparent;
    border: none;
    font-size: 18px;
    cursor: pointer;
    display: none; /* hidden on desktop */
    color: #333;
}

/* show only on mobile */
@media(max-width:768px) {
    .sidebar-close-btn {
        display: block;
    }
}
</style>