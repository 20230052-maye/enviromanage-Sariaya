<?php
session_start();

header('Content-Type: application/json');

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'collector'
) {
    echo json_encode([
        "success" => false,
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

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);
    exit;
}

if (
    !isset($_FILES["photo"]) ||
    $_FILES["photo"]["error"] !== UPLOAD_ERR_OK
) {
    echo json_encode([
        "success" => false,
        "message" => "No image uploaded."
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

$allowed = [
    "jpg",
    "jpeg",
    "png",
    "gif",
    "webp"
];

$extension = strtolower(
    pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION)
);

if (!in_array($extension, $allowed)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid image type."
    ]);
    exit;
}

// Create uploads folder if it doesn't exist
$uploadDir = __DIR__ . "/uploads/profile_photos/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}


// Generate unique filename
$fileName = "collector_" . $user_id . "_" . time() . "." . $extension;
$targetFile = $uploadDir . $fileName;
if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to save image."
    ]);
    exit;
}

// Save to database
$stmt = $conn->prepare("
    UPDATE users
    SET profile_photo=?
    WHERE id=?
");

$dbPath = "uploads/profile_photos/" . $fileName;

$stmt->bind_param("si", $dbPath, $user_id);
if (!$stmt->execute()) {

    echo json_encode([
        "success" => false,
        "message" => "Database update failed."
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Your profile is updated successfully!",
    "path" => $dbPath
]);