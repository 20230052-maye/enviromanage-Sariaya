<?php
session_start();

header('Content-Type: application/json');

// =========================
// ADMIN ONLY
// =========================
if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'admin'
) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access."
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
        "message" => "Database connection failed."
    ]);
    exit;
}

// =========================
// READ REQUEST
// =========================
$input = json_decode(file_get_contents("php://input"), true);

$newLimit = isset($input["max_schedule_hours"])
    ? (int)$input["max_schedule_hours"]
    : 0;

if ($newLimit < 1 || $newLimit > 24) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid maximum duration."
    ]);

    exit;
}

// =========================
// FETCH SCHEDULES
// =========================
$sql = "
SELECT
    s.id,
    s.barangay,
    s.day_of_week,
    s.start_time,
    s.end_time,
    s.garbage_type,
    t.plate_no AS truck
FROM schedules s
LEFT JOIN trucks t
ON s.truck_id=t.id
ORDER BY
s.day_of_week,
s.start_time
";

$result = $conn->query($sql);

$affected = [];

$totalReduced = 0;

// =========================
// CHECK EACH SCHEDULE
// =========================
while($row = $result->fetch_assoc()){

    $start = strtotime($row["start_time"]);
    $end   = strtotime($row["end_time"]);

    $duration =
        ($end-$start)/3600;

    if($duration <= $newLimit){
        continue;
    }

    $newEnd =
        date(
            "H:i:s",
            strtotime(
                $row["start_time"] .
                " +{$newLimit} hours"
            )
        );

    $reduced =
        $duration-$newLimit;

    $totalReduced +=
        $reduced;

    $affected[] = [

        "id"=>$row["id"],

        "barangay"=>$row["barangay"],

        "day_of_week"=>$row["day_of_week"],

        "truck"=>$row["truck"],

        "start_time"=>substr(
            $row["start_time"],
            0,
            5
        ),

        "old_end_time"=>substr(
            $row["end_time"],
            0,
            5
        ),

        "new_end_time"=>substr(
            $newEnd,
            0,
            5
        ),

        "old_duration"=>$duration,

        "new_duration"=>$newLimit,

        "hours_reduced"=>$reduced

    ];

}

// =========================
// RESPONSE
// =========================
echo json_encode([

    "success"=>true,

    "new_limit"=>$newLimit,

    "affected_count"=>count($affected),

    "total_hours_reduced"=>$totalReduced,

    "schedules"=>$affected

]);

$conn->close();