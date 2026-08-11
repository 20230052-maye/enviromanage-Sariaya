<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

session_start();

date_default_timezone_set('Asia/Manila');

if(
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'barangay_secretary'
){
    header("Location: login.php");
    exit;
}

// DB CONNECTION
$conn = new mysqli(
    "localhost",
    "u823857209_enviromanage",
    "Enviromanage4322",
    "u823857209_enviromanage"
);
if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}

$conn->set_charset("utf8mb4");

$userID = $_SESSION['user_id'];

$stmtBarangay = $conn->prepare("
    SELECT barangay
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmtBarangay->bind_param("i", $userID);
$stmtBarangay->execute();

$secretary = $stmtBarangay->get_result()->fetch_assoc();

$barangay = $secretary['barangay'];
$search = trim($_GET['search'] ?? '');
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
     resident_complaints.resolved_by,
     resident_complaints.resolved_at,
     resident_complaints.assigned_personnel_id,
     resident_complaints.assigned_personnel_type,
     resident_complaints.assigned_personnel_name,
     resident_complaints.assigned_by,
     resident_complaints.assigned_at,
     resident_complaints.submitted_at,
     resident_complaints.remarks,
     resident_complaints.admin_notes,

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

FROM  resident_complaints

INNER JOIN users
ON  resident_complaints.resident_id = users.id

WHERE users.barangay = ?
";

$params = [$barangay];
$types = "s";



if ($status != "All Status") {
    $sql .= " AND  resident_complaints.validation_status = ?";
    $params[] = $status;
    $types .= "s";
}

if ($search != "") {

    $sql .= " AND (
         resident_complaints.ticket_no LIKE ?
        OR CAST( resident_complaints.queue_no AS CHAR) LIKE ?
        OR  resident_complaints.complaint_location LIKE ?
        OR  resident_complaints.description LIKE ?
        OR  resident_complaints.validation_status LIKE ?
        OR  resident_complaints.action_status LIKE ?
        OR CONCAT(users.first_name,' ',users.last_name) LIKE ?
        OR users.email LIKE ?
        OR users.phone LIKE ?
        OR CONCAT(
            users.house_no,' ',
            users.street,' ',
            users.barangay,' ',
            users.postal_code
        ) LIKE ?
    )";

    $like = "%".$search."%";

  $params[] = $like; // ticket_no
$params[] = $like; // queue_no
$params[] = $like; // complaint_location
$params[] = $like; // description
$params[] = $like; // validation_status
$params[] = $like; // action_status
$params[] = $like; // resident name
$params[] = $like; // email
$params[] = $like; // phone
$params[] = $like; // address
  $types .= "ssssssssss";
}
// PAGINATION

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if($page < 1){
    $page = 1;
}

$offset = ($page - 1) * $limit;


// ADD LIMIT AFTER OFFSET IS READY

$sql .= " ORDER BY  resident_complaints.submitted_at DESC LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;

$types .= "ii";

if($page < 1){
    $page = 1;
}

$offset = ($page - 1) * $limit;
$countSQL = "
SELECT COUNT(*) as total
FROM  resident_complaints
INNER JOIN users
ON  resident_complaints.resident_id = users.id
WHERE  users.barangay = ?
";

$countParams = [$barangay];
$countTypes = "s";





if($status != "All Status"){

    $countSQL .= " AND  resident_complaints.validation_status = ?";
    $countParams[] = $status;
    $countTypes .= "s";

}


if($search != ""){

$countSQL .= "
AND (
     resident_complaints.ticket_no LIKE ?
    OR CAST( resident_complaints.queue_no AS CHAR) LIKE ?
    OR  resident_complaints.complaint_location LIKE ?
    OR  resident_complaints.description LIKE ?
    OR  resident_complaints.validation_status LIKE ?
    OR  resident_complaints.action_status LIKE ?
    OR CONCAT(users.first_name,' ',users.last_name) LIKE ?
    OR users.email LIKE ?
    OR users.phone LIKE ?
    OR CONCAT(
        users.house_no,' ',
        users.street,' ',
        users.barangay,' ',
        users.postal_code
    ) LIKE ?
)";

    $like = "%".$search."%";

 $countParams[] = $like; // ticket_no
$countParams[] = $like; // queue_no
$countParams[] = $like; // complaint_location
$countParams[] = $like; // description
$countParams[] = $like; // validation_status
$countParams[] = $like; // action_status
$countParams[] = $like; // resident name
$countParams[] = $like; // email
$countParams[] = $like; // phone
$countParams[] = $like; // address
$countTypes .= "ssssssssss";

}


$countStmt = $conn->prepare($countSQL);

$countStmt->bind_param(
    $countTypes,
    ...$countParams
);

$countStmt->execute();

$totalRecords = $countStmt
->get_result()
->fetch_assoc()['total'];


$totalPages = ceil($totalRecords / $limit);

$stmt = $conn->prepare($sql);

if(!$stmt){
    die("SQL Error: ".$conn->error);
}

$stmt->bind_param(
    $types,
    ...$params
);

$stmt->execute();

$result = $stmt->get_result();

?>




<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Resident Complaints</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    /* ===========================
   GOOGLE FONT
=========================== */

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#f4f7f9;
}

/* ===========================
   NAVBAR
=========================== */

.navbar{

    height:70px;
    background:#1f5d2f;
    z-index:1200;
    padding:0 20px;

}
.navbar .container-fluid{
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.navbar-actions{
    display:flex;
    align-items:center;
    gap:10px;
}

.nav-icon-btn{
    width:42px;
    height:42px;
    border:none;
    background:transparent;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
    border-radius:50%;
    transition:.3s;
}

.nav-icon-btn:hover{
    background:rgba(255,255,255,.15);
}

.notification-badge{
    position:absolute;
    top:4px;
    right:2px;
    width:18px;
    height:18px;
    background:#dc3545;
    color:#fff;
    border-radius:50%;
    font-size:10px;
    display:flex;
    align-items:center;
    justify-content:center;
}

.dropdown-toggle::after{
    display:none;
}
.navbar-brand img{

    height:45px;
    width:45px;
    object-fit:contain;

}

#hamburger{

    display:none;

    width:40px;
    height:40px;

    border:none;
    background:transparent;

    color:white;

    align-items:center;
    justify-content:center;

    font-size:22px;

}

/* ===========================
   SIDEBAR
=========================== */


.sidebar{
    position:fixed;
    top:70px;
    left:0;
    width:270px;
    height:calc(100vh - 70px);
    background:#fff;
    border-right:1px solid #dee2e6;
    padding:15px 0; /* tanggalin side gap */
    overflow-y:auto;
    transition:.3s ease;
    z-index:1100;
}
.sidebar .nav-link{

    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 20px;
    margin-bottom:8px;
    border-radius:0;
    color:#495057;
    text-decoration:none;
    white-space:nowrap;

}



.sidebar .nav-link i{

    font-size:20px;

    width:25px;

}


.sidebar .nav-link:hover,
.sidebar .nav-link.active{

    background:#1e5631;
    color:white;
}

/* ===========================
   SIDEBAR BUTTON
=========================== */

#sidebarControls{

    position:fixed;

    top:85px;

    left:270px;

    display:none;

    flex-direction:column;

    gap:8px; /* SPACE BETWEEN X AND ARROW */

    transition:.3s ease;

    z-index:1300;

}

#sidebarControls button{

    width:32px;
    height:32px;

    border:none;

    display:flex;

    align-items:center;
    justify-content:center;

    color:#fff;

    cursor:pointer;

}

#toggleBtn{

    background:#1e5631;

    border-radius:0 8px 8px 0;

}

#closeBtn{

    display:none;

    background:#dc3545;

    border-radius:0 8px 8px 0;

}

/* ===========================
   MAIN CONTENT
=========================== */

.main-content{

    margin-left:270px;

    padding:50px 25px 30px;

    transition:.3s ease;

    min-width:0;

}


/* ===========================
   SUMMARY CARDS
=========================== */

.summary-card{

    background:linear-gradient(135deg,#43a047,#66bb6a);

    color:#fff;

    border-radius:20px;

    padding:25px;

    display:flex;
    justify-content:space-between;
    align-items:center;

    box-shadow:0 12px 25px rgba(0,0,0,.08);

    transition:.3s;

}

.summary-card:hover{

    transform:translateY(-4px);

}

.summary-card h2{

    font-size:38px;

    font-weight:700;

    margin:10px 0;

}
.summary-card i{

    font-size:55px;

    opacity:.25;

}
.summary-card.orange{

    background:linear-gradient(135deg,#fb8c00,#ffa726);

}

.summary-card.green{

    background:linear-gradient(135deg,#2e7d32,#43a047);

}

.summary-card.blue{

    background:linear-gradient(135deg,#1976d2,#42a5f5);

}

.summary-card.red{

    background:linear-gradient(135deg,#d32f2f,#ef5350);

}

/* ===========================
   SEARCH CARD
=========================== */

.search-card{

    border:none;

    border-radius:18px;

    box-shadow:0 6px 15px rgba(0,0,0,.05);

}

.search-card .card-body{

    padding:25px;

}

.form-control,
.form-select{

    height:50px;

    border-radius:12px;

}

.form-control:focus,
.form-select:focus{

    border-color:#2e7d32;

    box-shadow:0 0 0 .15rem rgba(46,125,50,.2);

}

/* ===========================
   TABLE
=========================== */

.table-card{

    border:none;

    border-radius:18px;

    overflow:hidden;

    box-shadow:0 8px 18px rgba(0,0,0,.05);

}

.table-card .card-header{

    background:#fff;

    padding:20px 25px;

    border-bottom:1px solid #eee;

}

.table-card h5{

    margin:0;

    color:#1b5e20;

    font-weight:600;

}

.table{

    margin:0;

}

.table thead{

    background:#f1f8f4;

}

.table thead th{

    color:#2e7d32;

   font-weight:600;

    padding:18px;


}

.table tbody td{

    padding:18px;

}
/* ===========================
   TABLE COLUMN SIZE
=========================== */

.table{
    table-layout:auto;
    width:100%;
}

.table th,
.table td{
    white-space:nowrap;
    vertical-align:middle;
    padding:12px 10px;   /* mas dikit ang bawat column */
}

/* ID */
.table th:nth-child(1),
.table td:nth-child(1){
    width:75px;
}

/* Resident */
.table th:nth-child(2),
.table td:nth-child(2){
    min-width:140px;
}

/* Category */
.table th:nth-child(3),
.table td:nth-child(3){
    min-width:145px;
}

/* Priority */
.table th:nth-child(4),
.table td:nth-child(4){
    min-width:70px;
}

.table td:nth-child(4) .badge{
    font-size:11px;
    padding:5px 9px;
    border-radius:8px;
}

/* Barangay */
.table th:nth-child(5),
.table td:nth-child(5){
    min-width:125px;
}

/* Date */
.table th:nth-child(6),
.table td:nth-child(6){
    min-width:105px;
}

/* ===========================
   BUTTONS
=========================== */

.btn{

    border-radius:10px;

    font-weight:500;

}

.btn-success{

    background:#2e7d32;

    border:none;

}

.btn-success:hover{

    background:#1b5e20;

}

.btn-danger{

    border:none;

}

.table .btn{

    margin-right:5px;

}

/* ===========================
   BADGE
=========================== */

.badge{

    padding:8px 14px;

    border-radius:20px;

    font-size:13px;

}
/* ===========================
   MODAL FIX
=========================== */

.modal{
    z-index:2000;
}

.modal-content{
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 15px 40px rgba(0,0,0,.15);
}

.modal-header{

    background:linear-gradient(135deg,#2e7d32,#43a047);
    color:#fff;

    border:none;
    padding:20px 30px;

}

.modal-header h4,
.modal-header h5{

    margin:0;
    font-weight:600;

}

.modal-header .btn-close{

    filter:brightness(0) invert(1);

}

.modal-body{

    padding:30px;
    overflow-x:hidden;

}

.modal-footer{

    background:#fafafa;
    border:none;
    padding:20px 30px;
    gap:10px;

}
/* ===========================
   VIEW MODAL RESPONSIVE
=========================== */

.view-modal{

    width:95%;
    max-width:1200px;

}


/* LARGE DESKTOP */
@media(min-width:1200px){

    .view-modal{

        max-width:1100px;

        margin-left:calc(270px + 30px);
        margin-right:30px;

    }

}


/* LAPTOP */
@media(min-width:993px) and (max-width:1199px){

    .view-modal{

        max-width:900px;

        margin-left:calc(270px + 20px);
        margin-right:20px;

    }

}


/* TABLET */
@media(min-width:768px) and (max-width:992px){

    .view-modal{

        width:90%;
        max-width:850px;

        margin:auto;

    }

}


/* SMALL TABLET */
@media(min-width:576px) and (max-width:767px){

    .view-modal{

        width:92%;
        max-width:650px;

        margin:auto;

    }

}


/* MOBILE */
@media(max-width:575px){

    .view-modal{

        width:95%;
        max-width:none;

        margin:10px auto;

    }

}

.modal .row{

    margin-left:0;
    margin-right:0;

}

.modal img{

    max-width:100%;
    height:auto;

}

body.modal-open{

    overflow:hidden;
    padding-right:0 !important;

}

/* ===========================
   SMALL MODALS
=========================== */
.confirm-modal{
    width:100%;
    max-width:520px;
    margin-left:auto;
    margin-right:auto;
}

.confirm-modal .modal-content{
  width:100%;
    max-width:520px;
    margin:auto;
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 15px 35px rgba(0,0,0,.15);

}

.confirm-modal .modal-header{

    background:linear-gradient(135deg,#2e7d32,#43a047);
    color:#fff;

    border:none;

    padding:18px 25px;

}

.confirm-modal .modal-header h5{

    margin:0;
    font-weight:600;
    font-size:20px;

}

.confirm-modal .modal-body{

    padding:28px;

    font-size:15px;
    color:#555;
    line-height:1.7;

}

.confirm-modal .modal-body p{

    margin:0;

}

.confirm-modal .modal-body label{

    font-weight:600;
    color:#2e7d32;
    margin-bottom:10px;

}

.confirm-modal textarea{

    min-height:130px;
    resize:none;

    border-radius:12px;

}

.confirm-modal textarea:focus{

    border-color:#2e7d32;
    box-shadow:0 0 0 .15rem rgba(46,125,50,.2);

}

.confirm-modal .modal-footer{

    background:#fff;

    border:none;

    padding:18px 25px;

    display:flex;
    justify-content:flex-end;
    gap:10px;

}

.confirm-modal .btn{

    min-width:110px;
    border-radius:10px;
    font-weight:500;

}

.confirm-modal .btn-success{

    background:#2e7d32;
    border:none;

}

.confirm-modal .btn-success:hover{

    background:#1b5e20;

}

.confirm-modal .btn-danger{

    background:#dc3545;
    border:none;

}

.confirm-modal .btn-danger:hover{

    background:#bb2d3b;

}

.confirm-modal .btn-secondary{

    background:#6c757d;
    border:none;

}

.confirm-modal .btn-secondary:hover{

    background:#5a6268;

}

/* ===========================
   PROFILE
=========================== */

.modal-body img.rounded-circle{

    width:180px;
    height:180px;

    object-fit:cover;

    border:6px solid #e8f5e9;

    margin-bottom:15px;

}

.info-item{

    margin-bottom:8px;

}

.info-item strong{

    display:block;

    color:#2e7d32;

    margin-bottom:4px;

}

.info-item p{

    margin:0;
    color:#666;

}

.section-title{

    color:#2e7d32;

    font-weight:600;

    margin-bottom:18px;

}

/* ===========================
   INPUTS
=========================== */

.modal label{

    font-size:14px;

    font-weight:500;

    margin-bottom:6px;

    color:#555;

}

.modal textarea.form-control,
.modal input.form-control{

    border-radius:12px;

}

textarea.form-control{

    resize:none;
    overflow-y:hidden;
    min-height:110px;

}

/* ===========================
   RETURN REASON
=========================== */

#returnReasonBox{

    display:none;

    border-radius:15px;

}

/* ===========================
   EVIDENCE
=========================== */

.evidence-card{

    border:2px dashed #c8e6c9;

    border-radius:15px;

    text-align:center;

    padding:25px;

    background:#fafafa;

    transition:.3s;

}

.evidence-card:hover{

    background:#f1f8f4;

    transform:translateY(-3px);

}

.evidence-card i{

    font-size:45px;

    color:#2e7d32;

    margin-bottom:15px;

}

/* ===========================
   TIMELINE
=========================== */

.timeline{

    list-style:none;

    padding-left:28px;

    position:relative;

}

.timeline::before{

    content:"";

    position:absolute;

    left:8px;

    top:0;

    bottom:0;

    width:3px;

    background:#dcedc8;

}

.timeline li{

    position:relative;

    margin-bottom:18px;

    color:#666;

    padding-left:22px;

}

.timeline li::before{

    content:"";

    position:absolute;

    left:-1px;

    top:4px;

    width:18px;

    height:18px;

    border-radius:50%;

    background:#bdbdbd;

}

.timeline li.done::before{

    background:#2e7d32;

}

.timeline li.active::before{

    background:#ff9800;

}

/* ===========================
   ALERT
=========================== */

.alert{

    border:none;

    border-radius:15px;

}

/* ===========================
   SCROLLBAR
=========================== */

::-webkit-scrollbar{

    width:8px;

}

::-webkit-scrollbar-thumb{

    background:#b0b0b0;
    border-radius:20px;

}


::-webkit-scrollbar-thumb:hover{

    background:#8a8a8a;

}


::-webkit-scrollbar-track{

    background:#f5f5f5;

}
@media (min-width:993px){

    .search-card .card-body{
        display:flex;
        justify-content:flex-end;
        padding:20px 25px;
    }

    .search-card form.row{
        display:flex;
        flex-wrap:nowrap;
        align-items:center;
        gap:16px;           /* ito ang space sa pagitan */
        margin-left:auto;
        width:auto;
    }

    /* Search */
    .search-card .col-7.col-md-6{
        flex:0 0 420px;
        max-width:420px;
          margin-left:40px;   /* dagdag pakanan */
    
    }

    /* Status */
    .search-card .col-5.col-md-3{
        flex:0 0 200px;
        max-width:200px;
    }
     .table-card .card-header{
        display:flex;
        align-items:center;
        justify-content:space-between;
        padding:18px 25px;
        gap:20px;
    }

    .table-card .card-header h5{
        margin:0;
        white-space:nowrap;
        font-size:22px;
    }

    .table-card .card-header form{
        display:flex;
        align-items:center;
        gap:16px;
        margin-left:auto;
        flex-wrap:nowrap;
    }

    .table-card .col-7.col-md-6{
        flex:0 0 420px;
        max-width:420px;
    }

    .table-card .col-5.col-md-3{
        flex:0 0 200px;
        max-width:200px;
    }
}
/* ===========================
   TABLET
=========================== */


  @media(max-width:992px){
.navbar .container-fluid{
    position:relative;
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.navbar-brand{
    position:absolute;
    left:50%;
    transform:translateX(-50%);
    margin:0;
}

.navbar-actions{
    margin-left:auto;
}

    #hamburger{

        display:flex;

    }


    .sidebar{

        width:70px;

    }


    .sidebar .nav-link{

        justify-content:center;

        padding:12px 10px;

    }


    .sidebar .nav-link span{

        display:none;

    }


    #sidebarControls{

        display:flex;

        left:70px;

    }


    /* EXPANDED */

    .sidebar.expanded{

        width:270px;
   box-shadow:8px 0 20px rgba(0,0,0,.15);
    z-index:1200;
    }
    
.sidebar.hide-sidebar{
    transform:translateX(-100%);
}
.sidebar.hide-sidebar ~ .main-content{

    margin-left:0;

}

    .sidebar.expanded .nav-link{

        justify-content:flex-start;

    }


    .sidebar.expanded .nav-link span{

        display:inline;

    }


    .sidebar.expanded ~ #sidebarControls{

        left:270px;

    }


    /* SHOW X WHEN OPEN */

    .sidebar.expanded ~ #sidebarControls #toggleBtn{

        display:flex;

    }


    .sidebar.expanded ~ #sidebarControls #closeBtn{

        display:flex;

    }


    /* CONTENT */

  .main-content{

    margin-left:70px;

    min-width:0;

}

    .sidebar.expanded ~ .main-content{

        margin-left:70px;


    }
    .search-card .row.g-3{
    display:flex;
    flex-wrap:nowrap;
    align-items:center;
    gap:8px;
}

.search-card .col-lg-5{
    flex:1;
    max-width:none;
}

.search-card .col-lg-3{
    flex:0 0 170px;
    max-width:170px;
}

.search-card .col-lg-2{
    flex:0 0 150px;
    max-width:150px;
}


}


/* ===========================
   MOBILE
=========================== */

@media(max-width:576px){

    .navbar{

        padding:0 15px;

    }

    .navbar-brand img{

        width:40px;
        height:40px;

    }


.sidebar.hide-sidebar ~ .main-content{

    margin-left:0;

}
/* CENTER LOGO SA MOBILE */
.navbar .container-fluid{
    position: relative;
    justify-content: flex-start;
}

.navbar-brand{
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    margin: 0;
}
.nav-icon-btn{
    width:38px;
    height:38px;
    font-size:20px;
}

.notification-badge{
    width:16px;
    height:16px;
    font-size:9px;
}

.navbar-brand img{
    width:38px;
    height:38px;
}

.navbar .container-fluid{
    position:relative;
    justify-content:flex-start;
}

.navbar-brand{
    position:absolute;
    left:50%;
    transform:translateX(-50%);
    margin:0;
}

.navbar-actions{
    margin-left:auto;
}
.sidebar{

    width:70px;
    transform:translateX(0);
    box-shadow:5px 0 15px rgba(0,0,0,.1);

}

.sidebar .nav-link{

    justify-content:center;
    padding:12px 10px;

}

.sidebar .nav-link span{

    display:none;

}

/* kapag pinindot ang arrow */
.sidebar.expanded{

    width:270px;

}

.sidebar.expanded .nav-link{

    justify-content:flex-start;

}

.sidebar.expanded .nav-link span{

    display:inline;

}

 #sidebarControls{

    display:flex;
    left:70px;

}

.sidebar.expanded ~ #sidebarControls{

    left:270px;

}

.sidebar.expanded ~ #sidebarControls #closeBtn{

    display:flex;

}

.sidebar.expanded ~ #sidebarControls #toggleBtn{

    display:flex;

}

    .modal-body{

        padding:20px;

    }

    .modal-footer{

        flex-direction:column;

    }

    .modal-footer .btn{

        width:100%;

    }

    .modal-body img.rounded-circle{

        width:120px;
        height:120px;

    }
   /* MOBILE COMPLAINT MODAL FIX */

#complaintModal .view-modal{
    max-width:85%;
    margin:40px auto 15px;
}

#complaintModal .modal-content{
    border-radius:12px;
}

#complaintModal .modal-header{
    padding:12px 15px;
}

#complaintModal .modal-title{
    font-size:16px;
}

#complaintModal .modal-body{
    padding:12px;
    max-height:70vh;
    overflow-y:auto;
}

#complaintModal .modal-footer{
    padding:10px 12px;
}
/* SMALL MODALS MOBILE */

.confirm-modal{
    width:85%;
    max-width:320px;
    margin:0 auto;
}
.confirm-modal.modal-dialog-centered{
    min-height:calc(100% - 1rem);
    display:flex;
    align-items:center;
}

.confirm-modal .modal-content{
    width:100%;
    margin:auto;
}
.confirm-modal .modal-content{
    border-radius:15px;
}

.confirm-modal .modal-header{
    padding:12px 15px;
}

.confirm-modal .modal-body{
    padding:15px;
    font-size:13px;
    text-align:center;
}

.confirm-modal .modal-footer{
    padding:10px 15px 15px;
    flex-direction:row !important;
    justify-content:center;
    gap:10px;
}

.confirm-modal .btn{
    width:120px !important;
    height:38px;
    font-size:12px;
}
.main-content{
    margin-left:70px;
    padding:70px 12px 20px; /* dagdag taas para hindi dikit sa navbar */
}

/* SUMMARY CARDS MOBILE */

.main-content > .row.mt-4{
    margin-top:15px !important;
    --bs-gutter-x:10px;
    --bs-gutter-y:10px;
}

.main-content > .row.mt-4 .col-lg-3{
    width:50%;
    flex:0 0 50%;
    max-width:50%;
}

.summary-card{

    height:135px; /* pare-pareho size */
    padding:15px;
    border-radius:15px;

    display:flex;
    align-items:center;
    justify-content:space-between;

}

.summary-card h6{
    font-size:12px;
}

.summary-card h2{
    font-size:27px;
    margin:5px 0;
}

.summary-card span{
    font-size:10px;
}

.summary-card i{
    font-size:32px;
}



    /* ===========================
       SEARCH CARD
    =========================== */


    .search-card{

        margin-top:20px !important;
        border-radius:15px;

    }


    .search-card .card-body{

        padding:15px;

    }


/* ===========================
   MOBILE SEARCH INLINE FIX
=========================== */
/* ===========================
   MOBILE SEARCH
=========================== */

.search-card .card-body{
    padding:15px;
}

.search-card form{
    width:100%;
}

.search-card .row.g-2{
    display:flex;
    flex-wrap:nowrap;
    align-items:center;
    gap:8px;
    width:100%;
    margin:0;
}

/* Search - mas malaki */
.search-card .col-7.col-md-6{
    flex:1;
    max-width:none;
    width:auto;
    padding:0;
}

/* Status */
.search-card .col-5.col-md-3{
    flex:0 0 42%;
    max-width:42%;
    padding:0;
}

/* Inputs */
#searchInput,
#statusFilter{
    width:100%;
    height:42px;
    font-size:12px;
}

#statusFilter{
    padding-left:8px;
    padding-right:24px;
}
.search-card .col-7.col-md-6,
.search-card .col-5.col-md-3{
    flex:1;
    max-width:none;
    width:50%;
    padding:0;
}

#statusFilter option{
    font-size:12px;
}

.search-card .form-control{
    font-size:12px;
    height:42px;
}

.search-card .form-select{
    font-size:12px;
    height:42px;
    padding-left:8px;
    padding-right:20px;
}

.search-card .card-body{
    padding:15px;
}

    .search-card .btn{

        height:45px;

    }



    /* ===========================
       TABLE CARD
    =========================== */


    .table-card{

        margin-top:20px !important;

    }


    .table-card .card-header{

        padding:15px;

    }


    .table-card h5{

        font-size:16px;

    }


    /* horizontal scroll sa maliit */
    .table-responsive{

        overflow-x:auto;

    }


.table{
    min-width:850px;
    font-size:14px;
    table-layout:fixed;
}
.table thead th,
.table tbody td{
    padding:14px 12px;
    font-size:14px;
    line-height:1.4;
}
/* ===========================
   MOBILE TABLE CENTER ALIGN
=========================== */

.table-card .table th,
.table-card .table td{
    text-align:center;
    vertical-align:middle;
}

  /* LOCATION COLUMN ELLIPSIS */
.table-card td:nth-child(4),
    .table-card th:nth-child(4){
        width:120px;
        max-width:120px;
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
    }
/* SMALLER PRIORITY, STATUS, ACTION COLUMN */

.table-card th:nth-child(4),
.table-card td:nth-child(4){ 
    min-width:65px;
}

/* ADJUST STATUS AND ACTION COLUMN SPACE */

.table-card th:nth-child(7),
.table-card td:nth-child(7){
    min-width:110px;
    padding:10px;
}

.table-card th:nth-child(8),
.table-card td:nth-child(8){
    min-width:90px;
    padding:10px;
}


/* SMALLER STATUS BADGE */
.table-card td:nth-child(7) .badge{
    font-size:10px;
    padding:6px 8px;
}


/* SMALLER ACTION BUTTON */
.table-card td:nth-child(8) .btn{
    font-size:12px;
    padding:5px 10px;
    margin:0 auto;
}
.badge{
    padding:8px 14px;
    border-radius:20px;
    font-size:13px;
}
.table-card .badge{
    font-size:12px;
    padding:8px 12px;
    display:inline-flex;
    justify-content:center;
    align-items:center;
    border-radius:8px;
    min-height:24px;
}


.table-card .btn{
    font-size:12px;
    padding:7px 12px;
    border-radius:8px;
    margin:0 auto;
    min-height:36px;
}

/* MOBILE LOGOUT MODAL */
#logoutModal .modal-dialog{
    width:420px;
    max-width:95%;
    margin:auto;
    display:flex;
    align-items:center;
    min-height:100vh;
}

#logoutModal .modal-content{
    border-radius:14px;
     background:#fff;
}

#logoutModal .modal-body{
   padding:22px 24px 12px;
    text-align:center;
    font-size:16px;
    font-weight:500;
    color:#555;
      white-space:nowrap; /* one line lang */
}

#logoutModal .modal-footer{
      background:#fff;
    border:none;
    padding:12px 24px 20px;
    justify-content:center;
    gap:10px;
     flex-direction:row !important;
    display:flex;
}

#logoutModal .btn{
     width:auto !important;
    flex:1;
    flex:1;                    /* pantay ang width */
    min-width:110px;
    font-size:13px;
    padding:8px 12px;
}
  .swal2-popup{
        width:75% !important;
        max-width:300px !important;
        border-radius:15px !important;
        padding:20px !important;
    }

    .swal2-title{
        font-size:18px !important;
    }

    .swal2-html-container{
        font-size:13px !important;
    }

 .swal2-icon{
    transform:scale(.75);
    margin:5px auto !important;
}

.swal2-icon .swal2-icon-content{
    font-size:28px !important;
}

    .swal2-confirm,
    .swal2-cancel{
        font-size:12px !important;
        padding:8px 18px !important;
        border-radius:8px !important;
    }
/* COMPLAINT INFO MOBILE FIX */

.complaint-info-mobile .row{
    row-gap:4px;
}

.complaint-info-mobile .info-item{
    margin-bottom:4px;
}

.complaint-info-mobile strong{
    font-size:12px;
    margin-bottom:2px;
}

.complaint-info-mobile p{
    font-size:13px;
    margin:0;
    line-height:1.3;
    word-break:break-word;
}

.complaint-info-mobile .col-6{
    padding-left:6px;
    padding-right:6px;
}
/* ===========================
   COMPLAINT MODAL MOBILE SIZE
=========================== */

/* Resident Information hanggang Remarks */
#complaintModal .section-title{
    font-size:14px;
    margin-bottom:10px;
}

#complaintModal label{
    font-size:11px;
    margin-bottom:4px;
}

#complaintModal .form-control{
    font-size:12px;
    padding:8px 10px;
    border-radius:10px;
}

/* Textarea */
#complaintModal textarea.form-control{
    min-height:80px;
}

/* Description and remarks spacing */
#complaintModal hr{
    margin:12px 0;
}

/* Remarks box */
#complaintModal #remarksInput{
    font-size:12px;
    min-height:70px;
}

/* Return reason box */
#complaintModal #returnReasonBox{
    font-size:12px;
    padding:10px;
}

/* Modal footer buttons */
#complaintModal .modal-footer{
    flex-direction:row !important;
    gap:8px;
    padding:10px;
}

#complaintModal .modal-footer .btn{
    width:auto;
    flex:1;
    font-size:11px;
    padding:8px 6px;
    border-radius:8px;
}

#complaintModal .modal-footer i{
    font-size:11px;
}

/* Left profile name */
#complaintModal #modalName{
    font-size:16px;
}

#complaintModal .badge{
    font-size:10px;
    padding:5px 8px;
}

/* Avatar smaller */
#complaintModal .modal-body img.rounded-circle{
    width:90px;
    height:90px;
}
   .modal-dialog.confirm-modal{
        width:85%;
        max-width:320px;
        margin:auto;
    }
}



@media (min-width:993px){


    /* Notice / Confirm / Return */
    .confirm-modal{

        max-width:520px;
        margin:auto;

    }

}

body.modal-open{

    overflow:hidden;

    padding-right:0 !important;

}
.hide-sidebar{

   display:none !important;

}
/* ===========================
   SWEETALERT ABOVE MODAL
=========================== */

.swal2-container{
    z-index:3000 !important;
}
.confirm-modal .modal-content{
    border-radius:20px;
    overflow:hidden;
}

.confirm-modal .modal-header h5{
    color:white;
}

.confirm-modal .btn-close{
    filter:brightness(0) invert(1);
}
/* ===========================
   SIDEBAR SHADOW WHEN MODAL IS OPEN
=========================== */

body.modal-open .sidebar{
    box-shadow:0 0 0 9999px rgba(0,0,0,.45);
    z-index:1040;
}

body.modal-open #sidebarControls{
    z-index:1041;
}

body.modal-open .navbar{
    z-index:1040;
}
/* Grey shadow sa Complaint Details kapag may second modal */
#complaintModal.show .modal-content{
    transition:filter .2s ease;
}

body.second-modal-open #complaintModal .modal-content{
    filter:brightness(.65);
    pointer-events:none;
}
/* FIX CONFIRM MODAL CENTERING */
.modal-dialog.confirm-modal{
    display:flex;
    align-items:center;
    min-height:calc(100% - 1rem);
}

.modal-dialog.confirm-modal .modal-content{
    margin:auto;
}
/* ===========================
   FORWARD & RETURN MODAL WHITE BUTTONS
=========================== */

#forwardConfirmModal .modal-footer,
#returnModal .modal-footer{
    background:#fff;
}

#forwardConfirmModal .modal-footer .btn,
#returnModal .modal-footer .btn{
    background:#fff;
    border:1px solid #dee2e6;
    color:#555;
}

/* Cancel button */
#forwardConfirmModal .btn-secondary,
#returnModal .btn-secondary{
    background:#fff;
    border:1px solid #adb5bd;
    color:#555;
}

/* Forward button */
#forwardConfirmModal #confirmForwardBtn{
    background:#fff;
    border:1px solid #2e7d32;
    color:#2e7d32;
}

#forwardConfirmModal #confirmForwardBtn:hover{
    background:#2e7d32;
    color:#fff;
}

/* Return button */
#returnModal #confirmReturnBtn{
    background:#fff;
    border:1px solid #dc3545;
    color:#dc3545;
}

#returnModal #confirmReturnBtn:hover{
    background:#dc3545;
    color:#fff;
}
/* ===========================
   CONFIRM MODAL BUTTON COLORS
=========================== */

.confirm-modal .btn-secondary{
    background:#6c757d;
    border:none;
    color:#fff;
}

.confirm-modal .btn-secondary:hover{
    background:#5a6268;
}

.confirm-modal .btn-success{
    background:#2e7d32;
    border:none;
    color:#fff;
}

.confirm-modal .btn-success:hover{
    background:#1b5e20;
}

.confirm-modal .btn-danger{
    background:#dc3545;
    border:none;
    color:#fff;
}

.confirm-modal .btn-danger:hover{
    background:#bb2d3b;
}

/* ==========================
   PAGINATION RESPONSIVE FIX
========================== */
.pagination-wrapper{

    padding:15px 25px;

    border-top:1px solid #eee;

    background:#fff;

}

.complaint-photo{
    border-radius:10px;
    transition:.2s;
}

.complaint-photo:hover{
    transform:scale(1.05);
}
.complaint-description{

background:#f8f9fa;
border-radius:10px;
padding:15px;
min-height:120px;
white-space:pre-line;
font-size:15px;

}
.complaint-info-right .info-item p{
    margin-top:8px;
    font-size:15px;
    color:#555;
    word-break:break-word;
}


#modalAddress{
    max-width:100%;
}


#modalPhotos img{
    border-radius:15px;
}


.view-modal .modal-body{
    padding:35px;
}
.carousel-control-prev-icon,
.carousel-control-next-icon {
    filter: invert(1);
}
.complaint-info-mobile .info-item {
    margin-bottom: 8px;
}
/* FIX COMPLAINT INFO SPACING MOBILE */

@media(max-width:576px){

    .complaint-info-mobile .row{
        --bs-gutter-y:0;
    }

    .complaint-info-mobile .info-item{
        margin-bottom:5px !important;
    }

    .complaint-info-mobile .col-6,
    .complaint-info-mobile .col-12{
        margin-bottom:4px;
    }

    .complaint-info-mobile strong{
        margin-bottom:1px;
        line-height:1.2;
        color:#333;
    }

    .complaint-info-mobile p{
        color:#555;
        margin:0;
        line-height:1.3;
    }

}
@media(max-width:576px){

    #modalEmail{
        text-align:left;
        margin-left:-5px;
        padding-left:8px;
    }

    #modalEmail + *{
        margin-left:-5px;
    }

    .complaint-info-mobile .col-12{
        padding-left:0;
        padding-right:0;
    }
/* ===========================
   MOBILE TABLE BADGE + BUTTON SIZE FIX
=========================== */


/* Validation + Action Status badge */
.table-card td:nth-child(6) .badge,
.table-card td:nth-child(7) .badge{

    font-size:10px !important;
    padding:5px 8px !important;
    border-radius:8px;
    white-space:nowrap;

}


/* Smaller Action Buttons */
.table-card td:nth-child(7) .btn,
.table-card td:nth-child(8) .btn,
.continueReview{

    font-size:8px !important;
    padding:2px 5px !important;
    height:22px !important;
    min-height:22px !important;
    width:auto !important;
    line-height:1 !important;
    border-radius:5px !important;
    white-space:nowrap;

}
.continueReview{
    transform:scale(1.2);
    transform-origin:center;
}
}
/* ===========================
   DESKTOP COMPLAINT MODAL FIX
=========================== */

@media(min-width:992px){

#complaintModal .view-modal{
    max-width:1000px;
}


#complaintModal .modal-body{
    padding:30px;
}


#complaintModal .row{
    align-items:flex-start;
}


/* LEFT INFO */

#complaintModal .col-lg-4{
    border-right:1px solid #eee;
    padding-right:25px;
}


#complaintModal #modalAvatar{
    width:130px;
    height:130px;
    object-fit:cover;
}


#complaintModal #modalName{
    font-size:22px;
    margin-top:10px;
}


.complaint-info-mobile{
    margin-top:15px;
}


.complaint-info-mobile .info-item{
    margin-bottom:15px;
}


.complaint-info-mobile strong{
    font-size:13px;
    color:#555;
    display:block;
}


.complaint-info-mobile p{
    font-size:14px;
    margin:3px 0 0;
    color:#333;
    word-break:break-word;
}


/* RIGHT CONTENT */

#complaintModal .col-lg-8{
    padding-left:30px;
}


.section-title{
    font-size:17px;
    font-weight:600;
    margin-bottom:12px;
}


.complaint-description{
    min-height:140px;
    font-size:15px;
    line-height:1.6;
}


#modalPhotos img{
    height:300px !important;
    object-fit:cover;
}


#remarksInput{
    min-height:120px;
}


}
/* =========================
   IMAGE FULLSCREEN VIEWER
========================= */

#imageViewer{

    display:none;

    position:fixed;
    inset:0;

    background:#111;

    z-index:5000;

    align-items:center;
    justify-content:center;

}


#viewerImage{

    max-width:90%;
    max-height:90%;

    object-fit:contain;

}


#closeImageViewer{

    position:absolute;

    top:25px;
    right:35px;

    background:none;
    border:none;

    color:white;

    font-size:30px;

    cursor:pointer;

}


#closeImageViewer:hover{

    opacity:.7;

}
@media (max-width:576px){

.table-card .card-header{
    padding:15px;
}

.table-card .card-header h5{
    margin-bottom:12px;
    font-size:18px;
    text-align:left;   /* nasa kaliwa lang */
    font-weight:700;
}

.table-card .card-header form{
    display:flex;
    gap:8px;
    width:100%;
}

.table-card .card-header .col-7.col-md-6,
.table-card .card-header .col-5.col-md-3{
    flex:1;
    max-width:none;
    padding:0;
}

.table-card .card-header #searchInput,
.table-card .card-header #statusFilter{
    width:100%;
    height:42px;
    font-size:12px;
}

}
</style>
</head>

<body>

<!-- ===========================
     NAVBAR
=========================== -->
<nav class="navbar navbar-dark fixed-top">

<div class="container-fluid">

    <!-- Left -->
    <button id="hamburger">
        <i class="bi bi-list"></i>
    </button>

    <!-- Center Logo -->
    <a class="navbar-brand">
        <img src="assets/enviromanage-logo.png" alt="Logo">
    </a>

    <!-- Right -->
    <div class="navbar-actions">

    <a href="barangay-secretary-notification.php"
   class="text-decoration-none">

    <button class="nav-icon-btn position-relative">

        <i class="bi bi-bell-fill"></i>

      

    </button>

</a>
        <!-- Profile -->
        <div class="dropdown">

            <button
                class="nav-icon-btn dropdown-toggle"
                data-bs-toggle="dropdown">

                <i class="bi bi-person-circle"></i>

            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow">

                <li><hr class="dropdown-divider"></li>

             <li>
    <button
        class="dropdown-item text-danger"
        data-bs-toggle="modal"
        data-bs-target="#logoutModal">

        <i class="bi bi-box-arrow-right me-2"></i>

        Logout

    </button>
</li>
            </ul>

        </div>

    </div>

</div>

</nav>

<!-- ===========================
     SIDEBAR
=========================== -->

<div class="sidebar" id="sidebar">

    <div class="nav flex-column">

        <a class="nav-link" href="barangay-secretary-home.php">

            <i class="bi bi-person-check"></i>

            <span>User Applications</span>

        </a>

        <a class="nav-link active" href="barangay-secretary-complaints.php">

            <i class="bi bi-chat-left-text"></i>

            <span>Resident Complaints</span>

        </a>

             <a class="nav-link" href="barangay-secretary-announcements.php">
    <i class="bi bi-megaphone-fill"></i>
    <span>Announcements</span>
</a>

        <a class="nav-link" href="barangay-secretary-settings.php">

            <i class="bi bi-gear"></i>

            <span>Settings</span>

        </a>

    </div>

</div>

<div id="sidebarControls">

      <button id="closeBtn">

        <i class="bi bi-x-lg"></i>

    </button>

    <button id="toggleBtn">

        <i class="bi bi-chevron-right"></i>

    </button>

  
</div>

<!-- ===========================
     MAIN CONTENT
=========================== -->

<main class="main-content">

<div class="row mt-4 g-4">

<div class="col-lg-3">

<div class="summary-card orange">

<div>

<h6>Pending </h6>

<h2 id="pendingCount">15</h2>

<span>Waiting for review</span>

</div>

<i class="bi bi-hourglass-split"></i>

</div>

</div>

<div class="col-lg-3">

<div class="summary-card green">

<div>

<h6>Forwarded</h6>

<h2 id="forwardedCount">28</h2>

<span>Sent to MENRO</span>

</div>

<i class="bi bi-send-check-fill"></i>

</div>

</div>

<div class="col-lg-3">

<div class="summary-card blue">

<div>

<h6>Resolved</h6>

<h2 id="resolvedCount">16</h2>

<span>Completed</span>

</div>

<i class="bi bi-check-circle-fill"></i>

</div>

</div>

<div class="col-lg-3">

<div class="summary-card red">

<div>

<h6>Returned</h6>

<h2 id="returnedCount">2</h2>

<span>Needs Revision</span>

</div>

<i class="bi bi-arrow-return-left"></i>

</div>

</div>

</div>

<!-- ===========================
     SEARCH
=========================== -->



<div class="card table-card mt-4">

    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">

        <h5 class="mb-0">Resident Complaint List</h5>

        <form class="row g-2 ms-auto align-items-center">

            <div class="col-7 col-md-6">
                <input
                    type="text"
                    id="searchInput"
                    name="search"
                    class="form-control"
                    placeholder="Search complaint..."
                    value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="col-5 col-md-3">
               <select
    id="statusFilter"
    name="status"
    class="form-select">
                    <option <?= $status=="All Status" ? "selected" : "" ?>>All Status</option>
                    <option <?= $status=="Waiting" ? "selected" : "" ?>>Waiting</option>
                    <option <?= $status=="Under Review" ? "selected" : "" ?>>Under Review</option>
                    <option <?= $status=="Approved" ? "selected" : "" ?>>Approved</option>
                    <option <?= $status=="Rejected" ? "selected" : "" ?>>Rejected</option>

                </select>
            </div>

        </form>

    </div>

    <div class="table-responsive">

<table class="table align-middle">

<tr>

<th>Queue</th>

<th>Ticket No.</th>

<th>Resident</th>

<th>Location</th>

<th>Submitted</th>

<th>Validation</th>

<th>Action Status</th>

<th width="100">
Action
</th>

</tr>
</thead>
<tbody id="complaintsTable">

</tbody>

</table>

</div>


<!-- PAGINATION INSIDE TABLE CARD -->

<div class="pagination-wrapper d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">

    <div class="text-center text-md-start">

        <small id="paginationInfo" class="text-muted">

        <?php

        $start = ($totalRecords > 0)
        ? (($page - 1) * $limit) + 1
        : 0;

        $end = min($page * $limit, $totalRecords);

        echo "Showing ".$start." to ".$end." of ".$totalRecords." complaints";

        ?>

        </small>

    </div>


    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">


        <button id="prevPageBtn"
        class="btn btn-sm btn-outline-success"
        <?= ($page <= 1) ? "disabled" : "" ?>>

            Previous

        </button>


    <span id="pageNumber" class="fw-bold">

    Page <?= $page ?> of <?= max($totalPages,1) ?>

</span>


        <button id="nextPageBtn"
        class="btn btn-sm btn-outline-success"
        <?= ($page >= max($totalPages,1)) ? "disabled" : "" ?>>

            Next

        </button>


    </div>

</div>

</div>

</main>

<!-- ==========================================
     VIEW COMPLAINT MODAL
========================================== -->

<div class="modal fade" id="complaintModal" tabindex="-1">

<div class="modal-dialog modal-dialog-scrollable view-modal">
<div class="modal-content">

<div class="modal-header">

<h4 class="modal-title">

Complaint Details

</h4>

<button
class="btn-close"
data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<div class="row">

<!-- LEFT -->

<div class="col-lg-4">

<div class="text-center">


<img
id="modalAvatar"
src=""
class="rounded-circle shadow mb-3">

<h4 id="modalName"></h4>

<span
id="modalStatus"
class="badge">
</span>

</div>

<hr>

<div class="row complaint-info-mobile">

 <div class="col-6">
    <div class="info-item">
        <strong>Complaint ID</strong>
        <p id="modalID"></p>
    </div>
</div>

<div class="col-6">
    <div class="info-item">
        <strong>Date Submitted</strong>
        <p id="modalDate"></p>
    </div>
</div>


<div class="col-6">
    <div class="info-item">
        <strong>Category</strong>
        <p id="modalCategory"></p>
    </div>
</div>
<div class="col-6">
    <div class="info-item">
        <strong>Contact Number</strong>
        <p id="modalContact"></p>
    </div>
</div>
<div class="col-6">
    <div class="info-item">
        <strong>Barangay</strong>
        <p id="modalBarangay"></p>
    </div>
</div>
<div class="col-12">
    <div class="info-item">
        <strong>Email Address</strong>
        <p id="modalEmail"></p>
    </div>
</div>

</div> <!-- END complaint-info-mobile -->

</div> <!-- END LEFT COLUMN -->


<!-- RIGHT -->

<div class="col-lg-8">


<h5 class="section-title">
Complaint Description
</h5>


<div 
id="modalDescription"
class="complaint-description">
</div>


<!-- COMPLAINT PHOTOS -->
<hr>

<h5 class="section-title">
    Complaint Photos
</h5>


<div 
id="photoCarousel"
class="carousel slide"
data-bs-ride="carousel">


<div 
class="carousel-inner"
id="modalPhotos">

</div>


<button 
class="carousel-control-prev"
type="button"
data-bs-target="#photoCarousel"
data-bs-slide="prev">

<span class="carousel-control-prev-icon"></span>

</button>


<button 
class="carousel-control-next"
type="button"
data-bs-target="#photoCarousel"
data-bs-slide="next">

<span class="carousel-control-next-icon"></span>

</button>


</div>


<!-- SECRETARY REMARKS -->
<hr>

<h5 class="section-title">
    Secretary Remarks
</h5>


<textarea
id="remarksInput"
class="form-control"
rows="5"
placeholder="Enter remarks before forwarding or returning...">
</textarea>


<div
id="returnReasonBox"
class="alert alert-danger mt-3"
style="display:none;">

<strong>
Reason for Return
</strong>

<hr>

<p id="returnReasonText" class="mb-0"></p>

</div>


</div> <!-- end right column -->

</div> <!-- end row -->

</div> <!-- end modal-body -->


<div class="modal-footer">

<button
class="btn btn-success"
id="forwardBtn">

<i class="bi bi-send-check"></i>
Forward to MENRO

</button>


<button
class="btn btn-danger"
id="returnBtn">

<i class="bi bi-arrow-return-left"></i>
Return to Resident

</button>

</div>
</div>

</div>

</div>

<!-- ==========================================
     FORWARD CONFIRMATION
========================================== -->

<div class="modal fade" id="forwardConfirmModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered confirm-modal">

        <div class="modal-content">

            <div class="modal-header">

                <h5>
                    Forward Complaint
                </h5>

            
            </div>


            <div class="modal-body">

                <p>
                    Are you sure you want to forward this complaint to MENRO?
                </p>

            </div>


            <div class="modal-footer">

                <button
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Cancel

                </button>


                <button
                    id="confirmForwardBtn"
                    class="btn btn-success">

                    Forward

                </button>

            </div>

        </div>

    </div>

</div>
<!-- ==========================================
     RETURN MODAL
========================================== -->

<div class="modal fade" id="returnModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered confirm-modal">

        <div class="modal-content">
<div class="modal-header">

<h5>

Return Complaint

</h5>

</div>

<div class="modal-body">

<label class="form-label">

Reason for returning

</label>

<textarea

id="returnReasonInput"

class="form-control"

rows="4">

</textarea>

</div>

<div class="modal-footer">

<button

class="btn btn-secondary"

data-bs-dismiss="modal">

Cancel

</button>

<button

class="btn btn-danger"

id="confirmReturnBtn">

Return

</button>

</div>

</div>

</div>

</div>

<!-- IMAGE FULLSCREEN PREVIEW -->

<div id="imageViewer">

    <button id="closeImageViewer">
        <i class="bi bi-x-lg"></i>
    </button>

    <img id="viewerImage">

</div>
<!-- ==========================================
     NOTICE MODAL
========================================== -->

<div class="modal fade" id="noticeModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered confirm-modal">

        <div class="modal-content">


            <div class="modal-header">

                <h5>
                    Notice
                </h5>

                <button 
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>


            <div class="modal-body">

                <p id="noticeText"></p>

            </div>


            <div class="modal-footer">

                <button
                    class="btn btn-success"
                    data-bs-dismiss="modal">

                    OK

                </button>

            </div>


        </div>

    </div>

</div>

<!-- LOGOUT MODAL -->
<div class="modal fade" id="logoutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered confirm-modal">
        <div class="modal-content">

            <div class="modal-body">
                Are you sure you want to log out?
            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Cancel
                </button>

                <button
                    type="button"
                    class="btn btn-danger"
                    id="confirmLogout">
                    Yes
                </button>

            </div>

        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ======================================
// COUNTERS
// ======================================

let pending = Number(document.getElementById("pendingCount").textContent);
let forwarded = Number(document.getElementById("forwardedCount").textContent);
let returned = Number(document.getElementById("returnedCount").textContent);
let resolved = Number(document.getElementById("resolvedCount").textContent);

let returnReasons = {};
let secretaryRemarks = {};
const pendingCount = document.getElementById("pendingCount");
const forwardedCount = document.getElementById("forwardedCount");
const returnedCount = document.getElementById("returnedCount");
const resolvedCount = document.getElementById("resolvedCount");

function updateCounters(){

    pendingCount.textContent = pending;
    forwardedCount.textContent = forwarded;
    returnedCount.textContent = returned;
    resolvedCount.textContent = resolved;

}

// ======================================
// BOOTSTRAP MODALS
// ======================================

const complaintModal =
new bootstrap.Modal(document.getElementById("complaintModal"));

const forwardConfirmModal =
new bootstrap.Modal(document.getElementById("forwardConfirmModal"));

const returnModal =
new bootstrap.Modal(document.getElementById("returnModal"));

const noticeModal =
new bootstrap.Modal(document.getElementById("noticeModal"));
let currentComplaintID = null;
let currentRow = null;

// ======================================
// VIEW BUTTONS
// ======================================

function attachViewButtons(){

    document.querySelectorAll(".viewComplaint").forEach(btn=>{

        btn.onclick=function(){

            currentRow = this.closest("tr");

            const complaintID = this.dataset.id;

            loadComplaintForReview(complaintID);

        };

    });

}

attachViewButtons();
// ===============================
// SIDEBAR LOGIC
// ===============================

const sidebar = document.getElementById("sidebar");
const toggleBtn = document.getElementById("toggleBtn");
const closeBtn = document.getElementById("closeBtn");
const hamburger = document.getElementById("hamburger");

function isTabletOrMobile(){
    return window.innerWidth <= 992;
}
// > / < BUTTON

toggleBtn.addEventListener("click",()=>{

    sidebar.classList.toggle("expanded");


    const icon = toggleBtn.querySelector("i");


    if(sidebar.classList.contains("expanded")){

        icon.classList.remove("bi-chevron-right");
        icon.classList.add("bi-chevron-left");

    }else{

        icon.classList.remove("bi-chevron-left");
        icon.classList.add("bi-chevron-right");

    }

});


// X BUTTON

closeBtn.addEventListener("click", ()=>{

    if(!isTabletOrMobile()) return;


    sidebar.classList.add("hide-sidebar");

    sidebar.classList.remove("expanded");


    document.getElementById("sidebarControls").style.display = "none";


    const icon = toggleBtn.querySelector("i");

    icon.classList.remove("bi-chevron-left");
    icon.classList.add("bi-chevron-right");


});
// HAMBURGER

hamburger.addEventListener("click", ()=>{

    if(!isTabletOrMobile()) return;

    sidebar.classList.remove("hide-sidebar");

    // icon/sidebar controls lang ang ipakita
    document.getElementById("sidebarControls").style.display = "flex";

    // siguraduhing collapsed lang
    sidebar.classList.remove("expanded");

    const icon = toggleBtn.querySelector("i");
    icon.classList.remove("bi-chevron-left");
    icon.classList.add("bi-chevron-right");

});

// RESET

window.addEventListener("resize",()=>{

if(window.innerWidth > 992){

    sidebar.classList.remove("expanded");
    sidebar.classList.remove("show");
    sidebar.classList.remove("hide-sidebar");

}

});

// ======================================
// UPDATE AFTER ACTION
// ======================================

function refreshComplaintTable(){

    liveSearch(false);

}

// ======================================
// BUTTON EVENTS
// ======================================

document.getElementById("forwardBtn")
.addEventListener("click",()=>{

    if(!currentRow) return;

    const status =
    currentRow.cells[6].textContent.trim();

    if(status==="Forwarded"){

        document.getElementById("noticeText").textContent =
        "This complaint has already been forwarded to MENRO.";

        noticeModal.show();

        return;

    }

    if(status==="Returned"){

        document.getElementById("noticeText").textContent =
        "This complaint has already been returned.";

        noticeModal.show();

        return;

    }

    if(status==="Resolved"){

        document.getElementById("noticeText").textContent =
        "This complaint has already been resolved.";

        noticeModal.show();

        return;

    }

  const remarks =
document.getElementById("remarksInput").value.trim();

if(remarks===""){

    Swal.fire({

        icon:"warning",
        title:"Remarks Required",
        text:"Please enter secretary remarks before forwarding the complaint.",

        timer:3000,

        showConfirmButton:true,
        confirmButtonText:"OK"

    });

    return;

}

document.body.classList.add("second-modal-open");
forwardConfirmModal.show();
});

document.getElementById("confirmForwardBtn")
.addEventListener("click",()=>{


    const complaintID = currentRow
        .querySelector(".reviewComplaint, .continueReview, .viewComplaint")
        ?.dataset.id;


    const remarks =
    document.getElementById("remarksInput")
    .value.trim();



    const formData = new FormData();

    formData.append("id", complaintID);
    formData.append("remarks", remarks);



    fetch("barangay-secretary-forward-complaint.php",{

        method:"POST",

        body:formData

    })

    .then(response=>response.json())

    .then(data=>{


        if(data.success){


          const complaintID = currentRow.dataset.id;

const remarks =
document.getElementById("remarksInput").value.trim();


const formData = new FormData();

formData.append("id", complaintID);

formData.append("remarks", remarks);



fetch("barangay-secretary-forward-menro.php",{
    method:"POST",
    body:formData
})
.then(response=>response.json())
.then(data=>{

    if(data.success){

        forwardConfirmModal.hide();
        document.body.classList.remove("second-modal-open");
        complaintModal.hide();

        Swal.fire({
            icon:"success",
            title:"Forwarded to MENRO",
            text:data.message,
            timer:3000,
            showConfirmButton:true
        });

        refreshComplaintTable();

    }else{

        Swal.fire({
            icon:"warning",
            title:"Cannot Forward",
            text:data.message
        });

    }


});



            Swal.fire({

                icon:"success",

                title:"Complaint Forwarded",

                text:
                "The complaint has been forwarded to MENRO.",

                timer:3000,

                showConfirmButton:true

            })
            .then(()=>{

                liveSearch(false);

            });



        }else{


            Swal.fire({

                icon:"warning",

                title:"Cannot Forward",

                text:
                "Complaint is no longer available for forwarding."

            });


        }


    })


    .catch(()=>{


        Swal.fire({

            icon:"error",

            title:"Error",

            text:
            "Unable to forward complaint."

        });


    });

});

document.getElementById("returnBtn")
.addEventListener("click",()=>{

    if(!currentRow) return;

    const status =
    currentRow.cells[6].textContent.trim();

    if(status==="Forwarded"){

        document.getElementById("noticeText").textContent =
        "This complaint has already been forwarded to MENRO.";

        noticeModal.show();

        return;

    }

    if(status==="Returned"){

        document.getElementById("noticeText").textContent =
        "This complaint has already been returned.";

        noticeModal.show();

        return;

    }

    if(status==="Resolved"){

        document.getElementById("noticeText").textContent =
        "This complaint has already been resolved.";

        noticeModal.show();

        return;

    }

    document.getElementById("returnReasonInput").value="";

const remarks =
document.getElementById("remarksInput").value.trim();

if(remarks===""){

    Swal.fire({

        icon:"warning",
        title:"Remarks Required",
        text:"Please enter secretary remarks before returning the complaint.",

        timer:3000,

        showConfirmButton:true,
        confirmButtonText:"OK"

    });

    return;

}

document.body.classList.add("second-modal-open");
returnModal.show();
});
document.getElementById("confirmReturnBtn")
.addEventListener("click",()=>{


    const complaintID = currentRow
        .querySelector(".reviewComplaint, .continueReview, .viewComplaint")
        ?.dataset.id;


    const reason =
    document.getElementById("returnReasonInput")
    .value.trim();


    const remarks =
    document.getElementById("remarksInput")
    .value.trim();



    if(reason === ""){


        Swal.fire({

            icon:"warning",

            title:"Reason Required",

            text:
            "Please enter a reason before returning this complaint.",

            timer:3000,

            showConfirmButton:true

        });


        return;

    }



    const formData = new FormData();


    formData.append("id", complaintID);

    formData.append("remarks", remarks);

    formData.append("reason", reason);



    fetch(
        "barangay-secretary-return-complaint.php",
        {
            method:"POST",
            body:formData
        }
    )


    .then(response=>response.json())


    .then(data=>{


        if(data.success){


            returnModal.hide();

            document.body.classList.remove(
                "second-modal-open"
            );


            complaintModal.hide();


            Swal.fire({

                icon:"success",

                title:"Complaint Returned",

                text:
                "The complaint has been returned to the resident.",

                timer:3000,

                showConfirmButton:true

            })
            .then(()=>{

                liveSearch(false);

            });


        }else{


            Swal.fire({

                icon:"warning",

                title:"Cannot Return",

                text:
                data.message ||
                "Complaint cannot be returned."

            });


        }


    })


    .catch(()=>{


        Swal.fire({

            icon:"error",

            title:"Error",

            text:
            "Unable to return complaint."

        });


    });


});
document.querySelectorAll("textarea").forEach(textarea => {

    textarea.style.height = "auto";
    textarea.style.height = textarea.scrollHeight + "px";

    textarea.addEventListener("input", function () {
        this.style.height = "auto";
        this.style.height = this.scrollHeight + "px";
    });

});



document.getElementById("confirmLogout").addEventListener("click", function () {
    window.location.href = "login.php";
});
// ======================================
// PAGINATION BUTTONS
// ======================================

const prevPageBtn = document.getElementById("prevPageBtn");
const nextPageBtn = document.getElementById("nextPageBtn");


function changePage(page){

    const params = new URLSearchParams(window.location.search);

    params.set("page", page);

    window.location.href =
    "barangay-secretary-complaints.php?" + params.toString();

}


// PREVIOUS

if(prevPageBtn){

    prevPageBtn.addEventListener("click",()=>{

        const currentPage = <?= $page ?>;

        if(currentPage > 1){

            changePage(currentPage - 1);

        }

    });

}


// NEXT

if(nextPageBtn){

    nextPageBtn.addEventListener("click",()=>{

        const currentPage = <?= $page ?>;

        const totalPages = <?= $totalPages ?>;

        if(currentPage < totalPages){

            changePage(currentPage + 1);

        }

    });

}
document.getElementById("forwardConfirmModal")
.addEventListener("hidden.bs.modal", function () {
    document.body.classList.remove("second-modal-open");
});

document.getElementById("returnModal")
.addEventListener("hidden.bs.modal", function () {
    document.body.classList.remove("second-modal-open");
});


const searchInput = document.getElementById("searchInput");

const statusFilter = document.getElementById("statusFilter");

function submitFilter(){

    const params = new URLSearchParams(window.location.search);


    // SEARCH
    if(searchInput.value.trim() !== ""){
        params.set("search", searchInput.value.trim());
    }else{
        params.delete("search");
    }


   function submitFilter(){

    const params = new URLSearchParams(window.location.search);

    if(searchInput.value.trim() !== ""){
        params.set("search", searchInput.value.trim());
    }else{
        params.delete("search");
    }

    if(statusFilter.value !== "All Status"){
        params.set("status", statusFilter.value);
    }else{
        params.delete("status");
    }

    params.set("page",1);

    window.location.href =
    "barangay-secretary-complaints.php?" + params.toString();

}

    // STATUS
    if(statusFilter.value !== "All Status"){
        params.set("status", statusFilter.value);
    }else{
        params.delete("status");
    }


    // balik sa page 1 kapag nag-filter
    params.set("page",1);


    window.location.href =
    "barangay-secretary-complaints.php?" + params.toString();

}
function liveSearch(resetPage = true){

    if(resetPage){

        const params = new URLSearchParams(window.location.search);

        params.set("page",1);

        history.replaceState(
            null,
            "",
            "barangay-secretary-complaints.php?"+params.toString()
        );

    }

    let search = document.getElementById("searchInput").value.trim();

    let status = document.getElementById("statusFilter").value;

    let page = new URLSearchParams(window.location.search).get("page") || 1;

    fetch(
        "complaints-search.php?search=" +
        encodeURIComponent(search) +
        "&status=" +
        encodeURIComponent(status) +
        "&page=" +
        page
    )
    .then(response => response.text())
    .then(data => {

        document.getElementById("complaintsTable").innerHTML = data;

        attachViewButtons();
        attachContinueReviewButtons();

    });




// ======================================
// CONTINUE REVIEW BUTTON
// ======================================

function attachContinueReviewButtons(){

    document.querySelectorAll(".continueReview")
    .forEach(btn=>{

        btn.onclick=function(){

            const complaintID = this.dataset.id;

            loadComplaintForReview(complaintID);

        };

    });

}

attachContinueReviewButtons();


}


document
.getElementById("searchInput")
.addEventListener(
"input",
()=>liveSearch(true)
);





document
.getElementById("statusFilter")
.addEventListener(
"change",
()=>liveSearch(true)
);



// load agad pag bukas
liveSearch();
document.addEventListener("click", function(e){

    if(!e.target.classList.contains("reviewComplaint")){
        return;
    }


    currentRow = e.target.closest("tr");


    const complaintID = e.target.dataset.id;
    const formData = new FormData();
    formData.append("id", complaintID);

    fetch("barangay-secretary-start-review.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {

    if(data.success){

        loadComplaintForReview(complaintID);

        // i-refresh ang table para maging
        // Continue Review ang button
        liveSearch(false);

    }else{

        Swal.fire({
            icon:"warning",
            title:"Cannot Review",
            text:data.message
        });

    }

})
});

function loadComplaintForReview(complaintID){

    fetch("barangay-secretary-get-complaint.php?id=" + complaintID)

    .then(response => response.text())

    .then(text => {

        console.log("RAW RESPONSE:", text);

        const data = JSON.parse(text);


        if(!data.success){

            Swal.fire(
                "Error",
                data.message,
                "error"
            );

            return;

        }


        const complaint = data.complaint;



        // FILL EXISTING COMPLAINT MODAL

        document.getElementById("modalID").textContent =
        complaint.ticket_no;


        document.getElementById("modalName").textContent =
        complaint.first_name + " " + complaint.last_name;



        document.getElementById("modalCategory").textContent =
        complaint.category;



        document.getElementById("modalBarangay").textContent =
        complaint.complaint_location;



document.getElementById("modalDate").textContent =
new Date(complaint.submitted_at)
.toLocaleDateString(
"en-US",
{
month:"long",
day:"numeric",
year:"numeric"
}
);

document.getElementById("modalEmail").textContent =
complaint.email || "No Email";


document.getElementById("modalContact").textContent =
complaint.phone || "No Contact";

     document.getElementById("modalDescription").textContent =
complaint.description || "No Description";
const photoContainer =
document.getElementById("modalPhotos");


photoContainer.innerHTML = "";


if(
    complaint.photos &&
    complaint.photos.length > 0
){


    complaint.photos.forEach((photo,index)=>{


        photoContainer.innerHTML += `

        <div class="carousel-item ${index===0?'active':''}">

            <img
            src="${photo}"
            class="d-block w-100 complaint-photo"
            style="
            height:300px;
            object-fit:cover;
            cursor:pointer;
            "
          onclick="openImageViewer('${photo}')"

        </div>

        `;


    });



}else{


    photoContainer.innerHTML = `

    <div class="text-muted p-3">
        No photos uploaded.
    </div>

    `;


}

        document.getElementById("remarksInput").value =
        complaint.remarks || "";



        const avatar =
        complaint.first_name + " " + complaint.last_name;



        document.getElementById("modalAvatar").src =

        `https://ui-avatars.com/api/?name=${encodeURIComponent(avatar)}&background=2e7d32&color=fff&size=180`;



        // STATUS BADGE

     // STATUS BADGE

const modalBadge =
document.getElementById("modalStatus");


modalBadge.textContent =
complaint.validation_status;


if(complaint.validation_status=="Under Review"){

    modalBadge.className =
    "badge bg-primary";

}


complaintModal.show();
    })


   .catch(error=>{

    console.error(error);

    Swal.fire(
        "Error",
        "Unable to load complaint. Check console.",
        "error"
    );

});

}
function openImageViewer(src){

    document.getElementById("viewerImage").src = src;

    document.getElementById("imageViewer").style.display="flex";

}


document
.getElementById("closeImageViewer")
.addEventListener("click",()=>{

    document.getElementById("imageViewer").style.display="none";

});
</script>

</body>

</html>