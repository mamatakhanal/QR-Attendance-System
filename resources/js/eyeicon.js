// Teacher & Student - Login - Eye Icon
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
