<?php
session_start();


header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
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
    die(json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]));
}

$sql = "
SELECT
    cp.id,
    cp.collection_date,
    cp.status,
    cp.created_at,
    cp.updated_at,
    cp.barangay,
    cp.street,
    t.plate_no AS truck,
    CONCAT(
        u.first_name,
        ' ',
        IFNULL(CONCAT(u.middle_initial, '. '), ''),
        u.last_name
    ) AS collector,
    cr.issue_type,
cr.description AS issue_description,
cr.reported_at AS issue_date
    

FROM collection_progress cp

INNER JOIN schedules s
    ON cp.schedule_id = s.id

LEFT JOIN trucks t
    ON s.truck_id = t.id

LEFT JOIN users u
    ON t.collector_id = u.id

LEFT JOIN collection_reports cr
    ON cp.id = cr.progress_id

ORDER BY
    cp.collection_date DESC,
    cp.created_at DESC
";

$result = $conn->query($sql);

$records = [];

while ($row = $result->fetch_assoc()) {

    $records[] = [
        "id" => $row["id"],
        "date" => !empty($row["collection_date"])
            ? date("F j, Y", strtotime($row["collection_date"]))
            : "-",
        "barangay" => $row["barangay"],
        "street" => $row["street"],
       "truck" => !empty($row["truck"]) ? $row["truck"] : "-",
        "collector" => !empty($row["collector"]) ? $row["collector"] : "-",
        "status" => !empty($row["status"]) ? $row["status"] : "Pending",
       "issue_type" => !empty($row["issue_type"])
    ? $row["issue_type"]
    : "",

"issue_description" => !empty($row["issue_description"])
    ? $row["issue_description"]
    : "",

"issue_date" => !empty($row["issue_date"])
    ? gmdate(
        "F j, Y g:i A",
        strtotime($row["issue_date"]) + (8 * 3600)
      )
    : "",
        "last_updated" => !empty($row["updated_at"])
    ? date("F j, Y g:i A", strtotime($row["updated_at"]))
    : (
        !empty($row["created_at"])
            ? date("F j, Y g:i A", strtotime($row["created_at"]))
            : "-"
      )
    ];

}

echo json_encode([
    "success" => true,
    "records" => $records
]);

$conn->close();
?>