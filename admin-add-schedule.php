<?php
session_start();
header('Content-Type: application/json');

include "db.php"; // your connection file

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);
    exit;
}

// READ JSON INPUT (IMPORTANT FIX)
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON input"
    ]);
    exit;
}

$barangay = trim($data['barangay'] ?? '');
$day_of_week = $data['day_of_week'] ?? '';
$start_time = $data['start_time'] ?? '';
$end_time = $data['end_time'] ?? '';
$garbage_type = $data['garbage_type'] ?? '';
$truck_id = $data['truck_id'] ?? '';

if (
    empty($barangay) ||
    empty($day_of_week) ||
    empty($start_time) ||
    empty($end_time) ||
    empty($garbage_type) ||
    empty($truck_id)
) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields",
        "debug" => $data
    ]);
    exit;
}

if (!$barangay || !$day_of_week || !$start_time || !$end_time || !$garbage_type || !$truck_id) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields"
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("
        INSERT INTO schedules 
        (barangay, day_of_week, start_time, end_time, garbage_type, truck_id, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "sssssi",
        $barangay,
        $day_of_week,
        $start_time,
        $end_time,
        $garbage_type,
        $truck_id
    );

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Schedule added"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => $stmt->error
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>