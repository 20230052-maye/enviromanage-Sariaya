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

while ($row = $result->fetch_assoc()) {
    $addresses[] = $row;
}

$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EnviroManage Resident</title>

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

.main-content{
    padding:25px;
}

.card{
    border:none;
    border-radius:12px;
    box-shadow:0 3px 8px rgba(0,0,0,.08);
}

.dashboard-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    grid-template-rows:repeat(2,1fr);
    gap:20px;
    height:calc(100vh - 145px);
}

.dashboard-card{
    background:#fff;
    border-radius:15px;
    box-shadow:0 3px 10px rgba(0,0,0,.08);
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
    padding:20px;
    transition:.25s;
    cursor:pointer;
}

.dashboard-card:hover{
    transform:translateY(-4px);
    box-shadow:0 8px 20px rgba(0,0,0,.12);
}

.dashboard-card i{
    font-size:170px;          /* Same visual size as dashboard images */
    margin-bottom:15px;
    line-height:1;
    display:block;
}

/* Individual icon colors */
.dashboard-card .bi-bell-fill{
    color:#f4c430;            /* Golden yellow */
}

.dashboard-card .bi-camera-fill{
    color:#4b5563;            /* Dark grey */
}

.dashboard-card .bi-recycle,
.dashboard-card .bi-newspaper{
    color:#1e5631;            /* Green */
}

.dashboard-card h6{
    margin:0;
    font-weight:600;
    color:#333;
}

.dashboard-card p{
    margin:6px 10px 0;
    font-size:.82rem;
    color:#777;
}

/* ===========================
   LAYOUT
=========================== */

.app-layout{
    display:flex;
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

.main-content{
    flex:1;
    margin-left:220px;
    padding:25px 35px;
    overflow:hidden;                 /* no desktop scroll */
    height:calc(100vh - 70px);       /* fit below navbar */
}

/* ===========================
   MOBILE BOTTOM BAR
=========================== */

.mobile-nav{
    display:none;
}

@media(max-width:768px){

    .sidebar{
        display:none;
    }

   .main-content{
        margin-left:0;
        padding:20px 15px 90px;
        overflow:auto;
        height:auto;
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

@media (max-width:768px){

   .main-content{
    margin-left:0;
    width:100%;
    height:calc(100vh - 70px);

    padding:10px 8px 92px;

    display:block;
    box-sizing:border-box;

    overflow:hidden;
}

   .dashboard-grid{
    width:100%;
    height:100%;
    display:grid;
    grid-template-columns:repeat(2, minmax(0,1fr));
    grid-template-rows:repeat(3,1fr);
    gap:8px;
    box-sizing:border-box;
}

    .dashboard-card{
        width:100%;
    height:100%;
    min-height:0;
    padding:5px;
        border-radius:12px;

        display:flex;
        flex-direction:column;
        justify-content:space-evenly;
        align-items:center;
        text-align:center;

        box-sizing:border-box;
    }

   .dashboard-card i{
    flex:0 0 auto;
    display:flex;
    align-items:center;
    justify-content:center;
     width:70px;
    height:70px;
    font-size:70px;
    margin-bottom:4px;
    line-height:1;
}

     .dashboard-card h6{
        margin:0;
    font-size:clamp(.85rem,3.5vw,1.05rem);
    font-weight:700;
    line-height:1.15;
    color:#222;
    text-align:center;
    }

    .dashboard-card p{
     margin:3px 0 0;
    padding:0 3px;
    font-size:clamp(.6rem,2.2vw,.75rem);
    line-height:1.2;
        color:#777;
    }
}

@media(max-width:768px){

  /* MOBILE LOCATION ELLIPSIS */

    .location-wrapper{
        position:static;
        transform:none;
        margin:auto;
        width:169px;   /* adjust kung gusto mo mas mahaba o maikli */
    }

    .location-btn{
        display:flex;
        align-items:center;
        width:100%;
        overflow:hidden;
    }

    .location-btn i{
        flex:0 0 auto;
    }

    .location-btn span{
        flex:1;
        min-width:0;
        overflow:hidden;
        white-space:nowrap;
        text-overflow:ellipsis;
        font-size:.75rem;
    }


.location-search{
    top:70px;
}

}


/* Dashboard images (Desktop) */
.dashboard-img{
    width:170px;
    height:170px;
    object-fit:contain;
    margin-bottom:15px;
    display:block;
}

/* Mobile */
@media (max-width:768px){
    .dashboard-img{
             width:70px;
        height:70px;
        margin-bottom:4px;
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

<script>

const sessionLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;

if(navigator.onLine){

    if(!sessionLoggedIn){

        localStorage.removeItem("logged_in");
        localStorage.removeItem("role");

        window.location.href="login.php";

    }else{

        localStorage.setItem("logged_in","true");
        localStorage.setItem("role","resident");

    }

}else{

    if(
        localStorage.getItem("logged_in")!=="true" ||
        localStorage.getItem("role")!=="resident"
    ){

        document.body.innerHTML=`
        <div style="
        display:flex;
        justify-content:center;
        align-items:center;
        height:100vh;
        text-align:center;
        font-family:sans-serif;
        ">
            <div>
                <h3>Offline Access Unavailable</h3>
                <p>Please login online first.</p>
            </div>
        </div>
        `;

    }

}

</script>

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

    <ul class="navbar-nav flex-row align-items-center h-100">


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

<!-- CONTENT -->

<!-- CONTENT -->

<div class="app-layout">

    <!-- Desktop Sidebar -->

<div class="sidebar">

    <nav class="nav flex-column">

        <a class="nav-link active" href="resident-home.php">
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

    <!-- Main Content -->

   <div class="main-content">
<div class="dashboard-grid">

    <div class="dashboard-card" onclick="window.location.href='resident-garbage-truck-status.php'">
    <img src="assets/gts.png"
         class="dashboard-img"
         alt="Garbage Truck">
    <h6>Garbage Truck Status</h6>
    <p>Track your assigned garbage truck.</p>
</div>

<div class="dashboard-card" onclick="window.location.href='resident-collection-schedule.php'">
    <img src="assets/calendar.png"
         class="dashboard-img"
         alt="Calendar">
    <h6>Collection Schedule</h6>
    <p>View pickup schedules.</p>
</div>

    <div class="dashboard-card" onclick="window.location.href='resident-notifications.php'">
        <i class="bi bi-bell-fill"></i>
        <h6>Notifications</h6>
        <p>Latest collection alerts.</p>
    </div>

    <div class="dashboard-card" onclick="window.location.href='resident-click-complain.php'">
        <i class="bi bi-camera-fill"></i>
        <h6>Click &amp; Complain</h6>
        <p>Report missed pickups with photos.</p>
    </div>

    <div class="dashboard-card">
        <img src="assets/recycle.png"
         class="dashboard-img"
         alt="Recycling">
        <h6>Solid Waste Management</h6>
        <p>Learn proper waste segregation.</p>
    </div>

  <div class="dashboard-card" onclick="window.location.href='resident-news.php'">
    <img src="assets/news.png"
         class="dashboard-img"
         alt="News">
    <h6>News &amp; Articles</h6>
    <p>Read environmental news and updates.</p>
</div>

</div>

        </div>


<!-- Mobile Bottom Navigation -->


<nav class="mobile-nav">

    <a href="resident-home.php" class="active">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

    const currentUserId = <?= (int)$_SESSION['user_id']; ?>;

const toggle=document.getElementById("locationToggle");
const search=document.getElementById("locationSearch");
const arrow=document.getElementById("locationArrow");

toggle.onclick=function(){

    if(search.classList.contains("show")){

        checkAddressChange();

    }else{

        search.classList.add("show");

        arrow.classList.remove("bi-chevron-down");
        arrow.classList.add("bi-chevron-up");

    }

};

document.addEventListener("click", function(e){

    // Dropdown is closed
    if(!search.classList.contains("show")){
        return;
    }

    // Ignore clicks on the location button
    if(toggle.contains(e.target)){
        return;
    }

    // Ignore clicks inside the dropdown
    if(search.contains(e.target)){
        return;
    }

    // Ignore clicks inside the Add Address modal
    const modal = document.getElementById("addAddressModal");

    if(modal.classList.contains("show")){
        return;
    }

    checkAddressChange();

});

const locationText = document.getElementById("currentLocation");

const savedAddress = localStorage.getItem(`pickup_address_${currentUserId}`);
const savedAddressId = localStorage.getItem(`pickup_address_id_${currentUserId}`);

if(savedAddress){

    locationText.textContent = savedAddress;

    const radio = document.querySelector(
        `input[name="pickup_address"][value="${savedAddressId}"]`
    );

    if(radio){
        radio.checked = true;
    }

}

let activeRadio =
    document.querySelector(
        'input[name="pickup_address"]:checked'
    ) ||
    document.querySelector(
        'input[name="pickup_address"]'
    );

let pendingRadio = activeRadio;

if(!localStorage.getItem(`pickup_address_${currentUserId}`)){

    localStorage.setItem(
    `pickup_address_${currentUserId}`,
    locationText.textContent.trim()
);

    localStorage.setItem(
    `pickup_address_id_${currentUserId}`,
    activeRadio.value
);

}

document.querySelectorAll(
    'input[name="pickup_address"]'
).forEach(radio=>{

    radio.addEventListener("change",function(){

        pendingRadio = this;

    });

});

function closeDropdown(){

    search.classList.remove("show");

    arrow.classList.remove("bi-chevron-up");
    arrow.classList.add("bi-chevron-down");

}
async function checkAddressChange(){

    if(activeRadio === pendingRadio){

        closeDropdown();
        return;

    }

    const result = await Swal.fire({

        title: "Change Pickup Address?",

        html: `
            <div class="text-start">

                <small class="text-muted">
                    Your pickup address will be changed to:
                </small>

                <div class="fw-semibold mt-2">
                    <i class="bi bi-geo-alt-fill text-success"></i>
                    ${pendingRadio.dataset.address}
                </div>

            </div>
        `,

        icon: "question",

        showCancelButton: true,

        confirmButtonText: "Change",

        cancelButtonText: "Cancel",

        confirmButtonColor: "#1e5631",

        reverseButtons: true,

        allowOutsideClick: false,

        focusCancel: true

    });

    if(result.isConfirmed){

        locationText.textContent =
    pendingRadio.dataset.address;

activeRadio = pendingRadio;

localStorage.setItem(
    `pickup_address_${currentUserId}`,
    pendingRadio.dataset.address
);

localStorage.setItem(
    `pickup_address_id_${currentUserId}`,
    pendingRadio.value
);
        const form = new FormData();

        form.append(
            "address_id",
            pendingRadio.value || 0
        );

        const response = await fetch(
            "resident-set-active-address.php",
            {
                method:"POST",
                body:form
            }
        );

        const data = await response.json();

        if(data.success){

            Swal.fire({

                toast:true,

                position:"top-end",

                icon:"success",

                title:"Pickup address updated",

                showConfirmButton:false,

                timer:1800,

                timerProgressBar:true

            });

        }

    }else{

        activeRadio.checked = true;
        pendingRadio = activeRadio;

    }

    closeDropdown();

}

fetch("barangays.json")
.then(res => res.json())
.then(data=>{

    const barangayInput = document.getElementById("barangay");
    const streetInput = document.getElementById("street");

    const barangaySuggestions =
        document.getElementById("barangaySuggestions");

    const streetSuggestions =
        document.getElementById("streetSuggestions");

    const barangays = Object.keys(data);

    // -------------------------
    // BARANGAY AUTOCOMPLETE
    // -------------------------

    barangayInput.addEventListener("input",function(){

        const value=this.value.toLowerCase();

        barangaySuggestions.innerHTML="";

        if(value===""){

            barangaySuggestions.style.display="none";
            return;

        }

        barangays
        .filter(b=>b.toLowerCase().includes(value))
        .forEach(barangay=>{

            const item=document.createElement("button");

            item.type="button";
            item.className="list-group-item list-group-item-action";
            item.textContent=barangay;

            item.onclick=function(){

                barangayInput.value=barangay;

                barangaySuggestions.style.display="none";

                streetInput.value="";

            };

            barangaySuggestions.appendChild(item);

        });

        barangaySuggestions.style.display =
            barangaySuggestions.children.length
            ? "block"
            : "none";

    });

    // -------------------------
    // STREET AUTOCOMPLETE
    // -------------------------

    streetInput.addEventListener("input",function(){

        const barangay = barangayInput.value;

        if(!data[barangay]){

            streetSuggestions.style.display="none";
            return;

        }

        const value=this.value.toLowerCase();

        streetSuggestions.innerHTML="";

        data[barangay]
        .filter(s=>s.toLowerCase().includes(value))
        .forEach(street=>{

            const item=document.createElement("button");

            item.type="button";
            item.className="list-group-item list-group-item-action";
            item.textContent=street;

            item.onclick=function(){

                streetInput.value=street;

                streetSuggestions.style.display="none";

            };

            streetSuggestions.appendChild(item);

        });

        streetSuggestions.style.display =
            streetSuggestions.children.length
            ? "block"
            : "none";

    });

    document.addEventListener("click",function(e){

        if(!barangayInput.contains(e.target))
            barangaySuggestions.style.display="none";

        if(!streetInput.contains(e.target))
            streetSuggestions.style.display="none";

    });

});


document
.getElementById("addAddressForm")
.addEventListener("submit",async function(e){

    e.preventDefault();

    const formData = new FormData(this);

    const response = await fetch(
        "resident-add-address.php",
        {
            method:"POST",
            body:formData
        }
    );

    const result = await response.json();

    if(result.success){

        location.reload();

    }else{

        alert("Unable to save address.");

    }

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