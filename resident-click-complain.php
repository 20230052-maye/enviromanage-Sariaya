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

if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}



$address = "Unknown Location";


if(isset($_SESSION['user_id'])){


    $stmt = $conn->prepare("
        SELECT house_no, street, barangay
        FROM users
        WHERE id = ?
        LIMIT 1
    ");


    $stmt->bind_param(
        "i",
        $_SESSION['user_id']
    );


    $stmt->execute();


    $result = $stmt->get_result();


    if($row = $result->fetch_assoc()){


        $parts=[];


        if(!empty($row['house_no'])){
            $parts[]=$row['house_no'];
        }


        if(!empty($row['street'])){
            $parts[]=$row['street'];
        }


        if(!empty($row['barangay'])){
            $parts[]=$row['barangay'];
        }


        $address = implode(", ",$parts);

    }


    $stmt->close();

}


?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>Click & Complain</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



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

.main-content{
    margin-left:250px;
    padding:25px;
}


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

.location-btn span{
    max-width:320px;
    overflow:hidden;
    white-space:nowrap;
    text-overflow:ellipsis;
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


/* CAMERA CARD */


.complaint-card{

background:white;

border-radius:15px;

padding:20px;

box-shadow:0 3px 12px rgba(0,0,0,.08);

}



.camera-box{

height:420px;

background:#d9d9d9;

display:flex;

justify-content:center;

align-items:center;

color:#555;

font-size:60px;

border-radius:8px;
   position:relative;
    overflow:hidden;
}



#cameraPreview{

    width:100%;
    height:100%;
    object-fit:cover;
    border-radius:8px;
    transform:scaleX(1);

}


#cameraCanvas{

    display:none;

}

.camera-controls{

display:flex;

justify-content:space-between;

align-items:center;

margin-top:15px;

}



.capture-btn{

width:70px;

height:70px;

border-radius:50%;

background:white;

border:3px solid #ddd;

}



.side-camera{

font-size:30px;

color:#555;

}



.submit-btn{
    padding:10px 25px;
    border-radius:8px;
    display:block;
    margin-left:auto;
}
#profileDropdown{
    transform:translateX(-2px);
}
/* ==========================
   ADDRESS LIST
========================== */

.list-group-item{
    display:flex !important;
    align-items:flex-start !important;
    justify-content:flex-start !important;
    text-align:left;
    padding:15px 18px;
}

.list-group-item .form-check-input{
    margin-top:4px;
    margin-right:15px !important;
    flex-shrink:0;
}

.list-group-item > div{
    flex:1;
    text-align:left;
}

.list-group-item small{
    display:block;
    margin-top:3px;
    color:#6c757d;
    word-break:break-word;
}
.mobile-nav{
    display:none;
}




#complaintDetailsModal{



    z-index:1065;



}





#complaintDetailsModal .modal-content{



    border-radius:15px;

    box-shadow:0 8px 30px rgba(0,0,0,.35);



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


/* PHOTO SLIDER */



.photo-slider{



    display:flex;

    align-items:center;

    justify-content:center;

    gap:10px;



}


.photo-container{
    width:100%;
    display:flex;
    justify-content:center;
    align-items:center;
}



.photo-container img{
    width:100%;
    max-width:550px;
    height:340px;
    object-fit:cover;
    border-radius:12px;
    border:2px solid #1e5631;
}




.photo-control{



    width:35px;

    height:35px;



    border:none;



    border-radius:50%;



    background:#1e5631;



    color:white;



    display:flex;



    align-items:center;

    justify-content:center;



}






@media(max-width:768px){


.sidebar{

display:none;

}



.main-content{

margin-left:0;

padding:15px 12px 90px;

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
  /* MOBILE LOCATION CENTER */

.location-wrapper{
    position:absolute;
    left:51%;
    transform:translateX(-50%);
    width:65%;
    display:flex;
    justify-content:center;
    z-index:1;
}

.location-btn{
    width:100%;
    justify-content:center;
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
#complaintDetailsModal .modal-dialog{
    margin:0 auto;
    min-height:100%;
    display:flex;
    align-items:center;
}

    /* Bottom Navigation */

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
/* MY COMPLAINTS */

#complaintsModal .modal-dialog{

    margin:15px;

}


#complaintsModal .modal-content{

    height:85vh;
    max-height:85vh;

}

#complaintsModal .modal-body{

    overflow-y:auto;

}



/* ==========================
   MOBILE COMPLAINT DETAILS FIX
========================== */

/* ==========================
   MOBILE COMPLAINT DETAILS FIX
========================== */

#complaintDetailsModal .modal-dialog{

    margin:15px;
    display:flex;
    align-items:center;
    min-height:calc(100% - 30px);
    transform:none;

}


#complaintDetailsModal .modal-content{
    height:78vh;
    max-height:78vh;
    border-radius:15px;
}


#complaintDetailsModal .modal-body{

    overflow-y:auto;
    font-size:13px;
    padding:15px;

}


#complaintDetailsModal .modal-body{

    overflow-y:auto;
    font-size:13px;
    padding:15px;

}

/* MOBILE COMPLAINT PHOTO */

.photo-slider{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:12px;
    width:100%;
}


.photo-container{
    width:220px;
    height:170px;
    display:flex;
    justify-content:center;
    align-items:center;
}


.photo-container img{
    width:220px;
    height:170px;
    max-width:100%;
    object-fit:cover;
    border-radius:10px;
    border:2px solid #1e5631;
}


/* MOBILE PHOTO ARROWS */

.photo-control{
    width:35px;
    height:35px;
    border-radius:50%;
    font-size:14px;
    flex-shrink:0;
}

.page-header{

    margin-bottom:20px;

}


.page-title{

    font-size:1.35rem;

}



}
.capture-btn{

display:flex;
justify-content:center;
align-items:center;
font-size:28px;
color:#1e5631;
cursor:pointer;

}
#photoPreview{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    margin-top:15px;
}


.preview-item{
    position:relative;
    width:100px;
    height:100px;
}


.preview-item img{

    width:100px;
    height:100px;

    object-fit:cover;

    border-radius:10px;

    border:2px solid #1e5631;

}


.preview-item button{

    position:absolute;

    top:-8px;
    right:-8px;

    width:25px;
    height:25px;

    border:none;

    border-radius:50%;

    background:#dc3545;

    color:white;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:14px;

    cursor:pointer;

}
.complaint-action{
    display:flex;
    justify-content:flex-end;
    margin-bottom:15px;
}
#complaintsModal{
    z-index:1055;
}


#complaintsModal.show .modal-content{

    transition:.3s;

}


body.details-open #complaintsModal .modal-content{

    filter:brightness(.85);

    transform:scale(.98);

}




#complaintsModal .modal-content{



    border-radius:15px;

    box-shadow:0 5px 25px rgba(0,0,0,.25);



}



@media(min-width:769px){

   #complaintsModal .modal-dialog{
        max-width:1000px;
        width:90vw;
    }

    #complaintsModal .modal-content{
    max-height:90vh;
    border-radius:15px;
}

#complaintsModal .modal-body{
    overflow-y:auto;
}

  #complaintDetailsModal .modal-dialog{
    max-width:900px;
    width:85vw;
    margin:1rem auto;
    display:flex;
    align-items:center;
    min-height:calc(100vh - 2rem);
}

#complaintDetailsModal .modal-content{
    height:72vh;
    max-height:72vh;
}

#complaintDetailsModal .modal-body{
    overflow-y:auto;
    max-height:calc(90vh - 70px);
    padding-bottom:20px;
}

}



/* HIDE ARROW */



.photo-control.hidden{



    display:none;



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
    /* Complaint Details - same layout as desktop */
.complaint-info-row .col-6{
    font-size:12px;
    margin-bottom:10px;
}

.complaint-info-row strong{
    display:block;
    color:#1e5631;
    font-size:12px;
    margin-bottom:2px;
}

.complaint-info-row{
    --bs-gutter-x:.75rem;
    --bs-gutter-y:.75rem;
}

.complaint-info-row .col-6{
    word-break:break-word;
}
}


@media(max-width:576px){
/* Complaint Location input */
.form-control[readonly]{
    width:100%;
    box-sizing:border-box;
    font-size:11px;
    padding:6px 8px;
}

textarea.form-control{
    width:100%;
    box-sizing:border-box;
    font-size:11px;
    padding:6px 8px;
}
    .page-top{
        margin-bottom:18px;
    }

    .back-btn{
        width:36px;
        height:36px;
        font-size:18px;
    }

    .page-heading{
        font-size:18px;
    }

}
/* ==========================
   DESKTOP CAMERA SIZE FIX
========================== */

@media(min-width:769px){

  

    .camera-box{
        width:650px;
        height:450px;
        margin:0 auto;
    }


    .camera-controls{
        width:650px;
        margin:15px auto 0;
    }


    .side-camera{
        font-size:30px;
    }


    .capture-btn{
        width:70px;
        height:70px;
    }


    #photoPreview{
        width:650px;
        margin:15px auto 0;
        justify-content:center;
    }

}
/* IMAGE VIEWER */

.image-viewer{

    display:none;

    position:fixed;

    top:0;
    left:0;

    width:100%;
    height:100%;

    background:rgba(0,0,0,.95);

    z-index:2000;

    justify-content:center;
    align-items:center;

}


.image-viewer img{

    max-width:90%;
    max-height:90%;

    object-fit:contain;

    border-radius:10px;

}



.close-viewer{

    position:absolute;

    top:25px;
    right:35px;

    width:45px;
    height:45px;

    border:none;

    background:transparent;

    color:white;

    font-size:35px;

    z-index:2001;

}


.close-viewer i{

    font-size:40px;

}
/* =========================
   MODAL STACKING
========================= */

/* My Complaints */
#complaintsModal {
    z-index: 1050;
}

/* Complaint Details */
#complaintDetailsModal {
    z-index: 1060;
}

/* Appeal Complaint - FRONT */
#appealModal {
    z-index: 1070;
}

/* Default Bootstrap backdrop */
.modal-backdrop {
    z-index: 1040;
}

/* Backdrop when Complaint Details is open */
.modal-backdrop.details-backdrop {
    z-index: 1055;
}

/* Backdrop when Appeal is open */
.modal-backdrop.appeal-backdrop {
    z-index: 1065;
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

                    <label class="list-group-item d-flex align-items-start">

                        <input
                            class="form-check-input me-3 mt-1"
                            type="radio"
                            checked>

                        <div>

                            <div class="fw-semibold text-success">

                                Current Address

                            </div>

                            <small>

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

    <div class="modal-dialog modal-dialog-centered">

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

            <div class="modal-body">

                <div class="mb-3">

                    <label class="form-label">

                        House No.

                    </label>

                    <input type="text"
                           class="form-control"
                           placeholder="House Number">

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Barangay

                    </label>

                    <input type="text"
                           class="form-control"
                           placeholder="Barangay">

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Street / Sitio / Purok

                    </label>

                    <input type="text"
                           class="form-control"
                           placeholder="Street">

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Postal Code

                    </label>

                    <input type="text"
                           class="form-control"
                           value="4322">

                </div>

            </div>

            <div class="modal-footer">

               

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

        <a class="nav-link" href="resident-schedule-pickup.php">
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

      <div class="page-top">

    <a href="resident-home.php" class="back-btn">
        <i class="bi bi-arrow-left"></i>
    </a>

    <h3 class="page-heading">
        CLICK & COMPLAIN
    </h3>

</div>

<div class="complaint-card">

<div class="complaint-action">

   <button class="btn btn-success" onclick="viewComplaints()">
    <i class="bi bi-clock-history"></i>
    My Complaints
</button>
</div>

<div class="camera-box">

<video id="cameraPreview" autoplay playsinline></video>

<canvas id="cameraCanvas"></canvas>

</div>


<!-- PHOTO PREVIEW AREA -->

<div id="photoPreview" class="mt-3 d-flex flex-wrap gap-2"></div>


<div class="camera-controls">


<button class="btn side-camera" onclick="openGallery()">

<i class="bi bi-images"></i>

</button>





<button class="capture-btn" onclick="openCamera()">

<i class="bi bi-camera-fill"></i>

</button>


<button class="btn side-camera" onclick="switchCamera()">

<i class="bi bi-arrow-repeat"></i>

</button>

</div>






<div class="mt-4">

<div class="row g-3">

    <!-- Complaint Location -->
    <div class="col-md-6">

        <label class="fw-semibold mb-2">
            Complaint Location
        </label>

        <input 
        id="complaintLocation"
        type="text" 
        class="form-control"
        value="<?php echo htmlspecialchars($address); ?>"
        readonly>

    </div>
<!-- Complaint Category -->
<div class="col-md-6">

    <label class="fw-semibold mb-2">
        Complaint Category
    </label>
<select id="complaintCategory" class="form-select" required>

    <option value="" disabled selected hidden>
        Select Complaint Category
    </option>

    <option value="Missed Collection">
        Missed Collection
    </option>

    <option value="Overflowing Waste">
        Overflowing Waste
    </option>

    <option value="Illegal Dumping">
        Illegal Dumping
    </option>

    <option value="Uncollected Garbage">
        Uncollected Garbage
    </option>


    <option value="Other">
        Other
    </option>

</select>
</div>

</div>






<div class="mt-3">


<label class="fw-semibold mb-2">

Complaint Description

</label>


<textarea 
class="form-control"
rows="4"
placeholder="Describe your complaint..."></textarea>


</div>






<button 
class="btn btn-success mt-4 submit-btn"
onclick="submitComplaint()">

<i class="bi bi-send-fill"></i>

Submit Complaint

</button>



</div>


</div>





<!-- MOBILE NAVIGATION -->


<nav class="mobile-nav">

    <a href="resident-home.php">
        <i class="bi bi-house-door-fill"></i>
        <span>Home</span>
    </a>

    <a href="resident-schedule-pickup.php">
        <i class="bi bi-calendar-week-fill"></i>
        <span>Schedule Pickup</span>
    </a>

    <a href="resident-profile.php">
        <i class="bi bi-person-fill"></i>
        <span>Profile</span>
    </a>

</nav>



<!-- LOCATION MODAL -->


<div class="modal fade" id="locationModal">


<div class="modal-dialog modal-dialog-centered">


<div class="modal-content">


<div class="modal-header">

<h5 class="modal-title">

<i class="bi bi-geo-alt-fill"></i>

Current Location

</h5>


<button class="btn-close" data-bs-dismiss="modal"></button>


</div>



<div class="modal-body">


<p class="mb-0">

<?php echo htmlspecialchars($address); ?>

</p>


</div>


</div>


</div>


</div>

<!-- MY COMPLAINTS MODAL -->

<div class="modal fade" id="complaintsModal">

<div class="modal-dialog modal-lg modal-dialog-scrollable">

<div class="modal-content">


<div class="modal-header bg-success text-white">

<h5 class="modal-title">
<i class="bi bi-clock-history"></i>
My Complaints
</h5>

<button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

</div>


<div class="modal-body">


<div id="complaintsContainer">


<div class="text-center text-muted py-4">

<i class="bi bi-inbox fs-1"></i>

<h6 class="mt-2">
No complaints submitted yet.
</h6>

<p>
Your submitted complaints will appear here.
</p>

</div>


</div>


</div>


</div>

</div>

</div>



<!-- COMPLAINT DETAILS MODAL -->

<div class="modal fade" id="complaintDetailsModal">

<div class="modal-dialog modal-lg modal-dialog-scrollable">

<div class="modal-content">


<div class="modal-header bg-success text-white">

<h5 class="modal-title">
<i class="bi bi-file-earmark-text"></i>
Complaint Details
</h5>


<button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

</div>



<div class="modal-body" id="complaintDetails">


</div>


</div>

</div>

</div>
<!-- IMAGE VIEWER MODAL -->

<div id="imageViewer" class="image-viewer">

    <button class="close-viewer" onclick="closeImageViewer()">
        <i class="bi bi-x"></i>
    </button>

    <img id="viewerImage">

</div>
<!-- =========================
     APPEAL MODAL
========================= -->

<div
    class="modal fade"
    id="appealModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <!-- HEADER -->

            <div class="modal-header bg-success text-white">

                <h5 class="modal-title">

                    <i class="bi bi-arrow-repeat me-2"></i>

                    Appeal Returned Complaint

                </h5>


                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>

            </div>


            <!-- BODY -->

            <div class="modal-body">

                <input
                    type="hidden"
                    id="appealComplaintID">


                <!-- Ticket Number -->

                <div class="mb-3">

                    <label
                        class="form-label fw-semibold">

                        Ticket Number

                    </label>

                    <input
                        type="text"
                        id="appealTicketNo"
                        class="form-control"
                        readonly>

                </div>


                <!-- Reason for Return -->

                <div class="mb-3">

                    <label
                        class="form-label fw-semibold">

                        Reason for Return

                    </label>


                    <div
                        id="appealReturnReason"
                        class="border rounded p-3 bg-light text-muted">

                    </div>

                </div>


                <!-- Appeal Reason -->

                <div class="mb-3">

                    <label
                        for="appealReason"
                        class="form-label fw-semibold">

                        Appeal Reason

                        <span class="text-danger">
                            *
                        </span>

                    </label>


                    <textarea
                        id="appealReason"
                        class="form-control"
                        rows="5"
                        maxlength="1000"
                        placeholder="Explain why you are appealing this returned complaint or provide the additional information requested by the Barangay Secretary.">
                    </textarea>


                    <div
                        class="text-end small text-muted mt-1">

                        <span id="appealCharacterCount">
                            0
                        </span>/1000

                    </div>

                </div>


                <!-- INFORMATION -->

                <div
                    class="alert alert-info small mb-0">

                    <i
                        class="bi bi-info-circle me-1">
                    </i>

                    Your appeal will be reviewed by the
                    Barangay Secretary. You cannot submit another
                    appeal while this appeal is being reviewed.

                </div>

            </div>


            <!-- FOOTER -->

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary btn-sm"
                    data-bs-dismiss="modal">

                    Cancel

                </button>


                <button
                    type="button"
                    class="btn btn-success btn-sm"
                    id="submitAppealBtn"
                    onclick="submitAppeal()">

                    <i class="bi bi-send me-1"></i>

                    Submit Appeal

                </button>

            </div>

        </div>

    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



<script>

let cameraStream = null;

let currentCamera = "environment";

let photos = [];



// START CAMERA

async function startCamera(){


if(cameraStream){

cameraStream.getTracks().forEach(track=>track.stop());

}



try{


cameraStream = await navigator.mediaDevices.getUserMedia({

video:{
    facingMode: currentCamera
}

});



let video=document.getElementById("cameraPreview");


video.srcObject=cameraStream;


// Mirror front camera only
if(currentCamera === "user"){

    video.style.transform = "scaleX(-1)";

}else{

    video.style.transform = "scaleX(1)";

}


}

catch(error){


Swal.fire({

title:"Camera Error",

text:"Please allow camera permission.",

icon:"error",

confirmButtonColor:"#1e5631"

});


}


}




// FIRST CAMERA OPEN

function captureImage(){


startCamera();


}




// TAKE PHOTO

function openCamera(){


if(!cameraStream){

startCamera();

return;

}



let video=document.getElementById("cameraPreview");

let canvas=document.getElementById("cameraCanvas");



canvas.width=video.videoWidth;

canvas.height=video.videoHeight;



let ctx=canvas.getContext("2d");



if(currentCamera === "user"){

    ctx.translate(canvas.width,0);
    ctx.scale(-1,1);

}


ctx.drawImage(
video,
0,
0,
canvas.width,
canvas.height
);


let image=canvas.toDataURL("image/png");



photos.push(image);



displayPhotos();



}






// SWITCH FRONT/BACK

function switchCamera(){


if(currentCamera==="environment"){


currentCamera="user";


}else{


currentCamera="environment";


}



startCamera();


}





// DISPLAY PHOTOS BELOW
function displayPhotos(){

let container=document.getElementById("photoPreview");

container.innerHTML="";


photos.forEach((photo,index)=>{


container.innerHTML += `


<div class="preview-item">


<img 
src="${photo}"
onclick="openImageViewer('${photo}')">


<button onclick="removePhoto(${index})">
<i class="bi bi-x"></i>
</button>


</div>


`;


});


}
function openImageViewer(image){

    let viewer = document.getElementById("imageViewer");
    let img = document.getElementById("viewerImage");


    img.src = image;

    viewer.style.display="flex";

}



function closeImageViewer(){

    document.getElementById("imageViewer").style.display="none";

}
// REMOVE PHOTO

function removePhoto(index){


photos.splice(index,1);


displayPhotos();


}






// GALLERY

function openGallery(){


let input=document.createElement("input");


input.type="file";

input.accept="image/*";

input.multiple=true;



input.onchange=function(e){


Array.from(e.target.files).forEach(file=>{


let reader=new FileReader();



reader.onload=function(event){


photos.push(event.target.result);


displayPhotos();


}



reader.readAsDataURL(file);



});



};



input.click();


}

async function submitComplaint(){

let description = document.querySelector("textarea").value;
let location = document.getElementById("complaintLocation").value;
let category = document.getElementById("complaintCategory").value;

if (category === "") {
    Swal.fire({
        title: "Missing Information",
        text: "Please select a complaint category.",
        icon: "warning",
        confirmButtonColor: "#1e5631"
    });
    return;
}

Swal.fire({

    title:"Submit Complaint?",

    text:"Your complaint will be sent to Barangay Secretary for review.",

    icon:"question",

    showCancelButton:true,

    confirmButtonColor:"#1e5631",

    cancelButtonColor:"#6c757d",

    confirmButtonText:"Submit"

}).then(async(result)=>{


if(result.isConfirmed){


let formData = new FormData();


formData.append("location", location);
formData.append("category", category);
formData.append("description", description);

for(let i = 0; i < photos.length; i++){

    let blob = await fetch(photos[i])
    .then(response => response.blob());


    formData.append(
        "images[]",
        blob,
        "complaint_"+i+".png"
    );

}



fetch("resident-submit-complaint.php",{

    method:"POST",

    body:formData

})

.then(response=>response.json())

.then(data=>{


console.log(data);


if(data.status==="success"){


Swal.fire({

title:"Submitted!",

text:"Your complaint has been successfully submitted.",

icon:"success",

confirmButtonColor:"#1e5631"

});


document.querySelector("textarea").value="";

// Reset complaint category
document.getElementById("complaintCategory").value="";

photos=[];

displayPhotos();


}else{


Swal.fire({

title:"Error",

text:data.message,

icon:"error",

confirmButtonColor:"#1e5631"

});


}


})


.catch(error=>{

console.error(error);

Swal.fire({

title:"Error",

text:"Unable to submit complaint.",

icon:"error",

confirmButtonColor:"#1e5631"

});

});


}


});

}

function viewComplaints(){

    fetch("resident-get-complaints.php")

    .then(res => res.json())

    .then(data => {

        let container =
            document.getElementById("complaintsContainer");


        if(!container){

            console.error(
                "complaintsContainer not found."
            );

            return;
        }


        /* =========================
           NO COMPLAINTS
        ========================= */

        if(
            !data.success ||
            !data.complaints ||
            data.complaints.length === 0
        ){

            container.innerHTML = `

                <div class="text-center py-5">

                    <i class="bi bi-inbox fs-1 text-muted"></i>

                    <p class="text-muted mt-2">
                        No complaints submitted yet.
                    </p>

                </div>

            `;

        }else{

            container.innerHTML = "";


            data.complaints.forEach(item => {


                /* =========================
                   VALIDATION STATUS
                ========================= */

                let validationClass =
                    "bg-secondary";


                if(
                    item.validation_status ===
                    "Waiting"
                ){

                    validationClass =
                        "bg-secondary";

                }else if(
                    item.validation_status ===
                    "Under Review"
                ){

                    validationClass =
                        "bg-primary";

                }else if(
                    item.validation_status ===
                    "Approved"
                ){

                    validationClass =
                        "bg-success";

                }else if(
                    item.validation_status ===
                    "Rejected"
                ){

                    validationClass =
                        "bg-danger";

                }


                /* =========================
                   ACTION STATUS
                ========================= */

                let actionClass =
                    "bg-secondary";


                if(
                    item.action_status ===
                    "Pending Assignment"
                ){

                    actionClass =
                        "bg-warning text-dark";

                }else if(
                    item.action_status ===
                    "Assigned"
                ){

                    actionClass =
                        "bg-info text-dark";

                }else if(
                    item.action_status ===
                    "In Progress"
                ){

                    actionClass =
                        "bg-primary";

                }else if(
                    item.action_status ===
                    "Resolved"
                ){

                    actionClass =
                        "bg-success";

                }


                /* =========================
                   ACTION STATUS
                   ONLY AFTER APPROVED
                ========================= */

                let actionStatusHTML = "";


                if(
                    item.validation_status ===
                    "Approved"
                ){
actionStatusHTML = `

    <div class="mt-2">

        <span class="badge ${actionClass}">

            ${
                item.action_status ||
                "Pending Assignment"
            }

        </span>

    </div>

`;

                }


                /* =========================
                   CARD
                ========================= */

                container.innerHTML += `

                    <div
                        class="complaint-card mb-3"
                        onclick="showComplaintDetails(${item.id})"
                        style="cursor:pointer;">

                        <div
                            class="d-flex
                                   justify-content-between
                                   align-items-start">

                            <div>

                                <h6 class="fw-bold mb-1">

                                    ${item.ticket_no}

                                </h6>


                                <div
                                    class="text-muted small">

                                    ${
                                        item.complaint_location ||
                                        ""
                                    }

                                </div>

                            </div>


                            <span
                                class="badge ${validationClass}">

                                ${item.validation_status}

                            </span>

                        </div>


                        <p class="mt-3 mb-2">

                            ${
                                (item.description || "")
                                .substring(0,70)
                            }

                            ${
                                (item.description || "").length > 70
                                    ? "..."
                                    : ""
                            }

                        </p>


                        <div
                            class="small text-muted mb-2">

                            <i
                                class="bi bi-calendar3 me-1">
                            </i>

                            ${item.submitted_at}

                        </div>


                        ${actionStatusHTML}

                    </div>

                `;

            });

        }


        /* =========================
           OPEN MY COMPLAINTS MODAL
        ========================= */

        const modalElement =
            document.getElementById("complaintsModal");


        if(!modalElement){

            console.error(
                "complaintsModal not found."
            );

            return;
        }


        const modal =
            bootstrap.Modal.getOrCreateInstance(
                modalElement
            );


        modal.show();

    })

    .catch(error => {

        console.error(
            "Error loading complaints:",
            error
        );

    });

}

/* =========================
   OPEN APPEAL MODAL
========================= */

function openAppealModal(complaintID){

    fetch(
        "resident-get-complaint.php?id=" +
        encodeURIComponent(complaintID)
    )

    .then(res => res.json())

    .then(data => {

        if(!data.success){

            Swal.fire({
                icon: "error",
                title: "Unable to Open Appeal",
                text: data.message || "Complaint could not be found."
            });

            return;
        }


        let item = data.complaint;


        /* =========================
           SECURITY CHECK
        ========================= */

        if(item.validation_status !== "Rejected"){

            Swal.fire({
                icon: "warning",
                title: "Appeal Not Available",
                text: "Only rejected complaints can be appealed."
            });

            return;
        }


        if(item.appeal_status === "Pending"){

            Swal.fire({
                icon: "info",
                title: "Appeal Already Submitted",
                text: "Your appeal is currently being reviewed."
            });

            return;
        }


        if(item.appeal_status === "Approved"){

            Swal.fire({
                icon: "info",
                title: "Appeal Already Approved",
                text: "This complaint has already been accepted through appeal."
            });

            return;
        }


        /* =========================
           FILL MODAL
        ========================= */

        document.getElementById("appealComplaintID").value =
            item.id;

        document.getElementById("appealTicketNo").value =
            item.ticket_no || "";

        document.getElementById("appealReturnReason").textContent =
            item.remarks ||
            item.admin_notes ||
            "No return reason was provided.";

        document.getElementById("appealReason").value = "";

        document.getElementById("appealCharacterCount").textContent = "0";


        /* =========================
           SHOW MODAL
        ========================= */

        let modalElement =
            document.getElementById("appealModal");

        let modal =
            bootstrap.Modal.getOrCreateInstance(modalElement);

            function openAppealModal(id){

    const detailsModalEl =
        document.getElementById("complaintDetailsModal");

    const appealModalEl =
        document.getElementById("appealModal");

    // Save complaint ID
    document.getElementById("appealComplaintId").value = id;

    // Hide Complaint Details
    if(detailsModalEl){

        const detailsModal =
            bootstrap.Modal.getOrCreateInstance(
                detailsModalEl
            );

        detailsModal.hide();
    }

    // Open Appeal Modal
    if(appealModalEl){

        const appealModal =
            bootstrap.Modal.getOrCreateInstance(
                appealModalEl
            );

        appealModal.show();
    }
}
const appealModalEl =
    document.getElementById("appealModal");

if(appealModalEl){

    appealModalEl.addEventListener(
        "hidden.bs.modal",
        function(){

            // Show Complaint Details again
            const detailsModalEl =
                document.getElementById(
                    "complaintDetailsModal"
                );

            if(detailsModalEl){

                const detailsModal =
                    bootstrap.Modal.getOrCreateInstance(
                        detailsModalEl
                    );

                detailsModal.show();
            }
        }
    );
}
        modal.show();

    })

    .catch(error => {

        console.error(error);

        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Unable to load the complaint."
        });

    });

}


/* =========================
   CHARACTER COUNTER
========================= */

document.addEventListener("input", function(e){

    if(e.target.id === "appealReason"){

        document.getElementById("appealCharacterCount").textContent =
            e.target.value.length;

    }

});
/* =========================
   APPEAL MODAL STACKING
========================= */

document.addEventListener("show.bs.modal", function(e) {

    if (e.target.id !== "appealModal") {
        return;
    }

    const appealModal = e.target;

 
    // Wait for Bootstrap to create the backdrop
    setTimeout(function(){

        const backdrops =
            document.querySelectorAll(".modal-backdrop");

        if(backdrops.length > 0){

            // The newest backdrop belongs to Appeal Modal
            const appealBackdrop =
                backdrops[backdrops.length - 1];

            appealBackdrop.classList.add(
                "appeal-backdrop"
            );
        }

    }, 10);

});


document.addEventListener("shown.bs.modal", function(e) {

    if (e.target.id !== "appealModal") {
        return;
    }

    const detailsModal =
        document.getElementById("complaintDetailsModal");

    const complaintsModal =
        document.getElementById("complaintsModal");

    /*
       Keep both previous modals visible
       but underneath the Appeal Modal.
    */

    if(detailsModal &&
       detailsModal.classList.contains("show")){

        
    }

    if(complaintsModal &&
       complaintsModal.classList.contains("show")){

}
});


document.addEventListener("hidden.bs.modal", function(e) {

    if (e.target.id !== "appealModal") {
        return;
    }

    /*
       Remove Appeal backdrop styling
       after Appeal is closed.
    */

    document
        .querySelectorAll(".modal-backdrop")
        .forEach(function(backdrop){

            backdrop.classList.remove(
                "appeal-backdrop"
            );

        });

    /*
       Restore normal modal stacking.
    */

    const detailsModal =
        document.getElementById("complaintDetailsModal");

    const complaintsModal =
        document.getElementById("complaintsModal");

    if(detailsModal &&
       detailsModal.classList.contains("show")){

      
    }

    if(complaintsModal &&
       complaintsModal.classList.contains("show")){

    }

});
/* =========================
   SUBMIT APPEAL
========================= */

function submitAppeal(){

    let complaintID =
        document.getElementById("appealComplaintID").value;

    let appealReason =
        document.getElementById("appealReason").value.trim();


    if(!complaintID){

        Swal.fire({
            icon: "error",
            title: "Invalid Complaint",
            text: "Complaint information is missing."
        });

        return;
    }


    if(!appealReason){

        Swal.fire({
            icon: "warning",
            title: "Appeal Reason Required",
            text: "Please explain why you are appealing this complaint."
        });

        return;
    }


    if(appealReason.length < 10){

        Swal.fire({
            icon: "warning",
            title: "Appeal Reason Too Short",
            text: "Please provide more details about your appeal."
        });

        return;
    }


    let button =
        document.getElementById("submitAppealBtn");

    button.disabled = true;

    button.innerHTML = `
        <span class="spinner-border spinner-border-sm me-1"></span>
        Submitting...
    `;


    fetch("resident-submit-appeal.php", {

        method: "POST",

        headers: {
            "Content-Type":
                "application/x-www-form-urlencoded"
        },

        body:
            "complaint_id=" +
            encodeURIComponent(complaintID) +
            "&appeal_reason=" +
            encodeURIComponent(appealReason)

    })

    .then(res => res.json())

    .then(data => {

        button.disabled = false;

        button.innerHTML = `
            <i class="bi bi-send me-1"></i>
            Submit Appeal
        `;


        if(!data.success){

            Swal.fire({
                icon: "error",
                title: "Appeal Not Submitted",
                text: data.message || "Something went wrong."
            });

            return;
        }


        let modalElement =
            document.getElementById("appealModal");

        let modal =
            bootstrap.Modal.getInstance(modalElement);

        if(modal){

            modal.hide();

        }


        Swal.fire({
            icon: "success",
            title: "Appeal Submitted",
            text: "Your appeal has been submitted to the Barangay Secretary.",
            confirmButtonText: "OK"
        })
        .then(() => {

            viewComplaints();

        });

    })

    .catch(error => {

        console.error(error);

        button.disabled = false;

        button.innerHTML = `
            <i class="bi bi-send me-1"></i>
            Submit Appeal
        `;


        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Unable to submit your appeal."
        });

    });

}
let currentPhotos = [];
let currentPhotoIndex = 0;


function loadPhotos(photos){


currentPhotos = photos;

currentPhotoIndex = 0;


renderPhotos();


}

function renderPhotos(){

let container = document.getElementById("photoContainer");

if(!container) return;


container.innerHTML="";


let maxVisible = 1;



for(
let i=currentPhotoIndex;
i < currentPhotoIndex + maxVisible && i < currentPhotos.length;
i++
){

container.innerHTML += `
<img
    src="${currentPhotos[i]}"
    onclick="openImageViewer('${currentPhotos[i]}')"
    style="cursor:pointer;">
`;
}



// arrows


let prevBtn=document.querySelector(".photo-control:first-child");

let nextBtn=document.querySelector(".photo-control:last-child");


if(prevBtn){
    prevBtn.classList.toggle(
        "hidden",
        currentPhotos.length <= 1
    );
}

if(nextBtn){
    nextBtn.classList.toggle(
        "hidden",
        currentPhotos.length <= 1
    );
}

}
function prevPhoto(){

    if(currentPhotoIndex > 0){
        currentPhotoIndex--;
        renderPhotos();
    }

}

function nextPhoto(){

    if(currentPhotoIndex < currentPhotos.length - 1){
        currentPhotoIndex++;
        renderPhotos();
    }

}

function showComplaintDetails(id){
fetch("resident-get-complaint.php?id=" + id)
.then(response => response.json())
.then(data => {

    if(!data.success){
        return;
    }

   let item = data.complaint;
console.log("SUBMITTED DATE:", item.submitted_at);
let formattedDate = "";
let formattedTime = "";

if(item.submitted_at){

    let rawDate = item.submitted_at;

    // Remove microseconds kung meron
    rawDate = rawDate.split(".")[0];


    let submittedDate = new Date(
        rawDate.replace(" ","T")
    );


    if(!isNaN(submittedDate)){

        formattedDate = submittedDate.toLocaleDateString('en-US',{
            month:"long",
            day:"numeric",
            year:"numeric"
        });


        formattedTime = submittedDate.toLocaleTimeString('en-US',{
            hour:"numeric",
            minute:"2-digit",
            hour12:true
        });


    }else{

        formattedDate = rawDate;
        formattedTime = "";

    }

}

let details = document.getElementById("complaintDetails");
   let photosHTML = "";

if(item.photos && item.photos.length > 0){

    const showArrows = item.photos.length > 1;

    photosHTML = `
        <hr>

        <h6 class="fw-bold mb-3">
            Complaint Photo
        </h6>

        <div class="photo-slider">

            <button
                class="photo-control ${showArrows ? '' : 'hidden'}"
                id="prevBtn"
                onclick="prevPhoto()">
                <i class="bi bi-chevron-left"></i>
            </button>

            <div class="photo-container" id="photoContainer"></div>

            <button
                class="photo-control ${showArrows ? '' : 'hidden'}"
                id="nextBtn"
                onclick="nextPhoto()">
                <i class="bi bi-chevron-right"></i>
            </button>

        </div>
    `;
}
details.innerHTML = `

<div class="d-flex justify-content-between align-items-start mb-3">

    <div>
        <h5 class="fw-bold text-success mb-0">
            ${item.ticket_no}
        </h5>
    </div>

    <div class="text-end ms-2">
    <strong>
    ${formattedDate}
</strong>

<br>

<small>
    ${formattedTime}
</small>
    </div>

</div>
<hr>

<div class="row g-2 complaint-info-row">

    <div class="col-6">
        <strong>Category:</strong><br>
        ${item.category}
    </div>

    <div class="col-6">
        <strong>Location:</strong><br>
        ${item.complaint_location}
    </div>


    <!-- Validation Status -->
    <div class="col-6">
        <strong>Validation Status:</strong><br>

        <span class="badge ${
            item.validation_status === "Waiting" ? "bg-secondary" :
            item.validation_status === "Under Review" ? "bg-primary" :
            item.validation_status === "Approved" ? "bg-success" :
            "bg-danger"
        }">
            ${item.validation_status}
        </span>
    </div>


    ${
      item.validation_status === "Approved"

        ? `

            <!-- AFTER: Action Status beside Validation Status -->
            <div class="col-6">
                <strong>Action Status:</strong><br>

                <span class="badge ${
                    item.action_status === "Pending Assignment"
                        ? "bg-warning text-dark" :
                    item.action_status === "Assigned"
                        ? "bg-info text-dark" :
                    item.action_status === "In Progress"
                        ? "bg-primary" :
                    item.action_status === "Resolved"
                        ? "bg-success" :
                    "bg-danger"
                }">
                    ${item.action_status}
                </span>
            </div>

            <!-- AFTER: Description goes below -->
            <div class="col-12 mt-3">
                <strong>Description:</strong><br>
                ${item.description}
            </div>

        `

        : `

            <!-- BEFORE: Description beside Validation Status -->
            <div class="col-6">
                <strong>Description:</strong><br>
                ${item.description}
            </div>

        `
    }


    ${photosHTML}

${
    item.validation_status === "Rejected"

    ? `

        <div class="col-12 mt-3">

            <div class="alert alert-danger mb-3">

                <div class="fw-bold mb-2">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    Complaint Returned
                </div>

                <div class="small">
                    <strong>Reason:</strong>
                </div>

                <div class="mt-1">
                    ${
                        item.remarks ||
                        item.admin_notes ||
                        "No return reason was provided."
                    }
                </div>

            </div>


            ${
                item.appeal_status === "Pending"

                ? `

                    <button
                        type="button"
                        class="btn btn-warning w-100"
                        disabled>

                        <i class="bi bi-hourglass-split me-1"></i>
                        Appeal Pending

                    </button>

                `

                : item.appeal_status === "Approved"

                ? `

                    <button
                        type="button"
                        class="btn btn-success w-100"
                        disabled>

                        <i class="bi bi-check-circle me-1"></i>
                        Appeal Approved

                    </button>

                `

                : item.appeal_status === "Rejected"

                ? `

                    <button
                        type="button"
                        class="btn btn-danger w-100"
                        disabled>

                        <i class="bi bi-x-circle me-1"></i>
                        Appeal Rejected

                    </button>

                `

                : `

                  <div class="text-end">

    <button
        type="button"
        class="btn btn-primary btn-sm"
        onclick="openAppealModal(${item.id})">

        <i class="bi bi-arrow-repeat me-1"></i>
        Appeal Complaint

    </button>

</div>
                `
            }

        </div>

    `

    : ""
}
    `;
if(item.photos && item.photos.length > 0){
    loadPhotos(item.photos);
}

const complaintsModalEl =
    document.getElementById("complaintsModal");

const detailsModalEl =
    document.getElementById("complaintDetailsModal");

if(detailsModalEl){

    // Hide My Complaints first
    if(complaintsModalEl){

        const complaintsModal =
            bootstrap.Modal.getOrCreateInstance(
                complaintsModalEl
            );

        complaintsModal.hide();
    }

    // Open Complaint Details
    const detailsModal =
        bootstrap.Modal.getOrCreateInstance(
            detailsModalEl
        );

    detailsModal.show();
}

});



}
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
const detailsModal = document.getElementById("complaintDetailsModal");


detailsModal.addEventListener("show.bs.modal",function(){

    document.body.classList.add("details-open");

});


detailsModal.addEventListener(
    "hidden.bs.modal",
    function(){

        document.body.classList.remove("details-open");

        // Show My Complaints again
        const complaintsModalEl =
            document.getElementById("complaintsModal");

        if(complaintsModalEl){

            const complaintsModal =
                bootstrap.Modal.getOrCreateInstance(
                    complaintsModalEl
                );

            complaintsModal.show();
        }
    }
);
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
// Auto-open camera when the page loads
window.addEventListener("load", () => {
    startCamera();
});
</script>



</body>

</html>