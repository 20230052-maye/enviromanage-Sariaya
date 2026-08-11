<?php

session_start();
date_default_timezone_set('Asia/Manila');

// DB CONNECTION
$conn = new mysqli(
    "localhost",
    "u823857209_enviromanage",
    "Enviromanage4322",
    "u823857209_enviromanage"
);

$conn->set_charset("utf8mb4");


$userID = $_SESSION['user_id'];

$secretary = $conn->query("
SELECT barangay
FROM users
WHERE id='$userID'
")->fetch_assoc();

$barangay = $secretary['barangay'];

$checkReview = $conn->prepare("
SELECT rc.id
FROM resident_complaints rc
INNER JOIN users u
ON rc.resident_id = u.id
WHERE u.barangay = ?
AND rc.validation_status = 'Under Review'
LIMIT 1
");

$checkReview->bind_param("s",$barangay);
$checkReview->execute();

$hasUnderReview =
$checkReview->get_result()->num_rows > 0;
$barangay = $secretary['barangay'];
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? 'All Categories';
$status = $_GET['status'] ?? 'All Status';


$sql = "
SELECT

resident_complaints.id,
resident_complaints.ticket_no,
resident_complaints.queue_no,
resident_complaints.resident_id,
resident_complaints.complaint_location,
resident_complaints.description,

resident_complaints.validation_status,
resident_complaints.action_status,

resident_complaints.reviewed_by,
resident_complaints.reviewed_at,

resident_complaints.remarks,
resident_complaints.admin_notes,

resident_complaints.submitted_at,

users.first_name,
users.last_name,
users.email,
users.phone,

CONCAT(
users.house_no,' ',
users.street,', ',
users.barangay,', ',
users.postal_code
) AS address

FROM resident_complaints

INNER JOIN users
ON resident_complaints.resident_id = users.id

WHERE users.barangay = ?
";


$params = [$barangay];
$types = "s";





if($status != "All Status"){

$sql .= " AND resident_complaints.validation_status = ?";
$params[] = $status;
$types .= "s";

}
if($category != "All Categories" && $category != ""){

$sql .= " AND resident_complaints.category = ?";
$params[] = $category;
$types .= "s";

}

$search = trim($_GET['search'] ?? '');

if ($search !== "") {

    $sql .= "
    AND (
        CAST(resident_complaints.queue_no AS CHAR) LIKE ?
        OR resident_complaints.ticket_no LIKE ?
        OR CONCAT(users.first_name,' ',users.last_name) LIKE ?
        OR users.first_name LIKE ?
        OR users.last_name LIKE ?
        OR resident_complaints.complaint_location LIKE ?
        OR resident_complaints.description LIKE ?
        OR resident_complaints.validation_status LIKE ?
        OR resident_complaints.action_status LIKE ?
        OR users.email LIKE ?
        OR users.phone LIKE ?
        OR CONCAT(
            users.house_no,' ',
            users.street,' ',
            users.barangay,' ',
            users.postal_code
        ) LIKE ?
        OR CAST(resident_complaints.submitted_at AS CHAR) LIKE ?
    )
    ";

    $like = "%{$search}%";

    for ($i = 0; $i < 13; $i++) {
        $params[] = $like;
    }

    $types .= str_repeat("s", 13);
}


// PAGINATION
$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if($page < 1){
    $page = 1;
}

$offset = ($page - 1) * $limit;


$sql.=" ORDER BY resident_complaints.submitted_at DESC LIMIT ? OFFSET ?";


$params[] = $limit;
$params[] = $offset;

$types .= "ii";

$countSQL="
SELECT COUNT(*) as total
FROM resident_complaints
INNER JOIN users
ON resident_complaints.resident_id = users.id
WHERE users.barangay = ?
";

$countParams = [$barangay];
$countTypes = "s";





if($status != "All Status"){

$countSQL .= " AND resident_complaints.validation_status = ?";
$countParams[] = $status;
$countTypes .= "s";

}
if($category != "All Categories" && $category != ""){

$countSQL .= " AND resident_complaints.category = ?";
$countParams[] = $category;
$countTypes .= "s";

}
if ($search !== "") {

    $countSQL .= "
    AND (
        CAST(resident_complaints.queue_no AS CHAR) LIKE ?
        OR resident_complaints.ticket_no LIKE ?
        OR CONCAT(users.first_name,' ',users.last_name) LIKE ?
        OR users.first_name LIKE ?
        OR users.last_name LIKE ?
        OR resident_complaints.complaint_location LIKE ?
        OR resident_complaints.description LIKE ?
        OR resident_complaints.validation_status LIKE ?
        OR resident_complaints.action_status LIKE ?
        OR users.email LIKE ?
        OR users.phone LIKE ?
        OR CONCAT(
            users.house_no,' ',
            users.street,' ',
            users.barangay,' ',
            users.postal_code
        ) LIKE ?
        OR CAST(resident_complaints.submitted_at AS CHAR) LIKE ?
    )
    ";

    $like = "%{$search}%";

    for ($i = 0; $i < 13; $i++) {
        $countParams[] = $like;
    }

    $countTypes .= str_repeat("s", 13);
}

$countStmt=$conn->prepare($countSQL);

$countStmt->bind_param(
$countTypes,
...$countParams
);

$countStmt->execute();


$totalRecords=$countStmt
->get_result()
->fetch_assoc()['total'];


$totalPages=ceil($totalRecords/$limit);
$stmt=$conn->prepare($sql);

$stmt->bind_param(
$types,
...$params
);

$stmt->execute();


$result=$stmt->get_result();

if($result->num_rows == 0){

    if($search != ""){

        $message = "No complaints found for \"" . htmlspecialchars($search) . "\".";

    } 
  
    elseif($category != "All Categories" && $category != ""){

$message = "No complaints found under category \"" . htmlspecialchars($category) . "\".";

}

elseif($status != "All Status"){

$message = "No complaints found with status \"" . htmlspecialchars($status) . "\".";

}
    else{

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

while($row=$result->fetch_assoc()){

?>

<tr
data-id="<?= $row['id'] ?>"
data-email="<?=htmlspecialchars($row['email'])?>"
data-phone="<?=htmlspecialchars($row['phone'])?>"
data-address="<?=htmlspecialchars($row['address'])?>"
data-description="<?=htmlspecialchars($row['description'])?>"
>
<td>
<?=htmlspecialchars($row['queue_no'])?>
</td>


<td>
<?=htmlspecialchars($row['ticket_no'])?>
</td>


<td>
<?=htmlspecialchars(
$row['first_name']." ".$row['last_name']
)?>
</td>


<td>
<?=htmlspecialchars(
$row['complaint_location']
)?>
</td>


<td>
<?= date("M d, Y", strtotime($row['submitted_at'])) ?>
</td>

<td>

<?php

switch($row['validation_status']){

    case "Waiting":
        echo '<span class="badge bg-warning text-dark">Waiting</span>';
        break;

    case "Under Review":
        echo '<span class="badge bg-primary">Under Review</span>';
        break;

    case "Approved":
        echo '<span class="badge bg-success">Approved</span>';
        break;

    case "Rejected":
        echo '<span class="badge bg-danger">Rejected</span>';
        break;
}

?>

</td>

<td>

<?php

if($row['action_status']=="Forwarded"){

    echo '<span class="badge bg-success">
    Forwarded to MENRO
    </span>';

}
elseif($row['action_status']=="Returned"){

    echo '<span class="badge bg-danger">
    Returned to Resident
    </span>';

}
else{

    echo '<span class="badge bg-secondary">
    Waiting Action
    </span>';

}

?>

</td>
<td>
<?php if($row['validation_status'] == 'Waiting'): ?>

    <button
        class="btn btn-sm btn-primary reviewComplaint"
        data-id="<?= $row['id'] ?>"
        <?= $hasUnderReview ? 'disabled' : '' ?>
    >
        Review
    </button>


<?php elseif($row['validation_status'] == 'Under Review'): ?>


    <button
        class="btn btn-sm btn-warning continueReview"
        data-id="<?= $row['id'] ?>"
    >
        Continue Review
    </button>


<?php elseif(
    $row['validation_status'] == 'Approved' ||
    $row['validation_status'] == 'Rejected'
): ?>


    <button
        class="btn btn-sm btn-success viewComplaint"
        data-id="<?= $row['id'] ?>"
    >
        View
    </button>


<?php endif; ?>
</td>

</tr>

<?php

}

?>