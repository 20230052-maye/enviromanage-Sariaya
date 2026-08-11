```php
<?php

session_start();

header("Content-Type: application/json");

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

require_once "db.php";

$conn->set_charset("utf8mb4");

$secretaryID = intval($_SESSION['user_id'] ?? 0);
$appealID = intval($_POST['appeal_id'] ?? 0);
$remarks = trim($_POST['remarks'] ?? '');


/*
====================================================
VALIDATE INPUT
====================================================
*/

if ($secretaryID <= 0 || $appealID <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid appeal information."
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


/*
====================================================
GET APPEAL
====================================================
*/

$stmt = $conn->prepare("
    SELECT
        a.id,
        a.complaint_id,
        a.resident_id,
        a.status,
        u.barangay

    FROM complaint_appeals a

    INNER JOIN users u
        ON u.id = a.resident_id

    WHERE a.id = ?

    LIMIT 1
");

$stmt->bind_param("i", $appealID);

$stmt->execute();

$appeal = $stmt
    ->get_result()
    ->fetch_assoc();

$stmt->close();


if (!$appeal) {

    echo json_encode([
        "success" => false,
        "message" => "Appeal not found."
    ]);

    exit;
}


/*
====================================================
CHECK APPEAL STATUS
====================================================
*/

if (
    $appeal['status'] !== "Pending" &&
    $appeal['status'] !== "Under Review"
) {

    echo json_encode([
        "success" => false,
        "message" => "This appeal has already been processed."
    ]);

    exit;
}


$complaintID = intval($appeal['complaint_id']);


/*
====================================================
START TRANSACTION
====================================================
*/

$conn->begin_transaction();


try {

    /*
    ====================================================
    UPDATE APPEAL
    ====================================================
    */

    $updateAppeal = $conn->prepare("
        UPDATE complaint_appeals

        SET
            status = 'Approved',
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


    /*
    ====================================================
    UPDATE RESIDENT COMPLAINT
    ====================================================
    
    APPROVED APPEAL:
    
    validation_status = Approved
    appeal_status     = Approved
    
    The complaint has already passed
    the Secretary's review through the appeal.
    */

  $updateComplaint = $conn->prepare("
    UPDATE resident_complaints
    SET
        appeal_status = 'Approved',
        appeal_reviewed_by = ?,
        appeal_reviewed_at = NOW(),
        appeal_review_notes = ?,
        remarks = ?,
        validation_status = 'Approved',
        action_status = 'Pending Assignment'
    WHERE id = ?
");

$updateComplaint->bind_param(
    "issi",
    $secretaryID,
    $remarks,
    $remarks,
    $complaintID
);

$updateComplaint->execute();

if ($updateComplaint->affected_rows === 0) {

    $updateComplaint->close();

    throw new Exception(
        "Complaint could not be updated."
    );
}

$updateComplaint->close();
    /*
    ====================================================
    COMMIT
    ====================================================
    */

    $conn->commit();


    /*
    ====================================================
    SUCCESS
    ====================================================
    */

    echo json_encode([
        "success" => true,
        "message" => "Appeal approved. The complaint has been approved.",
        "appeal_status" => "Approved",
        "validation_status" => "Approved",
        "action_status" => "Pending Assignment",
        "complaint_id" => $complaintID
    ]);


} catch (Exception $e) {

    /*
    ====================================================
    ROLLBACK
    ====================================================
    */

    $conn->rollback();


    error_log(
        "barangay-secretary-approve-appeal.php ERROR: " .
        $e->getMessage()
    );


    echo json_encode([
        "success" => false,
        "message" => "Unable to approve appeal."
    ]);
}


$conn->close();

?>
```
