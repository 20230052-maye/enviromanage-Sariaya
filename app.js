window.addEventListener("load", () => {

    const splashDuration = 2000;
    const fadeDuration = 500;

    // ==========================
    // SERVICE WORKER
    // ==========================
    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register("service-worker.js")
            .then(reg => console.log("Service Worker registered:", reg))
            .catch(err => console.log("Service Worker registration failed:", err));
    }

    // ==========================
    // ROUTER
    // ==========================
    function routeUser() {

        const role = localStorage.getItem("role");
        const loggedIn = localStorage.getItem("logged_in");

        // If already logged in, go directly to dashboard
        if (loggedIn === "true" && role) {
            redirect(role);
            return;
        }

        // Otherwise, open the login page
        // (The login page now contains the install section.)
        window.location.href = "login.php";
    }

    function redirect(role) {

        switch (role) {

            case "admin":
                window.location.href = "admin-home.php";
                break;

            case "collector":
                window.location.href = "collector-home.php";
                break;

            case "resident":
                window.location.href = "resident-home.php";
                break;

            case "barangay_secretary":
                window.location.href = "barangay-secretary-home.php";
                break;

            default:
                window.location.href = "login.php";
        }

    }

    // ==========================
    // SPLASH SCREEN
    // ==========================
    setTimeout(() => {

        document.body.classList.add("fade-out");

        setTimeout(routeUser, fadeDuration);

    }, splashDuration);

});