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
        height: 64px;
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
    @media(max-width:768px) {
        .sidebar {
            position: fixed;
            z-index: 1050;
        }

        .search-box {
            display: block;
            width: 150px;
        }

        .navbar {
            padding: 0 10px;
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

    .action-btn {
        transition: 0.2s;
    }

    .action-btn:hover {
        transform: scale(1.1);
    }

    .dashboard-card {
        border-radius: 18px;
    }

    .dashboard-card .card-body {
        padding: 22px;
    }

    .small-toast {
        width: 280px !important;
        min-height: 50px !important;
        padding: 10px !important;
        font-size: 13px !important;
    }
</style>
