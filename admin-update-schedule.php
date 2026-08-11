<?php
session_start();
header('Content-Type: application/json');

// AUTH CHECK
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);
    exit;
}

require_once "db.php";

// Only allow POST JSON
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
    exit;
}

// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!is_array($data)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON input"
    ]);
    exit;
}

// Sanitize inputs
$id           = intval($data['id'] ?? 0);
$barangay     = trim($data['barangay'] ?? '');
$day_of_week  = trim($data['day_of_week'] ?? '');
$start_time   = trim($data['start_time'] ?? '');
$end_time     = trim($data['end_time'] ?? '');
$garbage_type = trim($data['garbage_type'] ?? '');
$truck_id     = intval($data['truck_id'] ?? 0);

// VALIDATION
if (
    $id <= 0 ||
    $barangay === '' ||
    $day_of_week === '' ||
    $start_time === '' ||
    $end_time === '' ||
    $garbage_type === '' ||
    $truck_id <= 0
) {
    echo json_encode([
        "success" => false,
        "message" => "All fields are required"
    ]);
    exit;
}

// TIME VALIDATION (safe compare using strtotime)
if (strtotime($start_time) >= strtotime($end_time)) {
    echo json_encode([
        "success" => false,
        "message" => "Start time must be earlier than end time"
    ]);
    exit;
}

try {

    // Optional: check if schedule exists
    $check = $conn->prepare("SELECT id FROM schedules WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        echo json_encode([
            "success" => false,
            "message" => "Schedule not found"
        ]);
        exit;
    }
    $check->close();

    // UPDATE QUERY
    $sql = "UPDATE schedules 
            SET barangay = ?, 
                day_of_week = ?, 
                start_time = ?, 
                end_time = ?, 
                garbage_type = ?, 
                truck_id = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssi",
        $barangay,
        $day_of_week,
        $start_time,
        $end_time,
        $garbage_type,
        $truck_id,
        $id
    );

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Schedule updated successfully"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to update schedule"
        ]);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Server error",
        "error" => $e->getMessage()
    ]);
}