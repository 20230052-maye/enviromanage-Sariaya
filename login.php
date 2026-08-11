<?php
ob_start();
session_start();
// DB CONNECTION
$conn = new mysqli(
    "localhost",
    "u823857209_enviromanage",
    "Enviromanage4322",
    "u823857209_enviromanage"
);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

/*
|--------------------------------------------------------------------------
| AUTO LOGOUT WHEN RETURNING TO LOGIN PAGE
|--------------------------------------------------------------------------
| If the user is already logged in and opens login.php directly
| (not by submitting the login form), log them out.
*/
if (
    isset($_SESSION['user_id']) &&
    $_SERVER['REQUEST_METHOD'] !== 'POST'
) {

    // Update database
    $stmt = $conn->prepare("UPDATE users SET is_logged_in = 0 WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    // Destroy current session
    session_unset();
    session_destroy();

    // Start a fresh empty session
    session_start();
}

$showInstallToast = isset($_GET['installed']) && $_GET['installed'] == 1;
$error = "";
$userRoleJS = "";

// DB CONNECTION
$conn = new mysqli(
    "localhost",
    "u823857209_enviromanage",
    "Enviromanage4322",
    "u823857209_enviromanage"
);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$showInstallToast = isset($_GET['installed']) && $_GET['installed'] == 1;
$error = "";
$userRoleJS = "";

// ==========================
// AUTO LOGIN (REMEMBER ME)
// ==========================
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {

    $userId = intval($_COOKIE['remember_me']);

   $stmt = $conn->prepare("SELECT id, role, approval_status FROM users WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $userRoleJS = $user['role'];
        $_SESSION['last_activity'] = time();
        $_SESSION['active'] = true;

        $stmtUpdate = $conn->prepare("UPDATE users SET is_logged_in=1, last_activity=NOW() WHERE id=?");
        $stmtUpdate->bind_param("i", $user['id']);
        $stmtUpdate->execute();
        $stmtUpdate->close();

      switch ($user['role']) {

    case 'admin':
        $redirect = "admin-home.php";
        break;

    case 'collector':
        $redirect = "collector-home.php";
        break;

 case 'resident':
    switch ($user['approval_status']) {
        case 'pending':
            $redirect = "resident-account-approval.php";
            break;

        case 'approved':
            $redirect = "resident-home.php";
            break;

        case 'rejected':
            $redirect = "resident-account-rejected.php";
            break;

        default:
            $redirect = "resident-account-approval.php";
            break;
    }
    break;

    case 'barangay_secretary':
        $redirect = "barangay-secretary-home.php";
        break;

    default:
        $redirect = "login.php";
}
    }

    $stmt->close();
}


// ==========================
// LOGIN FORM
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $stmt = $conn->prepare("SELECT id, password, role, approval_status FROM users WHERE BINARY email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $userRoleJS = $user['role'];
            $_SESSION['last_activity'] = time();
            $_SESSION['active'] = true;

            if ($remember) {
                setcookie('remember_me', $user['id'], time() + (30*24*60*60), "/");
            } else {
                setcookie('remember_me', '', time() - 3600, "/");
            }

            $stmtUpdate = $conn->prepare("UPDATE users SET is_logged_in=1, last_activity=NOW() WHERE id=?");
            $stmtUpdate->bind_param("i", $user['id']);
            $stmtUpdate->execute();
            $stmtUpdate->close();

            switch ($user['role']) {
    case 'admin':
        header("Location: admin-home.php");
        exit;

    case 'collector':
        header("Location: collector-home.php");
        exit;

  case 'resident':
    switch ($user['approval_status']) {
        case 'pending':
            header("Location: resident-account-approval.php");
            break;

        case 'approved':
            header("Location: resident-home.php");
            break;

        case 'rejected':
            header("Location: resident-account-rejected.php");
            break;

        default:
            header("Location: resident-account-approval.php");
            break;
    }
    exit;

    case 'barangay_secretary':
        header("Location: barangay-secretary-home.php");
        exit;

    default:
        header("Location: login.php");
        exit;
}

        } else {
            $error = "Incorrect password";
        }

    } else {
        $error = "User not found";
    }

    $stmt->close();
}

$conn->close();
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>

<link rel="manifest" href="/manifest.json">

<meta name="theme-color" content="#4CAF50">

<link rel="apple-touch-icon" href="/assets/enviromanage-logo-192.png">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>EnviroManage Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

  
body, html {
  margin:0;
  padding:0;
  font-family:'Roboto',sans-serif;
  height:100%;
  overflow:hidden;
}

.container-desktop{
    display:none;
    height:100vh;
}

.left-panel{
    width:50%;
    background:#1D4525;
    color:#fff;

    display:flex;
    justify-content:center;
    align-items:center;

    padding:60px;
    box-sizing:border-box;
}

.right-panel{
    width:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#f8f9fa;
}

.install-box{
    width:100%;
    max-width:500px;

    display:flex;
    flex-direction:column;
    align-items:center;

    text-align:center;
}

.install-box img{
    width:220px;
    margin-bottom:25px;
}

.install-box h2{
    font-weight:700;
    margin-bottom:15px;
}

.install-box p{
    line-height:1.7;
    margin-bottom:35px;
    opacity:.95;
}

.logo{
    width:200px;
    height:200px;
}

.btn-container{
    width:100%;
    max-width:320px;

    display:flex;
    flex-direction:column;
    align-items:center;

    gap:12px;

    margin:0 auto;
}

.btn-container button{
    width:100%;
    display:block;

    padding:12px 24px;

    background:#ffffff;
    color:#1D4525;

    border:2px solid #1D4525;
    border-radius:8px;

    cursor:pointer;
    font-weight:600;
    font-size:15px;

    transition:all .25s ease;
}

.btn-container button:hover{
    background:#f5f5f5;
    color:#163519;
    border-color:#163519;
    transform:translateY(-2px);
    box-shadow:0 4px 12px rgba(0,0,0,.15);
}

.login-card {
  width:100%;
  max-width:400px;
  padding:30px;
  border-radius:15px;
  box-shadow:0 4px 12px rgba(0,0,0,0.15);
  text-align:center;

  opacity:0;
  transform:translateY(20px);
  animation: fadeInLogo 0.7s forwards;
  animation-delay:0.8s;
}

.login-card h2 { color:#1D4525; }

.form-control { margin-bottom:15px; border-radius:8px; }

.btn-login {
  width:100%;
  background:#1D4525;
  color:white;
  border-radius:8px;
}

.btn-login:hover { background:#163519; }

.password-wrapper {
  position:relative;
}

.password-wrapper i {
  position:absolute;
  right:10px;
  top:50%;
  transform:translateY(-50%);
  cursor:pointer;
}

.error { color:red; margin-bottom:10px; }

.container-mobile {
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:flex-start;
  min-height:100vh;
  background-color:#ffffff;
}

@media(min-width:992px){
  .container-desktop { display:flex; }
  .container-mobile { display:none; }
  .left-panel, .right-panel { width:50%; }
}

.toast-container {
  position:fixed;
  bottom:20px;
  right:20px;
}

.signup-btn {
  width: 100%;
  margin-top: 10px;
  background: transparent;
  border: 2px solid #1D4525;
  color: #1D4525;
  border-radius: 8px;
  padding: 10px;
  font-weight: 500;
  transition: 0.2s;
}

.signup-btn:hover {
  background: #1D4525;
  color: #fff;
}

/* ANIMATION */
@keyframes dropSemiCircle {
  0% { transform:translateY(-200px); opacity:0; }
  100% { transform:translateY(0); opacity:1; }
}

@keyframes fadeInLogo {
  0% { opacity:0; transform:scale(0.8); }
  100% { opacity:1; transform:scale(1); }
}

/* HEADER */
.mobile-logo-bg {
  background-color:#1D4525;
  width:120%;
  height:260px;
  border-bottom-left-radius:80% 100%;
  border-bottom-right-radius:80% 100%;
  display:flex;
  justify-content:center;
  align-items:center;

  opacity:0;
  transform:translateY(-200px);
  animation:dropSemiCircle 0.7s forwards;
}

/* LOGO */
.mobile-logo-bg img {
  width:200px;
  height:200px;
  opacity:0;
  transform:scale(0.8);
  animation:fadeInLogo 0.7s forwards;
  animation-delay:0.5s;
}

/* CARD FIXED */
.mobile-card {
    width:90%;
    max-width:500px;

    margin-top:50px; /* was 100px */

    padding:30px 20px;
    border-radius:15px;
    box-shadow:0 4px 12px rgba(0,0,0,0.15);

    display:flex;
    flex-direction:column;
    align-items:center;
    text-align:center;

    opacity:0;
    animation:fadeInLogo 0.7s forwards;
    animation-delay:0.8s;
}

/* IMPORTANT: force inputs full width */
.mobile-card .form-control,
.mobile-card .password-wrapper {
  width:100%;
  margin-bottom:15px;
}

.forgot-link{
    font-size:14px;
    color:#1D4525;
    text-decoration:none;
    font-weight:500;
}

.forgot-link:hover{
    text-decoration:underline;
    color:#163519;
}

.form-check{
    margin:0;
}

.form-check-input{
    cursor:pointer;
}

.remember-label{
    font-size:14px;
    color:#555;
    cursor:pointer;
    user-select:none;
}

.form-check-input:checked{
    background-color:#1D4525;
    border-color:#1D4525;
}

.form-check-input:focus{
    box-shadow:0 0 0 .2rem rgba(29,69,37,.15);
}

.mobile-install{
    width:90%;
    max-width:500px;
    text-align:center;
    margin-top:25px;
}

.mobile-install p{
    font-size:14px;
    color:#666;
}

.mobile-install-prompt{

    position:fixed;

    left:15px;
    right:15px;
    bottom:20px;

    background:#fff;

    border-radius:18px;

    padding:18px;

    box-shadow:0 10px 35px rgba(0,0,0,.18);

    display:none;

    z-index:9999;

    animation:slideUp .35s ease;

}

.prompt-icon{

    width:55px;
    height:55px;

    margin-bottom:12px;

}

.prompt-icon img{

    width:100%;

}

.prompt-content h6{

    margin:0;
    color:#1D4525;
    font-weight:700;

}

.prompt-content p{

    margin:8px 0 15px;
    font-size:14px;
    color:#666;

}

.prompt-action{

    display:flex;
    gap:10px;

}

.prompt-btn{

    flex:1;

    padding:12px;

    border-radius:10px;

    border:none;

    background:#1D4525;

    color:#fff;

    font-weight:600;

}

.install-close{

    position:absolute;

    top:10px;
    right:10px;

    border:none;
    background:none;

    color:#888;

    font-size:18px;

}

@keyframes slideUp{

from{

    transform:translateY(100%);
    opacity:0;

}

to{

    transform:translateY(0);
    opacity:1;

}

}

@media (min-width:992px){

    .mobile-install-prompt{
        display:none !important;
    }

}

/* iOS Install Guide Modal */
#iosModal .modal-content{
    border:0;
    border-radius:18px;
    box-shadow:
        0 20px 60px rgba(0,0,0,.22),
        0 8px 20px rgba(0,0,0,.12);
}

#iosModal .modal-header{
    border-bottom:1px solid #f0f0f0;
    padding:18px 22px;
}

#iosModal .modal-body{
    padding:22px;
}

#iosModal ol{
    margin-bottom:0;
}

#iosModal li{
    margin-bottom:12px;
}

#iosModal{
    backdrop-filter: blur(4px);
}

#iosModal.show{
    background: rgba(0,0,0,.25);
}

</style>

</head>
<body>


<!-- ===========================
     DESKTOP
=========================== -->
<div class="container-desktop">

    <!-- INSTALL PANEL -->
    <div class="left-panel">

        <div class="install-box">

            <img src="assets/enviromanage-logo-512.png" class="logo" alt="EnviroManage Logo">

            <h2>Install EnviroManage!</h2>

            <p>
                Get a faster, offline-friendly, and app-like experience.<br>
                Stay updated with real-time garbage collection schedules.
            </p>

            <div class="btn-container">

                <button id="install-btn">
    Install App
</button>

                <button id="ios-btn">
                    Install on iPhone / iPad
                </button>

            </div>

        </div>

    </div>

    <!-- LOGIN PANEL -->
    <div class="right-panel">

        <div class="login-card">

            <h2>Login</h2>

            <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>

            <form method="POST">

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Email"
                    required
                >

                <div class="password-wrapper">
                    <input
                        type="password"
                        name="password"
                        id="pass1"
                        class="form-control"
                        placeholder="Password"
                        required
                    >
                    <i class="fa fa-eye-slash" onclick="toggle('pass1', this)"></i>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">

    <div class="form-check">
        <input
            class="form-check-input"
            type="checkbox"
            name="remember"
            id="rememberDesktop"
            <?php if(isset($_COOKIE['remember_me'])) echo "checked"; ?>
        >
        <label class="form-check-label remember-label" for="rememberDesktop">
            Remember Me
        </label>
    </div>

    <a href="forgot-password.php" class="forgot-link">
        Forgot Password?
    </a>

</div>

                <button class="btn btn-login">
                    Login
                </button>

                <a href="signup.php" class="btn signup-btn">
                    Create Account
                </a>

            </form>

        </div>

    </div>

</div>

<!-- ===========================
     MOBILE
=========================== -->
<div class="container-mobile">

    <!-- Header -->
    <div class="mobile-logo-bg">
        <img src="assets/enviromanage-logo-512.png" alt="EnviroManage Logo">
    </div>


    <!-- Login Card -->
    <div class="mobile-card">

        <h2>Login</h2>

        <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="POST" style="width:90%;">

            <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Email"
                required
            >

            <div class="password-wrapper">
                <input
                    type="password"
                    name="password"
                    id="pass2"
                    class="form-control"
                    placeholder="Password"
                    required
                >
                <i class="fa fa-eye-slash" onclick="toggle('pass2', this)"></i>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 w-100">

    <div class="form-check">
        <input
            class="form-check-input"
            type="checkbox"
            name="remember"
            id="rememberMobile"
            <?php if(isset($_COOKIE['remember_me'])) echo "checked"; ?>
        >
        <label class="form-check-label remember-label" for="rememberMobile">
            Remember Me
        </label>
    </div>

    <a href="forgot-password.php" class="forgot-link">
        Forgot Password?
    </a>

</div>

            <button class="btn btn-login">
                Login
            </button>

            <a href="signup.php" class="btn signup-btn">
                Create Account
            </a>

        </form>

    </div>

</div>

<!-- Mobile Install Prompt -->
<div class="mobile-install-prompt" id="mobileInstallPrompt">

    <button class="install-close" id="closeInstallPrompt">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <div class="prompt-content">

        <h6>Install EnviroManage</h6>

        <p>
            Faster access, offline support, and real-time collection updates.
        </p>

    </div>

    <div class="prompt-action">

        <button id="mobile-install-btn" class="prompt-btn">
            Install
        </button>

        <button id="mobile-ios-btn" class="prompt-btn">
            Guide
        </button>

    </div>

</div>

<!-- ===========================
     TOAST
=========================== -->
<div class="toast-container">
    <div id="installedToast" class="toast text-bg-success border-0">
        <div class="d-flex">
            <div class="toast-body">
                EnviroManage successfully installed!
            </div>

            <button
                class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast">
            </button>
        </div>
    </div>
</div>

<!-- ===========================
     iOS INSTALL MODAL
=========================== -->
<div class="modal fade" id="iosModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">

            <div class="modal-header">
                <h5 class="modal-title">Add to Home Screen</h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                <ol class="text-start">
                    <li>Tap the <strong>Share</strong> icon in Safari.</li>
                    <li>Select <strong>Add to Home Screen</strong>.</li>
                    <li>Tap <strong>Add</strong>.</li>
                </ol>

            </div>

        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

    window.addEventListener("pageshow", function (event) {
    if (event.persisted || performance.getEntriesByType("navigation")[0]?.type === "back_forward") {
        window.location.href = "force-logout.php";
    }
});

 let deferredPrompt = null;

const installButtons = [
    document.getElementById("install-btn"),
    document.getElementById("mobile-install-btn")
];

const iosButtons = [
    document.getElementById("ios-btn"),
    document.getElementById("mobile-ios-btn")
];

// Hide everything initially
installButtons.forEach(btn => {
    if (btn) btn.style.display = "none";
});

iosButtons.forEach(btn => {
    if (btn) btn.style.display = "none";
});

// Detect iPhone/iPad Safari
const isIOS =
    /iphone|ipad|ipod/i.test(navigator.userAgent);

const isSafari =
    /^((?!chrome|android).)*safari/i.test(navigator.userAgent);

const isStandalone =
    window.matchMedia("(display-mode: standalone)").matches ||
    window.navigator.standalone === true;

// Already installed → hide install UI
if (!isStandalone && isIOS && isSafari) {

    // Desktop iOS guide
    document.getElementById("ios-btn").style.display = "block";

    // Mobile bottom sheet
    document.getElementById("mobileInstallPrompt").style.display = "block";

    // Hide Android install button
    document.getElementById("mobile-install-btn").style.display = "none";

    // Show iOS guide button
    document.getElementById("mobile-ios-btn").style.display = "block";

}

// Android / Desktop install prompt
window.addEventListener("beforeinstallprompt", (e) => {

    e.preventDefault();
    deferredPrompt = e;

    // Desktop install button
    document.getElementById("install-btn").style.display = "block";

    // Mobile bottom sheet
    document.getElementById("mobileInstallPrompt").style.display = "block";

    // Show the Install button inside the prompt
    document.getElementById("mobile-install-btn").style.display = "block";

    // Hide iOS buttons because Android supports native install
    document.getElementById("ios-btn").style.display = "none";
    document.getElementById("mobile-ios-btn").style.display = "none";

});

// Native Install
async function installApp() {

    if (!deferredPrompt) {

        alert("Installation isn't available yet. Please use Chrome or wait until the page finishes loading.");

        return;
    }

    deferredPrompt.prompt();

    await deferredPrompt.userChoice;

    deferredPrompt = null;
    document.getElementById("mobileInstallPrompt").style.display="none";
}

// Android/Desktop buttons
installButtons.forEach(btn => {
    if (btn) btn.addEventListener("click", installApp);
});

// iOS Guide
function showIOSGuide() {

    // Hide the install prompt
    document.getElementById("mobileInstallPrompt").style.display = "none";

    const modal = new bootstrap.Modal(
        document.getElementById("iosModal")
    );

    modal.show();

}

const iosModal = document.getElementById("iosModal");

// When the guide is closed, show the install prompt again
iosModal.addEventListener("hidden.bs.modal", () => {

    if (
        !window.matchMedia("(display-mode: standalone)").matches &&
        isIOS &&
        isSafari
    ) {
        document.getElementById("mobileInstallPrompt").style.display = "block";
    }

});

document.getElementById("closeInstallPrompt").addEventListener("click",()=>{

    document.getElementById("mobileInstallPrompt").style.display="none";

});

iosButtons.forEach(btn => {
    if (btn) btn.addEventListener("click", showIOSGuide);
});

// Installed event
window.addEventListener("appinstalled", () => {

    installButtons.forEach(btn => {
        if (btn) btn.style.display = "none";
    });

    iosButtons.forEach(btn => {
        if (btn) btn.style.display = "none";
    });

    new bootstrap.Toast(
        document.getElementById("installedToast")
    ).show();

});

</script>

<?php if (!empty($userRoleJS)): ?>

<script>

localStorage.setItem("logged_in", "true");
localStorage.setItem("role", "<?= $userRoleJS ?>");

window.location.href = "<?= $redirect ?>";

</script>

<?php endif; ?>

<script>
if ("serviceWorker" in navigator) {

    window.addEventListener("load", () => {

        navigator.serviceWorker.register("service-worker.js")
            .then(reg => {
                console.log("✅ Service Worker registered:", reg);
            })
            .catch(err => {
                console.error("❌ Service Worker registration failed:", err);
            });

    });

}


</script>

<script>
function toggle(inputId, icon) {
    const input = document.getElementById(inputId);

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
}
</script>

</body>
</html>