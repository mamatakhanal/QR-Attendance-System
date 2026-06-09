<head>
  <title>Dashboard - Admin</title>
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
          Dashboard
        </h1>

        <div class="row g-4">
          <div class="col-md-3">
            <div class="card dashboard-card border-0 shadow-sm">
              <div class="card-body">
                <h6 class="text-muted">
                  Total Students
                </h6>
                <h1 class="fw-bold">
                  250
                </h1>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="card dashboard-card border-0 shadow-sm">
              <div class="card-body">
                <h6 class="text-muted">
                  Total Teachers
                </h6>
                <h1 class="fw-bold">
                  25
                </h1>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

