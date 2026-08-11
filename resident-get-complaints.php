
<?php

session_start();

header("Content-Type: application/json");

date_default_timezone_set("Asia/Manila");


/* ==========================================
   AUTHENTICATION
========================================== */

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== "resident"
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

if ($conn->connect_error) {

    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);

    exit;
}

$conn->set_charset("utf8mb4");


$resident = intval($_SESSION['user_id']);


/* ==========================================
   GET COMPLAINTS
========================================== */

$stmt = $conn->prepare("

    SELECT
        rc.id,
        rc.ticket_no,
        rc.complaint_location,
        rc.category,
        rc.description,
        rc.validation_status,
        rc.action_status,
        rc.submitted_at,

        ca.id AS appeal_id,
        ca.status AS appeal_status,
        ca.submitted_at AS appeal_submitted_at

    FROM resident_complaints rc

    LEFT JOIN complaint_appeals ca
        ON ca.id = (
            SELECT ca2.id
            FROM complaint_appeals ca2
            WHERE ca2.complaint_id = rc.id
            AND ca2.resident_id = ?
            ORDER BY ca2.id DESC
            LIMIT 1
        )

    WHERE rc.resident_id = ?

    ORDER BY rc.submitted_at DESC

");


if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "Failed to prepare complaint query."
    ]);

    $conn->close();

    exit;
}


$stmt->bind_param(
    "ii",
    $resident,
    $resident
);


if (!$stmt->execute()) {

    echo json_encode([
        "success" => false,
        "message" => "Failed to retrieve complaints."
    ]);

    $stmt->close();
    $conn->close();

    exit;
}


$result = $stmt->get_result();


$data = [];


/* ==========================================
   BUILD DATA
========================================== */

while ($row = $result->fetch_assoc()) {


    /* ==========================================
       SAVE ORIGINAL DATABASE VALUES
    ========================================== */

    $originalValidationStatus =
        $row["validation_status"];

    $originalActionStatus =
        $row["action_status"];

    $appealStatus =
        $row["appeal_status"];


    /* ==========================================
       DEFAULT DISPLAY VALUES
    ========================================== */

    $displayValidationStatus =
        $originalValidationStatus;

    $displayActionStatus =
        $originalActionStatus;


    /* ==========================================
       1. WAITING
       
       Waiting
       No Action Status
    ========================================== */

    if ($originalValidationStatus === "Waiting") {

        $displayValidationStatus = "Waiting";

        $displayActionStatus = null;

    }


    /* ==========================================
       2. UNDER REVIEW
       
       Under Review
       Has Action Status
    ========================================== */

    elseif ($originalValidationStatus === "Under Review") {

        $displayValidationStatus = "Under Review";

        /*
         * If there is already an action status,
         * keep it.
         *
         * If none exists, use Pending Assignment.
         */

        if (
            empty($displayActionStatus) ||
            $displayActionStatus === null
        ) {

            $displayActionStatus =
                "Pending Assignment";

        }

    }


    /* ==========================================
       3. APPROVED
       
       Approved
       Has Action Status
    ========================================== */

    elseif ($originalValidationStatus === "Approved") {

        $displayValidationStatus = "Approved";

        /*
         * Approved complaints should always
         * have an Action Status.
         */

        if (
            empty($displayActionStatus) ||
            $displayActionStatus === null
        ) {

            $displayActionStatus =
                "Pending Assignment";

        }

    }


    /* ==========================================
       4. REJECTED
       
       Rejected
       No Action Status
    ========================================== */

    elseif ($originalValidationStatus === "Rejected") {

        $displayValidationStatus = "Rejected";

        $displayActionStatus = null;

    }


   if ($appealStatus === "Pending") {

    // Appeal submitted but Secretary has not reviewed it yet
    $displayValidationStatus = "Waiting";
    $displayActionStatus = null;

}

elseif ($appealStatus === "Under Review") {

    // Secretary has started reviewing the appeal
    $displayValidationStatus = "Under Review";
    $displayActionStatus = "Pending Appeal";

}

elseif ($appealStatus === "Approved") {

    // Appeal approved
    $displayValidationStatus = "Approved";

    // Keep original complaint action status
    if (!empty($originalActionStatus)) {
        $displayActionStatus = $originalActionStatus;
    } else {
        $displayActionStatus = "Pending Assignment";
    }

}

elseif ($appealStatus === "Rejected") {

    // Appeal rejected
    $displayValidationStatus = "Rejected";
    $displayActionStatus = null;

}
    /* ==========================================
       7. APPEAL REJECTED
       
       Rejected
       No Action Status
    ========================================== */

    elseif ($appealStatus === "Rejected") {

        $displayValidationStatus = "Rejected";

        $displayActionStatus = null;

    }


    /* ==========================================
       APPLY DISPLAY VALUES
    ========================================== */

    $row["validation_status"] =
        $displayValidationStatus;

    $row["action_status"] =
        $displayActionStatus;


    /* ==========================================
       FORMAT DATE
    ========================================== */

    if (!empty($row["submitted_at"])) {

        $row["submitted_at"] = date(
            "F j, Y | g:i A",
            strtotime($row["submitted_at"])
        );

    }


    /* ==========================================
       CREATE APPEAL OBJECT
    ========================================== */

    if (!empty($row["appeal_id"])) {

        $row["appeal"] = [

            "id" =>
                $row["appeal_id"],

            "status" =>
                $row["appeal_status"],

            "submitted_at" =>
                !empty($row["appeal_submitted_at"])
                    ? date(
                        "F j, Y | g:i A",
                        strtotime(
                            $row["appeal_submitted_at"]
                        )
                    )
                    : null

        ];

    } else {

        $row["appeal"] = null;

    }


    /* ==========================================
       REMOVE INTERNAL APPEAL FIELDS
    ========================================== */

    unset($row["appeal_id"]);
    unset($row["appeal_status"]);
    unset($row["appeal_submitted_at"]);


    /* ==========================================
       ADD TO RESPONSE
    ========================================== */

    $data[] = $row;

}


/* ==========================================
   RESPONSE
========================================== */

echo json_encode([

    "success" => true,

    "complaints" => $data

]);


$stmt->close();
$conn->close();

?>