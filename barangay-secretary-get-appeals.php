<?php

session_start();

header("Content-Type: application/json");

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== "barangay_secretary"
) {

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized."
    ]);

    exit;
}

require_once "db.php";

$conn->set_charset("utf8mb4");


$sql = "
    SELECT
        a.id,
        a.complaint_id,
        a.resident_id,
        a.appeal_reason,
        a.status,
        a.secretary_remarks,
        a.submitted_at,

        c.ticket_no,
        c.queue_no,
        c.complaint_location,
        c.category,

        u.first_name,
        u.last_name,
        u.email,
        u.phone

    FROM complaint_appeals a

    INNER JOIN resident_complaints c
        ON c.id = a.complaint_id

    INNER JOIN users u
        ON u.id = a.resident_id

    ORDER BY a.submitted_at DESC
";


$result = $conn->query($sql);


if (!$result) {

    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $conn->error
    ]);

    exit;
}


$appeals = [];


while ($row = $result->fetch_assoc()) {

    $appeals[] = $row;

}


echo json_encode([
    "success" => true,
    "appeals" => $appeals
]);


$conn->close();

?>