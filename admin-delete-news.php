<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);
    exit;
}

require_once "db.php";

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['id']) || empty($input['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid ID"
    ]);
    exit;
}

$id = intval($input['id']);

try {

    /* =========================
       1. GET IMAGE PATHS FIRST
    ========================= */
    $imgStmt = $conn->prepare("SELECT image_path FROM news_images WHERE news_id = ?");
    $imgStmt->bind_param("i", $id);
    $imgStmt->execute();
    $imgResult = $imgStmt->get_result();

    $images = [];

    while ($row = $imgResult->fetch_assoc()) {
        if (!empty($row['image_path'])) {
            $images[] = $row['image_path'];
        }
    }

    $imgStmt->close();

    /* =========================
       2. DELETE IMAGE RECORDS
    ========================= */
    $delImg = $conn->prepare("DELETE FROM news_images WHERE news_id = ?");
    $delImg->bind_param("i", $id);
    $delImg->execute();
    $delImg->close();

    /* =========================
       3. DELETE NEWS ROW
    ========================= */
    $delNews = $conn->prepare("DELETE FROM news WHERE id = ?");
    $delNews->bind_param("i", $id);

    if ($delNews->execute()) {

        /* =========================
           4. DELETE FILES (HOSTINGER SAFE)
        ========================= */
        foreach ($images as $imgPath) {

            $cleanPath = ltrim($imgPath, '/\\');

            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $cleanPath;

            if (file_exists($filePath)) {
                unlink($filePath);
            } else {
                error_log("File not found: " . $filePath);
            }
        }

        echo json_encode([
            "success" => true,
            "message" => "News deleted successfully"
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to delete news"
        ]);
    }

    $delNews->close();

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$conn->close();
?>