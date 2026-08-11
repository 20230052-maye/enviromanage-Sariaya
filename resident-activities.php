<?php
session_start();

if(
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'resident'
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


$user_id = $_SESSION['user_id'];



/* ==========================
FETCH USER DATA
========================== */

$stmt = $conn->prepare("
SELECT
first_name,
middle_initial,
last_name,
house_no,
street,
barangay,
profile_photo
FROM users
WHERE id=?
LIMIT 1
");


$stmt->bind_param("i",$user_id);
$stmt->execute();

$resident = $stmt->get_result()->fetch_assoc();

$stmt->close();



/* ADDRESS */

$addressParts=[];

if(!empty($resident['house_no'])){
    $addressParts[]=$resident['house_no'];
}

if(!empty($resident['street'])){
    $addressParts[]=$resident['street'];
}

if(!empty($resident['barangay'])){
    $addressParts[]=$resident['barangay'];
}


$address = !empty($addressParts)
? implode(", ",$addressParts)
:"Unknown Location";


/* ==========================
SAMPLE ACTIVITIES (UI ONLY)
========================== */

$activities = [

    [
        "activity_type" => "Missed Pickup",
        "activity_date" => "2026-07-24",
        "status" => "Pending"
    ],

    [
        "activity_type" => "Overflowing Bin",
        "activity_date" => "2026-07-22",
        "status" => "Resolved"
    ],

    [
        "activity_type" => "Feedback",
        "activity_date" => "2026-07-20",
        "status" => "Resolved"
    ],

    [
        "activity_type" => "Schedule Pickup",
        "activity_date" => "2026-07-18",
        "status" => "Pending"
    ]

];
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Activities</title>


<link 
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<link 
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">


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
    margin-left:220px;
    padding:25px;
    width:calc(100% - 220px);
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


/* ACTIVITY CARD */


.activity-container{

   width:100%;
    max-width:none;
    margin:0;

}


.activity-card{

background:white;

border-radius:12px;

padding:12px 15px;

margin-bottom:10px;

box-shadow:0 2px 6px #0002;

display:flex;

justify-content:space-between;

align-items:center;

}



.activity-left{

display:flex;

gap:10px;

align-items:center;

}



.activity-left i{

font-size:18px;

}



.activity-name{

font-weight:600;

font-size:14px;

}



.activity-date{

font-size:12px;

color:#555;

}




.status{

padding:3px 12px;

border-radius:15px;

font-size:11px;

font-weight:600;

color:white;

}



.pending{

background:#d90429;

}



.resolved{

background:#9bc53d;

}




.filter-btn{

float:right;

border:none;

background:#ddd;

border-radius:8px;

padding:7px 14px;

font-size:13px;

margin-bottom:15px;

}



.mobile-nav{

display:none;

}
/* ==========================
MOBILE
========================== */


@media(max-width:991px){
.main-content{

    width:100%;
    margin-left:0;
    padding:20px;
    padding-bottom:90px;
    box-sizing:border-box;

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

.sidebar{

display:none;

}




.location-wrapper{

position:static;

transform:none;

margin:auto;

max-width:65%;

}



.location-btn{

width:100%;

overflow:hidden;

}



.location-btn span{

max-width:120px;

font-size:12px;

}



.activity-container{

width:100%;

}



.page-heading{
    font-size:18px;
    font-weight:700;
}

.back-btn{

width:36px;

height:36px;

font-size:18px;

}



.activity-card{

padding:10px;

}



.activity-name{

font-size:12px;

}



.activity-date{

font-size:11px;

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
    font-weight:700;
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
}
.filter-btn{
    background:#e9ecef;
    border:none;
    border-radius:8px;
    padding:8px 15px;
    font-size:13px;
    font-weight:600;
    color:#333;
}

.filter-btn:hover{
    background:#dfe3e6;
}

.dropdown-menu{
    min-width:200px;
    border-radius:10px;
}

.dropdown-item{
    font-size:14px;
    padding:10px 15px;
}

.dropdown-item:hover{
    background:#1e5631;
    color:#fff;
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
<!-- ==========================
MAIN CONTENT
========================== -->


<div class="main-content">


<div class="page-top">


<a href="resident-profile.php" class="back-btn">

<i class="bi bi-arrow-left"></i>

</a>


<h3 class="page-heading">

MY ACTIVITIES

</h3>


</div>



<div class="activity-container">


<div class="dropdown float-end mb-3">

  <button
    id="filterBtn"
    class="filter-btn dropdown-toggle"
    type="button"
    data-bs-toggle="dropdown"
    aria-expanded="false">

    <i class="bi bi-funnel"></i>
    Filter By

</button>

    <ul class="dropdown-menu dropdown-menu-end">

        <li>
            <a class="dropdown-item"
               href="#"
               onclick="applyFilter('All')">
                All Activities
            </a>
        </li>

        <li>
            <a class="dropdown-item"
               href="#"
               onclick="applyFilter('Missed Pickup')">
                Missed Pickup
            </a>
        </li>

        <li>
            <a class="dropdown-item"
               href="#"
               onclick="applyFilter('Overflowing Bin')">
                Overflowing Bin
            </a>
        </li>

        <li>
            <a class="dropdown-item"
               href="#"
               onclick="applyFilter('Feedback')">
                Feedback
            </a>
        </li>

        <li>
            <a class="dropdown-item"
               href="#"
               onclick="applyFilter('Schedule Pickup')">
                Schedule Pickup
            </a>
        </li>

    </ul>

</div>

<div style="clear:both;"></div>


<div style="clear:both;"></div>



<?php if(empty($activities)): ?>


<div class="text-center text-muted mt-5">

No activities found.

</div>



<?php else: ?>



<?php foreach($activities as $activity): ?>


<div class="activity-card"
     data-type="<?= htmlspecialchars($activity['activity_type']) ?>"
     data-date="<?= date('F d, Y', strtotime($activity['activity_date'])) ?>"
     data-status="<?= htmlspecialchars($activity['status']) ?>"
     onclick="openActivity(this)">


<div class="activity-left">


<?php
$icon = "bi-info-circle";

if($activity['activity_type']=="Missed Pickup"){

    $icon = "bi-trash-fill";

}
elseif($activity['activity_type']=="Feedback"){

    $icon = "bi-chat-dots-fill";

}
elseif($activity['activity_type']=="Overflowing Bin"){

    $icon = "bi-exclamation-triangle-fill";

}
elseif($activity['activity_type']=="Schedule Pickup"){

    $icon = "bi-calendar-check-fill";

}

?>


<i class="bi <?= $icon ?>"></i>



<div>


<div class="activity-name">

<?= htmlspecialchars($activity['activity_type']) ?>

</div>


<div class="activity-date">

<?= date("d-m-Y",strtotime($activity['activity_date'])) ?>

</div>


</div>


</div>





<span class="status 
<?= strtolower($activity['status'])=="pending"
?'pending'
:'resolved'
?>">


<?= htmlspecialchars($activity['status']) ?>


</span>



</div>


<?php endforeach; ?>



<?php endif; ?>



</div>



</div>



<div class="modal fade" id="activityModal">

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content">

<div class="modal-header bg-success text-white">

<h5 class="modal-title">
Activity Details
</h5>

<button
class="btn-close btn-close-white"
data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<div class="mb-3">

<label class="fw-semibold">
Activity
</label>

<div id="detailType"></div>

</div>

<div class="mb-3">

<label class="fw-semibold">
Date
</label>

<div id="detailDate"></div>

</div>

<div class="mb-3">

<label class="fw-semibold">
Status
</label>

<div id="detailStatus"></div>

</div>

<div class="mb-3">

<label class="fw-semibold">
Description
</label>

<p id="detailDescription" class="mb-0 text-muted"></p>

</div>

</div>

</div>

</div>

</div>
<!-- ==========================
FEEDBACK MODAL
========================== -->


<div class="modal fade" id="feedbackModal">


<div class="modal-dialog modal-dialog-centered">


<div class="modal-content">


<div class="modal-header bg-success text-white">


<h5 class="modal-title">

Feedback

</h5>


<button class="btn-close btn-close-white"
data-bs-dismiss="modal">

</button>


</div>



<div class="modal-body">


<p class="fw-semibold">

Help us improve BasuraTruck Sarigaya!

</p>



<label>

Barangay:

</label>


<select class="form-select mb-2">


<option>

Select Barangay

</option>


</select>



<label>

Rating:

</label>


<div class="fs-4">

☆ ☆ ☆ ☆ ☆

</div>



<label>

Comment:

</label>


<textarea class="form-control"
rows="4"></textarea>


</div>




<div class="modal-footer">


<button class="btn btn-success">

Submit

</button>


</div>



</div>


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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



<script>


/* LOCATION */


const locationToggle =
document.getElementById("locationToggle");


const locationSearch =
document.getElementById("locationSearch");



locationToggle.addEventListener("click",()=>{


locationSearch.classList.toggle("show");


});





document.addEventListener("click",(e)=>{


if(
!locationToggle.contains(e.target)
&&
!locationSearch.contains(e.target)
){

locationSearch.classList.remove("show");

}


});





/* FEEDBACK */


function openFeedback(){


let modal =
new bootstrap.Modal(
document.getElementById("feedbackModal")
);


modal.show();


}


/* ==========================
   FILTER ACTIVITIES
========================== */
function applyFilter(type) {

    const cards = document.querySelectorAll(".activity-card");

    cards.forEach(card => {

        const activityType = card.dataset.type;

        if (type === "All" || activityType === type) {
            card.style.display = "flex";
        } else {
            card.style.display = "none";
        }

    });

    document.getElementById("filterBtn").innerHTML =
        `<i class="bi bi-funnel"></i> ${type}`;

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