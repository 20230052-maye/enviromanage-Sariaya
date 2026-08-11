<?php
session_start();

header('Content-Type: application/json');

if(
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'resident'
){
    echo json_encode([
        "success"=>false
    ]);
    exit;
}

$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

$house_no = trim($_POST['house_no']);
$street = trim($_POST['street']);
$barangay = trim($_POST['barangay']);
$postal_code = trim($_POST['postal_code']);
$is_default = isset($_POST['is_default']) ? 1 : 0;

if($is_default){

    $stmt = $conn->prepare("
        UPDATE resident_addresses
        SET is_default = 0
        WHERE resident_id = ?
    ");

    $stmt->bind_param("i",$_SESSION['user_id']);
    $stmt->execute();
}

$stmt = $conn->prepare("
    INSERT INTO resident_addresses
    (
        resident_id,
        house_no,
        street,
        barangay,
        postal_code,
        is_default
    )
    VALUES
    (?,?,?,?,?,?)
");

$stmt->bind_param(
    "issssi",
    $_SESSION['user_id'],
    $house_no,
    $street,
    $barangay,
    $postal_code,
    $is_default
);

$success = $stmt->execute();

echo json_encode([
    "success"=>$success
]);