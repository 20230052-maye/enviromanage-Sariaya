<?php
session_start();

// DB CONNECTION
$conn = new mysqli(
    "localhost",
    "u823857209_enviromanage",
    "Enviromanage4322",
    "u823857209_enviromanage"
);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Update database
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("UPDATE users SET is_logged_in = 0 WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

// Clear session
$_SESSION = [];

// Delete PHP session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Delete Remember Me cookie
setcookie("remember_me", "", time() - 3600, "/");

// Destroy session
session_destroy();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Logging out...</title>
</head>
<body>

<script>
// Clear offline data
localStorage.removeItem("logged_in");
localStorage.removeItem("role");
localStorage.removeItem("pickup_address");
localStorage.removeItem("pickup_address_id");

// Replace history entry
window.location.replace("login.php");
</script>

</body>
</html>