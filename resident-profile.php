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
   FETCH RESIDENT DATA
========================== */

$stmt = $conn->prepare("
SELECT
    first_name,
    middle_initial,
    last_name,
    phone,
    house_no,
    street,
    barangay,
    profile_photo
FROM users
WHERE id = ?
LIMIT 1
");


$stmt->bind_param("i",$user_id);
$stmt->execute();


$result = $stmt->get_result();


$resident = $result->fetch_assoc();


$stmt->close();



$fullName = trim(
    $resident['first_name']." ".
    (!empty($resident['middle_initial']) 
        ? $resident['middle_initial'].". "
        : ""
    ).
    $resident['last_name']
);



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
    : "Unknown Location";



/* ==========================
   UPDATE PROFILE
========================== */

if(isset($_POST['updateProfile'])){


    $firstName =
        $_POST['first_name'];

    $middleInitial =
        $_POST['middle_initial'];

    $lastName =
        $_POST['last_name'];

    $phone =
        $_POST['phone'];

    $houseNo =
        $_POST['house_no'];

    $street =
        $_POST['street'];

    $barangay =
        $_POST['barangay'];



    $update=$conn->prepare("
    UPDATE users SET

        first_name=?,
        middle_initial=?,
        last_name=?,
        phone=?,
        house_no=?,
        street=?,
        barangay=?

    WHERE id=?
    ");



    $update->bind_param(
        "sssssssi",
        $firstName,
        $middleInitial,
        $lastName,
        $phone,
        $houseNo,
        $street,
        $barangay,
        $user_id
    );




    $update->close();

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Resident Profile</title>


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
}.page-heading{
    margin:0;
    font-size:28px;
    font-weight:700;
    color:#1e5631;
}



.page-title{
    margin:0;
    font-size:30px;
    font-weight:700;
    color:#1e5631;
}
.profile-avatar{
    width:170px;
    height:170px;
    margin:0 auto 20px;
    border-radius:50%;
    background:#B7CA7A;
    overflow:hidden;
    display:flex;
    justify-content:center;
    align-items:center;
}

.profile-avatar img{
    width:100%;
    height:100%;
    object-fit:cover;
}


.username{
    margin-top:15px;
    margin-bottom:25px;
    color:#9a9a9a;
    font-size:16px;
    text-align:center;
    font-weight:500;
}
.profile-container{
    width:100%;
    max-width:500px;
    margin:0 auto;
}
.profile-menu{
    width:100%;
    background:#fff;
    border-radius:10px;
    padding:13px 18px;
    margin-bottom:12px;
    box-shadow:0 2px 6px rgba(0,0,0,.18);
    text-decoration:none;
    color:#000;
    display:flex;
    justify-content:space-between;
    align-items:center;
    transition:.2s;
}

.profile-menu:hover{
    background:#f7f7f7;
    color:#000;
}

.profile-menu-left{
    display:flex;
    align-items:center;
    gap:12px;
    font-weight:600;
}

.profile-menu i{
    font-size:18px;
}

.profile-menu.logout:hover{
    background:#fff0f0;
}

.modal-content{
    border-radius:12px;
}

.green-btn{
    background:#B7CA7A;
    color:#fff;
    border:none;
    border-radius:20px;
    padding:8px 30px;
    font-weight:600;
}

.green-btn:hover{
    background:#9fb565;
}

.gray-btn{
    background:#e8e8e8;
    border:none;
    border-radius:20px;
    padding:8px 30px;
}
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

@media(max-width:576px){

    .profile-avatar{
        width:150px;
        height:150px;
    }

    .page-header{
        justify-content:center;
        margin-bottom:18px;
    }

    .page-title{
        font-size:22px;
        text-align:center;
        width:100%;
    }
 
/* MOBILE LOCATION ELLIPSIS */
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
/* =====================
LANGUAGE MODAL
===================== */

.language-box{

    width:420px;
    max-width:90%;
    margin:auto;
    border-radius:15px;
    overflow:hidden;

}


.language-box .modal-header{

    padding:16px 20px;
    border-bottom:1px solid #ddd;

}


.language-box .modal-title{

    font-size:20px;
    font-weight:700;
    color:#1e5631;

}



.language-box .modal-body{

    padding:30px 25px;

}


.language-option{

    display:flex;
    justify-content:center;
    gap:45px;

    font-size:17px;
    font-weight:600;

}


.language-option label{

    display:flex;
    align-items:center;
    gap:10px;
    cursor:pointer;

}


.language-option input{

    width:18px;
    height:18px;
    accent-color:#1e5631;

}



#saveLanguage{

    padding:10px 55px;
    font-size:16px;
    border-radius:25px;

}


/* MOBILE VIEW - keep current size */

@media(max-width:576px){

    .language-box{

        width:280px;
        max-width:90%;

    }


    .language-box .modal-header{

        padding:10px 15px;

    }


    .language-title,
    .language-box .modal-title{

        font-size:14px;

    }


    .language-box .modal-body{

        padding:18px 15px;

    }


    .language-option{

        gap:20px;
        font-size:13px;

    }


    .language-option input{

        width:15px;
        height:15px;

    }


    #saveLanguage{

        padding:6px 35px;
        font-size:13px;

    }
      #logoutModal .modal-dialog{
        max-width:270px;
        margin:1rem auto;
    }

    #logoutModal .modal-header{
        padding:10px 14px 4px;
    }

    #logoutModal .modal-title{
        font-size:15px;
    }

    #logoutModal .modal-body{
        padding:8px 16px 6px; /* mas maliit ang height */
    }

    #logoutModal .modal-body i{
        font-size:38px !important;
        margin-bottom:8px;
    }

    #logoutModal .modal-body h5{
        font-size:14px;
        margin:0 0 6px;
        white-space:nowrap; /* one line */
    }

    #logoutModal .modal-body p{
        font-size:11px;
        margin-bottom:0;
    }

    #logoutModal .modal-footer{
        padding:8px 14px 14px;
        gap:8px;
    }

    #logoutModal .modal-footer .btn{
        font-size:12px;
        padding:5px 18px !important;
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

        <a class="nav-link active" href="resident-profile.php">
            <i class="bi bi-person-circle"></i>
            <span>Profile</span>
        </a>

    </nav>

</div>
    <!-- MAIN CONTENT -->

 <div class="main-content">

 
        <div class="page-header">
        

            <h3 class="page-heading">
                  MY PROFILE
       
            </h3>

        </div>
        
        <!-- Profile Picture -->
        <div class="profile-avatar">

         <?php

$image = "uploads/profile_photos/default_profile.jpg";

if(
    !empty($resident['profile_photo']) &&
    file_exists("uploads/profile_photos/".$resident['profile_photo'])
){
    $image = "uploads/profile_photos/".$resident['profile_photo'];
}

?>

<img
    src="<?= htmlspecialchars($image) ?>"
    alt="Profile">
      
        </div>

        <!-- Username -->
        <div class="username">
            @<?= strtolower(htmlspecialchars($resident['first_name'])) ?>
        </div>

        <!-- My Account -->
        <a href="resident-accounts.php" class="profile-menu">

            <div class="profile-menu-left">
                <i class="bi bi-person-circle"></i>
                <span>My Account</span>
            </div>

            <i class="bi bi-chevron-right"></i>

        </a>

        <!-- Activities -->
        <a href="resident-activities.php" class="profile-menu">

            <div class="profile-menu-left">
                <i class="bi bi-clock-history"></i>
                <span>My Activities</span>
            </div>

            <i class="bi bi-chevron-right"></i>

        </a>

        <!-- Language -->
        <a href="#"
           class="profile-menu"
           data-bs-toggle="modal"
           data-bs-target="#languageModal">

            <div class="profile-menu-left">
                <i class="bi bi-translate"></i>
                <span>Change Language</span>
            </div>

            <i class="bi bi-chevron-right"></i>

        </a>

        <!-- Privacy -->
        <a href="resident-security.php"
           class="profile-menu">

            <div class="profile-menu-left">
                <i class="bi bi-shield-lock"></i>
                <span>Privacy & Security</span>
            </div>

            <i class="bi bi-chevron-right"></i>

        </a>

        <!-- Help -->
        <a href="resident-help.php"
           class="profile-menu">

            <div class="profile-menu-left">
                <i class="bi bi-question-circle"></i>
                <span>Help Center / FAQs</span>
            </div>

            <i class="bi bi-chevron-right"></i>

        </a>

        <!-- Rate -->
        <a href="resident-rate.php"
           class="profile-menu">

            <div class="profile-menu-left">
                <i class="bi bi-star"></i>
                <span>Rate Us</span>
            </div>

            <i class="bi bi-chevron-right"></i>

        </a>

       
   <!-- Logout -->
<a href="#"
   class="profile-menu logout"
   data-bs-toggle="modal"
   data-bs-target="#logoutModal">

    <div class="profile-menu-left">
        <i class="bi bi-box-arrow-right"></i>
        <span>Log Out</span>
    </div>

    <i class="bi bi-chevron-right"></i>

</a>
<!-- ==========================
LOGOUT MODAL
========================== -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content" style="border-radius:15px;">

            <div class="modal-header border-0 pb-0">

                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-box-arrow-right"></i>
                    Log Out
                </h5>

            </div>

            <div class="modal-body text-center">

                <i class="bi bi-exclamation-circle-fill text-danger"
                   style="font-size:60px;"></i>

                <h5 class="mt-3 mb-2">
                    Are you sure you want to log out?
                </h5>

                <p class="text-muted mb-0">
                    You will need to log in again to access your account.
                </p>

            </div>

            <div class="modal-footer border-0 justify-content-center pb-4">

                <button
                    type="button"
                    class="btn btn-secondary px-4"
                    data-bs-dismiss="modal">
                    Cancel
                </button>

                <a href="logout.php"
                   class="btn btn-danger px-4">
                    Yes
                </a>

            </div>

        </div>

    </div>

</div>

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



<a href="resident-profile.php"
class="active">

<i class="bi bi-person-fill"></i>

<span>Profile</span>

</a>


</nav>


<!-- LANGUAGE MODAL -->
<div class="modal fade" id="languageModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content language-box">

            <div class="modal-header">

                <h6 class="modal-title">
                    <i class="bi bi-translate"></i>
                    Change Language
                </h6>

            </div>


            <div class="modal-body">


                <div class="language-option">

                    <label>
                        <input 
                        type="radio" 
                        name="language"
                        id="english"
                        checked>

                        English
                    </label>


                    <label>

                        <input 
                        type="radio"
                        name="language"
                        id="tagalog">

                        Tagalog

                    </label>


                </div>


                <div class="text-center mt-3">

                    <button 
                    class="green-btn"
                    id="saveLanguage">

                        Save

                    </button>

                </div>


            </div>

        </div>

    </div>

</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

/*==========================
LOCATION DROPDOWN
==========================*/

const locationToggle = document.getElementById("locationToggle");
const locationSearch = document.getElementById("locationSearch");

if(locationToggle){

    locationToggle.addEventListener("click", function(e){

        e.stopPropagation();
        locationSearch.classList.toggle("show");

    });

}

document.addEventListener("click", function(e){

    if(
        locationToggle &&
        !locationToggle.contains(e.target) &&
        !locationSearch.contains(e.target)
    ){

        locationSearch.classList.remove("show");

    }

});


document.getElementById("saveLanguage")
.addEventListener("click",function(){


    let language =
    english.checked ? "english":"tagalog";


    localStorage.setItem(
        "resident_language",
        language
    );


    Swal.fire({

        icon:"success",

        title:"Saved!",

        text:"Language preference updated.",

        timer:1000,

        showConfirmButton:false

    }).then(()=>{


        // close language modal
        let modalElement = document.getElementById("languageModal");

        let modalInstance = bootstrap.Modal.getInstance(modalElement);

        if(modalInstance){

            modalInstance.hide();

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