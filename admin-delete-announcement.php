<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access"
    ]);
    exit;
}

// DB CONNECTION (edit credentials if needed)
$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

// GET JSON INPUT
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || empty($data['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid announcement ID"
    ]);
    exit;
}

$id = intval($data['id']);

// DELETE QUERY
$stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "success" => true,
            "message" => "Announcement deleted successfully"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Announcement not found"
        ]);
    }

} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to delete announcement"
    ]);
}

$stmt->close();
$conn->close();
?>