<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

date_default_timezone_set('Asia/Manila');

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'barangay_secretary'
) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");


if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$userId = (int)$_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| GET LOGGED-IN BARANGAY SECRETARY
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        id,
        first_name,
        last_name,
        barangay,
        profile_photo
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$secretary = $stmt->get_result()->fetch_assoc();

if (!$secretary) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$barangay = $secretary['barangay'];

/*
|--------------------------------------------------------------------------
| SUMMARY COUNTS
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        COUNT(CASE
            WHEN role='resident'
             AND approval_status='pending'
            THEN 1
        END) AS pending,

        COUNT(CASE
            WHEN role='resident'
             AND approval_status='approved'
             AND DATE(updated_at)=CURDATE()
            THEN 1
        END) AS approved_today,

        COUNT(CASE
            WHEN role='resident'
             AND approval_status='rejected'
             AND DATE(updated_at)=CURDATE()
            THEN 1
        END) AS rejected_today

    FROM users
    WHERE barangay = ?
");

$stmt->bind_param("s", $barangay);
$stmt->execute();

$summary = $stmt->get_result()->fetch_assoc();

$pendingCount  = (int)$summary['pending'];
$approvedToday = (int)$summary['approved_today'];
$rejectedToday = (int)$summary['rejected_today'];

$stmt->close();

/*
|--------------------------------------------------------------------------
| RESIDENT APPLICATIONS (PAGINATION)
|--------------------------------------------------------------------------
*/

$limit = 10;

$page = isset($_GET['page']) 
    ? max(1,(int)$_GET['page']) 
    : 1;

$offset = ($page - 1) * $limit;



// SEARCH + FILTER
$search = $_GET['search'] ?? '';

$statusFilter = $_GET['status'] ?? 'All Status';
// PAGINATION LINK WITH SEARCH PRESERVED
function pageLink($p){

    global $search, $statusFilter;

    return "?page=".$p
    ."&search=".urlencode($search)
    ."&status=".urlencode($statusFilter);

}

// COUNT TOTAL RECORDS

$countSQL = "
SELECT COUNT(*) as total

FROM users

WHERE role='resident'

AND barangay=?
";


$countParams = [$barangay];
$countTypes = "s";


// STATUS FILTER
if($statusFilter != "All Status"){

    $countSQL .= "
    AND approval_status = ?
    ";

    $countParams[] = $statusFilter;
    $countTypes .= "s";

}


// SEARCH
if($search != ""){


    $countSQL .= "

    AND(

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


    $like = "%".$search."%";


    for($i=0;$i<10;$i++){

        $countParams[] = $like;

    }


    $countTypes .= "ssssssssss";

}


// EXECUTE COUNT

$countStmt = $conn->prepare($countSQL);


$countStmt->bind_param(
    $countTypes,
    ...$countParams
);


$countStmt->execute();


$totalRows =
$countStmt
->get_result()
->fetch_assoc()['total'];


$countStmt->close();



$totalPages = ceil($totalRows / $limit);

// Fetch current page
$sql = "

SELECT

    id,
    first_name,
    middle_initial,
    last_name,
    email,
    phone,
    gender,
    birthdate,
    street,
    house_no,
    barangay,
    profile_photo,
    valid_id,
    approval_status,
    rejection_reason,
    created_at,
    updated_at

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


// SEARCH
if($search != ""){


    $sql .= "

    AND(

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


    $types.="ssssssssss";

}


// PAGINATION

$sql .= "

ORDER BY created_at DESC

LIMIT ? OFFSET ?

";


$params[]=$limit;
$params[]=$offset;

$types.="ii";



$stmt = $conn->prepare($sql);


$stmt->bind_param(
    $types,
    ...$params
);


$stmt->execute();


$applications = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>User Applications</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">

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


.navbar-brand img{

    height:45px;
    width:45px;
    object-fit:contain;

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
   SIDEBAR CONTROL BUTTON
=========================== */


#sidebarControls{

    position:fixed;

    top:85px;

    left:270px;

    display:none;

    flex-direction:column;
      gap:8px; /* space between X and >/< */

    z-index:1300;

    transition:.3s ease;

}

#sidebarControls button{

    width:32px;

    height:32px;

    border:none;

    display:flex;

    align-items:center;

    justify-content:center;

    color:white;

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
   COLLAPSED SIDEBAR DESKTOP
=========================== */

.sidebar.collapsed{

    width:70px;

}


.sidebar.collapsed .nav-link{

    justify-content:center;

    padding:12px 10px;

}


.sidebar.collapsed .nav-link span{

    display:none;

}


.sidebar.collapsed ~ #sidebarControls{

    left:70px;

}


.sidebar.collapsed ~ .main-content{

    margin-left:70px;

}


/* ICON POSITION WHEN COLLAPSED */

.sidebar.collapsed ~ #sidebarControls #toggleBtn i{

    transform:rotate(0deg);

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

    color:white;

    border-radius:20px;

    padding:25px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    box-shadow:0 12px 25px rgba(0,0,0,.08);

    transition:.3s;

}



.summary-card.green{

    background:linear-gradient(135deg,#2e7d32,#43a047);

}



.summary-card.red{

    background:linear-gradient(135deg,#e53935,#ef5350);

}



.summary-card:hover{

    transform:translateY(-4px);

}



.summary-card h2{

    font-size:38px;

    font-weight:700;

}



.summary-card h6{

    margin-bottom:8px;

}



.summary-card span{

    opacity:.9;

}



.summary-card i{

    font-size:55px;

    opacity:.25;

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
    padding:20px 25px;
    display:flex;
    justify-content:flex-end;
    align-items:center;
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

    background:white;

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
    padding:12px 14px;
    font-size:14px;
}

.table tbody td{
    padding:10px 14px;
    font-size:13px;
    line-height:1.3;
}

.table .btn-sm{
    padding:4px 10px;
    font-size:12px;
}

.table .badge{
    font-size:11px;
    padding:6px 10px;
}


.table tbody tr:hover{

    background:#f8fcf9;

}


.address-box{
    min-height: 100px;
    height: auto;
    white-space: 100%;
    background: #fff;
    overflow: visible;
    display: flex;
    align-items: flex-start;
    padding: 12px;
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



.btn-primary{

    background:#388e3c;

    border:none;

}



.btn-primary:hover{

    background:#2e7d32;

}



.btn-danger{

    border:none;

}



.table .btn{

    margin-right:5px;

    margin-bottom:5px;

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
   MODAL
=========================== */


.modal-content{

    border:none;

    border-radius:20px;

    overflow:hidden;

}



.modal-header{

    background:linear-gradient(135deg,#2e7d32,#43a047);

    color:white;

    border:none;

    padding:20px 30px;

}



.modal-header .btn-close{

    filter:brightness(0) invert(1);

}



.modal-body{

    padding:30px;

}



.modal-footer{

    border:none;

    padding:20px 30px;

}




/* ===========================
   PROFILE
=========================== */


.modal-body img{

    width:180px;

    height:180px;

    object-fit:cover;

    border:6px solid #e8f5e9;

}



.info-item{

    margin-bottom:18px;

}



.info-item strong{

    display:block;

    color:#2e7d32;

}



.info-item p{

    margin:0;

    color:#666;

}



.section-title{

    color:#2e7d32;

    font-weight:600;

    margin-bottom:20px;

}




/* ===========================
   DOCUMENT CARD
=========================== */


.document-card{

    border:2px dashed #c8e6c9;

    border-radius:15px;

    text-align:center;

    padding:30px;

    background:#fafafa;

    transition:.3s;

}



.document-card:hover{

    background:#f1f8f4;

    transform:translateY(-3px);

}



.document-card i{

    font-size:50px;

    color:#2e7d32;

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
.search-card .row.g-2{
    display:flex;
    flex-wrap:nowrap;
    align-items:center;
    justify-content:flex-end;
    gap:16px;                 /* space ng search at filter */
    width:auto;
    margin-left:auto;
}

.search-card .col-7.col-md-6{
    flex:0 0 400px;
    max-width:400px;
    margin-left:20px;         /* konting pakanan */
}

.search-card .col-5.col-md-3{
    flex:0 0 190px;
    max-width:190px;
}

}

/* TABLET AND BELOW */
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



    /* COLLAPSED SIDEBAR */

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



    /* BUTTON POSITION */

    #sidebarControls{

        display:flex;

        left:70px;

    }
/* DEFAULT ICON ONLY */

.sidebar{

    width:70px;

}


.sidebar .nav-link span{

    display:none;

}


.sidebar.collapsed ~ #sidebarControls{

    left:70px;

}






.sidebar.expanded .nav-link span{

    display:inline;

}


.sidebar.expanded ~ #sidebarControls{

    left:270px;

}






.sidebar.collapsed .nav-link span{

    display:none;

}


.sidebar.collapsed .nav-link{

    justify-content:center;

}


.sidebar.collapsed ~ #sidebarControls{

    left:70px;

}

    /* EXPANDED SIDEBAR */

    .sidebar.expanded{

        width:270px;
           box-shadow:8px 0 20px rgba(0,0,0,.15);
    z-index:1200;

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


.sidebar.expanded ~ #sidebarControls #toggleBtn{

    display:flex;

}


.sidebar.expanded ~ #sidebarControls #closeBtn{

    display:flex;

}



    /* CONTENT FOLLOW SIDEBAR */

.main-content{

    margin-left:70px;

    min-width:0;

}


    .sidebar.expanded ~ .main-content{

        margin-left:70px;


    }
 

.sidebar.hide-sidebar{
    transform:translateX(-100%);
}
.sidebar.hide-sidebar ~ .main-content{

    margin-left:0;

}


/* WHEN SIDEBAR IS HIDDEN, SHOW AGAIN USING HAMBURGER */
.sidebar.hide-sidebar ~ #sidebarControls{
    left:0;
      display:none;
}



/* EXPANDED STATE */
.sidebar.expanded{

    width:270px;

}


.sidebar.expanded .nav-link{

    justify-content:flex-start;

}


.sidebar.expanded .nav-link span{

    display:inline;

}


/* BUTTON MOVES WITH SIDEBAR */
.sidebar.expanded ~ #sidebarControls{

    left:270px;

}



/* COLLAPSED ICON ONLY */
.sidebar:not(.expanded):not(.hide-sidebar){

    width:70px;

}


.sidebar:not(.expanded):not(.hide-sidebar) .nav-link{

    justify-content:center;

}


.sidebar:not(.expanded):not(.hide-sidebar) .nav-link span{

    display:none;

}

}




/* MOBILE */

@media(max-width:576px){


/* CENTER LOGO SA MOBILE */
.navbar .container-fluid{
    position: relative;
    justify-content: flex-start;
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

.navbar{

        padding:0 15px;

    }

    .navbar-brand img{

        width:40px;
        height:40px;
    }

.navbar-brand{
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    margin: 0;
}


/* SEARCH + FILTER */
.search-card .row.g-2{
    display:flex;
    flex-wrap:nowrap;
    align-items:center;
    gap:8px;
    width:100%;
}

.search-card .col-7.col-md-6{
    flex:1 1 auto;
    max-width:none;
    width:auto;
    margin-left:0;
}

.search-card .col-5.col-md-3{
    flex:0 0 135px;
    max-width:135px;
}
.search-card .col-7.col-md-6 .form-control{
    width:100%;
}

.search-card .col-5.col-md-3 .form-select{
    width:100%;
}
#searchInput,
#statusFilter{
    height:42px;
    font-size:12px;
}

.main-content{

    margin-left:70px;
    padding:50px 15px 20px;
    transition:.3s ease;

}
.sidebar.hide-sidebar ~ .main-content{

    margin-left:0;

}

/* MOBILE */
.sidebar{
    width:70px;
    transform:translateX(0);
    box-shadow:5px 0 15px rgba(0,0,0,.1);
}

.sidebar .nav-link{
    justify-content:center;
}

.sidebar .nav-link span{
    display:none;
}

/* kapag expanded */
.sidebar.expanded{
    width:270px;
}

.sidebar.expanded .nav-link{
    justify-content:flex-start;
}

.sidebar.expanded .nav-link span{
    display:inline;
}

/* kapag tinago gamit ang X */
.sidebar.hide-sidebar{
    transform:translateX(-100%);
}

   #viewModal .view-modal{
        max-width:85%;
      margin:40px auto 15px;
    }

    #viewModal .modal-content{
        border-radius:12px;
    }

    #viewModal .modal-header{
        padding:12px 15px;
    }

    #viewModal .modal-title{
        font-size:16px;
    }

    #viewModal .modal-body{
        padding:12px;
        max-height:70vh;
        overflow-y:auto;
    }

    #viewModal .modal-footer{
        padding:10px 12px;
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

    .table-responsive{
        overflow-x:auto;
        width:100%;
    }


        .table-card .table{
        min-width:650px;
        table-layout:fixed;
    }


    /* Compact all columns */
    .table-card th,
    .table-card td{

        font-size:10px;
        padding:6px 4px;
        white-space:nowrap;
        text-align:center;
        vertical-align:middle;

    }


    /* Application ID */
    .table-card th:nth-child(1),
    .table-card td:nth-child(1){

        width:110px;
        min-width:110px;
        white-space:nowrap;

    }


    /* Applicant */
    .table-card th:nth-child(2),
    .table-card td:nth-child(2){

        width:150px;
        min-width:150px;

        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;

    }


    /* House No */
     .table-card th:nth-child(3),
    .table-card td:nth-child(3){

        width:80px;
        min-width:80px;

    }


    /* Address */
       .table-card th:nth-child(4),
    .table-card td:nth-child(4){

        width:120px;
        min-width:120px;

        white-space:normal;
        line-height:1.2;

    }


    /* Date */
     .table-card th:nth-child(5),
    .table-card td:nth-child(5){

        width:100px;
        min-width:100px;

    }


    /* Status */
      .table-card th:nth-child(6),
    .table-card td:nth-child(6){

        width:90px;
        min-width:90px;

    }


    /* Action */
      .table-card th:nth-child(7),
    .table-card td:nth-child(7){

        width:90px;
        min-width:90px;

    }



    .table-card .btn{

        font-size:10px;
        padding:5px 8px;

    }


    .table-card .badge{

        font-size:9px;
        padding:5px 7px;

    }


    .table-card .card-header{

        padding:12px;

    }


    .table-card .card-header h5{

        font-size:16px;

    }



/* Compact text */
.table-card th,
.table-card td{
    font-size:11px;
    padding:8px 6px;
    white-space:nowrap;
}

.table-responsive{
    width:100%;
    overflow-x:auto;
    -webkit-overflow-scrolling:touch;
}

.table-card{
    width:100%;
}

    .table-card .table{
        min-width:650px;
        table-layout:fixed;
    }

/* Action button */
.table-card .btn{
    font-size:12px;
    padding:7px 12px;
}

/* Header */
.table-card .card-header h5{
    font-size:18px;
}

.table-card .card-header{
    padding:17px;
}
/* CENTER TABLE CONTENT MOBILE */
.table-card .table th,
.table-card .table td{
    text-align:center;
    vertical-align:middle;
}
.table-card .badge,
.table-card .btn{
    margin:auto;
}
.summary-card{

    height:85px;
    padding:10px;
    border-radius:15px;

    display:flex;
    align-items:center;
    justify-content:space-between;

}

    .summary-card h2{
        font-size:22px;
        margin:0;
    }

   .summary-card h6{

    font-size:9px;
    line-height:1.1;
    white-space:normal;
    max-width:60px;

}

    .summary-card span{
        display:none;
    }

 .summary-card i{

    font-size:22px;

}

 .row.g-3 > .col-4,
.row.g-4 > .col-lg-4{

    width:33.33%;
    padding-left:4px;
    padding-right:4px;

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
     border:none;
    padding:12px 24px 20px;
    justify-content:center;
    gap:10px;
}

#logoutModal .btn{
    flex:1;                    /* pantay ang width */
    min-width:110px;
    font-size:13px;
    padding:8px 12px;
}
/* REJECT MODAL MOBILE SIZE */

.reject-modal{
    max-width:75%;
    margin:auto;
}

.reject-modal .modal-content{
    border-radius:15px;
}

.reject-modal .modal-header{
    padding:15px 20px;
}

.reject-modal .modal-header h5{
    font-size:16px;
}

.reject-modal .modal-body{
    padding:18px;
    font-size:14px;
}

.reject-modal .modal-footer{
    padding:12px 18px;
}

.reject-modal .btn{
    min-width:90px;
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

/* ===========================
   MODAL FIX
=========================== */

.modal{
    z-index:2000;
}



.modal-content{

    border-radius:20px;
    overflow:hidden;

}

.modal-body{

    overflow-x:hidden;

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
/* APPROVE MODAL MOBILE SIZE */
#approveConfirmModal .confirm-modal{
    max-width:75%;
    margin:auto;
}

#approveConfirmModal .modal-content{
    border-radius:15px;
}

#approveConfirmModal .modal-header{
    padding:15px 20px;
}

#approveConfirmModal .modal-header h5{
    font-size:16px;
}

#approveConfirmModal .modal-body{
    padding:18px;
    font-size:14px;
}

#approveConfirmModal .modal-footer{
    padding:12px 18px;
}

#approveConfirmModal .btn{
    min-width:90px;
    font-size:13px;
    padding:8px 12px;
}
/* ===========================
   VIEW MODAL
=========================== */

.view-modal{

    max-width:1000px;
    margin:1.75rem auto;

}
/* ===========================
   MOBILE VIEW MODAL INFO FIX
=========================== */

/* LEFT PROFILE INFO */
#viewModal .info-item{
    margin-bottom:10px;
}

#viewModal .info-item strong{
    font-size:12px;
}

#viewModal .info-item p{
    font-size:12px;
}


/* Application ID + Household ID + Barangay + Date */
#viewModal .col-lg-4 .info-item{
    display:flex;
    flex-direction:column;
}


/* gumawa ng 2 column layout sa left info */
#viewModal .col-lg-4 hr{
    margin:10px 0;
}


/* PERSONAL INFORMATION */
#viewModal label{
    font-size:12px;
    margin-bottom:3px;
}

#viewModal .form-control{
    height:38px;
    font-size:12px;
    padding:8px 10px;
}


/* Profile Name */
#viewModal #modalName{
    font-size:18px;
}


/* Status badge */
#viewModal #modalStatus{
    font-size:10px;
    padding:6px 10px;
}


/* Modal section titles */
#viewModal .section-title{
    font-size:15px;
    margin-bottom:12px;
}


/* Address */
#viewModal .address-box{
    font-size:12px;
    min-height:70px;
    padding:10px;
}


/* Household alert */
#viewModal .alert{
    font-size:12px;
    padding:12px;
}


/* Documents */
#viewModal .document-card{
    padding:15px;
}

#viewModal .document-card i{
    font-size:35px;
}

#viewModal .document-card h6{
    font-size:12px;
}


/* ===========================
   PERSONAL INFO 2 COLUMN
=========================== */

#viewModal .modal-body .row .col-md-6{

    width:50%;
    padding-left:5px;
    padding-right:5px;

}


/* ===========================
   LEFT DETAILS 2 COLUMN
=========================== */


/* Application details container */
/* MOBILE APPLICATION DETAILS */
#viewModal .col-lg-4 .info-item{
    width:50%;
}

#viewModal .col-lg-4 .info-item strong{
    display:block;
    white-space:nowrap;
    font-size:12px;
}

#viewModal .col-lg-4 .info-item p{
    margin-top:3px;
    font-size:12px;
}
#viewModal .col-lg-4 .info-item strong{
    display:block;
}

#viewModal .col-lg-4 .info-item p{
    margin-top:3px;
}
/* gawin grid ang details */
#viewModal .col-lg-4{

    display:flex;
    flex-wrap:wrap;

}


#viewModal .col-lg-4 .text-center{

    width:100%;

}


#viewModal .col-lg-4 hr{

    width:100%;

}

#viewModal .col-lg-4 .info-item{

    padding:0 5px;

}
}

@media (min-width:993px){

#viewModal .view-modal{
        max-width:1100px;
        margin:1.75rem auto;   /* center horizontally */
    }

 .search-card .row.g-2{
        display:flex;
        flex-wrap:nowrap;
        align-items:center;
        justify-content:flex-end;
        gap:16px;
    }

    .search-card .col-7.col-md-6{
        flex:0 0 400px;
        max-width:400px;
        margin-left:20px;
    }

    .search-card .col-5.col-md-3{
        flex:0 0 190px;
        max-width:190px;
    }
}
/* ==========================
   DESKTOP VIEW MODAL
========================== */

@media (min-width:993px){

    #viewModal .modal-body{
        padding:35px 40px;
    }

    /* More room for the details */
    #viewModal .col-lg-4{
        padding-right:28px;
    }

    #viewModal .col-lg-8{
        padding-left:28px;
    }

    /* Smaller profile image */
    #viewModal #modalAvatar{
        width:140px;
        height:140px;
    }

    /* Applicant name */
    #viewModal #modalName{
        font-size:24px;
        font-weight:600;
    }

    /* Left details */
    #viewModal .info-item{
        margin-bottom:16px;
    }

    #viewModal .info-item strong{
        font-size:13px;
        font-weight:600;
    }

    #viewModal .info-item p{
        font-size:13px;
        margin-top:4px;
        color:#666;
    }

    /* Section headings */
    #viewModal .section-title{
        font-size:20px;
        margin-bottom:18px;
    }

    /* Labels */
    #viewModal label{
        font-size:13px;
        font-weight:500;
        margin-bottom:6px;
    }

    /* Input fields */
    #viewModal .form-control{
        height:44px;
        font-size:13px;
    }

    /* Address box */
    #viewModal .address-box{
        min-height:90px;
        font-size:13px;
        line-height:1.6;
    }

    /* Rejection box */
    #viewModal .alert{
        font-size:13px;
    }

    /* Document card */
    #viewModal .document-card{
        padding:22px;
    }

    #viewModal .document-card h6{
        font-size:14px;
    }

    #viewModal .document-card .btn{
        font-size:13px;
        padding:8px 18px;
    }

    /* Footer buttons */
    #viewModal .modal-footer .btn{
        min-width:120px;
        font-size:14px;
    }

}

/* ===========================
   SMALL MODALS
=========================== */

.confirm-modal{

    max-width:380px;
    margin:1.75rem auto;

}

.confirm-modal .modal-content{

    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 12px 25px rgba(0,0,0,.08);

}

.confirm-modal .modal-header{

    background:linear-gradient(135deg,#2e7d32,#43a047);
    color:#fff;

    border:none;

    padding:20px 30px;

}

.confirm-modal .modal-header h5{

    margin:0;
    font-weight:600;

}

.confirm-modal .btn-close{

    filter:brightness(0) invert(1);

}

.confirm-modal .modal-body{

    padding:30px;
    color:#555;
    font-size:15px;
    line-height:1.7;

}

.confirm-modal .modal-footer{

    border:none;
    padding:20px 30px;
    background:#fff;
    gap:10px;

}
.confirm-modal textarea{

    border-radius:12px;
    resize:none;

}

.confirm-modal textarea:focus{

    border-color:#2e7d32;
    box-shadow:0 0 0 .15rem rgba(46,125,50,.2);

}

.confirm-modal .btn{

    min-width:110px;

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

body.view-open .sidebar{
    box-shadow:0 0 0 9999px rgba(0,0,0,.45);
    z-index:1040;
}

body.view-open #sidebarControls{
    z-index:1041;
}

body.view-open .navbar{
    z-index:1040;
}
/* ===========================
   DIM VIEW APPLICATION MODAL
=========================== */

body.confirm-open #viewModal .modal-content{
    filter:brightness(.55);
    transition:.3s ease;
}
#viewModal{
    z-index:2050;
}


#approveConfirmModal,
#rejectModal{
    z-index:2150;
}


.modal-backdrop{
    z-index:2000;
}
.confirm-modal .btn-danger{

    background:#2e7d32;
    border:none;

}


.confirm-modal .btn-danger:hover{

    background:#1b5e20;

}
.confirm-modal .btn-secondary{

    background:#6c757d;
    border:none;

}


.confirm-modal .btn-secondary:hover{

    background:#5c636a;

}
/* ONLY CONFIRM MODAL BUTTONS OUTLINE */

#approveConfirmModal .btn-success,
#rejectModal .btn-danger{

    background:transparent !important;
    border:2px solid #2e7d32 !important;
    color:#2e7d32 !important;

}


#approveConfirmModal .btn-success:hover,
#rejectModal .btn-danger:hover{

    background:#2e7d32 !important;
    color:white !important;

}
/* CANCEL BUTTON OUTLINE STYLE */

.confirm-modal .btn-secondary{

    background:transparent !important;
    border:2px solid #6c757d !important;
    color:#6c757d !important;

}

.confirm-modal .btn-secondary:hover{

    background:#6c757d !important;
    color:white !important;

}
/* LOGOUT MODAL YES BUTTON */

/* YES BUTTON - RED */
#logoutModal .btn-danger{
    background:#dc3545 !important;
    border:2px solid #dc3545 !important;
    color:#fff !important;
}

#logoutModal .btn-danger:hover{
    background:#bb2d3b !important;
    border-color:#bb2d3b !important;
    color:#fff !important;
}

/* CANCEL BUTTON - SOLID GRAY */
#logoutModal .btn-secondary{
    background:#6c757d !important;
    border:2px solid #6c757d !important;
    color:#fff !important;
}

#logoutModal .btn-secondary:hover{
    background:#5c636a !important;
    border-color:#5c636a !important;
    color:#fff !important;
}
#logoutModal{
    z-index:3000;
}

#logoutModal + .modal-backdrop{
    z-index:2990;
}

.document-card{
    border:2px dashed #c8e6c9;
    border-radius:15px;
    padding:20px;
    background:#fafafa;
    text-align:center;
}

.document-preview{
    width:100%;
    max-height:420px;
    object-fit:contain;
    border-radius:12px;
    border:1px solid #dee2e6;
    background:#fff;
    cursor:pointer;
    transition:.25s;
}

.document-preview:hover{
    transform:scale(1.02);
    box-shadow:0 8px 20px rgba(0,0,0,.15);
}

@media(max-width:576px){

    .document-preview{
        max-height:250px;
    }

}

.modal-footer .btn:disabled{
    opacity:.55;
    cursor:not-allowed;
    pointer-events:none;
}

.pagination .page-link{
    color:#2e7d32;
    border-radius:8px;
    margin:0 3px;
    border:1px solid #dee2e6;
}

.pagination .page-item.active .page-link{
    background:#2e7d32;
    border-color:#2e7d32;
    color:#fff;
}

.pagination .page-link:hover{
    background:#e8f5e9;
    color:#1b5e20;
}

/* ===============================
   FULLSCREEN VALID ID PREVIEW
================================ */


#validIDPreviewModal{

    z-index:4000 !important;

}


#validIDPreviewModal + .modal-backdrop{

    z-index:3990 !important;

}


/* Dark fullscreen background */

.valid-id-preview-content{

    background:#111;
    border:none;
    border-radius:0;

    height:100vh;
    width:100vw;

}


/* TOP RIGHT CLOSE BUTTON */

.valid-id-header{

    position:absolute;

    top:20px;
    right:25px;

    z-index:10;

}


.valid-id-close{

    position:absolute;

    top:20px;
    right:25px;

    width:32px;
    height:32px;

    display:flex;

    align-items:center;
    justify-content:center;

    background:transparent;

    border:none;

    color:white;

    font-size:22px;

    cursor:pointer;

    z-index:20;

}


.valid-id-close i{

    color:white;

    font-size:22px;

}


.valid-id-close:hover i{

    color:#ddd;

}



/* IMAGE HOLDER */

.valid-id-body{

    width:100%;
    height:100%;

    display:flex;

    align-items:center;

    justify-content:center;

    padding:30px;

}


/* IMAGE */

#fullValidIDImage{

    max-width:95%;

    max-height:95vh;

    object-fit:contain;

    border-radius:10px;

    box-shadow:
    0 0 30px rgba(0,0,0,.8);

}


/* MOBILE */

@media(max-width:576px){

    .valid-id-header{

        top:15px;
        right:15px;

    }


        .valid-id-close{

        top:15px;
        right:15px;

        width:28px;
        height:28px;

    }


    .valid-id-close i{

        font-size:18px;

    }



    .valid-id-body{

        padding:15px;

    }


    #fullValidIDImage{

        max-width:100%;

        max-height:90vh;

    }

}

body.valid-id-open #viewModal .modal-content{

    filter:brightness(.45);

}
/* =====================================
   NO RECORDS FOUND
===================================== */

#emptySearchRow td,
#emptyFilterRow td{

    text-align:center;
    padding:50px 20px;
    background:#fff;
    vertical-align:middle;

}

#emptySearchRow i,
#emptyFilterRow i{

    font-size:50px;
    color:#adb5bd;

}

#emptySearchRow h6,
#emptyFilterRow h6{

    margin-top:15px;
    margin-bottom:5px;
    font-weight:600;
    color:#6c757d;

}

#emptySearchRow small,
#emptyFilterRow small{

    color:#8c8c8c;

}


/* MOBILE */

@media (max-width:576px){

    #emptySearchRow td,
    #emptyFilterRow td{

        padding:35px 10px;

    }

    #emptySearchRow i,
    #emptyFilterRow i{

        font-size:38px;

    }

    #emptySearchRow h6,
    #emptyFilterRow h6{

        font-size:14px;

    }

    #emptySearchRow small,
    #emptyFilterRow small{

        font-size:11px;

    }

}
/* HEADER SEARCH */

.table-card .card-header{
    padding:18px 22px;
}

.table-card .card-header > div{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:16px;
}

.search-box{
    width:380px;
}

.filter-box{
    width:180px;
}

.search-box .form-control,
.filter-box .form-select{
    height:45px;
    border-radius:10px;
}

@media (max-width:576px){

    .table-card .card-header > div{
        flex-direction:column;
        align-items:flex-start;
    }

    .table-card .card-header h5{
        font-size:20px;
        font-weight:700;
        width:100%;
        text-align:left;
    }
    #searchForm{
        width:100%;
        display:flex;
        gap:10px;
    }

    .search-box,
    .filter-box{
        flex:1;
        width:100%;
    }

    .search-box .form-control,
    .filter-box .form-select{
        width:100%;
        height:42px;
        font-size:12px;
    }
}
</style>
</head>


<body>


<!-- NAVBAR -->

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
               <a
    class="dropdown-item text-danger"
    href="#"
    data-bs-toggle="modal"
    data-bs-target="#logoutModal">

    <i class="bi bi-box-arrow-right me-2"></i>

    Logout

</a>
                </li>

            </ul>

        </div>

    </div>

</div>
</nav>



<!-- SIDEBAR -->

<div class="sidebar" id="sidebar">


    <div class="nav flex-column">

        <a class="nav-link active" href="barangay-secretary-home.php">
            <i class="bi bi-person-check"></i>
            <span>User Applications</span>
        </a>


        <a class="nav-link" href="barangay-secretary-complaints.php">
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




<!-- MAIN CONTENT -->

<main class="main-content">

      <div class="row mt-4 g-4">

            <div class="col-lg-4">

                <div class="summary-card">

                    <div>

                        <h6>Pending Applications</h6>

                        <h2 id="pendingCount"><?= $pendingCount ?></h2>

                        <span>Waiting for review</span>

                    </div>

                    <i class="bi bi-hourglass-split"></i>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="summary-card green">

                    <div>

                        <h6>Approved Today</h6>

                        <h2 id="approvedCount"><?= $approvedToday ?></h2>

                        <span>Successfully approved</span>

                    </div>

                    <i class="bi bi-check-circle"></i>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="summary-card red">

                    <div>

                        <h6>Rejected Today</h6>

                       <h2 id="rejectedCount"><?= $rejectedToday ?></h2>

                        <span>Applications denied</span>

                    </div>

                    <i class="bi bi-x-circle"></i>

                </div>

            </div>

        </div>
        

        <!-- SEARCH -->
<div class="card table-card mt-4">

    <div class="card-header">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <h5 class="mb-0">
                Resident Applications
            </h5>

            <form method="GET"
                  id="searchForm"
                  class="d-flex flex-wrap gap-2 ms-auto align-items-center">

                <input type="hidden" name="page" value="1">

                <div class="search-box">
                    <input
                        type="text"
                        name="search"
                        id="searchInput"
                        class="form-control"
                        placeholder="Search applicant..."
                        value="<?= htmlspecialchars($search) ?>">
                </div>

                <div class="filter-box">
                    <select
                        class="form-select"
                        id="statusSelect"
                        name="status">

                        <option value="All Status" <?= $statusFilter=="All Status"?"selected":"" ?>>All Status</option>
                        <option value="Pending" <?= $statusFilter=="Pending"?"selected":"" ?>>Pending</option>
                        <option value="Approved" <?= $statusFilter=="Approved"?"selected":"" ?>>Approved</option>
                        <option value="Rejected" <?= $statusFilter=="Rejected"?"selected":"" ?>>Rejected</option>

                    </select>
                </div>

            </form>

        </div>

    </div>

    <div class="table-responsive">
                <table class="table align-middle">

                    <thead>

                    <tr>

                        <th>Application ID</th>

                        <th>Applicant</th>

                        <th>House No.</th>

                        <th>Address</th>

                        <th>Date</th>

                        <th>Status</th>

                        <th width="220">Action</th>

                    </tr>

                    </thead>

                  <tbody id="applicationTable"
       data-total="<?= $totalRows ?>"
       data-current="<?= $page ?>"
       data-total-pages="<?= $totalPages ?>">

<?php if($applications->num_rows > 0): ?>

    <?php while($row = $applications->fetch_assoc()): ?>

        <?php
        $name = trim(
            $row['first_name'] . ' ' .
            ($row['middle_initial'] ? $row['middle_initial'] . '. ' : '') .
            $row['last_name']
        );

        $status = ucfirst($row['approval_status']);

        $badge =
            $status == "Approved" ? "success" :
            ($status == "Rejected" ? "danger" : "warning");
        ?>

        <tr>

<td>
    APP<?= str_pad($row['id'],5,"0",STR_PAD_LEFT) ?>
</td>

<td>
    <?= htmlspecialchars($name) ?>
</td>

<td>
    <?= htmlspecialchars($row['house_no']) ?>
</td>

<td>
    <?= htmlspecialchars($row['street']) ?>,
    <?= htmlspecialchars($row['barangay']) ?>
</td>

<td>
    <?= date("M d, Y", strtotime($row['created_at'])) ?>
</td>

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
data-created="<?= date("M d, Y", strtotime($row['created_at'])) ?>"
data-house="<?= htmlspecialchars($row['house_no'] ?? '') ?>"
data-barangay="<?= htmlspecialchars($row['barangay'] ?? '') ?>"
data-street="<?= htmlspecialchars($row['street'] ?? '') ?>"
data-birthdate="<?= htmlspecialchars($row['birthdate']) ?>"
data-gender="<?= htmlspecialchars($row['gender'] ?? '') ?>"
data-phone="<?= htmlspecialchars($row['phone'] ?? '') ?>"
data-email="<?= htmlspecialchars($row['email'] ?? '') ?>"
data-profile="<?= htmlspecialchars($row['profile_photo'] ?? '') ?>"
data-validid="<?= htmlspecialchars($row['valid_id'] ?? '') ?>"
data-reason="<?= htmlspecialchars($row['rejection_reason'] ?? '') ?>"
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

</tbody>
                </table>

            </div>
<div class="card-footer bg-white">

<!-- PAGINATION -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">

    <div class="text-center text-md-start">
        <small id="paginationInfo" class="text-muted"></small>
    </div>


    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">


        <button 
            id="prevPageBtn" 
            class="btn btn-sm btn-outline-success">
            Previous
        </button>


        <span id="pageNumber" class="fw-semibold px-2">
            Page <?= $page ?> of <?= max($totalPages,1) ?>
        </span>


        <button 
            id="nextPageBtn" 
            class="btn btn-sm btn-outline-success">
            Next
        </button>


    </div>

</div>

</div>

        </div>

    </main>

</div>

<!-- ================= VIEW APPLICATION MODAL ================= -->

<div class="modal fade" id="viewModal" tabindex="-1">
 <div class="modal-dialog modal-dialog-scrollable view-modal">
          <div class="modal-content">

            <div class="modal-header">

                <h4 class="modal-title">
                    Resident Application Details
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
src="https://ui-avatars.com/api/?name=Maria+Santos&background=2e7d32&color=fff&size=180"
class="rounded-circle shadow mb-3">
                       <h4 id="modalName"></h4>

<span class="badge" id="modalStatus"></span>

</div>

<hr>

<div class="row g-0">

    <div class="col-6">
        <div class="info-item">
            <strong>Application ID</strong>
            <p id="modalAppID"></p>
        </div>
    </div>

    <div class="col-6">
        <div class="info-item">
            <strong>House No.</strong>
            <p id="modalHouseNo"></p>
        </div>
    </div>

    <div class="col-6">
        <div class="info-item">
            <strong>Barangay</strong>
            <p id="modalAddress"></p>
        </div>
    </div>

    <div class="col-6">
        <div class="info-item">
            <strong>Date Submitted</strong>
            <p id="modalDate"></p>
        </div>
    </div>

</div>
</div>
                    <!-- RIGHT -->

                    <div class="col-lg-8">

                        <h5 class="section-title">
                            Personal Information
                        </h5>

                        <div class="row">

                            <div class="col-md-6">

                                <label>Birthdate</label>

                               <input
id="modalBirthdate"
class="form-control"
readonly>

                            </div>


                            <div class="col-md-6 mt-3">

                                <label>Gender</label>

                                <input
id="modalGender"
class="form-control"
readonly>

                            </div>

                            <div class="col-md-6 mt-3">

                                <label>Contact Number</label>

                                <input
id="modalPhone"
class="form-control"
readonly>

                            </div>

                            <div class="col-md-6 mt-3">

                                <label>Email</label>

                                <input
id="modalEmail"
class="form-control"
readonly>

                            </div>


                        </div>

                        <hr>

                        <h5 class="section-title">

                            Address Information

                        </h5>

                      <div
id="modalFullAddress"
class="form-control address-box">
</div>
                        <hr>

                        <div
id="rejectReasonBox"
class="alert alert-danger mt-3"
style="display:none;">

    <strong>Reason for Rejection</strong>

    <hr>

    <p id="rejectReasonText" class="mb-0"></p>

</div>
                        <hr>

                        <h5 class="section-title">

                            Uploaded Documents

                        </h5>

                        <div class="row">

                           <div class="col-md-6">

    <div class="document-card">

        <h6 class="mb-3">
            Valid Government ID
        </h6>

        <img
            id="modalValidID"
            src=""
            alt="Valid Government ID"
            class="document-preview">

    </div>

</div>
                        </div>

                    </div>

                </div>

            </div>

          <div class="modal-footer">

    <button class="btn btn-success modalApproveBtn">
        <i class="bi bi-check-circle"></i>
        Approve
    </button>

    <button class="btn btn-danger modalRejectBtn">
        <i class="bi bi-x-circle"></i>
        Reject
    </button>

</div>
              

            </div>

        </div>

    </div>

</div>

<div class="modal fade" id="approveConfirmModal">
    <div class="modal-dialog modal-dialog-centered confirm-modal">
        <div class="modal-content">

            <div class="modal-header">

                <h5>Approve Application</h5>

            </div>

            <div class="modal-body">

                Are you sure you want to approve this application?

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary"
                data-bs-dismiss="modal">

                    Cancel

                </button>

                <button
                class="btn btn-success"
                id="confirmApproveBtn">

                    Approve

                </button>

            </div>

        </div>

    </div>

</div>
<div class="modal fade" id="rejectModal">
    <div class="modal-dialog modal-dialog-centered reject-modal">    <div class="modal-content">

            <div class="modal-header">

                <h5>Reject Application</h5>

            </div>

            <div class="modal-body">

                <label class="form-label">

                    Reason for rejection

                </label>

                <textarea
                id="rejectReasonInput"
                class="form-control"
                rows="4"></textarea>

            </div>

            <div class="modal-footer">

                <button
                class="btn btn-secondary"
                data-bs-dismiss="modal">

                    Cancel

                </button>

                <button
                class="btn btn-danger"
                id="confirmRejectBtn">

                    Reject

                </button>

            </div>

        </div>

    </div>

</div>
<div class="modal fade" id="alreadyApprovedModal">
    <div class="modal-dialog modal-dialog-centered confirm-modal">
        <div class="modal-content">

            <div class="modal-header">

                <h5>Notice</h5>

            </div>

            <div class="modal-body">

                <p id="alreadyApprovedModalText"></p>

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

<!-- ================= VALID ID FULLSCREEN PREVIEW MODAL ================= -->

<div class="modal fade" id="validIDPreviewModal" tabindex="-1">

    <div class="modal-dialog modal-fullscreen">

        <div class="modal-content valid-id-preview-content">

            <div class="valid-id-header">

                <button 
    type="button"
    class="valid-id-close"
    data-bs-dismiss="modal">
    
    <i class="bi bi-x-lg"></i>

</button>

            </div>


            <div class="valid-id-body">

                <img 
                    id="fullValidIDImage"
                    src=""
                    alt="Valid Government ID">

            </div>

        </div>

    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script >



    // ======================================
// COUNTERS
// ======================================

let pending = Number(document.getElementById("pendingCount").textContent);
let approved = Number(document.getElementById("approvedCount").textContent);
let rejected = Number(document.getElementById("rejectedCount").textContent);
let rejectionReasons = {};

const pendingCount = document.getElementById("pendingCount");
const approvedCount = document.getElementById("approvedCount");
const rejectedCount = document.getElementById("rejectedCount");

function updateCounters() {
    pendingCount.textContent = pending;
    approvedCount.textContent = approved;
    rejectedCount.textContent = rejected;
}


// ======================================
// VIEW MODAL
// ======================================
const approveConfirmModal =
new bootstrap.Modal(document.getElementById("approveConfirmModal"));

const rejectModal =
new bootstrap.Modal(document.getElementById("rejectModal"));

const alreadyApprovedModal =
new bootstrap.Modal(document.getElementById("alreadyApprovedModal"));


const modalElement = document.getElementById("viewModal");
const viewModal = new bootstrap.Modal(modalElement);
modalElement.addEventListener("show.bs.modal", () => {
    document.body.classList.add("view-open");
});

modalElement.addEventListener("hidden.bs.modal", () => {
    document.body.classList.remove("view-open");
});

let currentRow = null;
let currentResidentID = null;

function attachViewButtons() {

    document.querySelectorAll(".viewBtn").forEach(btn => {

        btn.onclick = function () {

            currentRow = this.closest("tr");
            currentResidentID = this.dataset.id;

            // Basic Information
            document.getElementById("modalAppID").textContent =
                "APP" + String(this.dataset.id).padStart(5, "0");

            document.getElementById("modalName").textContent =
                this.dataset.name;

            document.getElementById("modalStatus").textContent =
                this.dataset.status;

            document.getElementById("modalDate").textContent =
                this.dataset.created;

            document.getElementById("modalHouseNo").textContent =
    this.dataset.house;

            // Barangay
            document.getElementById("modalAddress").textContent =
                this.dataset.barangay;

            // Avatar
            const avatar = document.getElementById("modalAvatar");

            if (this.dataset.profile !== "") {

                avatar.src = this.dataset.profile;

            } else {

                avatar.src =
                    `https://ui-avatars.com/api/?name=${encodeURIComponent(this.dataset.name)}&background=2e7d32&color=fff&size=180`;

            }

            // Status badge color
            const badge = document.getElementById("modalStatus");
            

            badge.className = "badge";

            if (this.dataset.status === "Approved") {

                badge.classList.add("bg-success");

            } else if (this.dataset.status === "Rejected") {

                badge.classList.add("bg-danger");

            } else {

                badge.classList.add("bg-warning");

            }

            updateActionButtons(this.dataset.status);

            // Personal Information
            document.getElementById("modalBirthdate").value =
                this.dataset.birthdate;

            document.getElementById("modalGender").value =
                this.dataset.gender;

            document.getElementById("modalPhone").value =
                this.dataset.phone;

            document.getElementById("modalEmail").value =
                this.dataset.email;

            // Address
            document.getElementById("modalFullAddress").innerHTML =
                this.dataset.house +
                ", " +
                this.dataset.street +
                "<br>" +
                this.dataset.barangay;

            // ===============================
// VALID ID PREVIEW MODAL
// ===============================

const validID = document.getElementById("modalValidID");

const fullValidIDImage =
document.getElementById("fullValidIDImage");


const validIDModal =
new bootstrap.Modal(
    document.getElementById("validIDPreviewModal")
);


validID.src = this.dataset.validid;


validID.onclick = function(){

    fullValidIDImage.src = this.src;

    validIDModal.show();

};

            // Rejection Reason
            const reasonBox =
                document.getElementById("rejectReasonBox");

            if (
                this.dataset.reason &&
                this.dataset.reason.trim() !== ""
            ) {

                reasonBox.style.display = "block";

                document.getElementById("rejectReasonText").textContent =
                    this.dataset.reason;

            } else {

                reasonBox.style.display = "none";

            }

            viewModal.show();

        };

    });

}

attachViewButtons();


// ======================================
// SIDEBAR
// ======================================

const sidebar = document.getElementById("sidebar");
const toggleBtn = document.getElementById("toggleBtn");
const closeBtn = document.getElementById("closeBtn");
const hamburger = document.getElementById("hamburger");


function isTabletOrMobile(){
    return window.innerWidth <= 992;
}


// ======================================
// TOGGLE > < BUTTON
// ======================================

toggleBtn.addEventListener("click", ()=>{

    if(!isTabletOrMobile()) return;


    const icon = toggleBtn.querySelector("i");


    sidebar.classList.toggle("expanded");


    if(sidebar.classList.contains("expanded")){

        icon.classList.remove("bi-chevron-right");
        icon.classList.add("bi-chevron-left");


    }else{

        icon.classList.remove("bi-chevron-left");
        icon.classList.add("bi-chevron-right");

    }

});



// ======================================
// X CLOSE BUTTON
// ======================================

closeBtn.addEventListener("click", ()=>{

    if(!isTabletOrMobile()) return;


    sidebar.classList.add("hide-sidebar");


    sidebar.classList.remove("expanded");


    const icon = toggleBtn.querySelector("i");

    icon.classList.remove("bi-chevron-left");
    icon.classList.add("bi-chevron-right");


});



// ======================================
// HAMBURGER OPEN
// ======================================

hamburger.addEventListener("click", ()=>{


    if(!isTabletOrMobile()) return;


    sidebar.classList.remove("hide-sidebar");


    sidebar.classList.remove("expanded");


    const icon = toggleBtn.querySelector("i");


    icon.classList.remove("bi-chevron-left");
    icon.classList.add("bi-chevron-right");


});




// ======================================
// RESET DESKTOP
// ======================================

window.addEventListener("resize", ()=>{


    if(window.innerWidth > 992){

        sidebar.classList.remove("expanded");
        sidebar.classList.remove("show");

    }


});
function approveApplication(row) {

    const status = row.querySelector(".badge").textContent.trim();

 
    const badge = row.querySelector(".badge");

    badge.className = "badge bg-success";
    badge.textContent = "Approved";

    if(currentRow === row){

        document.getElementById("modalStatus").className="badge bg-success";
        document.getElementById("modalStatus").textContent="Approved";

    }

    pending--;
    approved++;

    updateCounters();
    updateActionButtons("Approved");
}

function rejectApplication(row, reason){

    const status = row.querySelector(".badge").textContent.trim();

 

    rejectionReasons[row.cells[0].textContent]=reason;

    const badge=row.querySelector(".badge");

    badge.className="badge bg-danger";
    badge.textContent="Rejected";

    if(currentRow===row){

        document.getElementById("modalStatus").className="badge bg-danger";
        document.getElementById("modalStatus").textContent="Rejected";

    }

    pending--;
    rejected++;

    updateCounters();
    updateActionButtons("Rejected");
}

function updateActionButtons(status){

    const approveBtn = document.querySelector(".modalApproveBtn");
    const rejectBtn  = document.querySelector(".modalRejectBtn");

    if(status === "Pending"){

        approveBtn.disabled = false;
        rejectBtn.disabled = false;

        approveBtn.classList.remove("disabled");
        rejectBtn.classList.remove("disabled");

    }else{

        approveBtn.disabled = true;
        rejectBtn.disabled = true;

        approveBtn.classList.add("disabled");
        rejectBtn.classList.add("disabled");

    }

}

document.querySelector(".modalApproveBtn").addEventListener("click", () => {

    if (!currentRow) return;

    const status = currentRow.querySelector(".badge").textContent.trim();

  if(status === "Approved"){

    Swal.fire({
        icon:"warning",
        title:"Already Approved",
        text:"This application has already been approved.",
        timer:3000,
        showConfirmButton:true,
        confirmButtonText:"OK"
    });

    return;

}

if(status === "Rejected"){

    Swal.fire({
        icon:"warning",
        title:"Already Rejected",
        text:"This application has already been rejected.",
        timer:3000,
        showConfirmButton:true,
        confirmButtonText:"OK"
    });

    return;

}

approveConfirmModal.show();

});

//approve application
document.getElementById("confirmApproveBtn").addEventListener("click", () => {

    fetch("barangay-secretary-update-application.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            id: currentResidentID,
            action: "approve"
        })
    })
    .then(res => res.json())
    .then(data => {

        if (!data.success) {

            Swal.fire({
                icon: "error",
                title: "Error",
                text: data.message
            });

            return;
        }

  approveApplication(currentRow);

approveConfirmModal.hide();

document.getElementById("approveConfirmModal")
.addEventListener("hidden.bs.modal", function handler() {

    this.removeEventListener("hidden.bs.modal", handler);

    viewModal.hide();

    Swal.fire({
        icon: "success",
        title: "Application Approved",
        text: "The resident application has been approved."
    });

}, { once: true });
    });

});

document.querySelector(".modalRejectBtn").addEventListener("click", () => {

    if (!currentRow) return;

    const status = currentRow.querySelector(".badge").textContent.trim();

 if(status === "Rejected"){

    Swal.fire({
        icon:"warning",
        title:"Already Rejected",
        text:"This application has already been rejected.",
        timer:3000,
        showConfirmButton:true,
        confirmButtonText:"OK"
    });

    return;

}


if(status === "Approved"){

    Swal.fire({
        icon:"warning",
        title:"Already Approved",
        text:"This application has already been approved.",
        timer:3000,
        showConfirmButton:true,
        confirmButtonText:"OK"
    });

    return;

}

    document.getElementById("rejectReasonInput").value = "";

 rejectModal.show();

});

//reject application
document.getElementById("confirmRejectBtn").addEventListener("click", () => {

    const reason = document.getElementById("rejectReasonInput").value.trim();

    if (reason === "") {

        Swal.fire({
            icon: "warning",
            title: "Reason Required",
            text: "Please enter a reason before rejecting this application."
        });

        return;
    }

    fetch("barangay-secretary-update-application.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            id: currentResidentID,
            action: "reject",
            reason: reason
        })
    })
    .then(res => res.json())
    .then(data => {

        if (!data.success) {

            Swal.fire({
                icon: "error",
                title: "Error",
                text: data.message
            });

            return;
        }

    rejectApplication(currentRow, reason);

rejectModal.hide();

document.getElementById("rejectModal")
.addEventListener("hidden.bs.modal", function handler() {

    this.removeEventListener("hidden.bs.modal", handler);

    viewModal.hide();

    Swal.fire({
        icon: "success",
        title: "Application Rejected",
        text: "The resident application has been rejected."
    });

}, { once: true });

    });

});
// ======================================
// SEARCH + STATUS FILTER
// ===const searchForm = document.getElementById("searchForm");
const searchInput = document.getElementById("searchInput");
const statusSelect = document.getElementById("statusSelect");

searchInput.addEventListener("input", function(){
    loadApplications();
});


statusSelect.addEventListener("change", function(){
    loadApplications();
});
function loadApplications(){

    const search = searchInput.value;
    const status = statusSelect.value;

    fetch(
        "barangay-secretary-search.php?search=" +
        encodeURIComponent(search) +
        "&status=" +
        encodeURIComponent(status)
    )
    .then(response => response.text())
    .then(html => {

        document.getElementById("applicationTable").innerHTML = html;

        attachViewButtons();

    })
    .catch(error => console.error(error));

}
// ======================================
// SORT (Newest / Oldest)
// ======================================

const sortSelect = document.getElementById("sortSelect");

if(sortSelect){

    sortSelect.addEventListener("change", function () {

        const tbody = document.getElementById("applicationTable");

        const rows = Array.from(
            tbody.querySelectorAll("tr:not(#emptySearchRow)")
        );

        if(this.value === "Newest"){

            rows.reverse();

        }else{

            rows.reverse();

        }

        rows.forEach(row => tbody.appendChild(row));

    });

}


// ======================================
// CARD HOVER
// ======================================

document.querySelectorAll(".summary-card").forEach(card => {

    card.addEventListener("mouseenter", () => {

        card.style.transform = "translateY(-5px)";

    });

    card.addEventListener("mouseleave", () => {

        card.style.transform = "translateY(0)";

    });

});
const logoutModal = document.getElementById("logoutModal");

logoutModal.addEventListener("show.bs.modal", () => {

    // alisin ang custom shadow
    document.body.classList.remove("view-open");
    document.body.classList.remove("confirm-open");

    // siguraduhing mawala ang backdrop ng View Modal
    document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());

    document.body.classList.remove("modal-open");
    document.body.style.removeProperty("padding-right");
    document.body.style.removeProperty("overflow");

});
document.getElementById("confirmLogout").addEventListener("click", function () {
    window.location.href = "logout.php";
});
const confirmModals = [
    document.getElementById("approveConfirmModal"),
    document.getElementById("rejectModal")
];


confirmModals.forEach(modal => {

    modal.addEventListener("show.bs.modal", function(){

        document.body.classList.add("confirm-open");

    });


    modal.addEventListener("hidden.bs.modal", function(){

        document.body.classList.remove("confirm-open");

    });

});

// ======================================
// PAGINATION
// ======================================

const rowsPerPage = 10;

let currentPage = Number(
    document.getElementById("applicationTable")
    .dataset.current
);


const totalRows = Number(
    document.getElementById("applicationTable")
    .dataset.total
);


const totalPages = Number(
    document.getElementById("applicationTable")
    .dataset.totalPages
);


function updateFilteredPagination(){

    const tbody = document.getElementById("applicationTable");


    const visibleRows = Array.from(
        tbody.querySelectorAll("tr")
    ).filter(row=>{

        return row.style.display !== "none"
        &&
        !row.id.includes("emptySearchRow");

    });


    const count = visibleRows.length;


    document.getElementById("paginationInfo")
    .textContent =
    count === 0
    ?
    ""
    :
    `Showing 1 to ${count} of ${count} records`;


}
function updatePagination(){


    const startIndex =
        ((currentPage - 1) * rowsPerPage) + 1;


    const endIndex =
        Math.min(
            currentPage * rowsPerPage,
            totalRows
        );


 document.getElementById("paginationInfo")
.textContent =
totalRows === 0
?
""
:
`Showing ${startIndex} to ${endIndex} of ${totalRows} records`;


    document.getElementById("pageNumber")
    .textContent =
    `Page ${currentPage} of ${totalPages || 1}`;



    document.getElementById("prevPageBtn")
    .disabled =
    currentPage <= 1;



    document.getElementById("nextPageBtn")
    .disabled =
    currentPage >= totalPages;

}



document.getElementById("prevPageBtn")
.addEventListener("click",()=>{


    if(currentPage > 1){

        currentPage--;

    window.location.href =
"?page=" + currentPage +
"&search=<?= urlencode($search) ?>" +
"&status=<?= urlencode($statusFilter) ?>";
    }

});



document.getElementById("nextPageBtn")
.addEventListener("click",()=>{


    if(currentPage < totalPages){

        currentPage++;

    window.location.href =
"?page=" + currentPage +
"&search=<?= urlencode($search) ?>" +
"&status=<?= urlencode($statusFilter) ?>";
    }

});



updatePagination();
// ======================================
// INITIALIZE
// ======================================

updateCounters();
attachViewButtons();

const validIDPreviewElement =
document.getElementById("validIDPreviewModal");


validIDPreviewElement.addEventListener(
"show.bs.modal",
()=>{

    document.body.classList.add("valid-id-open");

});


validIDPreviewElement.addEventListener(
"hidden.bs.modal",
()=>{

    document.body.classList.remove("valid-id-open");

});

</script>

</body>
</html>