<?php

session_start();

header('Content-Type: application/json');

require_once 'db.php';


/* =========================
   ROLE CHECK
========================= */

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'barangay_secretary'
) {

    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access.'
    ]);

    exit;
}


/* =========================
   SECRETARY ID
========================= */

$secretaryID = $_SESSION['user_id'] ?? null;

if (!$secretaryID) {

    echo json_encode([
        'success' => false,
        'message' => 'Secretary session not found.'
    ]);

    exit;
}


/* =========================
   INPUT
========================= */

$complaintID = filter_input(
    INPUT_POST,
    'complaint_id',
    FILTER_VALIDATE_INT
);

$action = trim(
    $_POST['action'] ?? ''
);

$reviewNotes = trim(
    $_POST['review_notes'] ?? ''
);


if (!$complaintID) {

    echo json_encode([
        'success' => false,
        'message' => 'Invalid complaint.'
    ]);

    exit;
}


/* =========================
   VALID ACTION
========================= */

if (
    $action !== 'approve' &&
    $action !== 'reject'
) {

    echo json_encode([
        'success' => false,
        'message' => 'Invalid appeal action.'
    ]);

    exit;
}


/* =========================
   GET SECRETARY BARANGAY
========================= */

$stmt = $conn->prepare("
    SELECT barangay
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param(
    "i",
    $secretaryID
);

$stmt->execute();

$result = $stmt->get_result();

$secretary = $result->fetch_assoc();

$stmt->close();


if (!$secretary) {

    echo json_encode([
        'success' => false,
        'message' => 'Secretary account not found.'
    ]);

    exit;
}


$secretaryBarangay = $secretary['barangay'];


/* =========================
   GET COMPLAINT
   AND VERIFY BARANGAY
========================= */

$stmt = $conn->prepare("
    SELECT
        c.id,
        c.validation_status,
        c.appeal_status,
        u.barangay
    FROM resident_complaints c

    INNER JOIN users u
        ON u.id = c.resident_id

    WHERE c.id = ?
    LIMIT 1
");

$stmt->bind_param(
    "i",
    $complaintID
);

$stmt->execute();

$result = $stmt->get_result();

$complaint = $result->fetch_assoc();

$stmt->close();


if (!$complaint) {

    echo json_encode([
        'success' => false,
        'message' => 'Complaint not found.'
    ]);

    exit;
}


/* =========================
   BARANGAY ACCESS CHECK
========================= */

if (
    $complaint['barangay'] !==
    $secretaryBarangay
) {

    echo json_encode([
        'success' => false,
        'message' => 'You are not authorized to review this complaint.'
    ]);

    exit;
}


/* =========================
   MUST BE REJECTED
========================= */

if ($complaint['validation_status'] !== 'Rejected') {

    echo json_encode([
        'success' => false,
        'message' => 'This complaint is no longer eligible for appeal review.'
    ]);

    exit;
}


/* =========================
   MUST HAVE PENDING APPEAL
========================= */

if ($complaint['appeal_status'] !== 'Pending') {

    echo json_encode([
        'success' => false,
        'message' => 'There is no pending appeal for this complaint.'
    ]);

    exit;
}


/* =========================
   REVIEW NOTES
========================= */

if ($action === 'reject' && $reviewNotes === '') {

    echo json_encode([
        'success' => false,
        'message' => 'Please provide a reason for rejecting the appeal.'
    ]);

    exit;
}


/* =========================
   APPROVE APPEAL
========================= */
$newValidationStatus = 'Approved';
$newActionStatus = 'Pending Assignment';

?>