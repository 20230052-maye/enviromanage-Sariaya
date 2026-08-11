<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']) &&
              isset($_SESSION['role']) &&
              $_SESSION['role'] === 'resident';

// DB CONNECTION
$conn = new mysqli(
    "localhost",
    "u823857209_enviromanage",
    "Enviromanage4322",
    "u823857209_enviromanage"
);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$residentName = "";
$contactNumber = "";
$address = "Unknown Location";
if (isset($_SESSION['user_id'])) {

    $stmt = $conn->prepare("
    SELECT
    first_name,
    middle_initial,
    last_name,
    phone,
    house_no,
    street,
    barangay
FROM users
WHERE id = ?
LIMIT 1
    ");

    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
$residentName = trim(
    $row['first_name'] . " " .
    (!empty($row['middle_initial']) ? $row['middle_initial'] . ". " : "") .
    $row['last_name']
);

$contactNumber = $row['phone'];
        $parts = [];

        if (!empty($row['house_no'])) {
            $parts[] = $row['house_no'];
        }

        if (!empty($row['street'])) {
            $parts[] = $row['street'];
        }

        if (!empty($row['barangay'])) {
            $parts[] = $row['barangay'];
        }

        $address = implode(", ", $parts);
    }

    $stmt->close();
}

$addresses = [];

$stmt = $conn->prepare("
SELECT *
FROM resident_addresses
WHERE resident_id = ?
ORDER BY is_default DESC, id DESC
");

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();

$result = $stmt->get_result();

while($row = $result->fetch_assoc()){
    $addresses[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Schedule Garbage Pickup</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
    font-family:"Segoe UI",Tahoma,Geneva,Verdana,sans-serif;
    background:#f8f9fa;
    margin:0;
    padding-top:70px;
}

.navbar{
    background:#1e5631 !important;
    height:70px;
}

.navbar-brand img{
    height:42px;
}

.location-wrapper{
    position:absolute;
    left:50%;
    transform:translateX(-50%);
}

.location-btn{
    color:#fff;
    display:flex;
    align-items:center;
    gap:6px;
    cursor:pointer;
    font-weight:600;
    user-select:none;
}

.location-btn:hover{
    opacity:.9;
}

.location-search{
    position:fixed;
    top:70px;
    left:0;
    width:100%;
    background:#fff;
    padding:15px;
    box-shadow:0 3px 10px rgba(0,0,0,.12);
    display:none;
    z-index:1040;
}

.location-search.show{
    display:block;
}

.location-search input{
    max-width:500px;
    margin:auto;
}


.profile-btn{
    color:#fff;
    font-size:1.7rem;
}
/* ===========================
   SIDEBAR
=========================== */

.sidebar{
    position:fixed;
    top:70px;
    left:0;
    width:220px;
    height:100%;
    background:#fff;
    border-right:1px solid #dee2e6;
    padding-top:15px;
    overflow-y:auto;
    z-index:1050;
}

.sidebar .nav-link{
    color:#495057;
    padding:10px 20px;
    display:flex;
    align-items:center;
    gap:10px;
    justify-content:flex-start;
}

.sidebar .nav-link span{
    display:inline;
}

.sidebar .nav-link:hover,
.sidebar .nav-link.active{
    background:#1e5631;
    color:#fff;
    border-radius:5px;
}

/* ===========================
   MAIN
=========================== */



.location-wrapper{
    position:absolute;
    left:50%;
    transform:translateX(-50%);
}

.location-btn{
    background:#1e5631;
    border:none;
    border-radius:30px;
    padding:8px 18px;
    font-size:.95rem;
    display:flex;
    align-items:center;
    gap:8px;
    color:#fff;
    cursor:pointer;
    font-weight:600;
    user-select:none;
    transition:.2s;
}

.location-btn:hover{
    opacity:.9;
}

.location-btn i{
    color:#fff;
}

.location-btn span{
    max-width:320px;
    overflow:hidden;
    white-space:nowrap;
    text-overflow:ellipsis;
}

.location-search{
    position:fixed;
    top:70px;
    left:0;
    width:100%;
    background:#fff;
    padding:15px;
    box-shadow:0 3px 10px rgba(0,0,0,.12);
    display:none;
    z-index:1040;
}

.location-search.show{
    display:block;
}

.location-search input{
    max-width:500px;
    margin:auto;
}


.navbar .container-fluid{
    position:relative;
    height:70px;
}

/* Logo */
.navbar-brand{
    position:absolute;
    left:15px;
    top:50%;
    transform:translateY(-50%);
    margin:0;
    z-index:1055;
}

/* Profile */
.navbar-nav{
    position:absolute;
    right:15px;
    top:50%;
    transform:translateY(-50%);
    margin:0;
    z-index:1055;
}


.dropdown-toggle::after{
    display:none;
}

.dropdown-menu{
    position:absolute !important;
    top:52px !important;
    right:0 !important;
    left:auto !important;
    margin-top:0 !important;
    min-width:140px;
}

/*==========================
PAGE TITLE
==========================*/

.page-header{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:25px;
}



.page-title{
    margin:0;
    font-size:30px;
    font-weight:700;
    color:#1e5631;
}
.page-top{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:25px;
}

.back-btn{
    width:42px;
    height:42px;
    background:#fff;
    border:1px solid #ddd;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    color:#1e5631;
    font-size:22px;
    box-shadow:0 2px 6px rgba(0,0,0,.08);
    transition:.2s;
}

.back-btn:hover{
    background:#1e5631;
    color:#fff;
}

.page-heading{
    margin:0;
    font-size:28px;
    font-weight:700;
    color:#1e5631;
}
/*=========================
CONTENT
=========================*/

.main-content{
    margin-left:220px;
    padding:10px 35px 30px;
}
/*=========================
PROFILE
=========================*/

.dropdown-toggle::after{
    display:none;
}

.dropdown-menu{
    margin-top:10px !important;
    right:0 !important;
    left:auto !important;
    min-width:140px;
}

/*=========================
MOBILE
=========================*/

.mobile-nav{
    display:none;
}
@media(max-width:991px){

    .main-content{
        margin-left:0;
        padding:20px;
        padding-bottom:90px;
    }
.sidebar{
        display:none;
    }

    .main-content{
        margin-left:0;
        padding:20px;
        padding-bottom:90px;
    }

  /* ===========================
   MOBILE BOTTOM NAVBAR
=========================== */

.mobile-nav{
    position:fixed;
    left:0;
    bottom:0;
    width:100%;
    height:70px;
    background:#14532d;
    display:flex;
    justify-content:space-around;
    align-items:center;
    box-shadow:0 -3px 15px rgba(0,0,0,.15);
    z-index:1050;
    border-radius:20px 20px 0 0;
    overflow:hidden;
}

.mobile-nav a{
    flex:1;
    height:100%;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    color:#ffffff;
    font-size:.75rem;
    font-weight:600;
    transition:.2s;
}

.mobile-nav a i{
    font-size:1.45rem;
    margin-bottom:4px;
    color:#ffffff;
}

/* ACTIVE */
.mobile-nav a.active{
    color:#c8d77b;
}

.mobile-nav a.active i{
    color:#c8d77b;
}


/* Hover */
.mobile-nav a:hover{
    color:#c8d77b;
}

.mobile-nav a:hover i{
    color:#c8d77b;
}
}
@media(max-width:768px){

.sidebar{
    display:none;
}

.main-content{

    margin-left:0;

    padding:18px 14px 90px;

}
.navbar .container-fluid{
    height:70px;
}

.navbar-nav{
    height:100%;
}

.navbar-nav .nav-item{
    position:relative;
    display:flex;
    align-items:center;
}

.navbar-nav .nav-link{
    height:100%;
    display:flex;
    align-items:center;
}

.dropdown-menu{
    position:absolute !important;
    right:0 !important;
    left:auto !important;
    transform:none !important;
}

.dropdown-item{
    font-size:.85rem;
    padding:10px 15px;
}

.swal2-popup{
    width:260px !important;
    padding:1rem !important;
    font-size:14px !important;
}

.swal2-title{
    font-size:20px !important;
}

.swal2-html-container{
    font-size:13px !important;
}

.swal2-icon{
    transform:scale(.8);
    margin:.5em auto;
}

.swal2-confirm{
    padding:6px 20px !important;
    font-size:14px !important;
}
/* ===========================
   MOBILE BOTTOM NAVBAR
=========================== */

.mobile-nav{
    position:fixed;
    left:0;
    bottom:0;
    width:100%;
    height:70px;
    background:#14532d;
    display:flex;
    justify-content:space-around;
    align-items:center;
    box-shadow:0 -3px 15px rgba(0,0,0,.15);
    z-index:1050;
    border-radius:20px 20px 0 0;
    overflow:hidden;
}

.mobile-nav a{
    flex:1;
    height:100%;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    color:#ffffff;
    font-size:.75rem;
    font-weight:600;
    transition:.2s;
}

.mobile-nav a i{
    font-size:1.45rem;
    margin-bottom:4px;
    color:#ffffff;
}

/* ACTIVE */
.mobile-nav a.active{
    color:#c8d77b;
}

.mobile-nav a.active i{
    color:#c8d77b;
}


/* Hover */
.mobile-nav a:hover{
    color:#c8d77b;
}

.mobile-nav a:hover i{
    color:#c8d77b;
}
.btn-success{
    padding:8px 18px;
    font-size:.85rem;
}

.d-flex.justify-content-end .btn{
    min-width:110px;
}
   .resident-info-card{
        padding:12px;
    }

    .resident-info-card div{
        font-size:.82rem;
        line-height:1.5;
    }

    .form-label{
        font-size:.82rem;
    }

    .form-check-label{
        font-size:.72rem;
    }

    .form-control,
    .form-select,
    textarea{
        font-size:.82rem;
    }

    .btn{
        font-size:.82rem;
    }

    .page-title{
        font-size:1.2rem;
    }
.submit-wrapper{
        justify-content:flex-end;
        margin-top:20px;
    }

    .submit-btn{
        width:auto;
        min-width:130px;
        padding:9px 22px;
        font-size:.82rem;
    }
        /* MOBILE LOCATION ELLIPSIS */

.location-wrapper{
    position:static;
    transform:none;
    margin:auto;
    max-width:65%;
}

.location-btn{
    width:100%;
    max-width:100%;
    overflow:hidden;
}

.location-btn span{
    display:block;
    max-width:120px;
    overflow:hidden;
    white-space:nowrap;
    text-overflow:ellipsis;
    font-size:.75rem;
}
.location-btn i{
    flex-shrink:0;
}
}

/*==================================================
SCHEDULE PICKUP PAGE
==================================================*/

.page-title{
    font-size:2rem;
    font-weight:700;
    color:#1e5631;
}

.page-subtitle{
    color:#6c757d;
    margin-bottom:25px;
}

.main-content .card{
    background:#1e5631;
    color:#fff;
    border:none;
    border-radius:18px;
    box-shadow:0 8px 20px rgba(0,0,0,.15);
}

.main-content .card-body{
    padding:35px;
}

/*==================================================
SECTION HEADINGS
==================================================*/

.main-content h5{
    color:#fff;
    font-weight:700;
    margin-bottom:18px;
}

/*==================================================
LABELS
==================================================*/

.form-label{
    font-weight:600;
    color:#fff;
    margin-bottom:8px;
}

/*==================================================
INPUTS
==================================================*/

.form-control,
.form-select{

    height:48px;

    background:#2b6d3b;
    color:#fff;

    border:1px solid rgba(255,255,255,.15);

    border-radius:10px;

    box-shadow:none;

    transition:.25s;

}
/* ADD ADDRESS MODAL INPUT FIX */

#addAddressModal .form-control{

    background:#fff;
    color:#000;

    height:40px;

    padding:8px 12px;

    border:1px solid #ced4da;

    border-radius:8px;

}
#addAddressModal .form-control::placeholder{
    color:#000;
    opacity:.6;
}
#addAddressModal .form-control:focus{

    background:#fff;
    color:#000;

    border-color:#1e5631;

    box-shadow:0 0 0 .15rem rgba(30,86,49,.15);

}
textarea.form-control{

    min-height:120px;

    resize:none;

}

.form-control:focus,
.form-select:focus{

    background:#2b6d3b;
    color:#fff;

    border:1px solid rgba(255,255,255,.35);

    box-shadow:0 0 0 .15rem rgba(255,255,255,.12);

}
.form-control::placeholder{
    color:rgba(255,255,255,.75);
}
.form-select option{
    background:#fff;
    color:#212529;
}
/*==================================================
READONLY
==================================================*/

.form-control[readonly]{

    background:#f8f9fa;

    color:#555;

}

/*==================================================
INPUT GROUP
==================================================*/

.input-group-text{

    background:#1e5631;

    color:#fff;

    border:none;

    font-weight:600;

}
.form-control::placeholder{
    color:#eef4ea;
}


/*==================================================
UPLOAD BOX
==================================================*/

.upload-box{

    border:2px dashed #1e5631;

    border-radius:15px;

    background:#f8fff9;

    padding:40px 20px;

    text-align:center;

    transition:.25s;

}

.upload-box:hover{

    background:#eef9f1;

}

.upload-box i{

    font-size:70px;

    color:#1e5631;

}

.upload-box h6{

    margin-top:15px;

    font-weight:700;

}

.upload-box p{

    color:#777;

    margin-bottom:20px;

}

/*==================================================
CHECKBOX
==================================================*/

.form-check{

    margin-top:25px;

}

.form-check-input{

    width:20px;

    height:20px;

    cursor:pointer;

}

.form-check-label{

    margin-left:8px;

    font-size:.95rem;

}

/*==================================================
BUTTONS
==================================================*/

.btn{

    border-radius:10px;

    padding:11px 26px;

    font-weight:600;

}

.btn-success{
    background:#2e7d32;
    border:1px solid #2e7d32;
    color:#fff;
    border-radius:10px;
    padding:11px 26px;
    font-weight:600;
    transition:.3s;
}

/*==================================================
DIVIDER
==================================================*/

hr{

    opacity:.12;

}
.resident-info-card{

    background:#2b6d3b;

    color:#fff;

    border-radius:14px;

    padding:18px 20px;

    margin-bottom:20px;

    box-shadow:0 5px 15px rgba(0,0,0,.12);

}

.resident-info-card strong{

    font-weight:700;

}

.resident-info-card div{

    font-size:1rem;

    line-height:1.8;

}

.resident-info-card .btn{

    color:#fff;

}

.resident-info-card .btn:hover{

    color:#d8f3dc;

}
/*==================================================
DESKTOP
==================================================*/

@media(min-width:992px){

.main-content{

    padding:10px 35px;

}

.main-content .card{

  max-width:1300px;

    margin:auto;

}

}

/*==================================================
TABLET
==================================================*/

@media(max-width:991px){

.main-content{

    padding:20px;

}

.main-content .card-body{

    padding:25px;

}

}

/*==================================================
MOBILE
==================================================*/

@media(max-width:768px){

.main-content{

    padding:15px 12px 95px;

}

.page-title{

    font-size:1.5rem;

}

.page-subtitle{

    font-size:.9rem;

}

.main-content .card{

    border-radius:14px;

}

.main-content .card-body{

    padding:18px;

}

.form-control,
.form-select{

    height:46px;

    font-size:.9rem;

}

textarea.form-control{

    min-height:100px;

}

.upload-box{

    padding:28px 15px;

}

.upload-box i{

    font-size:52px;

}

.upload-box h6{

    font-size:1rem;

}

.upload-box p{

    font-size:.85rem;

}

.action-buttons{
    display:flex;
    justify-content:flex-end;
    gap:10px;
}

.action-buttons .btn{
    width:auto;
    min-width:140px;
    margin-top:0;
}
.resident-info-card{

    padding:15px;

}

.resident-info-card div{

    font-size:.92rem;

    line-height:1.6;

}
   .form-check{
        margin-top:15px;
        margin-bottom:20px;
        align-items:flex-start;
    }

    .form-check-label{
        font-size:.75rem;
        line-height:1.4;
    }

    .submit-wrapper{
        margin-top:20px;
        margin-bottom:10px;
    }
}

/*==================================================
SMALL MOBILE
==================================================*/

@media(max-width:480px){

.main-content{

    padding:12px 10px 90px;

}

.main-content .card-body{

    padding:15px;

}

.page-title{

    font-size:1.3rem;

}

.form-label{

    font-size:.9rem;

}

.form-control,
.form-select{

    font-size:.88rem;

}

.upload-box{

    padding:22px 12px;

}

.upload-box i{

    font-size:46px;

}

}
/* FILE INPUT */
#photoUpload{
    width:100%;
    max-width:700px;
    margin:0 auto;
    display:block;
    background:#fff;
    color:#333;
    border:1px solid #ced4da;
    border-radius:10px;
    padding:8px 12px;
    box-sizing:border-box;
}
#imagePreviewWrapper{
    position:relative;
    display:flex;
    flex-direction:column;
    align-items:center;
    margin-top:15px;
}

.preview-image{
    width:100%;
    max-width:260px;
    height:190px;
    object-fit:cover;
    border-radius:12px;
    display:block;
}
/* PREVIEW AREA */
.preview-container{
    position:relative;
    display:inline-block;
}

.preview-image{
    width:100%;
    max-width:320px;
    height:220px;
    object-fit:cover;
    border-radius:12px;
    display:block;
    border:2px solid #fff;
    box-shadow:0 5px 15px rgba(0,0,0,.2);
}

#uploadPlaceholder{
    transition:.3s;
}

#previewCounter{
    font-weight:600;
}

@media(max-width:768px){

    .preview-image{
        max-width:230px;
        height:170px;
    }

}
/* CLASSIFICATION DEFAULT SIZE */
@media(min-width:769px){

    .classification-wrapper .form-check-label{
        font-size:1rem;
        font-weight:500;
    }

    .classification-wrapper .form-check-input{
        width:20px;
        height:20px;
    }

}
.preview-arrow{

    position:absolute;

    top:50%;

    transform:translateY(-50%);

    width:36px;

    height:36px;

    border:none;

    border-radius:50%;

    background:#1e5631;

    color:#fff;

    display:flex;

    justify-content:center;

    align-items:center;

    cursor:pointer;
}

.preview-arrow.left{
    left:10px;
}

.preview-arrow.right{
    right:10px;
}

.preview-arrow:hover{
    background:#2e7d32;
}

@media(max-width:768px){

.preview-image{
    max-width:200px;
    height:150px;
}

.preview-arrow{
    width:32px;
    height:32px;
}
  .form-check{
        margin-top:15px;
        margin-bottom:25px;
    }
    .row{
        display:block;
    }

    .row > .col-6{
        width:100%;
        max-width:100%;
        flex:0 0 100%;
        margin-bottom:15px;
    }
}
.form-check{
    margin-top:15px;
    margin-bottom:15px;
    margin-left:0;
    display:flex;
    align-items:flex-start;
    gap:8px;
}

.form-check-input{
    width:16px;
    height:16px;
    margin-top:0;
    cursor:pointer;
}

.form-check-label{
    font-size:.80rem;      /* mas maliit */
    color:#fff;
    line-height:1.3;
    margin:0;
}


.submit-wrapper{
    display:flex;
    justify-content:flex-end;
    margin-top:20px;
    padding-right:0;
}

.submit-btn{
    min-width:130px;
    padding:9px 22px;
    font-size:.82rem;
    transform:none;
}

/* LOGOUT SWEETALERT */

.logout-popup{
    width:350px !important;
    border-radius:15px !important;
    padding:20px !important;
}

.logout-title{
    font-size:1rem;
    font-weight:600;
    color:#555;
    white-space:nowrap;
    margin:0 0 20px 0;
}


.logout-yes,
.logout-cancel{
    font-size:.85rem !important;
    padding:8px 22px !important;
    border-radius:8px !important;
}


/* MOBILE */

@media(max-width:768px){

    .logout-popup{
        width:85% !important;
        padding:15px !important;
    }


    .logout-title{
        font-size:.75rem !important;
        white-space:nowrap !important;
        margin-bottom:15px !important;
    }


    .logout-yes,
    .logout-cancel{
        font-size:.75rem !important;
        padding:7px 18px !important;
    }

}
</style>

</head>

<body>

<!-- ==========================
     NAVBAR
========================== -->

<nav class="navbar navbar-dark fixed-top">

    <div class="container-fluid position-relative">

        <!-- Logo -->

        <a class="navbar-brand" href="resident-home.php">

        <img src="assets/enviromanage-logo.png">
        </a>

        <!-- Current Location -->

        <div class="location-wrapper">

            <div class="location-btn" id="locationToggle">

                <i class="bi bi-geo-alt-fill"></i>

                <span id="currentLocation">

                    <?= htmlspecialchars($address) ?>

                </span>

                <i class="bi bi-chevron-down" id="locationArrow"></i>

            </div>

        </div>

        <!-- Right Side -->

 <ul class="navbar-nav flex-row align-items-center ms-auto">
     

            <!-- Profile -->

            <li class="nav-item dropdown">

         <a class="nav-link text-white p-0"
   href="#"
   id="profileDropdown"
   role="button"
   data-bs-toggle="dropdown"
   data-bs-display="static"
   aria-expanded="false">
                    <i class="bi bi-person-circle fs-4"></i>

                </a>

                   <ul class="dropdown-menu dropdown-menu-end"
        aria-labelledby="profileDropdown">

        <li>
    <button class="dropdown-item text-center"
onclick="confirmLogout()">
    Logout <i class="bi bi-box-arrow-right ms-1"></i>
</button>
        </li>

    </ul>


            </li>

        </ul>

    </div>

</nav>

<!-- ==========================
     LOCATION DROPDOWN
========================== -->

<div class="location-search" id="locationSearch">

    <div class="container" style="max-width:600px;">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <h6 class="fw-semibold mb-3">

                    <i class="bi bi-geo-alt-fill text-success"></i>

                    Select Pickup Address

                </h6>

                <div class="list-group mb-3">

             <label class="list-group-item d-flex align-items-start text-start">

    <input
        class="form-check-input me-3 mt-1"
        type="radio"
        checked>

    <div class="flex-grow-1 text-start">

        <div class="fw-semibold text-success">
            Current Address
        </div>

        <small class="d-block text-start">
            <?= htmlspecialchars($address) ?>
        </small>

    </div>

</label>

                </div>

                <button
                    class="btn btn-success w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#addAddressModal">

                    <i class="bi bi-plus-circle me-1"></i>

                    Add New Address

                </button>

            </div>

        </div>

    </div>

</div>
<!-- ==========================
     ADD ADDRESS MODAL
========================== -->

<div class="modal fade" id="addAddressModal" tabindex="-1">

 <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">

                <h5 class="modal-title">

                    Add Pickup Address

                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>

            </div>

       <div class="modal-body py-2 px-3">
            <div class="mb-2">
                    <label class="form-label">

                        House No.

                    </label>

               <input type="text"
       class="form-control"
       id="modalHouseNo"
       placeholder="House Number">
                </div>

            <div class="mb-2">
                    <label class="form-label">

                        Barangay

                    </label>

                  <input type="text"
       class="form-control"
       id="modalBarangay"
       placeholder="Barangay">
                </div>

              <div class="mb-2">
                    <label class="form-label">

                        Street / Sitio / Purok

                    </label>

                  <input type="text"
       class="form-control"
       id="modalStreet"
       placeholder="Street">
                </div>

          <div class="mb-2">
                    <label class="form-label">

                        Postal Code

                    </label>

                 <input type="text"
       class="form-control"
       id="modalPostal"
       value="4322">
                </div>

            </div>

<div class="modal-footer py-2">
              

                <button class="btn btn-success">

                    Save Address

                </button>

            </div>

        </div>

    </div>

</div>


<div class="sidebar">

    <nav class="nav flex-column">

        <a class="nav-link" href="resident-home.php">
            <i class="bi bi-house-door-fill"></i>
            <span>Home</span>
        </a>

        <a class="nav-link active" href="resident-schedule-pickup.php">
            <i class="bi bi-calendar-week-fill"></i>
            <span>Schedule Pickup</span>
        </a>

        <a class="nav-link" href="resident-profile.php">
            <i class="bi bi-person-circle"></i>
            <span>Profile</span>
        </a>

    </nav>

</div>
    <!-- MAIN CONTENT -->

    <div class="main-content">
<div class="page-header"></div>
    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <form id="pickupForm">

                <!-- Resident Information -->

           <div class="resident-info-card">

    <div class="d-flex justify-content-between align-items-start">

        <div>

            <div>
                <strong>Name :</strong>
                <?= htmlspecialchars($residentName) ?>
            </div>

            <div>
                <strong>Address :</strong>
                <?= htmlspecialchars($address) ?>
            </div>

            <div>
                <strong>Contact # :</strong>
                <?= htmlspecialchars($contactNumber) ?>
            </div>

        </div>

   <button
    type="button"
    class="btn btn-link p-0 text-white"
    id="editAddressBtn">

    <i class="bi bi-pencil-square fs-5"></i>

</button>

    </div>

</div>

<hr class="my-4">
<!-- =====================================
CLASSIFICATION
===================================== -->
<div class="row">

  <div class="col-6 mb-3">

    <label class="form-label fw-bold">
        Classification
    </label>

 <div class="classification-wrapper d-flex align-items-center gap-4 mt-2 flex-wrap">
        <div class="form-check m-0">
            <input class="form-check-input"
                   type="radio"
                   name="classification"
                   id="bio"
                   value="Biodegradable">

            <label class="form-check-label ms-1" for="bio">
                Biodegradable
            </label>
        </div>

        <div class="form-check m-0">
            <input class="form-check-input"
                   type="radio"
                   name="classification"
                   id="nonbio"
                   value="Non-biodegradable">

            <label class="form-check-label ms-1" for="nonbio">
                Non-biodegradable
            </label>
        </div>

    </div>

</div>

    <div class="col-6 mb-3">

        <label class="form-label fw-bold">
            Waste Type
        </label>

        <select class="form-select" id="wasteType">

            <option value="" selected hidden>
                Select Waste Type
            </option>

            <option>Food Waste</option>
            <option>Plastic</option>
            <option>Paper</option>
            <option>Glass</option>
            <option>Metal</option>
            <option>Leaves</option>

        </select>

    </div>

</div>

<!-- =====================================
DESCRIPTION
===================================== -->

<div class="mb-3">

    <label class="form-label fw-bold">
        Description
    </label>

    <textarea class="form-control" rows="3"></textarea>

</div>

<div class="row">

    <div class="col-6 mb-3">

        <label class="form-label fw-bold">
            Preferred Date & Time
        </label>

        <input type="datetime-local"
               class="form-control">

    </div>

    <div class="col-6 mb-3">

        <label class="form-label fw-bold">
            Weight (kg)
        </label>

        <input type="number"
               class="form-control">

    </div>

</div>

<!-- =====================================
UPLOAD
===================================== -->

<div class="mb-3">

    <label class="form-label fw-bold">
        Upload Photo
    </label>

 <div class="upload-box" id="uploadBox">

    <div id="uploadPlaceholder">

        <i class="bi bi-cloud-arrow-up"></i>

        <div class="mt-3">

            <strong>
                Drag & Drop or
                <span class="text-primary">
                    Choose file
                </span>
                to upload
            </strong>

            <div class="small text-muted mt-1">
                JPG, JPEG, PNG
            </div>

        </div>

    </div>

    <div id="imagePreviewWrapper" class="d-none">

        <div class="preview-container">

            <button
                type="button"
                class="preview-arrow left"
                id="prevImage">

                <i class="bi bi-chevron-left"></i>

            </button>

            <img
                id="previewImage"
                class="preview-image"
                alt="Preview">

            <button
                type="button"
                class="preview-arrow right"
                id="nextImage">

                <i class="bi bi-chevron-right"></i>

            </button>

        </div>

        <div
            id="previewCounter"
            class="text-white fw-semibold mt-2">
        </div>

    </div>

    <div class="mt-3">

        <input
            type="file"
            class="form-control"
            id="photoUpload"
            accept="image/*"
            multiple>

    </div>

</div>
<div id="imagePreviewWrapper" class="mt-3 d-none">

    <div style="position:relative; display:inline-block;">

        <button type="button" class="preview-arrow left" id="prevImage">
            <i class="bi bi-chevron-left"></i>
        </button>

        <img id="previewImage" class="preview-image">

        <button type="button" class="preview-arrow right" id="nextImage">
            <i class="bi bi-chevron-right"></i>
        </button>

    </div>

    <div id="previewCounter" class="text-center mt-2 text-white"></div>

</div>


</div>
                <div class="form-check ">

                    <input
                    class="form-check-input"
                    type="checkbox"
                    id="agreeTerms">

                    <label
                    class="form-check-label"
                    for="agreeTerms">

                        I confirm that the information provided is accurate.

                    </label>

                </div>

<div class="submit-wrapper">

<button
    type="submit"
    class="btn btn-success submit-btn">
        Submit

    </button>

</div>
  
            </form>

        </div>

    </div>

</div>
</div>

</div>

<!-- =========================================
MOBILE NAVIGATION
========================================= -->

<nav class="mobile-nav">

<a href="resident-home.php">

<i class="bi bi-house-door-fill"></i>

<span>Home</span>

</a>

<a
href="resident-schedule-pickup.php"
class="active">

<i class="bi bi-calendar-week-fill"></i>

<span>Schedule Pickup</span>

</a>

<a href="resident-profile.php">

<i class="bi bi-person-fill"></i>

<span>Profile</span>

</a>

</nav>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>


// Toggle Location Dropdown

const locationToggle = document.getElementById("locationToggle");
const locationSearch = document.getElementById("locationSearch");
const locationArrow = document.getElementById("locationArrow");

if(locationToggle){

    locationToggle.addEventListener("click",function(){

        locationSearch.classList.toggle("show");

        locationArrow.classList.toggle("bi-chevron-up");
        locationArrow.classList.toggle("bi-chevron-down");

    });

}

// Close Location Dropdown

document.addEventListener("click",function(e){

    if(
        locationToggle &&
        !locationToggle.contains(e.target) &&
        !locationSearch.contains(e.target)
    ){

        locationSearch.classList.remove("show");

        locationArrow.classList.remove("bi-chevron-up");
        locationArrow.classList.add("bi-chevron-down");

    }

});


/*=========================================
TODAY DATE ONLY
=========================================*/

const dateInput = document.querySelector('input[type="datetime-local"]');

if(dateInput){

    const today = new Date();

    today.setMinutes(today.getMinutes() - today.getTimezoneOffset());

    dateInput.min = today.toISOString().slice(0,16);

}
const fileInput=document.getElementById("photoUpload");

const preview=document.getElementById("previewImage");
const uploadPlaceholder = document.getElementById("uploadPlaceholder");
const wrapper=document.getElementById("imagePreviewWrapper");

const counter=document.getElementById("previewCounter");

const prevBtn=document.getElementById("prevImage");

const nextBtn=document.getElementById("nextImage");

let files=[];

let current=0;

function showImage(index){

    if(files.length===0){

        wrapper.classList.add("d-none");

        return;

    }

    const reader=new FileReader();

    reader.onload=function(e){

        preview.src=e.target.result;

    };

    reader.readAsDataURL(files[index]);

    counter.innerHTML=`${index+1} / ${files.length}`;

    if(files.length<=2){

        prevBtn.style.display="none";

        nextBtn.style.display="none";

    }else{

        prevBtn.style.display="flex";

        nextBtn.style.display="flex";

    }

    wrapper.classList.remove("d-none");

}

fileInput.addEventListener("change",function(){

    files=[...this.files];

    if(files.length===0){

        wrapper.classList.add("d-none");

        return;

    }

    const allowed=[
        "image/jpeg",
        "image/png",
        "image/jpg"
    ];

    for(let file of files){
if(file.size > 5 * 1024 * 1024){

    Swal.fire({
        icon:"warning",
        title:"File Too Large",
        text:"Maximum file size is 5MB."
    });

    this.value="";
    files=[];
    wrapper.classList.add("d-none");

    return;

}
        if(!allowed.includes(file.type)){

            Swal.fire({
                icon:"error",
                title:"Invalid File",
                text:"Only JPG, JPEG and PNG files are allowed."
            });

            this.value="";

            files=[];

            wrapper.classList.add("d-none");

            return;

        }

    }

    current=0;

    showImage(current);

});

prevBtn.onclick=function(){

    current--;

    if(current<0){

        current=files.length-1;

    }

    showImage(current);

};

nextBtn.onclick=function(){

    current++;

    if(current>=files.length){

        current=0;

    }

    showImage(current);

};
// EDIT ADDRESS BUTTON

const editAddressBtn = document.getElementById("editAddressBtn");

editAddressBtn.addEventListener("click", function(){

    document.getElementById("modalHouseNo").value = "<?= htmlspecialchars($row['house_no'] ?? '') ?>";

    document.getElementById("modalBarangay").value = "<?= htmlspecialchars($row['barangay'] ?? '') ?>";

    document.getElementById("modalStreet").value = "<?= htmlspecialchars($row['street'] ?? '') ?>";

    document.getElementById("modalPostal").value = "4322";


    const modal = new bootstrap.Modal(
        document.getElementById("addAddressModal")
    );

    modal.show();

});
/*=========================================
SUBMIT VALIDATION WITH SWEETALERT
=========================================*/

const form = document.getElementById("pickupForm");

form.addEventListener("submit", function(e){

    e.preventDefault();


    const classification = document.querySelector(
        'input[name="classification"]:checked'
    );

    const wasteType = document.getElementById("wasteType").value;

    const description = document.querySelector("textarea").value.trim();

    const dateTime = document.querySelector(
        'input[type="datetime-local"]'
    ).value;

    const weight = document.querySelector(
        'input[type="number"]'
    ).value;

    const agree = document.getElementById("agreeTerms").checked;
// CHECK IF ALL FIELDS ARE EMPTY

if(
    !classification &&
    wasteType === "" &&
    description === "" &&
    dateTime === "" &&
    weight === "" &&
    fileInput.files.length === 0 &&
    !agree
){

    Swal.fire({
        icon:"warning",
        title:"Incomplete Information",
        text:"Please fill in all fields before submitting."
    });

    return false;
}


// 1. Classification Required
if(!classification){

    Swal.fire({
        icon:"warning",
        title:"Classification Required",
        text:"Please select waste classification."
    });

    return false;
}


// 2. Waste Type Required
if(wasteType === ""){

    Swal.fire({
        icon:"warning",
        title:"Waste Type Required",
        text:"Please select waste type."
    });

    return false;
}


// 3. Description Required
if(description === ""){

    Swal.fire({
        icon:"warning",
        title:"Description Required",
        text:"Please enter garbage description."
    });

    return false;
}


// 4. Description Length
if(description.length < 10){

    Swal.fire({
        icon:"warning",
        title:"Description Too Short",
        text:"Description must contain at least 10 characters."
    });

    return false;
}


// 5. Schedule Required
if(dateTime === ""){

    Swal.fire({
        icon:"warning",
        title:"Schedule Required",
        text:"Please select preferred pickup date and time."
    });

    return false;
}


// 6. Invalid Schedule
let selectedDate = new Date(dateTime);
let currentDate = new Date();

if(selectedDate < currentDate){

    Swal.fire({
        icon:"warning",
        title:"Invalid Schedule",
        text:"Pickup schedule cannot be in the past."
    });

    return false;
}


// 7. Weight Required
if(weight === ""){

    Swal.fire({
        icon:"warning",
        title:"Weight Required",
        text:"Please enter estimated weight."
    });

    return false;
}


if(weight <= 0){

    Swal.fire({
        icon:"warning",
        title:"Invalid Weight",
        text:"Weight must be greater than 0 kg."
    });

    return false;
}


// 8. Photo Required
if(fileInput.files.length === 0){

    Swal.fire({
        icon:"warning",
        title:"Photo Required",
        text:"Please upload garbage photo."
    });

    return false;
}


// 9. Checkbox Required
if(!agree){

    Swal.fire({

        icon:"warning",

        title:"Checkbox Required",

        text:"Please confirm that the information provided is accurate."

    });

    return false;

}

 // 8. Final Submit Confirmation

Swal.fire({

    title:"Submit Pickup Request?",

    text:"Your garbage pickup request will be sent to MENRO for approval.",

    icon:"question",

    showCancelButton:true,

    confirmButtonText:"Yes, Submit",

    cancelButtonText:"Cancel",

    confirmButtonColor:"#1e5631",

    cancelButtonColor:"#6c757d"


}).then((result)=>{


    if(result.isConfirmed){


        Swal.fire({

            icon:"success",

            title:"Pickup Request Submitted!",

            text:"Your request has been successfully submitted and is now waiting for MENRO approval.",

            confirmButtonText:"OK",

            confirmButtonColor:"#1e5631"


        }).then(()=>{


            // reset form after success

            form.reset();

            files=[];

            wrapper.classList.add("d-none");

            counter.innerHTML="";


        });


    }


});
});

function confirmLogout(){

Swal.fire({

    html: `
        <h5 class="logout-title">
            Are you sure you want to log out?
        </h5>
    `,

    showCancelButton:true,

    confirmButtonText:"Yes",

    cancelButtonText:"Cancel",

    confirmButtonColor:"#e3344f",

    cancelButtonColor:"#6c757d",

    reverseButtons:true,

    customClass:{
        popup:'logout-popup',
        confirmButton:'logout-yes',
        cancelButton:'logout-cancel'
    },

    allowOutsideClick:false

}).then((result)=>{

    if(result.isConfirmed){
        window.location.href="logout.php";
    }

});

}
</script>

</body>

</html>