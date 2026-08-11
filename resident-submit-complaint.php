<?php
session_start();
header("Content-Type: application/json");

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== "resident"
) {
    echo json_encode([
        "status" => "error",
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
        "status" => "error",
        "message" => "Database connection failed."
    ]);
    exit;
}

$residentID = $_SESSION['user_id'];

$location = trim($_POST['location'] ?? '');
$category = trim($_POST['category'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($location == "" || $category == "" || $description == "") {
    echo json_encode([
        "status" => "error",
        "message" => "Please complete all required fields."
    ]);
    exit;
}
/* ILAGAY MO DITO */
date_default_timezone_set("Asia/Manila");
$submittedAt = date("Y-m-d H:i:s");
/* Generate Queue Number */

$result = $conn->query("
SELECT IFNULL(MAX(queue_no),0)+1 AS nextQueue
FROM resident_complaints
");

$queue = $result->fetch_assoc()['nextQueue'];

/* Generate Ticket Number */

$ticket = "RC-" . str_pad($queue,5,"0",STR_PAD_LEFT);
$stmtUser = $conn->prepare("
    SELECT barangay
    FROM users
    WHERE id = ?
");

$stmtUser->bind_param("i", $residentID);
$stmtUser->execute();

$user = $stmtUser->get_result()->fetch_assoc();

$barangay = $user['barangay'];
$stmt = $conn->prepare("

INSERT INTO resident_complaints
(
    ticket_no,
    queue_no,
    resident_id,
    complaint_location,
    category,
    description,
    validation_status,
    action_status,
    submitted_at
)
VALUES
(
    ?, ?, ?, ?, ?, ?, 'Waiting', 'Pending Assignment', ?
)
");

$stmt->bind_param(
    "siissss",
    $ticket,
    $queue,
    $residentID,
    $location,
    $category,
    $description,
    $submittedAt
);

if($stmt->execute()){

    echo json_encode([
        
        "status"=>"success",
        "ticket_no"=>$ticket
        
    ]);
$complaintID = $stmt->insert_id;

if(isset($_FILES["images"])){

    $uploadDir = "uploads/complaints/";

    foreach($_FILES["images"]["tmp_name"] as $key=>$tmpName){

        if($_FILES["images"]["error"][$key] != 0){
            continue;
        }

        $extension = pathinfo(
            $_FILES["images"]["name"][$key],
            PATHINFO_EXTENSION
        );

        $filename = uniqid("CMP_").".".$extension;

        move_uploaded_file(
            $tmpName,
            $uploadDir.$filename
        );

        $photo = $conn->prepare("
            INSERT INTO resident_complaint_photos
            (complaint_id,photo)
            VALUES(?,?)
        ");

        $photo->bind_param(
            "is",
            $complaintID,
            $filename
        );

        $photo->execute();
    }

}
}else{

    echo json_encode([
        "status"=>"error",
        "message"=>$stmt->error
    ]);

}