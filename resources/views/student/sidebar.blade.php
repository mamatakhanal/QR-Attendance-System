<body>
    <!-- SIDEBAR -->
    <div class="sidebar d-flex flex-column border-end shadow-lg border-2 p-2 rounded-end-4">
        <!-- LOGO -->
        <div class="d-flex justify-content-between align-items-center mb-3 mt-2 px-2">
            <a href="{{ url('/student/dashboard') }}" class="text-decoration-none d-flex align-items-center border-bottom border-3 pb-1">
                <img src="{{ asset('images/logo.png') }}" alt="logo" height="30" class="me-2">
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
                <a href="{{ route('student.dashboard') }}"
                    class="nav-link {{ request()->routeIs('student.dashboard') ? 'active-sidebar' : '' }}">
                    <i class="bi bi-house-door-fill me-3"></i> Dashboard
                </a>
            </li>

              <!-- QR Code -->
            <li class="nav-item">
                <a href="{{ route('student.qrcode') }}"
                    class="nav-link {{ request()->routeIs('student.qrcode') ? 'active-sidebar' : '' }}">
                    <i class="bi bi-qr-code me-3"></i> QR Code
                </a>
            </li>

            <!-- CLASSES -->
            <li class="nav-item">
                <a href="{{ route('student.classes') }}"
                    class="nav-link {{ request()->routeIs('student.classes') ? 'active-sidebar' : '' }}">
                    <i class="bi bi-building me-3"></i> Classes
                </a>
            </li>

            <!-- ATTENDANCE -->
            <li class="nav-item">
                <a href="{{ route('student.attendance') }}"
                    class="nav-link {{ request()->routeIs('student.attendance') ? 'active-sidebar' : '' }}">
                    <i class="bi bi-qr-code-scan me-3"></i> Attendance
                </a>
            </li>

            {{-- <!-- REPORT -->
            <li class="nav-item">
                <a href="{{ route('student.reports') }}" 
                class="nav-link {{ request()->routeIs('student.reports') ? 'active-sidebar' : '' }}">
                    <i class="bi bi-clipboard-data me-3"></i> Reports
                </a>
            </li> --}}

        </ul>
    </div>
</body>