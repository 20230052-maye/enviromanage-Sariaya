<?php
session_start();
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
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
        "message" => "Database connection failed"
    ]);
    exit;
}

/* =========================
   READ FORM DATA
========================= */
$id = $_POST['id'] ?? null;
$title = trim($_POST['title'] ?? '');
$message = trim($_POST['message'] ?? '');
$audience = trim($_POST['audience'] ?? 'all');
$status = trim($_POST['status'] ?? 'posted');
$orientation = trim($_POST['orientation'] ?? 'landscape');

$allowed_orientation = ['landscape', 'portrait'];

if (!in_array($orientation, $allowed_orientation)) {
    $orientation = 'landscape';
}

if (!$id || $title === '' || $message === '') {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields"
    ]);
    exit;
}

/* =========================
   PHP TIME (FIXED)
========================= */
$updated_at = date('Y-m-d H:i:s');

/* =========================
   UPDATE ANNOUNCEMENT
========================= */
$stmt = $conn->prepare("
    UPDATE announcements
    SET title = ?,
        message = ?,
        audience = ?,
        status = ?,
        image_orientation = ?,
        updated_at = ?
    WHERE id = ?
");

$stmt->bind_param(
    "ssssssi",
    $title,
    $message,
    $audience,
    $status,
    $image_orientation,
    $updated_at,
    $id
);

if (!$stmt->execute()) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to update announcement"
    ]);
    exit;
}

$stmt->close();

/* =========================
   IMAGE SYNC (FULL FIX)
========================= */

// 1. Get existing images from frontend
$existing_images = $_POST['existing_images'] ?? [];

if (!is_array($existing_images)) {
    $existing_images = [$existing_images];
}


// 2. DELETE OLD IMAGE RECORDS
$deleteStmt = $conn->prepare("
    DELETE FROM announcement_images
    WHERE announcement_id = ?
");

$deleteStmt->bind_param("i", $id);
$deleteStmt->execute();
$deleteStmt->close();


// 3. RE-INSERT KEPT EXISTING IMAGES
if (count($existing_images) > 0) {

    $reinsertStmt = $conn->prepare("
        INSERT INTO announcement_images (announcement_id, image_path)
        VALUES (?, ?)
    ");

    foreach ($existing_images as $imgPath) {

        if (empty($imgPath)) continue;

        $reinsertStmt->bind_param(
            "is",
            $id,
            $imgPath
        );

        $reinsertStmt->execute();
    }

    $reinsertStmt->close();
}

// 4. HANDLE NEW UPLOADED IMAGES
$uploadDir = "uploads/announcements/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!empty($_FILES['new_images']['name'][0])) {

    $imgStmt = $conn->prepare("
        INSERT INTO announcement_images (announcement_id, image_path)
        VALUES (?, ?)
    ");

    $total = count($_FILES['new_images']['name']);

    for ($i = 0; $i < $total; $i++) {

        $tmp = $_FILES['new_images']['tmp_name'][$i];
        $name = $_FILES['new_images']['name'][$i];
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg','jpeg','png','webp'])) continue;

        $newName = uniqid("ann_", true) . "." . $ext;
        $path = $uploadDir . $newName;

        if (move_uploaded_file($tmp, $path)) {
            $imgStmt->bind_param("is", $id, $path);
            $imgStmt->execute();
        }
    }

    $imgStmt->close();
}

echo json_encode([
    "success" => true,
    "message" => "Announcement updated successfully"
]);

$conn->close();
?>