<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'resident'
) {
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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

/* ==========================
   FETCH RESIDENT INFORMATION
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
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$resident = $stmt->get_result()->fetch_assoc();

$stmt->close();

/* ==========================
   CURRENT ADDRESS
========================== */

$addressParts = [];

if (!empty($resident['house_no'])) {
    $addressParts[] = $resident['house_no'];
}

if (!empty($resident['street'])) {
    $addressParts[] = $resident['street'];
}

if (!empty($resident['barangay'])) {
    $addressParts[] = $resident['barangay'];
}

$address = !empty($addressParts)
    ? implode(", ", $addressParts)
    : "Unknown Location";


?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Resident Feedback</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">
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

/* ==========================
MAIN CONTENT
========================== */

.main-content{
    margin-left:220px;
    width:calc(100% - 220px);
    padding:25px;
}

/* ==========================
PAGE HEADER
========================== */

.page-top{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:25px;
}

.back-btn{
    width:42px;
    height:42px;
    border-radius:50%;
    background:#fff;
    border:1px solid #ddd;
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

/* ==========================
FEEDBACK CARD
========================== */

.feedback-card{
    background:#fff;
    border-radius:15px;
    box-shadow:0 2px 8px rgba(0,0,0,.15);
    padding:25px;
    width:100%;
    max-width:100%;
}

.feedback-card h5{
    font-weight:700;
    margin-bottom:20px;
}

.feedback-card label{
    font-weight:600;
    margin-bottom:6px;
}

.form-select,
.form-control{
    border-radius:10px;
}

textarea{
    resize:none;
}

/* ==========================
STAR RATING
========================== */

.star-rating{
    display:flex;
    gap:8px;
    font-size:34px;
    margin-bottom:15px;
}

.star{
    color:#ccc;
    cursor:pointer;
    transition:.2s;
}

.star.active{
    color:#ffc107;
}

/* ==========================
SUBMIT BUTTON
========================== */

.submit-btn{
    background:#a8c66c;
    border:none;
    color:#000;
    border-radius:30px;
    padding:10px 35px;
    font-weight:600;
    display:block;
    margin-left:auto;
}
.submit-btn:hover{
    background:#97b85c;
}

/* ==========================
MOBILE NAVIGATION
========================== */

.mobile-nav{
    display:none;
}

/* ==========================
RESPONSIVE
========================== */

@media(max-width:991px){

.main-content{
    margin-left:0;
    width:100%;
    padding:20px;
    padding-bottom:90px;
}

.sidebar{
    display:none;
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

/* MOBILE LOCATION */

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
    overflow:hidden;
    white-space:nowrap;
    text-overflow:ellipsis;
}

.location-btn i{
    flex-shrink:0;
}

.page-heading{
    font-size:18px;
}

.back-btn{
    width:36px;
    height:36px;
    font-size:18px;
}

.feedback-card{
    padding:18px;
}

.star-rating{
    font-size:30px;
}

.submit-btn{
    width:auto;
    display:block;
    margin:15px 0 25px auto;
    padding:8px 28px;
}
/* Resident Information Card - Mobile */

.card-header{
    font-size:15px;
    padding:10px 14px;
}

.card-body{
    padding:14px;
}

.card-body .row > div{
    margin-bottom:10px !important;
}

.card-body small{
    font-size:11px;
}

.card-body .fw-semibold{
    font-size:13px;
    line-height:1.3;
    word-break:break-word;
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
.feedback-card{
    margin-top:-15px;
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
            RATE US
        </h3>

    </div>
<?php
$fullName = trim(
    $resident['first_name'] . ' ' .
    $resident['middle_initial'] . ' ' .
    $resident['last_name']
);
?>

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-success text-white fw-bold">
        <i class="bi bi-person-vcard-fill me-2"></i>
        Resident Information
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4 mb-3">

                <small class="text-muted">
                    Resident Name
                </small>

                <div class="fw-semibold">
                    <?= htmlspecialchars($fullName) ?>
                </div>

            </div>

            <div class="col-md-5 mb-3">

                <small class="text-muted">
                    Current Address
                </small>

                <div class="fw-semibold">
                    <?= htmlspecialchars($address) ?>
                </div>

            </div>

            <div class="col-md-3 mb-3">

                <small class="text-muted">
                    Feedback Date
                </small>

                <div class="fw-semibold">
                    <?= date("F d, Y") ?>
                </div>

            </div>

        </div>

    </div>

</div>
    <div class="feedback-card">

        <h5>
            Help us improve EnviroManage!
        </h5>

   
<div class="mb-3">

<label class="form-label">
Feedback Category
</label>
<select
class="form-select"
id="category">
<option value="" selected disabled hidden>
    Select Category
</option>

<option value="Collection Service">
    Collection Service
</option>

<option value="Garbage Truck">
    Garbage Truck
</option>

<option value="Cleanliness">
    Cleanliness
</option>

<option value="Collector Performance">
    Collector Performance
</option>


<option value="Suggestion">
    Suggestion
</option>

</select>

</div>
        <!-- Rating -->

        <div class="mb-2">

            <label class="form-label">
                Rating
            </label>

       <div class="star-rating">

<i class="bi bi-star star" data-value="1"></i>
<i class="bi bi-star star" data-value="2"></i>
<i class="bi bi-star star" data-value="3"></i>
<i class="bi bi-star star" data-value="4"></i>
<i class="bi bi-star star" data-value="5"></i>

</div>

<div
id="ratingText"
class="fw-semibold text-success">

No rating selected.

</div>

        </div>

        <!-- Comment -->

        <div class="mb-3">

            <label class="form-label">
                Comment
            </label>
<textarea
maxlength="300"
                id="comment"
                class="form-control"
                rows="5"
                placeholder="Write your feedback here..."></textarea>

        </div>
<div class="text-end">

<small
id="charCount"
class="text-muted">

0 / 300 characters

</small>

</div>
        <button
            type="button"
            class="submit-btn"
            id="submitFeedback">

            Submit

        </button>
<div class="alert alert-success mt-3 mb-0">

<h6 class="fw-bold">

<i class="bi bi-lightbulb-fill"></i>

Reminder

</h6>

<ul class="mb-0">

<li>
Please provide honest feedback.
</li>

<li>
Avoid offensive or inappropriate language.
</li>

<li>
Your feedback helps MENRO improve waste collection services.
</li>

</ul>

</div>
        <div class="clearfix"></div>

    </div>

</div>
<!-- ==========================
     MOBILE NAVIGATION
========================== -->

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

/* ==========================
   LOCATION DROPDOWN
========================== */

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


/* ==========================
   STAR RATING
========================== */

let selectedRating = 0;

const stars =
document.querySelectorAll(".star");

stars.forEach((star)=>{

    star.addEventListener("click",function(){

        selectedRating =
        this.dataset.value;
const texts={

1:"Very Poor 😡",

2:"Poor 😕",

3:"Average 😐",

4:"Good 🙂",

5:"Excellent 🤩"

};

document.getElementById("ratingText").innerHTML=
texts[selectedRating];
        stars.forEach((s,index)=>{

            if(index < selectedRating){

                s.classList.remove("bi-star");
                s.classList.add("bi-star-fill");
                s.classList.add("active");

            }else{

                s.classList.remove("bi-star-fill");
                s.classList.add("bi-star");
                s.classList.remove("active");

            }

        });

    });

});
const comment=
document.getElementById("comment");

const charCount=
document.getElementById("charCount");

comment.addEventListener("input",function(){

charCount.innerHTML=
this.value.length+" / 300 characters";

});

/* ==========================
   SUBMIT
========================== */

document
.getElementById("submitFeedback")
.addEventListener("click",function(){

    const barangay =
    document.getElementById("barangay").value;
const category=
document.getElementById("category").value;
    const comment =
    document.getElementById("comment").value.trim();

    if(barangay==""){

        Swal.fire({

            icon:"warning",
            title:"Barangay Required",
            text:"Please select your barangay."

        });

        return;

    }
if(category==""){

Swal.fire({

icon:"warning",

title:"Category Required",

text:"Please select a feedback category."

});

return;

}
    if(selectedRating==0){

        Swal.fire({

            icon:"warning",
            title:"Rating Required",
            text:"Please select your rating."

        });

        return;

    }

    if(comment==""){

        Swal.fire({

            icon:"warning",
            title:"Comment Required",
            text:"Please enter your feedback."

        });

        return;

    }

    Swal.fire({

        icon:"success",
        title:"Thank You!",
        text:"Your feedback has been submitted successfully.",
        confirmButtonColor:"#1e5631"

    });
document.getElementById("category").value = "";
document.getElementById("ratingText").innerHTML=
"No rating selected.";

document.getElementById("charCount").innerHTML=
"0 / 300 characters";
    /* RESET FORM */

   
    document
    .getElementById("comment")
    .value="";

    selectedRating=0;

    stars.forEach((s)=>{

        s.classList.remove("bi-star-fill");
        s.classList.add("bi-star");
        s.classList.remove("active");

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
</body>
