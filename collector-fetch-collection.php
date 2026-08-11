<?php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

if ($conn->connect_error) {
    die(json_encode([
        "success"=>false,
        "message"=>"Database connection failed."
    ]));
}

$schedule_id = intval($_GET["schedule_id"] ?? 0);
$barangay    = $_GET["barangay"] ?? "";
$street      = $_GET["street"] ?? "";

$sql = "
SELECT *
FROM collection_progress
WHERE schedule_id=?
AND barangay=?
AND street=?
LIMIT 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode([
        "success" => false,
        "error" => $conn->error
    ]));
}

$stmt->bind_param(
    "iss",
    $schedule_id,
    $barangay,
    $street
);

$stmt->execute();

$result = $stmt->get_result();

if($row=$result->fetch_assoc()){

    echo json_encode([
        "success"=>true,
        "exists"=>true,
        "progress_id" => $row["id"],
        "record"=>$row
    ]);

}else{

    echo json_encode([
        "success"=>true,
        "exists"=>false
    ]);

}