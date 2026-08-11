<?php
session_start();
header('Content-Type: application/json');
include "db.php"; //database connection

if (!isset($_SESSION['verified_reset']) || !isset($_SESSION['reset_email'])) {
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$email = $_SESSION['reset_email'];
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($password !== $confirm) {
    echo json_encode(['error' => 'Passwords do not match.']);
    exit();
}

if (strlen($password) < 8) {
    echo json_encode(['error' => 'Password must be at least 8 characters.']);
    exit();
}

// Get current password hash from database
$stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($current_hash);
$stmt->fetch();
$stmt->close();

if (!$current_hash) {
    echo json_encode(['error' => 'User not found.']);
    exit();
}

// Compare new password with current password
if (password_verify($password, $current_hash)) {
    echo json_encode(['error' => 'New password cannot be the same as your previous password. Please choose a different password.']);
    exit();
}

// Hash new password and update
$new_hash = password_hash($password, PASSWORD_DEFAULT);
$update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$update->bind_param("ss", $new_hash, $email);

if ($update->execute()) {
    // Remove session to prevent re-use
    unset($_SESSION['verified_reset']);
    unset($_SESSION['reset_email']);
    echo json_encode(['success' => 'Password has been updated successfully.']);
} else {
    echo json_encode(['error' => 'Failed to update password. Please try again.']);
}

$update->close();
$conn->close();
?>