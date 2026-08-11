<?php
header('Content-Type: application/json');
session_start();
// DB CONNECTION
$conn = new mysqli(
    "localhost",
    "u823857209_enviromanage",
    "Enviromanage4322",
    "u823857209_enviromanage"
);
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'DB connection failed'
    ]);
    exit;
}

// Read JSON input safely
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

// Prevent invalid JSON crash
if (!is_array($data)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON input'
    ]);
    exit;
}

$plate_no = trim($data['plate_no'] ?? '');
$collector_id = $data['collector_id'] ?? null;

// Normalize empty collector_id
if ($collector_id === "" || $collector_id === 0) {
    $collector_id = null;
}

if ($plate_no === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Plate number is required'
    ]);
    exit;
}

// Check if collector already assigned (only if not null)
if (!is_null($collector_id)) {
    $stmt_check = $conn->prepare("SELECT id FROM trucks WHERE collector_id = ?");
    $stmt_check->bind_param("i", $collector_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'This collector is already assigned to another truck'
        ]);
        exit;
    }

    $stmt_check->close();
}

// INSERT
$stmt = $conn->prepare("INSERT INTO trucks (plate_no, collector_id) VALUES (?, ?)");

// IMPORTANT FIX: handle NULL properly
if (is_null($collector_id)) {
    $stmt->bind_param("ss", $plate_no, $collector_id);
} else {
    $stmt->bind_param("si", $plate_no, $collector_id);
}

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Truck added successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add truck',
        'error' => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>