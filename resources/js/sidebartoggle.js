// TOGGLE SCRIPT

document.addEventListener("DOMContentLoaded", function() {

    const toggleBtn = document.getElementById("toggleSidebar");
    const closeBtn = document.getElementById("closeSidebar");
    const wrapper = document.querySelector(".main-wrapper");

    function checkScreen() {
      if (window.innerWidth <= 768) {
        wrapper.classList.add("sidebar-collapsed"); // hide on mobile
      } else {
        wrapper.classList.remove("sidebar-collapsed"); // show on desktop
      }
    }

    checkScreen();

    window.addEventListener("resize", checkScreen);

    // toggle button
    toggleBtn.addEventListener("click", function() {
      wrapper.classList.toggle("sidebar-collapsed");
    });

    // close button
    closeBtn.addEventListener("click", function() {
      wrapper.classList.add("sidebar-collapsed");
    });

  });
