<?php
session_start();
date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized session"
    ]);
    exit;
}

require_once "db.php";

/* =========================
   SAFE QUERY (NO GROUP_CONCAT ISSUES)
========================= */
$sql = "
SELECT
    id,
    title,
    category,
    content,
    status,
    image_orientation,
    created_at,
    updated_at
FROM news
ORDER BY id DESC
";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);
    exit;
}

$data = [];

/* =========================
   FETCH IMAGES PER NEWS (SAFE)
========================= */
while ($row = $result->fetch_assoc()) {

    $news_id = $row['id'];

    $imgStmt = $conn->prepare("
        SELECT image_path 
        FROM news_images 
        WHERE news_id = ?
        ORDER BY id ASC
    ");

    $imgStmt->bind_param("i", $news_id);
    $imgStmt->execute();

    $imgResult = $imgStmt->get_result();

    $images = [];

    while ($img = $imgResult->fetch_assoc()) {
        $images[] = $img['image_path'];
    }

    $row['images'] = $images;

    $data[] = $row;
}

echo json_encode($data);
exit;
?>