<?php
session_start();

require_once "db.php";


// ============================
// 1. CHECK ACTIVE SESSION
// ============================
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: admin-home.php");
            exit;

        case 'collector':
            header("Location: collector-home.html");
            exit;

        case 'resident':
            header("Location: resident-home.php");
            exit;
    }
}


// ============================
// 2. CHECK REMEMBER ME COOKIE
// ============================
if (isset($_COOKIE['remember_me'])) {

    $userId = intval($_COOKIE['remember_me']);

    $stmt = $conn->prepare("SELECT id, role FROM users WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {

        $user = $result->fetch_assoc();

        // Recreate session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['last_activity'] = time();

        switch ($user['role']) {
            case 'admin':
                header("Location: admin-home.php");
                exit;

            case 'collector':
                header("Location: collector-home.html");
                exit;

            case 'resident':
                header("Location: resident-home.php");
                exit;
        }
    }

    $stmt->close();
}

$conn->close();


// ============================
// 3. DEFAULT → LOGIN
// ============================
header("Location: login.php");
exit;
?>