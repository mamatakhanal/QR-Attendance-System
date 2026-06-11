<head>
    <title>Profile - Admin</title>
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
            <div class="card shadow-sm border-0 m-3 px-4 rounded-4">
                <form id="editAdminProfile" class=" p-4">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="edit_id">

                    <div class="modal-header mb-3">
                        <h3 class="modal-title fw-bold"> Profile </h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control">
                            <small class="text-danger" id="edit_name_error"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                            <small class="text-danger" id="edit_email_error"></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control edit-password">
                                <span class="input-group-text toggle-password">
                                    <i class="ri-eye-off-line"></i>
                                </span>
                            </div>
                            <small class="text-muted"> Leave blank to keep current password </small> <br>
                            <small class="text-danger" id="edit_password_error"></small>
                        </div>
                    </div>

                    <div class="modal-footer mb-0">
                        <button type="submit" class="btn btn-primary">Save Profile</button>
                    </div>
                </form>
            </div>
        </div>
</body>

<!-- JS-Password-Eye -->
<script>
    document.querySelectorAll(".toggle-password").forEach(function(toggle) {

        toggle.addEventListener("click", function() {

            let input = this.parentElement.querySelector(".edit-password");
            let icon = this.querySelector("i");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("ri-eye-off-line");
                icon.classList.add("ri-eye-line");
            } else {
                input.type = "password";
                icon.classList.remove("ri-eye-line");
                icon.classList.add("ri-eye-off-line");
            }
        });
    });
</script>