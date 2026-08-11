<?php
session_start();

header("Content-Type: application/json");

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'collector'
) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access."
    ]);
    exit;
}

// DB CONNECTION
$conn = new mysqli(
    "localhost",
    "u823857209_enviromanage",
    "Enviromanage4322",
    "u823857209_enviromanage"
);
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);
    exit;
}

// Get POST data
$schedule_id = intval($_POST['schedule_id'] ?? 0);
$barangay    = trim($_POST['barangay'] ?? '');
$street      = trim($_POST['street'] ?? '');
$status      = trim($_POST['status'] ?? '');

// Validation
$allowedStatus = [
    "Pending",
    "In Progress",
    "Completed",
    "Incomplete"
];

if (
    $schedule_id <= 0 ||
    $barangay == "" ||
    $street == "" ||
    !in_array($status, $allowedStatus)
) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid collection data."
    ]);
    exit;
}

// Check if record already exists
$check = $conn->prepare("
    SELECT id
    FROM collection_progress
    WHERE schedule_id = ?
      AND barangay = ?
      AND street = ?
");

$check->bind_param(
    "iss",
    $schedule_id,
    $barangay,
    $street
);

$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {

    // UPDATE existing progress
    $row = $result->fetch_assoc();

    $update = $conn->prepare("
        UPDATE collection_progress
        SET status = ?,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");

    $update->bind_param(
        "si",
        $status,
        $row['id']
    );

    if ($update->execute()) {

        echo json_encode([
            "success" => true,
            "action" => "updated",
            "message" => "Collection progress updated successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Unable to update collection progress."
        ]);

    }

} else {

    // INSERT new progress
    $insert = $conn->prepare("
        INSERT INTO collection_progress
        (
            schedule_id,
            barangay,
            street,
            status
        )
        VALUES
        (
            ?, ?, ?, ?
        )
    ");

    $insert->bind_param(
        "isss",
        $schedule_id,
        $barangay,
        $street,
        $status
    );

    if ($insert->execute()) {

        echo json_encode([
            "success" => true,
            "action" => "inserted",
            "message" => "Collection progress saved successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Unable to save collection progress."
        ]);

    }

}

$conn->close();
?>