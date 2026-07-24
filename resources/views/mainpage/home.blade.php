<head>
  <title>QR-Based Attendance System</title>
  @include('layouts.link')
  @include('mainpage.login')
  <style>
    /* Home Page */
    body {
      font-family: "Times New Roman", Times, serif;
      Background: #f6efe9;
      margin: 0;
      justify-content: center;
      align-items: center;
    }

    .banner {
      padding: 120px 20px;
      background: linear-gradient(135deg, #0f172a, #2a489d);
    }

    .btn-login:hover {
      background: #0e3a7c;
    }

    .feature-box {
      padding: 25px;
      border-radius: 15px;
      transition: 0.3s;
      border: none;
      background: #f8f9fa;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .feature-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>

<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg bg-white shadow fixed-top">
    <div class="container">
      <!-- LOGO -->
      <a href="{{ url('/home') }}" class="text-decoration-none d-flex align-items-center">
        <img src="{{ asset('images/logo.png') }}" alt="logo" height="50" style="margin-right:4px;">
        <h2 class=" fw-bold mt-2">
          <span class="text-success">QR</span> <span class="text-primary">Attendance</span>
        </h2>
      </a>
      <!-- BUTTON -->
      <div class="d-flex ms-auto gap-3">
        <button class="btn btn-outline-success rounded-3 px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#teacherlogin"> Teacher </button>
        <button class="btn btn-outline-primary rounded-3 px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#studentlogin"> Student </button>
      </div>
    </div>
  </nav>



  <!-- Banner -->
  <section id="home" class="banner text-center text-white mt-5">
    <h1 class="display-4 fw-bold mt-5">
      QR Based College Attendance System
    </h1>
    <p class="lead mt-3 ">
      A smart and easy system to manage student attendance using QR code technology.
    </p>

    <div class="d-flex justify-content-center gap-4 mt-4">
      <a href="#features" class="btn btn-warning px-4 py-2 fw-semibold">
        Explore Features
      </a>
      <a href="#howitworks" class="btn btn-light px-4 py-2 fw-semibold">
        How It Works
      </a>
    </div>
  </section>



  <!-- Features -->
  <section id="features" class="text-center container mt-5">
    <h2 class="fw-bold">Key Features</h2>
    <p class="text-muted"> Core features provided by the QR-Based Attendance System</p>
    <div class="row mt-2 g-4">
      <div class="col-md-4">
        <div class="feature-box">
          <i class="bi bi-qr-code-scan  fs-1 text-success"></i>
          <h5 class="mt-3">QR Scan</h5>
          <p class="text-muted">Quick attendance marking using QR code.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-box">
          <i class="bi bi-shield-check fs-1 text-primary"></i>
          <h5 class="mt-3">Secure Access</h5>
          <p class="text-muted">Separate login for teacher and student.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-box">
          <i class="bi bi-graph-up fs-1 text-warning"></i>
          <h5 class="mt-3">Reports</h5>
          <p class="text-muted">Teachers can view attendance anytime.</p>
        </div>
      </div>
    </div>
  </section>



  <!-- How It Works -->
  <section id="howitworks" class="text-center container mt-5">
    <h2 class="fw-bold">How It Works</h2>
    <p class="text-muted">Step-by-step process of QR-Based Attendance System</p>
    <div class="row mt-2 g-4">
      <div class="col-md-3">
        <div class="feature-box">
          <i class="bi bi-person-badge fs-1 text-primary"></i>
          <h5 class="mt-3">Step 1</h5>
          <p class="text-muted">Student receives a unique QR code.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="feature-box">
          <i class="bi bi-qr-code fs-1 text-success"></i>
          <h5 class="mt-3">Step 2</h5>
          <p class="text-muted">Student shows QR code for scanning.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="feature-box">
          <i class="bi bi-phone fs-1 text-warning"></i>
          <h5 class="mt-3">Step 3</h5>
          <p class="text-muted">Teacher scans the QR <br> code.</p>
        </div>
      </div>
      <div class="col-md-3">
        <div class="feature-box">
          <i class="bi bi-check-circle fs-1 text-danger"></i>
          <h5 class="mt-3">Step 4</h5>
          <p class="text-muted">Attendance is recorded successfully.</p>
        </div>
      </div>
    </div>
  </section>



  <!-- Login Section -->
  <section class="text-center container mt-5">
    <h2 class="fw-bold">System Access</h2>
    <p class="text-muted">Select your role to access the system</p>
    <div class="row justify-content-center mt-2 g-4">
      <!-- Teacher Login -->
      <div class="col-md-4">
        <div class="feature-box">
          <i class="bi bi-person-workspace fs-1 text-success"></i>
          <h5 class="mt-3">Teacher Login</h5>
          <p class="text-muted">Manage student attendance records.</p>
          <button type="button" class="btn btn-success rounded-3 w-100" data-bs-toggle="modal" data-bs-target="#teacherlogin"> Login as Teacher </button>
        </div>
      </div>
      <!-- Student Login -->
      <div class="col-md-4">
        <div class="feature-box">
          <i class="bi bi-person-circle fs-1 text-primary"></i>
          <h5 class="mt-3">Student Login</h5>
          <p class="text-muted">View your attendance status and records.</p>
          <button type="button" class="btn btn-primary rounded-3 w-100" data-bs-toggle="modal" data-bs-target="#studentlogin"> Login as Student </button>
        </div>
      </div>
    </div>
  </section>



  <!-- About -->
  <section class="banner text-white text-center mt-5 ">
    <div class="container">
      <h1 class="display-4 fw-bold"> About System </h1>
      <p class="lead">
        The QR-Based Attendance System is designed to simplify student attendance using QR code technology. <br>
        It improves accuracy, reduces manual work, and ensures a fast and secure attendance process for both students and teachers.
      </p>
    </div>
  </section>



  <!-- FOOTER -->
  <footer class="text-center bg-light p-1 pt-3 ">
    <p> Copyright © 2026 QR Attendance System. All Rights Reserved.</p>
  </footer>

</body>