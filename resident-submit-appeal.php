<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

date_default_timezone_set("Asia/Manila");


/* ==========================================
   HELPER
========================================== */

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
    $_SESSION['role'] !== "resident"
) {

    responseJSON([
        "success" => false,
        "message" => "Unauthorized."
    ], 401);
}


/* ==========================================
   DATABASE
========================================== */

$conn = null;

try {

    $conn = new mysqli(
        "localhost",
        "u823857209_enviromanage",
        "Enviromanage4322",
        "u823857209_enviromanage"
    );

    $conn->set_charset("utf8mb4");


    /* ==========================================
       GET DATA
    ========================================== */

    $residentID = intval($_SESSION['user_id']);

    $complaintID = intval(
        $_POST['complaint_id'] ?? 0
    );

    $appealReason = trim(
        $_POST['appeal_reason'] ?? ''
    );


    /* ==========================================
       VALIDATION
    ========================================== */

    if ($residentID <= 0) {

        responseJSON([
            "success" => false,
            "message" => "Invalid resident account."
        ], 400);
    }


    if ($complaintID <= 0) {

        responseJSON([
            "success" => false,
            "message" => "Invalid complaint."
        ], 400);
    }


    if ($appealReason === "") {

        responseJSON([
            "success" => false,
            "message" => "Appeal reason is required."
        ], 400);
    }


    if (strlen($appealReason) < 10) {

        responseJSON([
            "success" => false,
            "message" => "Please provide more details about your appeal."
        ], 400);
    }


    if (strlen($appealReason) > 1000) {

        responseJSON([
            "success" => false,
            "message" => "Appeal reason cannot exceed 1000 characters."
        ], 400);
    }


    /* ==========================================
       GET COMPLAINT
    ========================================== */

    $stmt = $conn->prepare("
        SELECT
            id,
            resident_id,
            validation_status,
            action_status
        FROM resident_complaints
        WHERE id = ?
        AND resident_id = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        "ii",
        $complaintID,
        $residentID
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {

        $stmt->close();

        responseJSON([
            "success" => false,
            "message" => "Complaint not found."
        ], 404);
    }

    $complaint = $result->fetch_assoc();

    $stmt->close();


    /* ==========================================
       ONLY REJECTED COMPLAINTS
    ========================================== */

    if ($complaint['validation_status'] !== "Rejected") {

        responseJSON([
            "success" => false,
            "message" => "This complaint is not eligible for appeal."
        ], 400);
    }


    /* ==========================================
       CHECK EXISTING APPEAL
    ========================================== */

    $checkAppeal = $conn->prepare("
        SELECT
            id,
            status
        FROM complaint_appeals
        WHERE complaint_id = ?
        AND resident_id = ?
        ORDER BY id DESC
        LIMIT 1
    ");

    $checkAppeal->bind_param(
        "ii",
        $complaintID,
        $residentID
    );

    $checkAppeal->execute();

    $existingAppealResult =
        $checkAppeal->get_result();


    if ($existingAppealResult->num_rows > 0) {

        $existingAppeal =
            $existingAppealResult->fetch_assoc();

        $checkAppeal->close();

        switch ($existingAppeal['status']) {

            case "Pending":

                responseJSON([
                    "success" => false,
                    "message" =>
                        "Your appeal has already been submitted and is waiting for review."
                ], 400);

                break;


            case "Under Review":

                responseJSON([
                    "success" => false,
                    "message" =>
                        "Your appeal is currently under review."
                ], 400);

                break;


            case "Approved":

                responseJSON([
                    "success" => false,
                    "message" =>
                        "This complaint has already been approved through appeal."
                ], 400);

                break;


            case "Rejected":

                responseJSON([
                    "success" => false,
                    "message" =>
                        "This complaint already has a rejected appeal."
                ], 400);

                break;


            default:

                responseJSON([
                    "success" => false,
                    "message" =>
                        "An appeal has already been submitted for this complaint."
                ], 400);
        }
    }

    $checkAppeal->close();


    /* ==========================================
       TRANSACTION
    ========================================== */

    $conn->begin_transaction();


    try {

        /* ==========================================
           PHILIPPINE DATE/TIME
        ========================================== */

        $appealSubmittedAt =
            date("Y-m-d H:i:s");


        /* ==========================================
           INSERT APPEAL
        ========================================== */

        $insertAppeal = $conn->prepare("
            INSERT INTO complaint_appeals
            (
                complaint_id,
                resident_id,
                appeal_reason,
                status,
                submitted_at
            )
            VALUES
            (
                ?,
                ?,
                ?,
                'Pending',
                ?
            )
        ");

        $insertAppeal->bind_param(
            "iiss",
            $complaintID,
            $residentID,
            $appealReason,
            $appealSubmittedAt
        );

        $insertAppeal->execute();

        $newAppealID =
            $conn->insert_id;

        $insertAppeal->close();


        /* ==========================================
           RESET COMPLAINT
        ========================================== */

        $updateComplaint = $conn->prepare("
            UPDATE resident_complaints

            SET
                validation_status = 'Waiting',
                action_status = 'Pending Assignment'

            WHERE id = ?
            AND resident_id = ?
        ");

        $updateComplaint->bind_param(
            "ii",
            $complaintID,
            $residentID
        );

        $updateComplaint->execute();

        $updateComplaint->close();


        /* ==========================================
           COMMIT
        ========================================== */

        $conn->commit();


        /* ==========================================
           CLOSE
        ========================================== */

        $conn->close();


        /* ==========================================
           SUCCESS
        ========================================== */

        responseJSON([
            "success" => true,
            "message" =>
                "Your appeal has been submitted successfully.",

            "complaint_id" =>
                $complaintID,

            "appeal_id" =>
                $newAppealID,

            "appeal_status" =>
                "Pending",

            "validation_status" =>
                "Waiting",

            "submitted_at" =>
                date(
                    "F j, Y | g:i A",
                    strtotime($appealSubmittedAt)
                )
        ]);


    } catch (Throwable $e) {

        $conn->rollback();

        throw $e;
    }


} catch (Throwable $e) {


    /* ==========================================
       LOG ACTUAL ERROR
    ========================================== */

    error_log(
        "resident-submit-appeal.php ERROR: " .
        $e->getMessage()
    );


    /* ==========================================
       CLOSE CONNECTION
    ========================================== */

    if ($conn instanceof mysqli) {

        try {
            $conn->close();
        } catch (Throwable $closeError) {
        }
    }


    /* ==========================================
       RESPONSE
    ========================================== */

    responseJSON([
        "success" => false,
        "message" => "Unable to submit appeal."
    ], 500);
}

?>