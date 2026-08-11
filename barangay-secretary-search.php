<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'barangay_secretary'
) {
    exit;
}

$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

$conn->set_charset("utf8mb4");

$userId = (int)$_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT barangay
    FROM users
    WHERE id=?
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$barangay = $stmt->get_result()->fetch_assoc()['barangay'];
$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? 'All Status';


$sql = "
SELECT
    id,
    first_name,
    middle_initial,
    last_name,
    house_no,
    street,
    barangay,
    created_at,
    approval_status,
    birthdate,
    gender,
    phone,
    email,
    profile_photo,
    valid_id,
    rejection_reason

FROM users

WHERE role='resident'

AND barangay=?
";


$params = [$barangay];
$types = "s";


// STATUS FILTER
if($statusFilter != "All Status"){

    $sql .= "
    AND approval_status = ?
    ";

    $params[] = $statusFilter;
    $types .= "s";

}



// LIVE SEARCH
if($search != ""){


    $sql .= "

    AND (

        CAST(id AS CHAR) LIKE ?

        OR CONCAT('APP',LPAD(id,5,'0')) LIKE ?

        OR CONCAT(first_name,' ',last_name) LIKE ?

        OR CONCAT(
            first_name,' ',
            middle_initial,'. ',
            last_name
        ) LIKE ?

        OR email LIKE ?

        OR phone LIKE ?

        OR house_no LIKE ?

        OR street LIKE ?

        OR barangay LIKE ?

        OR approval_status LIKE ?

    )

    ";


    $like="%".$search."%";


    for($i=0;$i<10;$i++){

        $params[]=$like;

    }


    $types .= "ssssssssss";

}



$sql .= "

ORDER BY created_at DESC

";



$stmt=$conn->prepare($sql);


$stmt->bind_param(
    $types,
    ...$params
);


$stmt->execute();


$result=$stmt->get_result();
?>

<?php if($result->num_rows): ?>

<?php while($row=$result->fetch_assoc()): ?>

<?php

$name = trim(
$row['first_name'].' '.
($row['middle_initial']
? $row['middle_initial'].'. '
: '').
$row['last_name']
);

$status = ucfirst($row['approval_status']);

$badge =
$status=="Approved"
? "success"
:
($status=="Rejected"
? "danger"
: "warning");

?>

<tr>

<td>APP<?= str_pad($row['id'],5,"0",STR_PAD_LEFT) ?></td>

<td><?= htmlspecialchars($name) ?></td>

<td><?= htmlspecialchars($row['house_no']) ?></td>

<td><?= htmlspecialchars($row['street']) ?>,
<?= htmlspecialchars($row['barangay']) ?></td>

<td><?= date("M d, Y",strtotime($row['created_at'])) ?></td>

<td>
<span class="badge bg-<?= $badge ?>">
<?= $status ?>
</span>
</td>

<td>

<button
class="btn btn-sm btn-primary viewBtn"

data-id="<?= $row['id'] ?>"

data-name="<?= htmlspecialchars($name) ?>"

data-status="<?= $status ?>"

data-created="<?= date("M d, Y",strtotime($row['created_at'])) ?>"

data-house="<?= htmlspecialchars($row['house_no']) ?>"

data-barangay="<?= htmlspecialchars($row['barangay']) ?>"

data-street="<?= htmlspecialchars($row['street']) ?>"

data-birthdate="<?= htmlspecialchars($row['birthdate']) ?>"

data-gender="<?= htmlspecialchars($row['gender']) ?>"

data-phone="<?= htmlspecialchars($row['phone']) ?>"

data-email="<?= htmlspecialchars($row['email']) ?>"

data-profile="<?= htmlspecialchars($row['profile_photo']) ?>"

data-validid="<?= htmlspecialchars($row['valid_id']) ?>"

data-reason="<?= htmlspecialchars($row['rejection_reason']) ?>"

>

View

</button>

</td>

</tr>

<?php endwhile; ?>

<?php else: 

if($search != ""){

    $message = "No applications found for \"" 
    . htmlspecialchars($search) . "\".";

}
elseif($statusFilter != "All Status"){

    $message = "No applications found with status \"" 
    . htmlspecialchars($statusFilter) . "\".";

}
else{

    $message = "No applications available.";

}

?>

<tr id="emptySearchRow">

<td colspan="7" class="text-center py-5">

<div class="text-muted">

<i class="bi bi-inbox fs-2 d-block mb-2"></i>

<h6><?= $message ?></h6>

<small>
Try changing your search or filter.
</small>

</div>

</td>

</tr>

<?php endif; ?>