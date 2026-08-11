<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set("Asia/Manila");


/* ==========================================
   AUTHENTICATION
========================================== */

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== "barangay_secretary"
) {

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized."
    ]);

    exit;
}


/* ==========================================
   DATABASE
========================================== */

$conn = new mysqli(
    "localhost",
    "u823857209_enviromanage",
    "Enviromanage4322",
    "u823857209_enviromanage"
);

$conn->set_charset("utf8mb4");


try {

    $id = intval($_POST["id"] ?? 0);

    $remarks = trim(
        $_POST["remarks"] ?? ""
    );

    $secretary = intval(
        $_SESSION["user_id"]
    );


    /* ==========================================
       VALIDATION
    ========================================== */

    if ($id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid complaint."
        ]);

        exit;
    }


    if ($remarks === "") {

        echo json_encode([
            "success" => false,
            "message" => "Secretary remarks are required."
        ]);

        exit;
    }


    /* ==========================================
       RETURN COMPLAINT
    ========================================== */

    $stmt = $conn->prepare("
        UPDATE resident_complaints

        SET
            validation_status = 'Rejected',
            action_status = 'Returned',
            remarks = ?,
            reviewed_by = ?,
            reviewed_at = NOW()

        WHERE id = ?
        AND validation_status = 'Under Review'
    ");

    $stmt->bind_param(
        "sii",
        $remarks,
        $secretary,
        $id
    );

    $stmt->execute();


    /* ==========================================
       CHECK RESULT
    ========================================== */

    if ($stmt->affected_rows === 0) {

        $stmt->close();
        $conn->close();

        echo json_encode([
            "success" => false,
            "message" => "Complaint cannot be returned or has already been processed."
        ]);

        exit;
    }


    $stmt->close();
    $conn->close();


    /* ==========================================
       SUCCESS
    ========================================== */

    echo json_encode([
        "success" => true,
        "message" => "Complaint has been returned to the resident."
    ]);

} catch (Throwable $e) {

    error_log(
        "barangay-secretary-return-complaint.php ERROR: " .
        $e->getMessage()
    );

    if ($conn instanceof mysqli) {
        $conn->close();
    }

    echo json_encode([
        "success" => false,
        "message" => "Unable to return complaint."
    ]);

}
?>