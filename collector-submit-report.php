<?php
session_start();
date_default_timezone_set('Asia/Manila');

header("Content-Type: application/json");

// DB CONNECTION
$conn = new mysqli(
    "localhost",
    "u823857209_enviromanage",
    "Enviromanage4322",
    "u823857209_enviromanage"
);
$conn->query("SET time_zone = '+08:00'");

if ($conn->connect_error) {
    die(json_encode([
        "success"=>false,
        "message"=>"Database connection failed."
    ]));
}

$progress_id = intval($_POST["progress_id"]);
$schedule_id = intval($_POST["schedule_id"]);
$barangay = $_POST["barangay"];
$street = $_POST["street"];
$issue_type = $_POST["issue_type"];
$description = $_POST["description"];
$reported_by = $_SESSION["user_id"];

$stmt = $conn->prepare("
INSERT INTO collection_reports
(
progress_id,
schedule_id,
barangay,
street,
issue_type,
description,
reported_by
)
VALUES (?,?,?,?,?,?,?)
");

$stmt->bind_param(
    "iissssi",
    $progress_id,
    $schedule_id,
    $barangay,
    $street,
    $issue_type,
    $description,
    $reported_by
);

$stmt->execute();

echo json_encode([
    "success"=>true
]);