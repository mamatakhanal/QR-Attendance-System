<body>
    <!-- SIDEBAR -->
    <div class="sidebar d-flex flex-column border-end shadow-lg border-2 p-2 rounded-end-4">
        <!-- LOGO -->
        <div class="d-flex justify-content-between align-items-center mb-3 mt-2 px-2">
            <a href="{{ url('/teacher/dashboard') }}"
                class="text-decoration-none d-flex align-items-center border-bottom border-3 pb-2">
                <img src="{{ asset('storage/images/logo.png') }}" alt="logo" height="30" class="me-2">
                <h4 class="fw-bold mt-2">
                    <span class="text-success">QR</span>
                    <span class="text-primary">Attendance</span>
                </h4>
            </a>
            <button class="sidebar-close-btn mb-2" id="closeSidebar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- MENU -->
        <ul class="nav flex-column ">
            <!-- DASHBOARD -->
            <li class="nav-item">
                <a href="{{ route('teacher.dashboard') }}"
                    class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active-sidebar' : '' }}">
                    <i class="bi bi-house-door-fill me-3"></i> Dashboard
                </a>
            </li>

            <!-- CLASSES -->
            <li class="nav-item">
                <a href="{{ route('teacher.assignclass') }}"
                    class="nav-link {{ request()->routeIs('teacher.assignclass') ? 'active-sidebar' : '' }}">
                    <i class="bi bi-building me-3"></i> Assigned Classes
                </a>
            </li>

            <!-- STUDENT -->
            <li class="nav-item">
                <a href="{{ route('teacher.students') }}"
                    class="nav-link {{ request()->routeIs('teacher.students') ? 'active-sidebar' : '' }}">
                    <i class="bi bi-people me-3"></i> Students List
                </a>
            </li>

            <!-- Take ATTENDANCE -->
            <li class="nav-item">
                <a href="{{ route('teacher.attendance') }}"
                    class="nav-link {{ request()->routeIs('teacher.attendance') ? 'active-sidebar' : '' }}">
                    <i class="bi bi-qr-code-scan me-3"></i> Take Attendance
                </a>
            </li>

            <!-- Attendance Records -->
            <li class="nav-item">
                <a href="{{ route('teacher.attendancerecords') }}"
                    class="nav-link {{ request()->routeIs('teacher.attendancerecords') ? 'active-sidebar' : '' }}">
                    <i class="bi bi-clipboard-data me-3"></i> Attendance Records
                </a>
            </li>

        </ul>
    </div>
</body>
