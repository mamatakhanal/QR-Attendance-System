<head>
    <title>Profile - Teacher</title>
    @include('layouts.link')
    @include('layouts.style')
</head>

<body>

    <!-- MAIN LAYOUT -->
    <div class="main-wrapper">
        @include('teacher.sidebar')
        <div class="main-area">
            @include('teacher.navbar')

            <!-- CONTENT -->
            <div class="main-content">

                <h1 class="fw-semibold mb-4">
                    Profile
                </h1>

            </div>
        </div>
    </div>
</body>