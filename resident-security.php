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

$birthdate =
    $_POST['birthdate'];
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

      move_uploaded_file(
    $_FILES['profile_photo']['tmp_name'],
    "uploads/profile_photos/".$photoName
);

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
        window.onload=function(){

            Swal.fire({
                icon:'success',
                title:'Profile Updated!',
                text:'Your profile information has been updated.',
                confirmButtonColor:'#1e5631'
            }).then(()=>{
                window.location.href='resident-profile.php';
            });

        }
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
.account-avatar{
    width:150px;
    height:150px;
    border-radius:50%;
    background:#B7CA7A;
    display:flex;
    justify-content:center;
    align-items:center;
    flex-shrink:0;
}
.account-avatar i{
    font-size:70px;
    color:#fff;
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
    color:#fff;
    border:none;
    border-radius:25px;
    padding:12px 35px;
    font-weight:600;
}

.save-btn:hover{
    background:#9fb565;
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
    max-width:none;
    margin:0;
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

 .account-container{
    width:100%;
    max-width:none;
    margin:0;
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

    .account-avatar img{
        width:100px !important;
        height:100px !important;
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
SECURITY PAGE
========================== */

.security-container{
    width:100% !important;
    max-width:none !important;
    display:block;
}
.security-card{
    width:100% !important;
    max-width:none !important;
    background:white;
    height:45px;
    border-radius:8px;
    margin-bottom:8px;
    padding:0 15px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    box-shadow:0 2px 5px rgba(0,0,0,.15);
    font-size:14px;
    cursor:pointer;
}


.security-card span{

    font-weight:500;

}


.security-card i{

    color:#555;

}



.switch{

    position:relative;

    width:38px;

    height:20px;

}


.switch input{

    display:none;

}


.slider{

    position:absolute;

    inset:0;

    background:#ccc;

    border-radius:20px;

}


.slider:before{

    content:"";

    position:absolute;

    width:16px;

    height:16px;

    background:white;

    border-radius:50%;

    left:2px;

    top:2px;

    transition:.3s;

}


.switch input:checked + .slider{

    background:#1e5631;

}


.switch input:checked + .slider:before{

    transform:translateX(18px);

}


/* MOBILE */

@media(max-width:991px){

.security-container{

    width:100%;

}


.security-card{

    height:42px;

    font-size:12px;

}


}
/* FORCE FULL WIDTH PRIVACY PAGE */

.main-content{
    width:calc(100% - 220px) !important;
}

.security-container{
    width:100% !important;
}

.security-card{
    width:100% !important;
}


@media(max-width:991px){

    .main-content{
        width:100% !important;
        margin-left:0 !important;
    }

    .security-container{
        width:100% !important;
    }

    .security-card{
        width:100% !important;
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
PRIVACY & SECURITY
</h3>

</div>



<div class="security-container">

<div class="security-card"
onclick="window.location.href='forgot-password.php'">

    <span>
        Change Password
    </span>

    <i class="bi bi-chevron-right"></i>

</div>



<div class="security-card">

<span>
Allow push notifications
</span>

<label class="switch">

<input type="checkbox" checked>

<span class="slider"></span>

</label>

</div>



<div class="security-card">

<span>
Allow photo uploads
</span>


<label class="switch">

<input type="checkbox">

<span class="slider"></span>

</label>


</div>



<div class="security-card">

<span>
Location permission (optional)
</span>


<label class="switch">

<input type="checkbox">

<span class="slider"></span>

</label>


</div>



<div class="security-card delete-btn"
onclick="confirmDelete()">


<span>
Request Account Deletion
</span>


<i class="bi bi-chevron-right"></i>


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

<script>


function confirmDelete(){


Swal.fire({

title:"Delete Account",

text:"Are you sure you want to delete your account? This action cannot be undone.",

icon:"warning",

showCancelButton:true,

confirmButtonText:"YES",

cancelButtonText:"Cancel",

confirmButtonColor:"#B7CA7A",

cancelButtonColor:"#ddd"


}).then((result)=>{


if(result.isConfirmed){


Swal.fire({

icon:"success",

title:"Request Submitted",

text:"Your account deletion request has been sent.",

confirmButtonColor:"#1e5631"

});


}


});


}
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