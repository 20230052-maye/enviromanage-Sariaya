<?php

session_start();

header("Content-Type: application/json");
date_default_timezone_set("Asia/Manila");


// ==========================================
// AUTHENTICATION
// ==========================================

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


// ==========================================
// DATABASE
// ==========================================

require_once "db.php";


// ==========================================
// GET INPUT
// ==========================================

$secretaryID = intval($_SESSION['user_id']);

$id = intval($_POST['id'] ?? 0);

$remarks = trim($_POST['remarks'] ?? '');


// ==========================================
// VALIDATE ID
// ==========================================

if ($id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid complaint ID."
    ]);

    exit;
}


// ==========================================
// VALIDATE REMARKS
// ==========================================

if ($remarks === "") {

    echo json_encode([
        "success" => false,
        "message" => "Remarks are required."
    ]);

    exit;
}


// ==========================================
// CHECK COMPLAINT
// ==========================================

$check = $conn->prepare("
    SELECT
        id,
        validation_status,
        action_status
    FROM resident_complaints
    WHERE id = ?
");

if (!$check) {

    echo json_encode([
        "success" => false,
        "message" => "CHECK PREPARE ERROR: " . $conn->error
    ]);

    exit;
}


$check->bind_param("i", $id);


if (!$check->execute()) {

    echo json_encode([
        "success" => false,
        "message" => "CHECK EXECUTE ERROR: " . $check->error
    ]);

    $check->close();
    exit;
}


$result = $check->get_result();


// ==========================================
// COMPLAINT NOT FOUND
// ==========================================

if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Complaint not found."
    ]);

    $check->close();
    exit;
}


$complaint = $result->fetch_assoc();

$check->close();


// ==========================================
// CHECK STATUS
// ==========================================

if ($complaint["validation_status"] !== "Under Review") {

    echo json_encode([
        "success" => false,
        "message" =>
            "Complaint cannot be forwarded. Current validation status is: " .
            $complaint["validation_status"]
    ]);

    exit;
}


// ==========================================
// FORWARD TO MENRO
// ==========================================

$stmt = $conn->prepare("
    UPDATE resident_complaints
    SET
        validation_status = 'Approved',
        action_status = 'Pending Assignment',
        remarks = ?,
        reviewed_by = ?,
        reviewed_at = NOW()
    WHERE id = ?
    AND validation_status = 'Under Review'
");


if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "UPDATE PREPARE ERROR: " . $conn->error
    ]);

    exit;
}


$stmt->bind_param(
    "sii",
    $remarks,
    $secretaryID,
    $id
);


if (!$stmt->execute()) {

    echo json_encode([
        "success" => false,
        "message" => "UPDATE ERROR: " . $stmt->error
    ]);

    $stmt->close();
    exit;
}


// ==========================================
// CHECK UPDATE
// ==========================================

if ($stmt->affected_rows <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Complaint was not updated."
    ]);

    $stmt->close();
    exit;
}


// ==========================================
// SUCCESS
// ==========================================

echo json_encode([
    "success" => true,
    "message" => "Complaint forwarded to MENRO.",
    "complaint_id" => $id,
    "validation_status" => "Approved",
    "action_status" => "Pending Assignment"
]);


$stmt->close();

exit;

?>