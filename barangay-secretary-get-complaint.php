<?php
session_start();

header("Content-Type: application/json");

if(
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] != "barangay_secretary"
){
    echo json_encode([
        "success"=>false,
        "message"=>"Unauthorized"
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


$id = intval($_GET["id"] ?? 0);


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


$stmt->bind_param(
"i",
$id
);


$stmt->execute();


$result = $stmt->get_result();



if($result->num_rows == 0){

    echo json_encode([
        "success"=>false,
        "message"=>"Complaint not found."
    ]);

    exit;

}



$row = $result->fetch_assoc();
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

while($photo = $photoResult->fetch_assoc()){

    $photos[] = "uploads/complaints/" . $photo["photo"];

}

$row["photos"] = $photos;


echo json_encode([
    "success" => true,
    "complaint" => $row
]);