<?php
session_start();

header('Content-Type: application/json');

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'barangay_secretary'
) {
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

$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);
    exit;
}

$secretaryId = (int)$_SESSION['user_id'];

$search = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$where = "
WHERE a.status='posted'
AND a.audience IN ('all','Barangay Secretary')
AND d.id IS NULL
";

$params = [$secretaryId];
$types = "i";

if ($search !== "") {

    $where .= "
    AND (
        a.title LIKE ?
        OR a.message LIKE ?
    )";

    $keyword = "%{$search}%";

    $params[] = $keyword;
    $params[] = $keyword;

    $types .= "ss";
}

$countSql = "
SELECT COUNT(*) AS total
FROM announcements a

LEFT JOIN secretary_deleted_announcements d
ON a.id = d.announcement_id
AND d.secretary_id = ?

{$where}
";

$stmt = $conn->prepare($countSql);
$stmt->bind_param($types, ...$params);

$stmt->execute();

$total = $stmt->get_result()->fetch_assoc()['total'];

$stmt->close();

$sql = "
SELECT
a.id,
a.title,
a.message,
a.created_at,
GROUP_CONCAT(ai.image_path) AS announcement_images

FROM announcements a

LEFT JOIN announcement_images ai
ON a.id = ai.announcement_id

LEFT JOIN secretary_deleted_announcements d
ON a.id = d.announcement_id
AND d.secretary_id = ?

{$where}

GROUP BY a.id

ORDER BY a.created_at DESC

LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);

$params2 = $params;
$types2 = $types . "ii";

$params2[] = $limit;
$params2[] = $offset;

$stmt->bind_param($types2, ...$params2);

$stmt->execute();

$result = $stmt->get_result();

$rows = [];

while ($row = $result->fetch_assoc()) {

    $row['announcement_images'] = 
        $row['announcement_images']
        ? explode(",", $row['announcement_images'])
        : [];

    $rows[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode([
    "success" => true,
    "total" => (int)$total,
    "page" => $page,
    "limit" => $limit,
    "rows" => $rows
]);