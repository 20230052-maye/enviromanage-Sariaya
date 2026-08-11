<?php
session_start();

header('Content-Type: application/json');

// Admin only
if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'admin'
) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access."
    ]);
    exit;
}

// Database
$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);
    exit;
}

$stmt = $conn->prepare("
    SELECT setting_value
    FROM system_settings
    WHERE setting_key = ?
    LIMIT 1
");

$key = "max_schedule_hours";
$stmt->bind_param("s", $key);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Schedule setting not found."
    ]);

    exit;
}

$row = $result->fetch_assoc();

echo json_encode([
    "success" => true,
    "max_schedule_hours" => (int)$row["setting_value"]
]);

$stmt->close();
$conn->close();