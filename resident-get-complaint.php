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

/* ==========================================
   DB CONNECTION
========================================== */

$conn = new mysqli(
    "localhost",
    "u823857209_enviromanage",
    "Enviromanage4322",
    "u823857209_enviromanage"
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

if ($complaintID <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid complaint ID."
    ]);
    exit;
}


/* ==========================================
   GET COMPLAINT
========================================== */

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
        remarks,
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


/* ==========================================
   FORMAT SUBMITTED DATE
========================================== */

if (!empty($complaint["submitted_at"])) {

    $complaint["submitted_at"] = date(
        "F j, Y | g:i A",
        strtotime($complaint["submitted_at"])
    );

}


/* ==========================================
   LOAD COMPLAINT PHOTOS
========================================== */

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

/* ==========================================
   LOAD COMPLAINT APPEAL
========================================== */

$appeal = null;

$appealStmt = $conn->prepare("
    SELECT
        id,
        complaint_id,
        resident_id,
        appeal_reason,
        status,
        reviewed_at,
        secretary_remarks,
        submitted_at
    FROM complaint_appeals
    WHERE complaint_id = ?
    AND resident_id = ?
    ORDER BY id DESC
    LIMIT 1
");

$appealStmt->bind_param("ii", $complaintID, $residentID);
$appealStmt->execute();

$appealResult = $appealStmt->get_result();

if ($appealResult && $appealResult->num_rows > 0) {

    $appeal = $appealResult->fetch_assoc();

if (!empty($appeal["submitted_at"])) {

    $submittedDate = DateTime::createFromFormat(
        "Y-m-d H:i:s",
        $appeal["submitted_at"],
        new DateTimeZone("Asia/Manila")
    );

    if ($submittedDate !== false) {

        $appeal["submitted_at"] =
            $submittedDate->format("F j, Y | g:i A");

    }
}


if (!empty($appeal["reviewed_at"])) {

    $reviewedDate = DateTime::createFromFormat(
        "Y-m-d H:i:s",
        $appeal["reviewed_at"],
        new DateTimeZone("Asia/Manila")
    );

    if ($reviewedDate !== false) {

        $appeal["reviewed_at"] =
            $reviewedDate->format("F j, Y | g:i A");

    }
}
}

$complaint["appeal"] = $appeal;


/* ==========================================
   RETURN RESPONSE
========================================== */

echo json_encode([
    "success" => true,
    "complaint" => $complaint,
    "appeal" => $appeal
]);

$stmt->close();
$photoStmt->close();
$appealStmt->close();
$conn->close();
?>