<?php
session_start();

header("Content-Type: application/json");
date_default_timezone_set("Asia/Manila");

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== "barangay_secretary"
) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
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


$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
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

        resident_complaints.id,
        resident_complaints.ticket_no,
        resident_complaints.queue_no,

        resident_complaints.complaint_location,
        resident_complaints.description,

        resident_complaints.category,

        resident_complaints.validation_status,
        resident_complaints.action_status,

        resident_complaints.remarks,
        resident_complaints.admin_notes,

        resident_complaints.submitted_at,

        users.first_name,
        users.last_name,

        users.email,
        users.phone,

        CONCAT(
            users.house_no,' ',
            users.street,', ',
            users.barangay,', ',
            users.postal_code
        ) AS address

    FROM resident_complaints

    INNER JOIN users
        ON resident_complaints.resident_id = users.id

    WHERE resident_complaints.id = ?

    LIMIT 1
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows == 0) {

    echo json_encode([
        "success" => false,
        "message" => "Complaint not found."
    ]);

    exit;
}


$row = $result->fetch_assoc();


/* ==========================================
   FORMAT COMPLAINT DATE
========================================== */

if (!empty($row["submitted_at"])) {

    $row["submitted_at"] = date(
        "F j, Y | g:i A",
        strtotime($row["submitted_at"])
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
    ORDER BY id ASC
");

$photoStmt->bind_param("i", $id);
$photoStmt->execute();

$photoResult = $photoStmt->get_result();

while ($photo = $photoResult->fetch_assoc()) {

    $photos[] =
        "uploads/complaints/" .
        $photo["photo"];

}

$row["photos"] = $photos;


/* ==========================================
   LOAD LATEST APPEAL
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

    ORDER BY id DESC

    LIMIT 1
");

$appealStmt->bind_param("i", $id);
$appealStmt->execute();

$appealResult = $appealStmt->get_result();


if ($appealResult && $appealResult->num_rows > 0) {

    $appeal = $appealResult->fetch_assoc();


    /* ==========================================
       FORMAT APPEAL DATES
    ========================================== */

    if (!empty($appeal["submitted_at"])) {

        $appeal["submitted_at"] = date(
            "F j, Y | g:i A",
            strtotime($appeal["submitted_at"])
        );

    }


    if (!empty($appeal["reviewed_at"])) {

        $appeal["reviewed_at"] = date(
            "F j, Y | g:i A",
            strtotime($appeal["reviewed_at"])
        );

    }

}


/* ==========================================
   ATTACH APPEAL TO COMPLAINT
========================================== */

$row["appeal"] = $appeal;


/* ==========================================
   RETURN RESPONSE
========================================== */

echo json_encode([
    "success" => true,
    "complaint" => $row
]);


/* ==========================================
   CLOSE
========================================== */

$stmt->close();
$photoStmt->close();
$appealStmt->close();
$conn->close();

?>