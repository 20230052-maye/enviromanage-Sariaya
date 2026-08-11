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
       GET IMAGE PATHS
    ========================= */
    $stmt = $conn->prepare("SELECT image_path FROM news_images WHERE news_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $images = [];

    while ($row = $result->fetch_assoc()) {
        if (!empty($row['image_path'])) {
            $images[] = $row['image_path'];
        }
    }

    $stmt->close();

    /* =========================
       DELETE IMAGE RECORDS
    ========================= */
    $delImg = $conn->prepare("DELETE FROM news_images WHERE news_id = ?");
    $delImg->bind_param("i", $id);
    $delImg->execute();
    $delImg->close();

    /* =========================
       DELETE NEWS ROW
    ========================= */
    $delNews = $conn->prepare("DELETE FROM news WHERE id = ?");
    $delNews->bind_param("i", $id);

    if (!$delNews->execute()) {
        echo json_encode([
            "success" => false,
            "message" => "Failed to delete news"
        ]);
        exit;
    }

    $delNews->close();

    /* =========================
       DELETE FILES (HOSTINGER FIX)
       DB path: uploads/news/file.jpg
       REAL PATH: public_html/uploads/news/file.jpg
    ========================= */
    foreach ($images as $imgPath) {

        // clean path (remove leading slash if any)
        $cleanPath = ltrim($imgPath, "/\\");

        // IMPORTANT HOSTINGER FIX
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/" . $cleanPath;

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

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$conn->close();
?>