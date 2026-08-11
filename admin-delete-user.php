<?php
session_start();
header('Content-Type: application/json');

// SAME DB AS YOUR SYSTEM
$host = "localhost";
$user = "u820562602_fleurscents";
$pass = "Aa2RmDG?Pe0";
$db   = "u820562602_fleurscents_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB Connection failed']);
    exit;
}

// CURRENT LOGGED IN USER (MATCHES YOUR admin-usermanagement.php SESSION CHECK)
$currentUserId = $_SESSION['user_id'] ?? 0;

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$id = intval($data['id']);

// Prevent self delete
if ($id === $currentUserId) {
    echo json_encode(['success' => false, 'message' => 'You cannot delete your own account']);
    exit;
}

// CHECK USER EXISTS
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$user = $result->fetch_assoc();

// ONLY ALLOW DELETE FOR ADMIN / COLLECTOR
if (!in_array($user['role'], ['admin', 'collector', 'barangay_secretary'])) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete residents']);
    exit;
}

// DELETE USER
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>