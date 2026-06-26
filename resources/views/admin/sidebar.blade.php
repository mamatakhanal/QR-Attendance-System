<body>
    <!-- SIDEBAR -->
    <div class="sidebar d-flex flex-column border-end shadow-lg border-2 p-2 rounded-end-4">
        <!-- LOGO -->
        <div class="d-flex justify-content-between align-items-center mb-3 mt-2 px-2" >
            <a href="{{ url('/admin/dashboard') }}" class="text-decoration-none d-flex align-items-center border-bottom border-3 pb-2">
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
                            <i class="bi bi-people-fill me-3"></i> Students  
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
                        <a href="{{ route('admin.subjects') }}"
                            class="nav-link {{ request()->routeIs('admin.subjects') ? 'active-sidebar' : '' }}">
                            <i class="bi bi-book me-3"></i> Subjects
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.assignclass') }}"
                            class="nav-link {{ request()->routeIs('admin.assignclass') ? 'active-sidebar' : '' }}">
                            <i class="bi bi-building me-3"></i> Assign Classes
                        </a>
                    </li>
                </ul>
            </li>

            <!-- ATTENDANCE -->
            <li class="nav-item">
                <a href="{{ route('admin.attendance') }}"
                    class="nav-link {{ request()->routeIs('admin.attendance') ? 'active-sidebar' : '' }}">
                    <i class="bi bi-qr-code-scan me-3"></i> Attendance
                </a>
            </li>

            <!-- REPORT -->
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-clipboard-data me-3"></i> Reports
                </a>
            </li>

        </ul>
    </div>
</body>