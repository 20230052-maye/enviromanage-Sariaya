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

// =========================
// DATABASE
// =========================
$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

if ($conn->connect_error) {

    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);

    exit;
}

$conn->begin_transaction();

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

try{

    // =========================
    // SAVE NEW LIMIT
    // =========================

    $stmt = $conn->prepare("
        UPDATE system_settings
        SET setting_value=?
        WHERE setting_key='max_schedule_hours'
    ");

    $value = (string)$newLimit;

    $stmt->bind_param("s",$value);

    $stmt->execute();

    $stmt->close();

    // =========================
    // FETCH ALL SCHEDULES
    // =========================

    $result = $conn->query("
        SELECT
            id,
            start_time,
            end_time
        FROM schedules
    ");

    $updated = 0;

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

        $update = $conn->prepare("
            UPDATE schedules
            SET end_time=?
            WHERE id=?
        ");

        $update->bind_param(
            "si",
            $newEnd,
            $row["id"]
        );

        $update->execute();

        $update->close();

        $updated++;

    }

    $conn->commit();

    echo json_encode([

        "success"=>true,

        "updated"=>$updated,

        "message"=>"$updated schedules updated successfully."

    ]);

}
catch(Exception $e){

    $conn->rollback();

    echo json_encode([

        "success"=>false,

        "message"=>$e->getMessage()

    ]);

}

$conn->close();