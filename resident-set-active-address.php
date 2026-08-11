<?php
session_start();

header("Content-Type: application/json");

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'resident'
){
    echo json_encode([
        "success" => false
    ]);
    exit;
}

$_SESSION['selected_address_id'] =
    isset($_POST['address_id'])
    ? (int)$_POST['address_id']
    : 0;

echo json_encode([
    "success" => true
]);