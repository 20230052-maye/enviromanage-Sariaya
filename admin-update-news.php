<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

require_once "db.php";

$conn->begin_transaction();

try {

    $id = intval($_POST['id']);
    $title = $_POST['title'];
    $category = $_POST['category'];
    $content = $_POST['content'];
    $status = $_POST['status'];
    $now = date('Y-m-d H:i:s');

    /* ======================
       UPDATE NEWS
    ====================== */
    $stmt = $conn->prepare("
        UPDATE news
        SET title=?, category=?, content=?, status=?, updated_at=?
        WHERE id=?
    ");

    $stmt->bind_param("sssssi", $title, $category, $content, $status, $now, $id);

    if (!$stmt->execute()) {
        throw new Exception("News update failed");
    }


   /* ======================
   FRONTEND DATA
====================== */
$deleted = json_decode($_POST['deleted_images'] ?? '[]', true);

if (!is_array($deleted)) {
    $deleted = [];
}

/* ======================
   DELETE REMOVED IMAGES
====================== */
$delStmt = $conn->prepare("
    DELETE FROM news_images
    WHERE news_id = ? AND image_path = ?
");

foreach ($deleted as $img) {

    $imgPath = is_array($img)
        ? ($img['path'] ?? '')
        : $img;

    $imgPath = trim($imgPath);

    if (!$imgPath) {
        continue;
    }

    // Delete database record
    $delStmt->bind_param("is", $id, $imgPath);
    $delStmt->execute();

    // Delete physical file
    $filePath = __DIR__ . '/' . ltrim($imgPath, '/');

    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

$delStmt->close();

/* ======================
   PREPARE INSERT
====================== */
$ins = $conn->prepare("
    INSERT INTO news_images (news_id, image_path)
    VALUES (?, ?)
");

    /* ======================
       NEW IMAGE UPLOADS
    ====================== */
    if (!empty($_FILES['new_images']['name'][0])) {

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/news/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['new_images']['tmp_name'] as $i => $tmp) {

            if (!is_uploaded_file($tmp)) continue;

            $fileName = time() . "_" . basename($_FILES['new_images']['name'][$i]);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($tmp, $targetPath)) {

                $dbPath = "uploads/news/" . $fileName;

                $ins->bind_param("is", $id, $dbPath);
                $ins->execute();
            }
        }
    }

    $stmt->close();
$ins->close();

    $conn->commit();

    echo json_encode([
        "success" => true,
        "message" => "Updated successfully"
    ]);

} catch (Exception $e) {

    $conn->rollback();

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>