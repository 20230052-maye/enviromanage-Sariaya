<?php
session_start();
date_default_timezone_set('Asia/Manila');
require_once "db.php";

header('Content-Type: application/json');

// Validate required inputs
if (
    empty($_POST['title']) ||
    empty($_POST['category']) ||
    empty($_POST['content']) ||
    empty($_POST['status'])
) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields"
    ]);
    exit;
}

$title = $_POST['title'];
$category = $_POST['category'];
$content = $_POST['content'];
$status = $_POST['status'];
$image_orientation = $_POST['image_orientation'] ?? 'landscape';

// Insert news
$stmt = $conn->prepare("
    INSERT INTO news (
        title,
        category,
        content,
        status,
        image_orientation,
        created_at,
        updated_at
    )
    VALUES (?, ?, ?, ?, ?, NOW(), NOW())
");

$stmt->bind_param(
    "sssss",
    $title,
    $category,
    $content,
    $status,
    $image_orientation
);

if (!$stmt->execute()) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to insert news"
    ]);
    exit;
}

$news_id = $stmt->insert_id;

// Upload directory
$uploadDir = "uploads/news/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Upload images (MULTIPLE)
if (!empty($_FILES['images']['tmp_name'][0])) {

    foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {

        if (empty($tmpName)) continue;

        // Optional: validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($_FILES['images']['type'][$key], $allowedTypes)) {
            continue;
        }

        // Safe unique filename
        $filename = uniqid() . '_' . basename($_FILES['images']['name'][$key]);
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($tmpName, $targetPath)) {

            $stmtImage = $conn->prepare("
                INSERT INTO news_images (news_id, image_path)
                VALUES (?, ?)
            ");

            $stmtImage->bind_param("is", $news_id, $targetPath);
            $stmtImage->execute();
        }
    }
}

// Success response
echo json_encode([
    "success" => true,
    "news_id" => $news_id
]);