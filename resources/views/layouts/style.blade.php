<style>
    body {
        font-family: "Inter", "Roboto", "Poppins", sans-serif;
        background: whitesmoke;
        margin: 0;
    }

    /* MAIN */
    .main-wrapper {
        display: flex;
        min-height: 100vh;
    }

    .main-area {
        padding: 0;
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

    .table {
        min-width: 850px;
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
        min-height: 64px;
        background: #ffffff;
        border-bottom: 1px solid #ececec;
        display: flex;
        align-items: center;
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
        background: #bbcdf0;
        color: #072c91 !important;
        font-weight: 500;
    }

    .nav-link:hover,
    .nav-link.active {
        background-color: #bbcdf0;
        border-radius: 10px;
        color: #072c91 !important;
        font-weight: 500;
    }

    .search-box {
        width: 360px;
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

    .menu-toggle[aria-expanded="true"] .arrow {
        transform: rotate(180deg);
    }

    .sidebar-close-btn {
        background: transparent;
        border: none;
        font-size: 18px;
        cursor: pointer;
        display: none;
    }

    /* MOBILE */
    @media (max-width:768px) {

        .navbar {
            padding: 10px 12px;
        }

        .navbar .container-fluid {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .search-box {
            width: 170px;
        }

        .search-box input {
            font-size: 14px;
        }

        .dropdown span {
            display: none;
        }

        .navbar-right {
            gap: 10px !important;
        }

        .main-area {
            overflow-x: auto;
        }

        .dropdown .rounded-circle {
            width: 30px !important;
            height: 30px !important;
            font-size: 13px !important;
        }
    }

    @media(max-width:768px) {
        .sidebar-close-btn {
            display: block;
        }
    }

    @media (max-width:992px) {
        .search-box {
            width: 230px;
        }
    }

    /* Edit Delete */
    .action-btn {
        transition: 0.2s;
    }

    .action-btn:hover {
        transform: scale(1.1);
    }


    /* Success Msg Toast */
    .small-toast {
        width: 280px !important;
        min-height: 50px !important;
        padding: 10px !important;
        font-size: 13px !important;
    }


    /* Dashboard Card */
    .dashboard-card {
        border-radius: 15px;
        transition: 0.3s ease;
        cursor: pointer;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
    }

    /* Semester-btn */
    .semester-btn {
        border: none !important;
        border-radius: 10px;
        padding: 6px 16px;
        margin: 3px;
        background: #f1f3f5;
        color: #333;
        font-weight: 500;
        transition: .3s;
    }

    .semester-btn:hover {
        background: #4f46e5;
        color: white;
    }

    .semester-btn.active {
        background: #421dd5 !important;
        color: white !important;
        box-shadow: 0 2px 2px rgba(15, 9, 110, 0.25);
    }

    /* Quick Action */
    .quick-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 20px;
        transition: all .3s ease;
    }

    .quick-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .6rem 1.2rem rgba(0, 0, 0, .12);
        border-color: #c9dfc7;
    }

    .quick-icon {
        font-size: 24px;
        color: #2563eb;
    }

    .quick-link {
        color: #2563eb;
        font-size: 14px;
        font-weight: 600;
    }

    .quick-card:hover .quick-link {
        text-decoration: underline;
    }
</style>
