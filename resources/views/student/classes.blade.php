<head>
    <title>Classes - Student</title>
    @include('layouts.link')
    @include('layouts.style')
</head>

<body>

    <!-- MAIN LAYOUT -->
    <div class="main-wrapper">
        @include('student.sidebar')
        <div class="main-area">
            @include('student.navbar')

            <!-- CONTENT -->
            <div class="main-content">

                <h1 class="fw-semibold mb-4">
                    Classes
                </h1>

            </div>
        </div>
    </div>
</body>