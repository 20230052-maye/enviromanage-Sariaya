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

<title>EnviroManage | Notifications</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

:root{
    --green:#1e5631;
    --green2:#2f7d44;
    --light:#f5f7f9;
    --card:#ffffff;
    --border:#e5e7eb;
    --text:#2d3436;
    --muted:#6c757d;
}

*{
    box-sizing:border-box;
}

  
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

/* ===========================
   SECTION TITLE
=========================== */

.section-title{

    color:#6c757d;

    font-size:1.05rem;

    font-weight:700;

    margin:30px 0 15px;

}

/* ===========================
   NOTIFICATION CARD
=========================== */

.notification-item{

    background:#fff;

    border:1px solid var(--border);

    border-radius:14px;

    padding:18px 20px;

    margin-bottom:15px;

    cursor:pointer;

    box-shadow:0 3px 10px rgba(0,0,0,.08);

    transition:.25s;

}

.notification-item:hover{

    transform:translateY(-3px);

    border-color:var(--green);

    box-shadow:0 8px 18px rgba(0,0,0,.12);

}

.notification-header{

    display:flex;

    justify-content:space-between;

    align-items:flex-start;

    gap:15px;

}

.notification-title{

    font-size:1.1rem;

    font-weight:700;

    color:#222;

    margin-bottom:8px;

}

.notification-preview{

    color:#555;

    margin:0;

    display:-webkit-box;

    -webkit-line-clamp:2;

    -webkit-box-orient:vertical;

    overflow:hidden;

}

.notification-time{

    white-space:nowrap;

    color:#777;

    font-size:.85rem;

}

/* ===========================
   MODAL
=========================== */

.modal-dialog{

    max-width:800px;

}

.modal-content{

    border:none;

    border-radius:18px;

    overflow:hidden;

}

.modal-header{

    border-bottom:1px solid #eee;

}

.modal-body{

    padding:25px;

}

.notification-info{

    background:#f8f9fa;

    border:1px solid #e9ecef;

    border-radius:12px;

    padding:14px 16px;

    margin-bottom:18px;

}

.notification-info i{

    color:var(--green);

    margin-right:8px;

}

.notification-message{

    background:#f8f9fa;

    border:1px solid #e9ecef;

    border-radius:12px;

    padding:18px;

    color:#444;

    line-height:1.8;

}

/* ===========================
   MOBILE NAVIGATION
=========================== */

.mobile-nav{

    display:none;

}

/* ===========================
   MOBILE
=========================== */

@media(max-width:768px){

.sidebar{

    display:none;

}

.main-content{

    margin-left:0;

    padding:18px 14px 90px;

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
.page-header{

    margin-bottom:20px;

}


.page-title{

    font-size:1.35rem;

}

.notification-item{

    padding:16px;

}

.notification-title{

    font-size:1rem;

}

.notification-preview{

    font-size:.92rem;

    -webkit-line-clamp:2;

}

.notification-time{

    font-size:.78rem;

}

.modal-dialog{
    max-width:88%;
    margin:.75rem auto;
}

.modal-content{
    border-radius:12px;
}

.modal-header{
    padding:12px 16px;
}

.modal-header h4{
    font-size:1.1rem;
    margin:0;
}

.modal-body{
    padding:14px;
}

.notification-info{
    display:flex;
    align-items:center;
    gap:6px;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
    font-size:.85rem;
}
.notification-message{
    padding:14px;
    font-size:.92rem;
    line-height:1.6;
}

.modal-footer{
    padding:10px 16px;
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

<!-- NAVBAR -->

<nav class="navbar navbar-dark fixed-top">

<div class="container-fluid position-relative">

    <!-- Logo -->

    <a class="navbar-brand" href="#">
        <img src="assets/enviromanage-logo.png">
    </a>

    <!-- Center Location -->

    <div class="location-wrapper">

        <div class="location-btn" id="locationToggle">

            <i class="bi bi-geo-alt-fill"></i>

       <span id="currentLocation">
    <?= htmlspecialchars($address) ?>
</span>
            <i class="bi bi-chevron-down" id="locationArrow"></i>

        </div>

    </div>

    <!-- Right Icons -->

 <ul class="navbar-nav flex-row align-items-center ms-auto">

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

<!-- LOCATION DROPDOWN -->

<div class="location-search" id="locationSearch">

    <div class="container" style="max-width:600px;">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <h6 class="fw-semibold mb-3">
                    <i class="bi bi-geo-alt-fill text-success"></i>
                    Select Pickup Address
                </h6>

                <div class="list-group mb-3">

    <!-- Current Address -->

    <label class="list-group-item d-flex align-items-start text-start">

        <input
    class="form-check-input me-3 mt-1"
    type="radio"
    name="pickup_address"
    value="0"
    <?= !isset($_SESSION['selected_address_id']) ? 'checked' : '' ?>
    data-address="<?= htmlspecialchars($address, ENT_QUOTES) ?>">

        <div class="flex-grow-1 text-start">

            <div class="fw-semibold text-success">
                Current Address
            </div>

            <small>
                <?= htmlspecialchars($address) ?>
            </small>

        </div>

    </label>

    <!-- Saved Addresses -->

    <?php foreach($addresses as $a): ?>

   <label class="list-group-item d-flex align-items-start text-start">

       <input
    class="form-check-input me-3 mt-1"
    type="radio"
    name="pickup_address"
    value="<?= $a['id'] ?>"
    <?= isset($_SESSION['selected_address_id']) && $_SESSION['selected_address_id']==$a['id'] ? 'checked' : '' ?>
    data-address="<?= htmlspecialchars($a['house_no'].', '.$a['street'].', '.$a['barangay'], ENT_QUOTES) ?>">

        <div class="flex-grow-1 text-start">

            <div class="fw-semibold">
                Saved Address
            </div>

            <small>
                <?= htmlspecialchars($a['house_no']) ?>,
                <?= htmlspecialchars($a['street']) ?>,
                <?= htmlspecialchars($a['barangay']) ?>
            </small>

        </div>

    </label>

    <?php endforeach; ?>

</div>

                <!-- Add Address -->

                <button
    type="button"
    class="btn btn-success w-100"
    onclick="event.stopPropagation()"
    data-bs-toggle="modal"
    data-bs-target="#addAddressModal">

    <i class="bi bi-plus-circle me-1"></i>
    Add New Address

</button>

            </div>

        </div>

    </div>

</div>

<!-- ADD ADDRESS MODAL -->

<div class="modal fade"
     id="addAddressModal"
     tabindex="-1">

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content">

<form id="addAddressForm">

<div class="modal-header bg-success text-white">

    <h5 class="modal-title">
        Add Pickup Address
    </h5>

    <button
        type="button"
        class="btn-close btn-close-white"
        data-bs-dismiss="modal">
    </button>

</div>

<div class="modal-body">

    <div class="mb-3">

        <label class="form-label">
            House No.
        </label>

        <input
            type="text"
            class="form-control"
            name="house_no"
            required>

    </div>


    <div class="mb-3">

    <label class="form-label">
        Barangay
    </label>

    <input
        type="text"
        class="form-control"
        id="barangay"
        name="barangay"
        autocomplete="off"
        required>

    <div
        id="barangaySuggestions"
        class="list-group position-absolute w-100 shadow"
        style="z-index:1056;max-height:200px;overflow-y:auto;display:none;">
    </div>

</div>

 <div class="mb-3">

    <label class="form-label">
        Street / Sitio / Purok
    </label>

    <input
        type="text"
        class="form-control"
        id="street"
        name="street"
        autocomplete="off"
        required>

    <div
        id="streetSuggestions"
        class="list-group position-absolute w-100 shadow"
        style="z-index:1056;max-height:200px;overflow-y:auto;display:none;">
    </div>

</div>

    <div class="mb-3">

        <label class="form-label">
            Postal Code
        </label>

        <input
            type="text"
            class="form-control"
            name="postal_code"
            value="4322">

    </div>

    <div class="form-check">

        <input
            class="form-check-input"
            type="checkbox"
            name="is_default"
            id="isDefault">

        <label
            class="form-check-label"
            for="isDefault">

            Set as Default Address

        </label>

    </div>

</div>

<div class="modal-footer">

   

    <button
        type="submit"
        class="btn btn-success">

        Save Address

    </button>

</div>

</form>

</div>

</div>

</div>
<!-- ===========================
     SIDEBAR
=========================== -->

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

<!-- ===========================
     MAIN CONTENT
=========================== -->

<div class="main-content">

<div class="page-top">

    <a href="resident-home.php" class="back-btn">
        <i class="bi bi-arrow-left"></i>
    </a>

    <h3 class="page-heading">
        NOTIFICATIONS
    </h3>

</div>

<!-- ===========================
     TODAY
=========================== -->

<h5 class="section-title">
    Today
</h5>

<div class="notification-item"
     data-bs-toggle="modal"
     data-bs-target="#notification1">

    <div class="notification-header">

        <div>

            <div class="notification-title">

                <i class="bi bi-trash-fill text-success me-2"></i>

                Garbage Collection

            </div>

            <p class="notification-preview">

                Non-Biodegradable waste collection will begin today at
                exactly 6:00 AM in Route 1.

            </p>

        </div>

        <div class="notification-time">

            6:00 AM

        </div>

    </div>

</div>

<div class="notification-item"
     data-bs-toggle="modal"
     data-bs-target="#notification2">

    <div class="notification-header">

        <div>

            <div class="notification-title">

                <i class="bi bi-truck text-success me-2"></i>

                Truck Update

            </div>

            <p class="notification-preview">

                Truck #2 is currently servicing Barangay Poblacion 2.

            </p>

        </div>

        <div class="notification-time">

            8:30 AM

        </div>

    </div>

</div>

<!-- ===========================
     YESTERDAY
=========================== -->

<h5 class="section-title">

    Yesterday

</h5>

<div class="notification-item"
     data-bs-toggle="modal"
     data-bs-target="#notification3">

    <div class="notification-header">

        <div>

            <div class="notification-title">

                <i class="bi bi-exclamation-circle-fill text-warning me-2"></i>

                Missed Collection

            </div>

            <p class="notification-preview">

                Collection in Sitio Riverside has been rescheduled due
                to heavy rainfall.

            </p>

        </div>

        <div class="notification-time">

            Jul 23

        </div>

    </div>

</div>

<!-- ===========================
     LAST WEEK
=========================== -->

<h5 class="section-title">

    Last Week

</h5>

<div class="notification-item"
     data-bs-toggle="modal"
     data-bs-target="#notification4">

    <div class="notification-header">

        <div>

            <div class="notification-title">

                <i class="bi bi-megaphone-fill text-success me-2"></i>

                MENRO Announcement

            </div>

            <p class="notification-preview">

                Residents are encouraged to properly segregate waste
                before collection.

            </p>

        </div>

        <div class="notification-time">

            Jul 18

        </div>

    </div>

</div>

<!-- ======================================
     NOTIFICATION MODAL 1
====================================== -->

<div class="modal fade"
     id="notification1"
     tabindex="-1">

<div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">

<div class="modal-content">

<div class="modal-header">

<div>

<h4 class="fw-bold mb-2">

<i class="bi bi-trash-fill text-success me-2"></i>

Garbage Collection

</h4>

<span class="badge bg-success">

Collection Schedule

</span>

</div>

<button class="btn-close"
        data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<div class="notification-info">

<i class="bi bi-calendar-event"></i>

<strong>Published:</strong>

July 24, 2026 • 6:00 AM

</div>

<div class="notification-message">

<p>

The assigned garbage truck will begin collecting
non-biodegradable waste in Route 1 starting at
6:00 AM.

</p>

<p>

Residents are advised to place their garbage
outside before the scheduled collection time.

</p>

</div>

</div>

</div>

</div>

</div>

<!-- ======================================
     NOTIFICATION MODAL 2
====================================== -->

<div class="modal fade"
     id="notification2"
     tabindex="-1">

<div class="modal-dialog modal-dialog-centered modal-lg">

<div class="modal-content">

<div class="modal-header">

<h4 class="fw-bold">

Truck Update

</h4>

<button class="btn-close"
        data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<div class="notification-info">

<i class="bi bi-clock-history"></i>

<strong>Updated:</strong>

Today • 8:30 AM

</div>

<div class="notification-message">

Truck #2 is currently servicing Barangay
Poblacion 2.

Estimated arrival to your area is within
30 minutes.

</div>

</div>

</div>

</div>

</div>

<!-- ======================================
     NOTIFICATION MODAL 3
====================================== -->

<div class="modal fade"
     id="notification3"
     tabindex="-1">

<div class="modal-dialog modal-dialog-centered modal-lg">

<div class="modal-content">

<div class="modal-header">

<h4 class="fw-bold">

Missed Collection

</h4>

<button class="btn-close"
        data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<div class="notification-info">

<i class="bi bi-calendar-event"></i>

<strong>Date:</strong>

July 23, 2026

</div>

<div class="notification-message">

Collection in Sitio Riverside has been postponed
due to heavy rainfall.

A new collection schedule will be announced
soon.

</div>

</div>

</div>

</div>

</div>

<!-- ======================================
     NOTIFICATION MODAL 4
====================================== -->

<div class="modal fade"
     id="notification4"
     tabindex="-1">

<div class="modal-dialog modal-dialog-centered modal-lg">

<div class="modal-content">

<div class="modal-header">

<h4 class="fw-bold">

MENRO Announcement

</h4>

<button class="btn-close"
        data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<div class="notification-info">

<i class="bi bi-megaphone-fill"></i>

<strong>Announcement</strong>

</div>

<div class="notification-message">

Residents are encouraged to properly segregate
their biodegradable and non-biodegradable waste
before collection.

Proper segregation helps improve waste management
and collection efficiency.

</div>

</div>

</div>

</div>

</div>
    </div>
    <!-- End Main Content -->

</div>
<!-- End App Layout -->

<!-- ===========================
     MOBILE BOTTOM NAVIGATION
=========================== -->

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
<!-- ===========================
     BOOTSTRAP
=========================== -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ===========================
     JAVASCRIPT
=========================== -->

<script>

// Hover effect
document.querySelectorAll(".notification-item").forEach(item=>{

    item.addEventListener("mouseenter",function(){

        this.style.borderColor="#1e5631";

    });

    item.addEventListener("mouseleave",function(){

        this.style.borderColor="#e8e8e8";

    });

});

// Prevent double click when clicking buttons (future-proof)
document.querySelectorAll(".notification-item button").forEach(btn=>{

    btn.addEventListener("click",function(e){

        e.stopPropagation();

    });

});

// Automatically scroll to top whenever a modal opens
document.querySelectorAll(".modal").forEach(modal=>{

    modal.addEventListener("shown.bs.modal",function(){

        const body=this.querySelector(".modal-body");

        if(body){

            body.scrollTop=0;

        }

    });

});
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
document.querySelectorAll('input[name="pickup_address"]').forEach(function(radio){

    radio.addEventListener("change",function(){

        document.getElementById("currentLocation").textContent =
            this.dataset.address;

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