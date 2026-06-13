<head>
    <title>Admin Login</title>
    @include('layouts.link')
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            Background: #f6efe9;
            margin: 0;
            justify-content: center;
            align-items: center;
        }

        .signin-card {
            width: 100%;
            max-width: 460px;
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 15px 40px rgba(239, 241, 245, 0.2);
        }

        .btn-signin:hover {
            background: #0e3a7c;
        }
    </style>
    <script src="{{ asset('js/eyeicon.js') }}"></script>
</head>

<body style="background: linear-gradient(135deg, #0f172a, #2a489d); display: flex;">
    <div class="signin-card">
        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <!-- LOGO -->
            <div class="d-flex justify-content-center mb-2">
                <a href="{{ url('/home') }}" class="text-decoration-none d-flex align-items-center">
                    <img src="{{ asset('storage/images/logo.png') }}" alt="logo" height="30" style="margin-right:4px;">
                    <h4 class="fw-bold mt-2">
                        <span class="text-success">QR</span>
                        <span class="text-primary">Attendance</span>
                    </h4>
                </a>
            </div>
            <div class="mb-4">
                <h2 class="text-center fs-3 fw-semibold"> Sign in </h2>
            </div>
            @if(session('error'))
            <div class="alert alert-danger text-center py-2">
                {{ session('error') }}
            </div>
            @endif
            <div class="mb-4">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" required>
                <span class="text-danger">@error('email'){{ $message }}@enderror</span>
            </div>

            <div class="mb-4">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <span class="input-group-text bg-white" onclick="togglePassword()" style="cursor:pointer;">
                        <i class="ri-eye-off-line" id="eyeIcon"></i></span>
                </div>
                <span class="text-danger">@error('password'){{ $message }}@enderror</span>
            </div>
            <button type="submit" class="btn btn-signin btn-primary w-100 rounded-3"> Sign in </button>
        </form>
    </div>
    @yield('content')
</body>

<script>
    function togglePassword() {
        let pass = document.getElementById("password");
        let icon = document.getElementById("eyeIcon");

        if (pass.type === "password") {
            pass.type = "text";
            icon.classList.remove("ri-eye-off-line");
            icon.classList.add("ri-eye-line");
        } else {
            pass.type = "password";
            icon.classList.remove("ri-eye-line");
            icon.classList.add("ri-eye-off-line");
        }
    }
</script>