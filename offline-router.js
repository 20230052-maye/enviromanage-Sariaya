// ==========================
// EnviroManage OFFLINE APP MODE
// ==========================

(function () {

  const role = localStorage.getItem("role");
  const userId = localStorage.getItem("user_id");
  const loggedIn = localStorage.getItem("logged_in");

  const isOffline = !navigator.onLine;

  // Only run offline logic when offline
  if (!isOffline) return;

  const path = window.location.pathname;

  // ==========================
  // CASE 1: USER IS ALREADY LOGGED IN BEFORE
  // ==========================
  if (loggedIn === "true" && role && userId) {

    // Prevent login page from showing offline
    if (path.includes("login.php") || path === "/" || path.includes("index.php")) {

      redirectToDashboard(role);
      return;
    }

    // If user somehow lands elsewhere, fix it
    if (!path.includes(roleHome(role))) {
      redirectToDashboard(role);
      return;
    }
  }

  // ==========================
  // CASE 2: NO LOGIN DATA
  // ==========================
  else {

    // Force login page (must be cached)
    if (!path.includes("login.php")) {
      window.location.href = "login.php";
    }
  }

  // ==========================
  // HELPERS
  // ==========================

  function redirectToDashboard(role) {
    window.location.href = roleHome(role);
  }

  function roleHome(role) {
    switch (role) {
      case "admin":
        return "admin-home.php";
      case "collector":
        return "collector-home.html";
      case "resident":
      default:
        return "resident-home.php";
    }
  }

})();