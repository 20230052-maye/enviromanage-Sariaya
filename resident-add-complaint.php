<?php
session_start();

header("Content-Type: application/json");

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== "resident"
) {
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized."
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
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed."
    ]);
    exit;
}

$resident_id = $_SESSION['user_id'];

$category = trim($_POST['category'] ?? "");
$location = trim($_POST['location'] ?? "");
$description = trim($_POST['description'] ?? "");

if ($category === "" || $location === "" || $description === "") {
    echo json_encode([
        "status" => "error",
        "message" => "Please complete all required fields."
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Generate Complaint Code
|--------------------------------------------------------------------------
*/

do {

    $complaint_code = "CMP-" . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

    $check = $conn->prepare("
        SELECT id
        FROM complaints
        WHERE complaint_code = ?
    ");

    $check->bind_param("s", $complaint_code);
    $check->execute();
    $check->store_result();

} while ($check->num_rows > 0);

$check->close();

/*
|--------------------------------------------------------------------------
| Get Barangay and Address
|--------------------------------------------------------------------------
*/

$barangay = "";

$parts = array_map("trim", explode(",", $location));

if (count($parts) > 0) {
    $barangay = end($parts);
}

$address = $location;

/*
|--------------------------------------------------------------------------
| Upload Photo
|--------------------------------------------------------------------------
*/

$photoPath = null;

if (
    isset($_FILES['images']) &&
    !empty($_FILES['images']['name'][0])
) {

    $uploadFolder = "uploads/complaints/";

    if (!is_dir($uploadFolder)) {
        mkdir($uploadFolder, 0777, true);
    }

    $extension = pathinfo(
        $_FILES['images']['name'][0],
        PATHINFO_EXTENSION
    );

    $fileName = uniqid("complaint_") . "." . $extension;

    $photoPath = $uploadFolder . $fileName;

    move_uploaded_file(
        $_FILES['images']['tmp_name'][0],
        $photoPath
    );
}

/*
|--------------------------------------------------------------------------
| Save Complaint
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
INSERT INTO resident_complaints
(
ticket_no,
queue_no,
resident_id,
complaint_location,
description,
validation_status,
action_status,
submitted_at
)
VALUES
(
?,
?,
?,
?,
?,
'Waiting',
'Pending Assignment',
NOW()
)
");
$stmt->bind_param(
    "sisssss",
    $complaint_code,
    $resident_id,
    $category,
    $description,
    $barangay,
    $address,
    $photoPath
);

if ($stmt->execute()) {

    echo json_encode([
        "status" => "success",
        "message" => "Complaint submitted successfully.",
        "complaint_code" => $complaint_code
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Failed to save complaint."
    ]);

}

$stmt->close();
$conn->close();
?>