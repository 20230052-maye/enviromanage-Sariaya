<?php
session_start();
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

// AUTH CHECK
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
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
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

// INPUTS
$title = trim($_POST['title'] ?? '');
$message = trim($_POST['message'] ?? '');
$audience = trim($_POST['audience'] ?? 'all');
$status = trim($_POST['status'] ?? 'posted');
$image_orientation = trim($_POST['image_orientation'] ?? 'landscape');

// VALIDATE STATUS
$allowed_status = ['posted', 'draft'];
if (!in_array($status, $allowed_status)) {
    $status = 'posted';
}

$allowed_orientation = ['landscape', 'portrait'];

if (!in_array($image_orientation, $allowed_orientation)) {
    $image_orientation = 'landscape';
}

// VALIDATION
if ($title === '' || $message === '') {
    echo json_encode([
        "success" => false,
        "message" => "Title and message are required"
    ]);
    exit;
}

$conn->begin_transaction();

try {

    $created_at = date('Y-m-d H:i:s');

    // INSERT ANNOUNCEMENT
    $stmt = $conn->prepare("
    INSERT INTO announcements
    (title, message, audience, status, image_orientation, created_at)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssssss",
    $title,
    $message,
    $audience,
    $status,
    $image_orientation,
    $created_at
);

    if (!$stmt->execute()) {
        throw new Exception("Failed to save announcement");
    }

    $announcement_id = $stmt->insert_id;
    $stmt->close();

    // IMAGE UPLOAD DIR
    $uploadDir = "uploads/announcements/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // CHECK FILES SAFELY
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {

        $total = count($_FILES['images']['name']);

        $imgStmt = $conn->prepare("
            INSERT INTO announcement_images (announcement_id, image_path)
            VALUES (?, ?)
        ");

        for ($i = 0; $i < $total; $i++) {

            $tmpName = $_FILES['images']['tmp_name'][$i];
            $origName = $_FILES['images']['name'][$i];

            if (!$tmpName || !$origName) continue;

            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                continue;
            }

            $newName = uniqid("ann_", true) . "." . $ext;
            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $imgStmt->bind_param("is", $announcement_id, $targetPath);
                $imgStmt->execute();
            }
        }

        $imgStmt->close();
    }

    $conn->commit();

    echo json_encode([
        "success" => true,
        "message" => "Announcement saved successfully",
        "id" => $announcement_id,
        "status" => $status
    ]);

} catch (Exception $e) {

    $conn->rollback();

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}

$conn->close();
?>