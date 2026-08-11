<?php
session_start();
header('Content-Type: application/json');
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No active session']);
    exit;
}

$event = $_POST['event'] ?? '';
$userId = $_SESSION['user_id'];


switch($event) {
    case 'heartbeat':
    case 'active':
        // Only update last_activity if user is active
        $stmt = $conn->prepare("UPDATE users SET is_logged_in = 1, last_activity = NOW() WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
        $_SESSION['last_activity'] = time();
        $_SESSION['active'] = true;
        echo json_encode(['success' => true, 'status' => 'active']);
        break;

    case 'idle':
        $stmt = $conn->prepare("UPDATE users SET is_logged_in = 0 WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
        $_SESSION['active'] = false;
        echo json_encode(['success' => true, 'status' => 'idle']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid event']);
}

$conn->close();
?>