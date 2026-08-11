<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set("Asia/Manila");


function responseJSON($data, $statusCode = 200)
{
    http_response_code($statusCode);

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/* ==========================================
   AUTHENTICATION
========================================== */

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== "barangay_secretary"
) {

    responseJSON([
        "success" => false,
        "message" => "Unauthorized."
    ], 401);
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

    $secretaryID = intval($_SESSION['user_id']);

    $appealID = intval(
        $_POST['appeal_id'] ?? 0
    );

    $remarks = trim(
        $_POST['remarks'] ?? ''
    );


    /* ==========================================
       VALIDATE
    ========================================== */

    if ($appealID <= 0) {

        responseJSON([
            "success" => false,
            "message" => "Invalid appeal."
        ], 400);

    }


    if ($remarks === "") {

        responseJSON([
            "success" => false,
            "message" => "Secretary remarks are required."
        ], 400);

    }


    /* ==========================================
       GET SECRETARY BARANGAY
    ========================================== */

    $secretaryStmt = $conn->prepare("
        SELECT barangay
        FROM users
        WHERE id = ?
        AND role = 'barangay_secretary'
        LIMIT 1
    ");

    $secretaryStmt->bind_param(
        "i",
        $secretaryID
    );

    $secretaryStmt->execute();

    $secretaryResult =
        $secretaryStmt->get_result();

    if ($secretaryResult->num_rows === 0) {

        $secretaryStmt->close();

        responseJSON([
            "success" => false,
            "message" => "Secretary account not found."
        ], 404);

    }

    $secretary =
        $secretaryResult->fetch_assoc();

    $secretaryStmt->close();

    $barangay = $secretary['barangay'];


    /* ==========================================
       GET APPEAL
    ========================================== */

    $appealStmt = $conn->prepare("
        SELECT
            a.id,
            a.complaint_id,
            a.status,
            c.validation_status,
            u.barangay

        FROM complaint_appeals a

        INNER JOIN resident_complaints c
            ON c.id = a.complaint_id

        INNER JOIN users u
            ON u.id = a.resident_id

        WHERE a.id = ?
        AND u.barangay = ?

        LIMIT 1
    ");

    $appealStmt->bind_param(
        "is",
        $appealID,
        $barangay
    );

    $appealStmt->execute();

    $appealResult =
        $appealStmt->get_result();

    if ($appealResult->num_rows === 0) {

        $appealStmt->close();

        responseJSON([
            "success" => false,
            "message" =>
                "Appeal not found or does not belong to your barangay."
        ], 404);

    }

    $appeal =
        $appealResult->fetch_assoc();

    $appealStmt->close();


    $complaintID =
        intval($appeal['complaint_id']);


    /* ==========================================
       CHECK APPEAL STATUS
    ========================================== */

    if (
        $appeal['status'] !== "Pending" &&
        $appeal['status'] !== "Under Review"
    ) {

        responseJSON([
            "success" => false,
            "message" =>
                "This appeal has already been processed."
        ], 400);

    }


    /* ==========================================
       TRANSACTION
    ========================================== */

    $conn->begin_transaction();


    /* ==========================================
       UPDATE APPEAL
       
       THIS IS IMPORTANT:
       Appeal itself becomes Rejected.
    ========================================== */

    $updateAppeal = $conn->prepare("
        UPDATE complaint_appeals

        SET
            status = 'Rejected',
            reviewed_by = ?,
            reviewed_at = NOW(),
            secretary_remarks = ?

        WHERE id = ?
        AND status IN ('Pending', 'Under Review')
    ");

    $updateAppeal->bind_param(
        "isi",
        $secretaryID,
        $remarks,
        $appealID
    );

    $updateAppeal->execute();


    if ($updateAppeal->affected_rows === 0) {

        $updateAppeal->close();

        throw new Exception(
            "This appeal has already been processed."
        );
    }

    $updateAppeal->close();

/* ==========================================
   UPDATE ORIGINAL COMPLAINT
==========================================

   Appeal rejected
   =
   Original complaint remains rejected

   We only update the original complaint's
   validation status and remarks.
========================================== */

$updateComplaint = $conn->prepare("
    UPDATE resident_complaints

    SET
        validation_status = 'Rejected',
        remarks = ?

    WHERE id = ?
");

$updateComplaint->bind_param(
    "si",
    $remarks,
    $complaintID
);

$updateComplaint->execute();

if ($updateComplaint->affected_rows === 0) {

    /*
     * affected_rows can be 0 if the values
     * are already the same, so do not treat
     * this alone as a SQL failure.
     */

    if ($updateComplaint->errno) {

        $error =
            $updateComplaint->error;

        $updateComplaint->close();

        throw new Exception(
            "Complaint update failed: " . $error
        );
    }
}

$updateComplaint->close();

    /* ==========================================
       COMMIT
    ========================================== */

    $conn->commit();

    $conn->close();


    /* ==========================================
       SUCCESS
    ========================================== */

    responseJSON([
        "success" => true,
        "message" =>
            "Appeal rejected. The complaint remains rejected.",
        "appeal_status" => "Rejected",
        "validation_status" => "Rejected",
        "complaint_id" => $complaintID
    ]);


} catch (Throwable $e) {

    if ($conn instanceof mysqli) {

        try {
            $conn->rollback();
        } catch (Throwable $rollbackError) {
        }

        try {
            $conn->close();
        } catch (Throwable $closeError) {
        }
    }

    error_log(
        "barangay-secretary-reject-appeal.php ERROR: " .
        $e->getMessage()
    );

    responseJSON([
        "success" => false,
        "message" => $e->getMessage()
    ], 500);
}
?>