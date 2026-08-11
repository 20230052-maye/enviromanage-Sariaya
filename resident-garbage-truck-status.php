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

$address = "Unknown Location";

if (isset($_SESSION['user_id'])) {

    $stmt = $conn->prepare("
        SELECT house_no, street, barangay
        FROM users
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

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

<title>Garbage Truck Status</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

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

/*==========================
MAP CARD
==========================*/

.card{
    border:none;
    border-radius:14px;
    box-shadow:0 3px 12px rgba(0,0,0,.08);
}

#truckMap{
    width:100%;
    height:480px;
    border-radius:12px;
    background:#edf2f5;
    border:2px dashed #c9d1d9;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:22px;
    color:#8c8c8c;
    font-weight:600;
}

.map-legend{
    margin-top:15px;
    display:flex;
    gap:30px;
    flex-wrap:wrap;
    color:#666;
    font-size:.95rem;
}

.legend-item{
    display:flex;
    align-items:center;
    gap:8px;
}

.legend-dot{
    width:14px;
    height:14px;
    border-radius:50%;
}

.user-dot{
    background:#0d6efd;
}

.truck-dot{
    background:#198754;
}

/*==========================
TRUCK CARD
==========================*/

.truck-card{
    margin-top:25px;
}

.truck-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.refresh-btn{
    border:none;
    background:#1e5631;
    color:#fff;
    width:42px;
    height:42px;
    border-radius:50%;
    transition:.2s;
}

.refresh-btn:hover{
    transform:rotate(180deg);
}

.info-item{
    margin-bottom:18px;
}

.info-item h6{
    margin-bottom:5px;
    color:#1e5631;
    font-weight:700;
}

.info-item p{
    margin:0;
    color:#555;
}

.last-update{
    text-align:right;
    color:#888;
    font-size:.9rem;
}
/* ==========================
   RESPONSIVE
========================== */

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



@media (max-width:768px){

    /* Sidebar */

    .sidebar{
        display:none;
    }

    /* Main */

    .main-content{
        margin-left:0;
        padding:20px 15px 90px;
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

    /* Title */

   .page-header{

    margin-bottom:20px;

}


.page-title{

    font-size:1.35rem;

}

    /* Map */

    #truckMap{
        height:320px;
        font-size:18px;
    }

    /* Legend */

    .map-legend{
        flex-direction:column;
        gap:10px;
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
}
#profileDropdown{
    transform:translateX(-2px);
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
}


@media(max-width:576px){

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
        GARBAGE TRUCK STATUS
    </h3>

</div>

        <!-- MAP -->

        <div class="card p-4">

            <div id="truckMap">

                <div class="text-center">

                    <i class="bi bi-map display-1"></i>

                    <h4 class="mt-3">

                        Map Placeholder

                    </h4>

                    <p class="text-muted">

                        Live garbage truck tracking map
                        will appear here.

                    </p>

                </div>

            </div>

            <!-- LEGEND -->

            <div class="map-legend">

                <div class="legend-item">

                    <div class="legend-dot user-dot"></div>

                    <span>Your Location</span>

                </div>

                <div class="legend-item">

                    <div class="legend-dot truck-dot"></div>

                    <span>Garbage Truck</span>

                </div>

            </div>

        </div>

        <!-- TRUCK DETAILS -->

        <div class="card truck-card">

            <div class="card-body">

                <div class="truck-header">

                    <div>

                        <h4 class="mb-1">

                            <i class="bi bi-truck text-success me-2"></i>

                            Truck #1 (NKA-1234)

                        </h4>

                        <small class="text-muted">

                            Assigned Garbage Truck

                        </small>

                    </div>

                    <button class="refresh-btn">

                        <i class="bi bi-arrow-clockwise"></i>

                    </button>

                </div>

                <hr>

                <div class="row">

                    <div class="col-md-6">

                        <div class="info-item">

                            <h6>

                                Collector

                            </h6>

                            <p>

                                Juan Dela Cruz

                            </p>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="info-item">

                            <h6>

                                Current Location

                            </h6>

                            <p>

                                Rizal Street

                            </p>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="info-item">

                            <h6>

                                Assigned Route

                            </h6>

                            <p>

                                Route 1<br>
                                Poblacion 1<br>
                                Woodlane<br>
                                Balubal

                            </p>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="info-item">

                            <h6>

                                Waste Capacity

                            </h6>

                            <div class="progress mb-2"
                                 style="height:12px;">

                                <div class="progress-bar bg-success"
                                     style="width:75%;">

                                </div>

                            </div>

                            <p>

                                75% - Near Full

                            </p>

                        </div>

                    </div>

                </div>

                <div class="last-update">

                    Last Updated:
                    8:42 AM

                </div>

            </div>

        </div>

    </div>

</div>

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
<!-- ==========================
     BOOTSTRAP
========================== -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ==========================
     PAGE JAVASCRIPT
========================== -->

<script>

// Refresh Button Animation
const refreshBtn = document.querySelector(".refresh-btn");

if(refreshBtn){

    refreshBtn.addEventListener("click",function(){

        const icon = this.querySelector("i");

        icon.style.transition = "transform .5s ease";

        icon.style.transform = "rotate(360deg)";

        setTimeout(function(){

            icon.style.transform = "rotate(0deg)";

        },500);

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

// Placeholder for Future Map

const truckMap = document.getElementById("truckMap");

if(truckMap){

    console.log("Map placeholder loaded.");

    // Future Leaflet map initialization goes here.

}

// Placeholder Refresh Function

function refreshTruckStatus(){

    console.log("Refresh truck status.");

    // Future AJAX request

}

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