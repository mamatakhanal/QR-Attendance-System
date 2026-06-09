<body>
    <!-- TEACHER - LOGIN MODAL -->
    <div class="modal fade" id="teacherlogin" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <form method="POST" action="{{ route('login') }}" class="px-4 py-3">
                    @csrf
                    <input type="hidden" name="role" value="teacher">
                    <div class="modal-header">
                        <h3 class="modal-title fw-bold">Teacher Login</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label"> Email <span class="text-danger">*</span> </label>
                            <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Password <span class="text-danger">*</span> </label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control password-field" placeholder="Enter Password" required>
                                <span class="input-group-text bg-white toggle-password" style="cursor:pointer;">
                                    <i class="ri-eye-off-line"></i>
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-login btn-primary w-100 rounded-3"> Login as Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- STUDENT - LOGIN MODAL -->
    <div class="modal fade" id="studentlogin" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <form method="POST" action="{{ route('login') }}" class="px-4 py-3">
                    @csrf
                    <input type="hidden" name="role" value="student">
                    <div class="modal-header">
                        <h3 class="modal-title fw-bold">Student Login</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label"> Email <span class="text-danger">*</span> </label>
                            <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Password <span class="text-danger">*</span> </label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control password-field" placeholder="Enter Password" required>
                                <span class="input-group-text bg-white toggle-password" style="cursor:pointer;">
                                    <i class="ri-eye-off-line"></i>
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-login btn-primary w-100 rounded-3"> Login as Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>


<script>
    document.querySelectorAll(".toggle-password").forEach(function(toggle) {
        toggle.addEventListener("click", function() {
            let input = this.parentElement.querySelector(".password-field");
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