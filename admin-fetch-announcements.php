<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access"
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
   FETCH ANNOUNCEMENTS
========================= */
$sql = "SELECT
            id,
            title,
            message,
            audience,
            status,
            image_orientation,
            created_at,
            updated_at
        FROM announcements
        ORDER BY created_at DESC";

$result = $conn->query($sql);

$data = [];

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $announcementId = (int)$row['id'];

        /* =========================
           DEFAULT ORIENTATION
        ========================== */
        $row['orientation'] = $row['image_orientation'] ?: 'landscape';

        /* =========================
           FORMAT CREATED
        ========================== */
        $row['created'] = $row['created_at'];

        /* =========================
           FORMAT UPDATED
        ========================== */
        $row['updated'] = !empty($row['updated_at'])
            ? $row['updated_at']
            : "-";

        /* =========================
           FETCH IMAGES
        ========================== */
        $stmt = $conn->prepare("
            SELECT image_path
            FROM announcement_images
            WHERE announcement_id = ?
        ");

        $stmt->bind_param("i", $announcementId);
        $stmt->execute();

        $imgResult = $stmt->get_result();

        $images = [];

        while ($imgRow = $imgResult->fetch_assoc()) {
            $images[] = $imgRow['image_path'];
        }

        $stmt->close();

        $row['images'] = $images;

        $data[] = $row;
    }
}

echo json_encode($data);

$conn->close();