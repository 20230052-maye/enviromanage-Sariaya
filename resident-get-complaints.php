<?php
session_start();
header("Content-Type: application/json");
date_default_timezone_set('Asia/Manila');
if(
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] != "resident"
){
    echo json_encode([
        "success"=>false
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
$conn->set_charset("utf8mb4");

$resident = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT
    id,
    ticket_no,
    complaint_location,
    category,
    description,
    validation_status,
    action_status,
    submitted_at
FROM resident_complaints
WHERE resident_id=?
ORDER BY submitted_at DESC
");

$stmt->bind_param("i",$resident);
$stmt->execute();

$result=$stmt->get_result();

$data=[];

while($row = $result->fetch_assoc()){

    $row["submitted_at"] = date(
        "F j, Y | g:i A",
        strtotime($row["submitted_at"])
    );

    $data[] = $row;
}
echo json_encode([
    "success" => true,
    "complaints" => $data
]);