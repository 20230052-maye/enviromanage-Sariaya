<?php
header('Content-Type: application/json');

$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB Connection failed']);
    exit;
}

/* =========================
   ID
========================= */
$id = intval($_POST['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

/* =========================
   TEXT DATA
========================= */
$first     = trim($_POST['first'] ?? '');
$last      = trim($_POST['last'] ?? '');
$mi        = strtoupper(trim($_POST['mi'] ?? ''));
$email     = trim($_POST['email'] ?? '');
$role      = trim($_POST['role'] ?? '');
$phone     = trim($_POST['phone'] ?? '');

$gender    = trim($_POST['gender'] ?? '');
$birthdate = trim($_POST['birthdate'] ?? '');

$street    = trim($_POST['street'] ?? '');   // ✅ FIXED (was house_street)
$house_no  = trim($_POST['house_no'] ?? '');
$barangay  = trim($_POST['barangay'] ?? '');
$postal    = trim($_POST['postal_code'] ?? '');

/* =========================
   ROLE VALIDATION
========================= */
$allowedRoles = ['admin', 'collector', 'barangay_secretary'];

if (!in_array($role, $allowedRoles)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid role'
    ]);
    exit;
}

/* =========================
   REQUIRED FIELDS CHECK
========================= */
if (
    empty($first) ||
    empty($last) ||
    empty($email) ||
    empty($role) ||
    empty($phone) ||
    empty($gender) ||
    empty($birthdate) ||
    empty($street) ||
    empty($house_no) ||
    empty($barangay) ||
    empty($postal)
) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

/* =========================
   GET CURRENT PHOTO
========================= */
$currentPhoto = null;

$stmt = $conn->prepare("SELECT profile_photo FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($currentPhoto);
$stmt->fetch();
$stmt->close();

/* =========================
   PHOTO UPLOAD (OPTIONAL)
========================= */
$newPhotoPath = $currentPhoto;

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {

    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

    $mime = mime_content_type($_FILES['photo']['tmp_name']);

    if (!in_array($mime, $allowedTypes)) {
        echo json_encode([
            'success' => false,
            'message' => 'Only JPG, JPEG, and PNG are allowed'
        ]);
        exit;
    }

    if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
        echo json_encode([
            'success' => false,
            'message' => 'Image must not exceed 5MB'
        ]);
        exit;
    }

    $uploadDir = "uploads/profile_photos/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

    $newFileName = 'user_' . time() . '_' . uniqid() . '.' . $extension;

    $targetFile = $uploadDir . $newFileName;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {

        $newPhotoPath = $targetFile;

        // delete old photo (safe check)
        if (!empty($currentPhoto) && file_exists($currentPhoto)) {
            unlink($currentPhoto);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
        exit;
    }
}

/* =========================
   UPDATE USER
========================= */
$stmt = $conn->prepare("
    UPDATE users 
    SET first_name=?,
        middle_initial=?,
        last_name=?,
        email=?,
        role=?,
        phone=?,
        gender=?,
        birthdate=?,
        street=?,
        house_no=?,
        barangay=?,
        postal_code=?,
        profile_photo=?
    WHERE id=?
");

$stmt->bind_param(
    "sssssssssssssi",
    $first,
    $mi,
    $last,
    $email,
    $role,
    $phone,
    $gender,
    $birthdate,
    $street,
    $house_no,
    $barangay,
    $postal,
    $newPhotoPath,
    $id
);

/* =========================
   EXECUTE
========================= */
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>