<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

$host = "localhost";
$user = "u820562602_fleurscents";
$pass = "Aa2RmDG?Pe0";
$db   = "u820562602_fleurscents_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'DB Connection failed'
    ]);
    exit;
}

// ===============================
// RECEIVE DATA (MATCH UI)
// ===============================

$first    = trim($_POST['first'] ?? '');
$last     = trim($_POST['last'] ?? '');
$mi       = strtoupper(trim($_POST['mi'] ?? ''));
$email    = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role = trim($_POST['role'] ?? '');
$phone    = trim($_POST['phone'] ?? '');

$gender      = trim($_POST['gender'] ?? '');
$birthdate   = trim($_POST['birthdate'] ?? '');
$street      = trim($_POST['street'] ?? '');      // ✅ FIXED
$house_no    = trim($_POST['house_no'] ?? '');    // ✅ ADDED
$barangay    = trim($_POST['barangay'] ?? '');
$postal_code = trim($_POST['postal_code'] ?? '');

// ===============================
// VALIDATION
// ===============================

if (
    empty($first) ||
    empty($last) ||
    empty($email) ||
    empty($username) ||
    empty($password) ||
    empty($role) ||
    empty($phone) ||
    empty($gender) ||
    empty($birthdate) ||
    empty($street) ||
    empty($house_no) ||
    empty($barangay) ||
    empty($postal_code)
) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email address'
    ]);
    exit;
}

// ✅ MATCH DB ENUM (includes resident)
if (!in_array($role, ['admin', 'collector', 'barangay_secretary'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid role'
    ]);
    exit;
}

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Weak password'
    ]);
    exit;
}

if (!preg_match('/^9\d{9}$/', $phone)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid phone number'
    ]);
    exit;
}

// ===============================
// DUPLICATE CHECK
// ===============================

$stmt = $conn->prepare("
    SELECT id FROM users
    WHERE email = ? OR username = ? OR phone = ?
    LIMIT 1
");

$stmt->bind_param("sss", $email, $username, $phone);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'User already exists'
    ]);
    exit;
}
$stmt->close();

// ===============================
// PHOTO UPLOAD
// ===============================

$photoPath = null;

if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === 0) {

    $allowed = ['image/jpeg', 'image/jpg', 'image/png'];
    $mime = mime_content_type($_FILES['photo']['tmp_name']);

    if (!in_array($mime, $allowed)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid image type'
        ]);
        exit;
    }

    if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
        echo json_encode([
            'success' => false,
            'message' => 'Image too large'
        ]);
        exit;
    }

    $dir = "uploads/profile_photos/";
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

    $file = 'user_' . time() . '_' . uniqid() . '.' . $ext;
    $path = $dir . $file;

    move_uploaded_file($_FILES['photo']['tmp_name'], $path);
    $photoPath = $path;
}

// ===============================
// PASSWORD HASH
// ===============================

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// ===============================
// INSERT (MATCH YOUR TABLE EXACTLY)
// ===============================

$stmt = $conn->prepare("
    INSERT INTO users (
        first_name,
        middle_initial,
        last_name,
        email,
        username,
        password,
        role,
        phone,
        gender,
        birthdate,
        street,
        house_no,
        barangay,
        postal_code,
        profile_photo,
        is_logged_in
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)
");

$stmt->bind_param(
    "sssssssssssssss",
    $first,
    $mi,
    $last,
    $email,
    $username,
    $hashedPassword,
    $role,
    $phone,
    $gender,
    $birthdate,
    $street,
    $house_no,
    $barangay,
    $postal_code,
    $photoPath
);

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