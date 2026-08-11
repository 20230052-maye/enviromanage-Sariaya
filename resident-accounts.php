<?php
session_start();

if(
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'resident'
){
    header("Location: login.php");
    exit;
}


$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
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
    gender,
    birthdate,
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

     $gender =
    $_POST['gender'];

$birthdate = $_POST['birthdate'];

$birth = new DateTime($birthdate);
$today = new DateTime();

$age = $today->diff($birth)->y;

if($age < 18){

    echo "
    <script>
    window.onload=function(){
        Swal.fire({
            icon:'warning',
            title:'Invalid Birthdate',
            text:'Resident must be 18 years old or above.',
            confirmButtonColor:'#1e5631'
        });
    }
    </script>";

    exit;
}
    $phone =
        $_POST['phone'];

    $houseNo =
        $_POST['house_no'];

    $street =
        $_POST['street'];

    $barangay =
        $_POST['barangay'];
$photoName = $resident['profile_photo'];

if(
    isset($_FILES['profile_photo']) &&
    $_FILES['profile_photo']['error']==0
){

    $extension = strtolower(
        pathinfo(
            $_FILES['profile_photo']['name'],
            PATHINFO_EXTENSION
        )
    );

    $allowed = ['jpg','jpeg','png','webp'];

    if(in_array($extension,$allowed)){

        $photoName =
            time()."_".$user_id.".".$extension;


$target = "uploads/profile_photos/".$photoName;

if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target)) {
    die("UPLOAD FAILED");
}
    }

}


    $update=$conn->prepare("
UPDATE users SET

first_name=?,
middle_initial=?,
last_name=?,
gender=?,
birthdate=?,
phone=?,
house_no=?,
street=?,
barangay=?,
profile_photo=?
WHERE id=?
    ");



$update->bind_param(

"ssssssssssi",
$firstName,
$middleInitial,
$lastName,
$gender,
$birthdate,
$phone,
$houseNo,
$street,
$barangay,
$photoName,
$user_id
);

if($update->execute()){

    echo "
    <script>
    window.onload = function(){

        Swal.fire({
            icon:'success',
            title:'Profile Updated!',
            text:'Your profile has been updated successfully.',
            confirmButtonColor:'#1e5631'
        }).then(() => {
            window.location.href='resident-accounts.php';
        });

    };
    </script>";

}else{

    echo "
    <script>
    window.onload = function(){

        Swal.fire({
            icon:'error',
            title:'Update Failed',
            text:'".$update->error."',
            confirmButtonColor:'#d33'
        });

    };
    </script>";

}

$update->close();


}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Resident Account</title>


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
/*==========================
ACCOUNT PAGE
==========================*/
.account-container{
    width:100%;
    max-width:none;
    margin:0;
}

.account-header{
    display:flex;
    align-items:center;
    gap:16px;
    margin-bottom:30px;
}
.account-avatar-wrapper{
    position:relative;
    width:150px;
    height:150px;
}
.account-avatar{
    width:150px;
    height:150px;
    border-radius:50%;
    overflow:hidden;
    border:6px solid #fff;
    box-shadow:0 4px 12px rgba(0,0,0,.18);
    background:#B7CA7A;

    display:flex;
    align-items:center;
    justify-content:center;
}
.account-avatar img{
    width:100%;
    height:100%;
    object-fit:cover;
    object-position:center center;
    border-radius:50%;
    display:block;
}
.camera-btn{
    position:absolute;
    right:-2px;
    bottom:-2px;
    width:48px;
    height:48px;
    border-radius:50%;
    background:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    box-shadow:0 3px 10px rgba(0,0,0,.25);
    border:2px solid #e5e5e5;
    transition:.2s;
}

.camera-btn:hover{
    transform:scale(1.08);
}

.camera-btn i{
    font-size:20px;
    color:#555;
}
.account-hello{
    font-size:26px;
    font-weight:bold;
    margin-top:20px;
}

.account-username{
    color:#888;
    margin-bottom:25px;
}
.account-card{
    background:#fff;
    padding:40px;
    border-radius:15px;
    box-shadow:0 2px 8px rgba(0,0,0,.12);
}
.account-info{
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.account-hello{
    margin:0;
    font-size:30px;
    font-weight:700;
}

.account-username{
    margin:0;
    color:#999;
    font-size:18px;
    font-weight:600;
}
.form-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:24px;
}
.form-group{
    display:flex;
    flex-direction:column;
}

/* alisin na ito */
.full-width{
    grid-column:auto;
}

.account-card label{
    font-weight:600;
    margin-bottom:6px;
}

.account-card .form-control,
.account-card .form-select{
    border-radius:10px;
    margin-bottom:18px;
}

.save-btn{
    background:#B7CA7A;
    color:#000; /* black text */
    border:none;
    border-radius:25px;
    padding:12px 35px;
    font-weight:600;
}

.save-btn:hover{
    background:#9fb565;
    color:#000;
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
      .account-container{
        width:100%;
        max-width:760px;
        margin:auto;
    }

    .account-card{
        padding:22px;
    }

    /* 3 columns pa rin */
    .form-grid{
        display:grid;
        grid-template-columns:repeat(3,1fr);
        gap:14px;
    }

    .account-card label{
        font-size:13px;
    }

    .account-card .form-control,
    .account-card .form-select{
        font-size:13px;
        padding:8px 10px;
        margin-bottom:8px;
    }

    .account-avatar img{
        width:120px !important;
        height:120px !important;
    }

    .account-hello{
        font-size:22px;
    }

    .account-username{
        font-size:14px;
    }

    .save-btn{
        width:100%;
    }

}

@media(max-width:576px){

    .profile-avatar{
        width:150px;
        height:150px;
    }

    .profile-title{
        font-size:28px;
    }
  .account-container{
        width:98%;
        max-width:100%;
    }

    .account-card{
        padding:15px;
    }

    .form-grid{
        display:grid;
        grid-template-columns:repeat(3,1fr);
        gap:10px;
    }

    .account-card label{
        font-size:11px;
        margin-bottom:4px;
    }

    .account-card .form-control,
    .account-card .form-select{
        font-size:11px;
        padding:6px 8px;
        margin-bottom:6px;
    }

 

    .account-hello{
        font-size:20px;
    }

    .account-username{
        font-size:12px;
    }

  .text-center.mt-4{
    text-align:right !important;
}

.save-btn{
    width:auto;
    min-width:140px;
    padding:10px 20px;
    font-size:13px;
    color:#000;
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
    font-weight:700;
}
.account-header{
    gap:10px;
}

.account-avatar-wrapper{
    width:100px;
    height:100px;
}

.account-avatar{
    width:100px;
    height:100px;
}

.account-avatar img{
    width:100% !important;
    height:100% !important;
    object-fit:cover;
    border-radius:50%;
}

.camera-btn{
    width:38px;
    height:38px;
}

.camera-btn i{
    font-size:16px;
}
.account-hello{
    font-size:24px;
}

.account-username{
    font-size:14px;
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
/* ==========================
PROFILE FULL WIDTH ALL SCREEN
========================== */

.main-content{
    margin-left:220px !important;
    width:calc(100% - 220px) !important;
    padding:25px !important;
}

.account-container{
    width:100% !important;
    max-width:none !important;
    margin:0 !important;
}

.account-card{
    width:100% !important;
}


/* TABLET + MOBILE */

@media(max-width:991px){

    .main-content{
        width:100% !important;
        margin-left:0 !important;
        padding:20px !important;
        padding-bottom:90px !important;
    }

    .account-container{
        width:100% !important;
        max-width:none !important;
    }

    .account-card{
        width:100% !important;
    }

}


/* SMALL MOBILE */

@media(max-width:576px){

    .main-content{
        width:100% !important;
        margin-left:0 !important;
        padding:15px !important;
        padding-bottom:90px !important;
    }

    .account-container{
        width:100% !important;
    }

    .account-card{
        width:100% !important;
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

<div class="main-content container-fluid">
<div class="page-top">

    <a href="resident-profile.php" class="back-btn">
        <i class="bi bi-arrow-left"></i>
    </a>

    <h3 class="page-heading">
        MY ACCOUNT
    </h3>

</div>
    <div class="account-container">

        <div class="account-header">

  <div class="account-avatar-wrapper">

<?php

$image = "uploads/profile_photos/default_profile.jpg";

if(
    !empty($resident['profile_photo']) &&
    file_exists("uploads/profile_photos/".$resident['profile_photo'])
){
    $image = "uploads/profile_photos/".$resident['profile_photo'];
}

?>

<div class="account-avatar">

    <img
        id="profilePreview"
        src="<?= htmlspecialchars($image) ?>"
        alt="Profile Photo">

</div>


<div
    class="camera-btn"
    onclick="document.getElementById('profilePhotoInput').click();">

    <i class="bi bi-camera-fill"></i>

</div>

</div>



         <div class="account-info">

    <h2 class="account-hello">
        Hello!
    </h2>

    <p class="account-username">
        @<?= strtolower(htmlspecialchars($resident['first_name'])) ?>
    </p>

</div>
</div>

        <div class="account-card">

     <form
    id="profileForm"
    method="POST"
    enctype="multipart/form-data">
<div class="form-grid">
<input
    type="file"
    id="profilePhotoInput"
    name="profile_photo"
    accept="image/*"
    style="display:none;">
    <div class="form-group">
        <label>First Name</label>
        <input
            type="text"
            name="first_name"
            class="form-control"
            value="<?= htmlspecialchars($resident['first_name']) ?>"
            required>
    </div>

    <div class="form-group">
        <label>Middle Initial</label>
        <input
            type="text"
            name="middle_initial"
            class="form-control"
            value="<?= htmlspecialchars($resident['middle_initial']) ?>">
    </div>

    <div class="form-group">
        <label>Last Name</label>
        <input
            type="text"
            name="last_name"
            class="form-control"
            value="<?= htmlspecialchars($resident['last_name']) ?>"
            required>
    </div>

    <div class="form-group">
        <label>Gender</label>
      <select class="form-select" name="gender" required>
    <option value="" disabled <?= empty($resident['gender']) ? 'selected' : '' ?>>
        Select Gender
    </option>

    <option value="Male" <?= ($resident['gender']=="Male") ? "selected" : "" ?>>
        Male
    </option>

    <option value="Female" <?= ($resident['gender']=="Female") ? "selected" : "" ?>>
        Female
    </option>

    <option value="Others" <?= ($resident['gender']=="Others") ? "selected" : "" ?>>
        Others
    </option>
</select>
    </div>

    <div class="form-group">
        <label>Birthdate</label>
       <input
    type="date"
    class="form-control"
    name="birthdate"
    id="birthdate"
    value="<?= htmlspecialchars($resident['birthdate']) ?>"
    max="<?= date('Y-m-d', strtotime('-18 years')) ?>"
    required>
    </div>

    <div class="form-group">
        <label>Phone Number</label>
      <input
    type="tel"
    name="phone"
    id="phone"
    class="form-control"
    value="<?= htmlspecialchars($resident['phone']) ?>"
    maxlength="11"
    pattern="[0-9]{11}"
    inputmode="numeric"
    required>
    </div>

    <div class="form-group">
        <label>House No.</label>
<input
    type="text"
    name="house_no"
    id="house_no"
    class="form-control"
    inputmode="numeric"
    maxlength="5"
    value="<?= htmlspecialchars($resident['house_no']) ?>">
</div>

    <div class="form-group">
        <label>Street</label>
        <input
            type="text"
            name="street"
            class="form-control"
            value="<?= htmlspecialchars($resident['street']) ?>">
    </div>

   <div class="form-group">
        <label>Barangay</label>
        <input
            type="text"
            name="barangay"
            class="form-control"
            value="<?= htmlspecialchars($resident['barangay']) ?>">
    </div>

</div>

<div class="text-end mt-4">
  <button
    type="submit"
    name="updateProfile"
    class="save-btn">
    Save Changes
</button>
</div>

</form>

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
const photoInput = document.getElementById("profilePhotoInput");
photoInput.addEventListener("click", () => {
    console.log("File input clicked");
});

photoInput.addEventListener("change", function () {
    console.log("Selected:", this.files);

    if (this.files.length > 0) {
        preview.src = URL.createObjectURL(this.files[0]);
    }
});
console.log(photoInput);
const preview = document.getElementById("profilePreview");

photoInput.addEventListener("change", function(){

    if(this.files && this.files[0]){
        preview.src = URL.createObjectURL(this.files[0]);
    }

});
/*==========================
PROFILE VALIDATION
==========================*/
const form = document.getElementById("profileForm");

const originalValues = {};

form.querySelectorAll("input:not([type=file]), select").forEach(input => {
    originalValues[input.name] = input.value;
});
const phone = document.getElementById("phone");

phone.addEventListener("input", function () {

    // Numbers only
    this.value = this.value.replace(/\D/g, "");

    // Maximum 11 digits
    if (this.value.length > 11) {
        this.value = this.value.slice(0, 11);
    }

});
const houseNo = document.getElementById("house_no");

houseNo.addEventListener("input", function () {

    // Numbers only
    this.value = this.value.replace(/\D/g, "");

    // Maximum 5 digits
    if (this.value.length > 5) {
        this.value = this.value.slice(0, 5);
    }

});

const birthdate = document.getElementById("birthdate");

birthdate.addEventListener("change", function(){

    const selectedDate = new Date(this.value);
    const today = new Date();

    const age = today.getFullYear() - selectedDate.getFullYear();

    const monthDiff = today.getMonth() - selectedDate.getMonth();

    if(
        monthDiff < 0 || 
        (monthDiff === 0 && today.getDate() < selectedDate.getDate())
    ){
        age--;
    }


    if(age < 18){

        Swal.fire({
            icon:"warning",
            title:"Invalid Birthdate",
            text:"You must be 18 years old or above.",
            confirmButtonColor:"#1e5631"
        });

        this.value="";
    }

});
form.addEventListener("submit", function(e){

    console.log("SUBMIT");
});
console.log("Files:", photoInput.files);



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
