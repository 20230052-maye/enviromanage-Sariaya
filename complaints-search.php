<?php

session_start();
date_default_timezone_set('Asia/Manila');

/* =========================
   DB CONNECTION
========================= */

$conn = new mysqli(
    "localhost",
    "u823857209_enviromanage",
    "Enviromanage4322",
    "u823857209_enviromanage"
);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");


/* =========================
   SECRETARY
========================= */

$userID = intval($_SESSION['user_id'] ?? 0);

if ($userID <= 0) {
    exit;
}

$secretaryStmt = $conn->prepare("
    SELECT barangay
    FROM users
    WHERE id = ?
");

$secretaryStmt->bind_param("i", $userID);
$secretaryStmt->execute();

$secretary = $secretaryStmt
    ->get_result()
    ->fetch_assoc();

$secretaryStmt->close();

if (!$secretary) {
    exit;
}

$barangay = $secretary['barangay'];


/* =========================
   CHECK EXISTING NORMAL REVIEW
========================= */

$checkReview = $conn->prepare("
    SELECT rc.id
    FROM resident_complaints rc

    INNER JOIN users u
        ON rc.resident_id = u.id

    LEFT JOIN complaint_appeals ca
        ON ca.id = (
            SELECT MAX(ca2.id)
            FROM complaint_appeals ca2
            WHERE ca2.complaint_id = rc.id
        )

    WHERE u.barangay = ?

    AND rc.validation_status = 'Under Review'

    AND (
        ca.id IS NULL
        OR ca.status NOT IN ('Pending', 'Under Review')
    )

    LIMIT 1
");

$checkReview->bind_param(
    "s",
    $barangay
);

$checkReview->execute();

$hasUnderReview =
    $checkReview->get_result()->num_rows > 0;

$checkReview->close();


/* =========================
   FILTERS
========================= */

$search = trim($_GET['search'] ?? '');

$category =
    $_GET['category'] ?? 'All Categories';

$status =
    $_GET['status'] ?? 'All Status';


/* =========================
   MAIN QUERY
========================= */

$sql = "
SELECT

    rc.id,
    rc.ticket_no,
    rc.queue_no,
    rc.resident_id,

    rc.complaint_location,
    rc.description,

    rc.validation_status,
    rc.action_status,

    rc.reviewed_by,
    rc.reviewed_at,

    rc.remarks,
    rc.admin_notes,

    rc.submitted_at,
    rc.category,

    u.first_name,
    u.last_name,
    u.email,
    u.phone,

    CONCAT(
        u.house_no,' ',
        u.street,', ',
        u.barangay,', ',
        u.postal_code
    ) AS address,

    ca.id AS appeal_id,
    ca.appeal_reason,
    ca.status AS appeal_status,
    ca.reviewed_by AS appeal_reviewed_by,
    ca.reviewed_at AS appeal_reviewed_at,
    ca.secretary_remarks AS appeal_secretary_remarks,
    ca.submitted_at AS appeal_submitted_at

FROM resident_complaints rc

INNER JOIN users u
    ON rc.resident_id = u.id

LEFT JOIN complaint_appeals ca
    ON ca.id = (
        SELECT MAX(ca2.id)
        FROM complaint_appeals ca2
        WHERE ca2.complaint_id = rc.id
    )

WHERE u.barangay = ?
";


$params = [$barangay];
$types = "s";


/* =========================
   STATUS FILTER
========================= */

if ($status != "All Status") {

    /*
     * Pending / Under Review appeal
     * should behave as Waiting / Under Review
     */

    if ($status === "Waiting") {

        $sql .= "
        AND (
            rc.validation_status = 'Waiting'
            OR ca.status = 'Pending'
        )
        ";

    }

    elseif ($status === "Under Review") {

        $sql .= "
        AND (
            rc.validation_status = 'Under Review'
            OR ca.status = 'Under Review'
        )
        ";

    }

    else {

        $sql .= "
        AND rc.validation_status = ?
        ";

        $params[] = $status;
        $types .= "s";

    }

}


/* =========================
   CATEGORY FILTER
========================= */

if (
    $category != "All Categories" &&
    $category != ""
) {

    $sql .= "
        AND rc.category = ?
    ";

    $params[] = $category;
    $types .= "s";
}


/* =========================
   SEARCH
========================= */

if ($search !== "") {

    $sql .= "
    AND (
        CAST(rc.queue_no AS CHAR) LIKE ?
        OR rc.ticket_no LIKE ?

        OR CONCAT(
            u.first_name,' ',u.last_name
        ) LIKE ?

        OR u.first_name LIKE ?
        OR u.last_name LIKE ?

        OR rc.complaint_location LIKE ?
        OR rc.description LIKE ?

        OR rc.validation_status LIKE ?
        OR rc.action_status LIKE ?

        OR ca.status LIKE ?

        OR u.email LIKE ?
        OR u.phone LIKE ?

        OR CONCAT(
            u.house_no,' ',
            u.street,' ',
            u.barangay,' ',
            u.postal_code
        ) LIKE ?

        OR CAST(rc.submitted_at AS CHAR) LIKE ?
    )
    ";

    $like = "%{$search}%";

    for ($i = 0; $i < 14; $i++) {
        $params[] = $like;
    }

    $types .= str_repeat("s", 14);
}


/* =========================
   PAGINATION
========================= */

$limit = 10;

$page = isset($_GET['page'])
    ? (int)$_GET['page']
    : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;


$sql .= "
    ORDER BY rc.submitted_at DESC
    LIMIT ?
    OFFSET ?
";

$params[] = $limit;
$params[] = $offset;

$types .= "ii";


/* =========================
   COUNT QUERY
========================= */

$countSQL = "
SELECT COUNT(*) AS total

FROM resident_complaints rc

INNER JOIN users u
    ON rc.resident_id = u.id

LEFT JOIN complaint_appeals ca
    ON ca.id = (
        SELECT MAX(ca2.id)
        FROM complaint_appeals ca2
        WHERE ca2.complaint_id = rc.id
    )

WHERE u.barangay = ?
";


$countParams = [$barangay];
$countTypes = "s";


/* =========================
   COUNT STATUS
========================= */

if ($status != "All Status") {

    if ($status === "Waiting") {

        $countSQL .= "
        AND (
            rc.validation_status = 'Waiting'
            OR ca.status = 'Pending'
        )
        ";

    }

    elseif ($status === "Under Review") {

        $countSQL .= "
        AND (
            rc.validation_status = 'Under Review'
            OR ca.status = 'Under Review'
        )
        ";

    }

    else {

        $countSQL .= "
            AND rc.validation_status = ?
        ";

        $countParams[] = $status;
        $countTypes .= "s";

    }

}


/* =========================
   COUNT CATEGORY
========================= */

if (
    $category != "All Categories" &&
    $category != ""
) {

    $countSQL .= "
        AND rc.category = ?
    ";

    $countParams[] = $category;
    $countTypes .= "s";
}


/* =========================
   COUNT SEARCH
========================= */

if ($search !== "") {

    $countSQL .= "
    AND (
        CAST(rc.queue_no AS CHAR) LIKE ?
        OR rc.ticket_no LIKE ?

        OR CONCAT(
            u.first_name,' ',u.last_name
        ) LIKE ?

        OR u.first_name LIKE ?
        OR u.last_name LIKE ?

        OR rc.complaint_location LIKE ?
        OR rc.description LIKE ?

        OR rc.validation_status LIKE ?
        OR rc.action_status LIKE ?

        OR ca.status LIKE ?

        OR u.email LIKE ?
        OR u.phone LIKE ?

        OR CONCAT(
            u.house_no,' ',
            u.street,' ',
            u.barangay,' ',
            u.postal_code
        ) LIKE ?

        OR CAST(rc.submitted_at AS CHAR) LIKE ?
    )
    ";

    $like = "%{$search}%";

    for ($i = 0; $i < 14; $i++) {
        $countParams[] = $like;
    }

    $countTypes .= str_repeat("s", 14);
}


$countStmt = $conn->prepare($countSQL);

$countStmt->bind_param(
    $countTypes,
    ...$countParams
);

$countStmt->execute();

$totalRecords =
    $countStmt
        ->get_result()
        ->fetch_assoc()['total'];

$countStmt->close();


$totalPages = ceil(
    $totalRecords / $limit
);


/* =========================
   EXECUTE MAIN QUERY
========================= */

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query prepare failed: " . $conn->error);
}

$stmt->bind_param(
    $types,
    ...$params
);

$stmt->execute();

$result = $stmt->get_result();


/* =========================
   NO RESULTS
========================= */

if ($result->num_rows == 0) {

    if ($search != "") {

        $message =
            "No complaints found for \"" .
            htmlspecialchars($search) .
            "\".";

    }

    elseif (
        $category != "All Categories" &&
        $category != ""
    ) {

        $message =
            "No complaints found under category \"" .
            htmlspecialchars($category) .
            "\".";

    }

    elseif ($status != "All Status") {

        $message =
            "No complaints found with status \"" .
            htmlspecialchars($status) .
            "\".";

    }

    else {

        $message = "No complaints available.";

    }


    echo '
    <tr>
        <td colspan="8" class="text-center py-5">

            <div class="text-muted">

                <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                <h6>'.$message.'</h6>

                <small>
                    Try changing your search or filter.
                </small>

            </div>

        </td>
    </tr>
    ';

    exit;
}
/* =========================
   DISPLAY ROWS
========================= */

while ($row = $result->fetch_assoc()) {

    /*
     * ==========================================
     * NORMALIZE APPEAL STATUS
     * ==========================================
     */

    $appealStatus = trim(
        (string)($row['appeal_status'] ?? '')
    );


    /*
     * ==========================================
     * EFFECTIVE VALIDATION STATUS
     * ==========================================
     *
     * Appeal has priority over original
     * complaint validation status.
     */

    $effectiveValidationStatus =
        trim((string)$row['validation_status']);


    if ($appealStatus === "Pending") {

        $effectiveValidationStatus = "Waiting";

    }

    elseif ($appealStatus === "Under Review") {

        $effectiveValidationStatus = "Under Review";

    }

?>

<tr
    data-id="<?= (int)$row['id'] ?>"
    data-email="<?= htmlspecialchars($row['email'] ?? '') ?>"
    data-phone="<?= htmlspecialchars($row['phone'] ?? '') ?>"
    data-address="<?= htmlspecialchars($row['address'] ?? '') ?>"
    data-description="<?= htmlspecialchars($row['description'] ?? '') ?>"
>

    <!-- QUEUE -->

    <td>
        <?= htmlspecialchars($row['queue_no'] ?? '') ?>
    </td>


    <!-- TICKET -->

    <td>
        <?= htmlspecialchars($row['ticket_no'] ?? '') ?>
    </td>


    <!-- RESIDENT -->

    <td>
        <?= htmlspecialchars(
            trim(
                ($row['first_name'] ?? '') .
                ' ' .
                ($row['last_name'] ?? '')
            )
        ) ?>
    </td>


    <!-- LOCATION -->

    <td>
        <?= htmlspecialchars(
            $row['complaint_location'] ?? ''
        ) ?>
    </td>


    <!-- SUBMITTED -->

    <td>
        <?php

        if (!empty($row['submitted_at'])) {

            echo date(
                "M d, Y",
                strtotime($row['submitted_at'])
            );

        } else {

            echo "N/A";

        }

        ?>
    </td>


    <!-- =========================================
         VALIDATION STATUS
    ========================================== -->

    <td>

        <?php

        switch ($effectiveValidationStatus) {

            case "Waiting":

                echo '
                <span class="badge bg-warning text-dark">
                    Waiting
                </span>';

                break;


            case "Under Review":

                echo '
                <span class="badge bg-primary">
                    Under Review
                </span>';

                break;


            case "Approved":

                echo '
                <span class="badge bg-success">
                    Approved
                </span>';

                break;


            case "Rejected":

                echo '
                <span class="badge bg-danger">
                    Rejected
                </span>';

                break;


            default:

                echo '
                <span class="badge bg-secondary">
                    ' .
                    htmlspecialchars(
                        $effectiveValidationStatus ?: "Unknown"
                    )
                    .
                '</span>';

                break;

        }

        ?>

    </td>


      <!-- =========================================
         ACTION STATUS
    ========================================== -->

    <td>

        <?php

        /*
         * ==========================================
         * APPEAL ALWAYS HAS PRIORITY
         * ==========================================
         */

        if ($appealStatus === "Pending") {

            echo '
            <span class="badge bg-warning text-dark">
                Pending Appeal
            </span>
            ';

        }

        elseif ($appealStatus === "Under Review") {

            echo '
            <span class="badge bg-primary">
                Appeal Under Review
            </span>
            ';

        }

        elseif ($appealStatus === "Approved") {

            echo '
            <span class="badge bg-success">
                Forwarded to MENRO
            </span>
            ';

        }

        elseif ($appealStatus === "Rejected") {

            echo '
            <span class="badge bg-danger">
                Returned to Resident
            </span>
            ';

        }

        /*
         * ==========================================
         * NORMAL COMPLAINT STATUS
         * ==========================================
         */

        elseif (
            $row['validation_status'] === "Approved"
        ) {

            echo '
            <span class="badge bg-success">
                Forwarded to MENRO
            </span>
            ';

        }

        elseif (
            $row['validation_status'] === "Rejected"
        ) {

            echo '
            <span class="badge bg-danger">
                Returned to Resident
            </span>
            ';

        }

        else {

            echo '
            <span class="badge bg-secondary">
                Waiting Action
            </span>
            ';

        }

        ?>

    </td>


    <!-- =========================================
         ACTION BUTTON
    ========================================== -->

    <td>

        <?php

        /*
         * ==========================================
         * APPEAL WORKFLOW HAS PRIORITY
         * ==========================================
         *
         * Pending
         *     → Review Appeal
         *
         * Under Review
         *     → Continue Appeal
         *
         * Approved
         *     → Review Appeal
         *
         * Rejected
         *     → Review Appeal
         */


        /* ==========================================
           1. PENDING APPEAL
        ========================================== */

        if ($appealStatus === "Pending") {

        ?>

            <button
                type="button"
                class="btn btn-sm btn-danger reviewAppeal"
                data-id="<?= (int)$row['id'] ?>"
                data-appeal-id="<?= (int)$row['appeal_id'] ?>"
            >
                Review Appeal
            </button>

        <?php

        }


        /* ==========================================
           2. APPEAL UNDER REVIEW
        ========================================== */

        elseif ($appealStatus === "Under Review") {

        ?>

            <button
                type="button"
                class="btn btn-sm btn-warning continueAppealReview"
                data-id="<?= (int)$row['id'] ?>"
                data-appeal-id="<?= (int)$row['appeal_id'] ?>"
            >
                Continue Appeal
            </button>

        <?php

        }


        /* ==========================================
           3. APPEAL APPROVED
        ========================================== */

        elseif ($appealStatus === "Approved") {

        ?>

            <button
                type="button"
                class="btn btn-sm btn-success reviewAppeal"
                data-id="<?= (int)$row['id'] ?>"
                data-appeal-id="<?= (int)$row['appeal_id'] ?>"
            >
               View
            </button>

        <?php

        }


        /* ==========================================
           4. APPEAL REJECTED
        ========================================== */

        elseif ($appealStatus === "Rejected") {

        ?>

            <button
                type="button"
                class="btn btn-sm btn-success reviewAppeal"
                data-id="<?= (int)$row['id'] ?>"
                data-appeal-id="<?= (int)$row['appeal_id'] ?>"
            >
              View
            </button>

        <?php

        }


        /* ==========================================
           5. NORMAL COMPLAINT — WAITING
        ========================================== */

        elseif (
            $row['validation_status'] === "Waiting"
        ) {

        ?>

            <button
                type="button"
                class="btn btn-sm btn-primary reviewComplaint"
                data-id="<?= (int)$row['id'] ?>"
                <?= $hasUnderReview ? 'disabled' : '' ?>
            >
                Review
            </button>

        <?php

        }


        /* ==========================================
           6. NORMAL COMPLAINT — UNDER REVIEW
        ========================================== */

        elseif (
            $row['validation_status'] === "Under Review"
        ) {

        ?>

            <button
                type="button"
                class="btn btn-sm btn-warning continueReview"
                data-id="<?= (int)$row['id'] ?>"
            >
                Continue Review
            </button>

        <?php

        }


        /* ==========================================
           7. NORMAL COMPLAINT — FINAL
        ========================================== */

        elseif (
            $row['validation_status'] === "Approved" ||
            $row['validation_status'] === "Rejected"
        ) {

        ?>

            <button
                type="button"
                class="btn btn-sm btn-success viewComplaint"
                data-id="<?= (int)$row['id'] ?>"
            >
                View
            </button>

        <?php

        }

        ?>

    </td>

</tr>

<?php

}

$stmt->close();
$conn->close();

?>