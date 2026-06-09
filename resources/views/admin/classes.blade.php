<head>
    <title>Classes - Admin</title>
    @include('layouts.link')
    @include('layouts.style')
</head>

<body>
    <!-- MAIN LAYOUT -->
    <div class="main-wrapper">
        @include('admin.sidebar')
        <div class="main-area">
            @include('admin.navbar')

            <!-- CONTENT -->
            <div class="main-content">
                <h1 class="fw-semibold mb-4">
                    Class
                </h1>
            </div>
        </div>
    </div>
</body>