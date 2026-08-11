<?php
session_start();
header("Content-Type: application/json");
date_default_timezone_set('Asia/Manila');
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== "resident"
) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized."
    ]);
    exit;
}

$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);
    exit;
}

$residentID = $_SESSION['user_id'];
$complaintID = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("
SELECT
    id,
    ticket_no,
    queue_no,
    complaint_location,
    category,
    description,
    validation_status,
    action_status,
    submitted_at
FROM resident_complaints
WHERE id = ?
AND resident_id = ?
LIMIT 1
");

$stmt->bind_param("ii", $complaintID, $residentID);
$stmt->execute();

$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) {

    echo json_encode([
        "success" => false,
        "message" => "Complaint not found."
    ]);
    exit;
}

$complaint = $result->fetch_assoc();
$complaint["submitted_at"] = date(
    "F j, Y | g:i A",
    strtotime($complaint["submitted_at"])
);
/* Load complaint photos */

$photos = [];

$photoStmt = $conn->prepare("
SELECT photo
FROM resident_complaint_photos
WHERE complaint_id = ?
");

$photoStmt->bind_param("i", $complaintID);
$photoStmt->execute();

$photoResult = $photoStmt->get_result();

while ($row = $photoResult->fetch_assoc()) {

    $photos[] = "uploads/complaints/" . $row["photo"];

}

$complaint["photos"] = $photos;

echo json_encode([
    "success" => true,
    "complaint" => $complaint
]);