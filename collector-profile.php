
<?php
session_start();

// DB CONNECTION
$conn = new mysqli(
    "localhost",
    "u823857209_enviromanage",
    "Enviromanage4322",
    "u823857209_enviromanage"
);

$conn->set_charset("utf8mb4");

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT profile_photo, first_name, last_name
    FROM users
    WHERE id=?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

$profilePhoto = $user['profile_photo'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Collector Profile</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

/* ==========================
   RESET
========================== */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#f4f7f9;
    padding-top:70px;
}

/* ==========================
   NAVBAR
========================== */

.navbar{
    background:#1e5631!important;
    height:70px;
    box-shadow:0 2px 8px rgba(0,0,0,.12);
}

.navbar .container-fluid{
    height:70px;
}

.navbar-brand img{
    height:42px;
}
.navbar .container-fluid{
    position:relative;
}

.navbar-logo{
    display:flex;
    align-items:center;
    height:70px;
    margin:0;
    padding:0;
    transform:translateY(-3px);
}

.navbar-logo img{
    height:42px;
}

.navbar-actions{
    display:flex;
    align-items:center;
    gap:12px;
}

.nav-icon-btn{
    width:42px;
    height:42px;
    border:none;
    border-radius:50%;
    background:transparent;
    color:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    cursor:pointer;
    transition:.25s;
     transform:translateY(-3px);
}

.nav-icon-btn i{
    font-size:24px;
}

.nav-icon-btn:hover{
    background:rgba(255,255,255,.15);
}



/* ==========================
   PAGE LAYOUT
========================== */

.page-wrapper{
    display:flex;
    
}

/* ===========================
   SIDEBAR
=========================== */

.sidebar{
    width:270px;
    background:#fff;
    border-right:1px solid #dee2e6;
    display:flex;
    flex-direction:column;
    flex-shrink:0;
    position:sticky;
    top:70px;
    height:calc(100vh - 70px);
    overflow-y:auto;
}

.sidebar-menu{
    display:flex;
    flex-direction:column;
    padding:15px 0;
}

.sidebar-item{
    display:flex;
    align-items:center;
    gap:15px;

    padding:15px 22px;

    text-decoration:none;

    color:#495057;

    font-size:18px;

    line-height:1.2;

    transition:.25s ease;
}

.sidebar-item i{
    width:26px;
     font-size:20px;
    width:25px;
    text-align:center;
    color:inherit;
}

.sidebar-item img{
    width:22px;
    height:22px;
    object-fit:contain;
}

.sidebar-item span{
    white-space:nowrap;
}

.sidebar-item:hover{
    background:#1e5631;
    color:#fff;
}

.sidebar-item.active{
    background:#1e5631;
    color:#fff;
}
.bottom-nav .nav-item img{
    width:32px;
    height:32px;
    margin-bottom:4px;
    object-fit:contain;
}
/* ==========================
   MAIN
========================== */

.main-content{
    flex:1;
   padding:90px 35px 35px;
}
.profile-container{
    width:95%;
    max-width:1400px;
    margin:0 auto;
}
.profile-wrapper{
    max-width:900px;
    margin:auto;
}

/* ==========================
   PROFILE CARD
========================== */

.profile-card{
    max-width:2000px;
    margin:auto;
    height: 500px;
    background:#184D27;
    color:#fff;
    border-radius:25px;
    padding:25px;
    box-shadow:0 10px 25px rgba(0,0,0,.15);
}

.profile-top{

    display:flex;

    align-items:center;

    gap:25px;

    margin-bottom:35px;

}

.avatar{

    width:120px;

    height:120px;

    border-radius:50%;

    background:#b6c66b;

    display:flex;

    justify-content:center;

    align-items:center;

    overflow:hidden;

}

.avatar i{

    font-size:70px;

    color:#fff;

}

.profile-name h2{

    margin:0;

    font-weight:700;

    color:#fff;

}

.profile-name p{

    margin:5px 0 0;

    font-size:18px;

    color:#d8d8d8;

}

.info-box{

    background:#fff;

    color:#333;

    border-radius:10px;

    padding:14px 18px;

    margin-bottom:18px;

    font-weight:600;

}



/* ==========================
   CHAT BUTTON
========================== */

.chat-btn{

    position:fixed;

    right:30px;

    bottom:95px;

    width:58px;

    height:58px;

    border:none;

    border-radius:50%;

    background:#ececec;

    display:flex;

    justify-content:center;

    align-items:center;

    box-shadow:0 4px 12px rgba(0,0,0,.18);

    z-index:1100;

}

.chat-btn i{

    font-size:28px;

}

.badge{

    position:absolute;

    top:6px;

    right:6px;

    width:18px;

    height:18px;

    border-radius:50%;

    background:#fff;

    color:#333;

    border:1px solid #666;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:10px;

}
/* ===========================
   BOTTOM NAVIGATION
=========================== */

.bottom-nav{
    display:none;

    position:fixed;
    left:0;
    bottom:0;
    width:100%;
    height:75px;
    background:#184D27;
    justify-content:space-around;
    align-items:center;
    border-top-left-radius:20px;
    border-top-right-radius:20px;
    box-shadow:0 -2px 10px rgba(0,0,0,.15);
    z-index:1000;
}

.nav-item{
    color:#fff;
    text-decoration:none;

    display:flex;
    flex-direction:column;
    align-items:center;

    font-size:11px;
    font-weight:600;
}

.nav-item i{
    font-size:26px;
    margin-bottom:4px;
}

.nav-item img{
    width:32px;
    margin-bottom:4px;
}

.nav-item.active{
    color:#b8c77c;
}
/* ===========================
   PROFILE PAGE
=========================== */

.profile-card{
    max-width:1700px;
    margin:auto;
    background:#184D27;
    color:#fff;
    border-radius:25px;
    padding:35px;
    min-height:auto;
    box-shadow:0 10px 25px rgba(0,0,0,.15);
}

.profile-handle{
    width:90px;
    height:5px;
    background:#d8d8d8;
    border-radius:50px;
    margin:0 auto 28px;
    display:none;
}

.profile-header{
    display:flex;
    align-items:center;
    gap:18px;
    margin-bottom:28px;
}
.profile-avatar{
    width:140px;
    height:140px;
    border-radius:50%;
    background:#b7c97a;
    position:relative;
    overflow:visible;
    flex-shrink:0;
}

.profile-avatar img{
    width:100%;
    height:100%;
    border-radius:50%;
    object-fit:cover;
    object-position:center;
    display:block;
}

.avatar-initials{
    width:100%;
    height:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:48px;
    font-weight:700;
    color:#fff;
    user-select:none;
}


.change-photo{
    position:absolute;
    right:5px;
    bottom:5px;

    width:38px;
    height:38px;

    border-radius:50%;

    background:#fff;
    border:2px solid #e5e7eb;

    color:#555;

    display:flex;
    justify-content:center;
    align-items:center;

    cursor:pointer;

    box-shadow:0 4px 10px rgba(0,0,0,.25);

    z-index:10;

    transition:.2s ease;
}

.change-photo:hover{
    background:#f3f4f6;
    color:#1e5631;
}

.change-photo i{
    font-size:17px;
}
.profile-avatar input{
    display:none;
}
.profile-info h3{
    margin:0;
    font-size:42px;
    font-weight:700;
}

.profile-info h5{
    margin-top:5px;
    font-size:26px;
    color:#d7d7d7;
}

.profile-details{
    display:flex;
    flex-direction:column;
    gap:14px;
    margin-bottom:12px;
}

.info-box{
    background:#fff;
    color:#000;
    border-radius:18px;
    padding:12px 20px;
    font-size:15px;
    font-weight:500;
}

.profile-actions{
    display:flex;
    justify-content:flex-end;
    margin-top:0;
}

.logout-btn{
    display:flex;
    align-items:center;
    gap:6px;

    width:auto;
    padding:6px 12px;

    background:none;
    border:none;

    color:#fff;
    font-size:18px;
    font-weight:600;
}

.logout-btn i{
    font-size:18px;
}

.logout-btn:hover{
    color:#d8e79c;
}

/* ===========================
   RESPONSIVE
=========================== */

@media(max-width:991px){
.navbar .container-fluid{

    position:relative;
}



    .navbar-brand img{
        height:38px;
    }
    .profile-container{
        max-width:800px;
    }



.sidebar{
display:none;
}

.page-wrapper{
display:block;
}

.main-content{
 padding:90px 15px 110px;
}

.bottom-nav{
display:flex;
}


.profile-card{
padding:25px;
}

.profile-top{
flex-direction:column;
text-align:center;
}

}

/* ==========================
   MOBILE
========================== */
@media (max-width:576px){

    body{
        overflow:hidden;
        height:100vh;
    }

    .main-content{
        display:flex;
        justify-content:center;   /* center horizontally */
        align-items:flex-end;     /* nasa ibaba */
        height:calc(100vh - 140px);
        padding:0;
    }

  .profile-container{
    width:100%;
    max-width:none;
    margin:0;
    padding:0;
}

.profile-card{
    width:100%;
    height:240px;
    margin:0;
    margin-bottom:-20px;
    border-radius:30px 30px 0 0;
    padding:25px 18px;
    box-shadow:none;
    overflow:hidden;
    transition:.4s ease;
}
.change-photo{
    position:absolute;

    width:30px;
    height:30px;

    right:-3px;
    bottom:0px;

    border-radius:50%;

    background:#fff;
    border:2px solid #e5e7eb;

    display:flex;
    justify-content:center;
    align-items:center;

    box-shadow:0 3px 8px rgba(0,0,0,.25);

    z-index:20;
}

.change-photo i{
    font-size:14px !important;
}
   
.change-photo:hover{
    background:#f3f4f6;
    color:#000;
}

    .profile-handle{
        display:block;
    }

    .profile-header{
        gap:15px;
    }

 .profile-avatar{
    width:85px;
    height:85px;
    overflow:visible;
}

.profile-avatar img{
    width:100%;
    height:100%;
    object-fit:cover;
    object-position:center;
}
    .profile-avatar i{
        font-size:36px;
    }

    .profile-info h3{
        font-size:28px;
    }

    .profile-info h5{
        font-size:17px;
    }

    .info-box{
        font-size:14px;
        padding:12px 14px;
    }

    .profile-actions{
        gap:12px;
    }

    .profile-btn,
    .logout-btn{
        height:46px;
    }

    .bottom-nav{
        height:70px;
    }

    .bottom-nav .nav-item{
        font-size:10px;
    }

    .bottom-nav .nav-item i{
        font-size:22px;
        margin-bottom:4px;
        line-height:1;
    }

    .bottom-nav .nav-item img{
        width:28px;
        height:28px;
        margin-bottom:4px;
        object-fit:contain;
    }

    .chat-btn{
        width:52px;
        height:52px;
        right:15px;
        bottom:90px;
    }
    .profile-details,
.profile-actions{
    opacity:1;
    transform:translateY(0);
    pointer-events:auto;
    transition:.4s ease;
}

.profile-card{
    height:70vh;
}

.profile-card.closed{
    height:240px;
}

.profile-card.closed .profile-details,
.profile-card.closed .profile-actions{
    opacity:0;
    transform:translateY(40px);
    pointer-events:none;
}
}
/* ===========================
   LOGOUT MODAL
=========================== */

.confirm-modal{
    max-width:380px;
    margin:1.75rem auto;
}

.confirm-modal .modal-content{
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 12px 25px rgba(0,0,0,.08);
}

.confirm-modal .modal-body{
    padding:30px;
    color:#555;
    font-size:15px;
    line-height:1.7;
    text-align:center;
}

.confirm-modal .modal-footer{
    border:none;
    padding:20px 30px;
    background:#fff;
    gap:10px;
    justify-content:center;
}

.confirm-modal .btn{
    min-width:110px;
}
/* Cancel Button */

.confirm-modal .btn-secondary{
    background:#6c757d !important;
    border:2px solid #6c757d !important;
    color:#fff !important;
}

.confirm-modal .btn-secondary:hover{
    background:#5a6268 !important;
    border-color:#5a6268 !important;
    color:#fff !important;
}

/* Logout Button */

.confirm-modal .btn-danger{
    background:#2e7d32 !important;
    border:2px solid #2e7d32 !important;
    color:#fff !important;
}

.confirm-modal .btn-danger:hover{
    background:#1b5e20 !important;
    border-color:#1b5e20 !important;
    color:#fff !important;
}

/* MOBILE */

@media(max-width:576px){

    #logoutModal .modal-dialog{
        width:420px;
        max-width:95%;
        margin:auto;
        display:flex;
        align-items:center;
        min-height:100vh;
    }

    #logoutModal .modal-content{
        border-radius:14px;
    }

    #logoutModal .modal-body{
        padding:22px 24px 12px;
        text-align:center;
        font-size:16px;
        font-weight:500;
        color:#555;
        white-space:nowrap;
    }

    #logoutModal .modal-footer{
        border:none;
        padding:12px 24px 20px;
        justify-content:center;
        gap:10px;
    }

    #logoutModal .btn{
        flex:1;
        min-width:110px;
        font-size:13px;
        padding:8px 12px;
    }
}
</style>

</head>

<body>


    <!-- ================= NAVBAR ================= -->

 <nav class="navbar navbar-dark fixed-top">
    <div class="container-fluid">

        <!-- Logo -->
        <a class="navbar-brand navbar-logo" href="#">
            <img src="assets/enviromanage-logo.png" alt="EnviroManage Logo">
        </a>
         <!-- Profile -->
        <ul class="navbar-nav ms-auto">
            <a href="collector-notification.php"
       class="text-decoration-none">

        <button class="nav-icon-btn position-relative">

            <i class="bi bi-bell-fill"></i>

        </button>

    </a>

        </ul>

    </div>
</nav>

 

    <!-- ================= MAIN CONTENT ================= -->
<div class="page-wrapper">
<!-- ================= SIDEBAR ================= -->
<aside class="sidebar" id="sidebar">

    <div class="sidebar-menu">

        <!-- Home -->
        <a href="collector-home.php" class="sidebar-item">
            <i class="bi bi-house-fill"></i>
            <span>Home</span>
        </a>

        <!-- Route Map -->
        <a href="collector-route-map.php" class="sidebar-item ">
            <img src="assets/location.png" alt="Route Map">
            <span>Route Map</span>
        </a>

        <!-- Profile -->
        <a href="collector-profile.php" class="sidebar-item active">
            <i class="bi bi-person-fill"></i>
            <span>Profile</span>
        </a>

    </div>

</aside>

    <main class="main-content">

    <div class="container profile-container">

        <div class="profile-card" id="profileCard">
            <!-- Top Handle -->
          <div class="profile-handle" id="profileHandle" onclick="toggleProfile()"></div>

            <!-- Profile Header -->
            <div class="profile-header">

       <div class="profile-avatar" id="avatarContainer">
<input 
    type="file"
    id="profileImage"
    accept="image/*"
    hidden>
    <!-- Default Initials -->
    <div 
class="avatar-initials" 
id="avatarInitials"
style="<?php echo !empty($profilePhoto) ? 'display:none;' : ''; ?>">
      <?php
echo strtoupper(
    substr($user['first_name'],0,1) .
    substr($user['last_name'],0,1)
);
?>
    </div>

    <!-- Uploaded Image -->
<?php if(!empty($profilePhoto)): ?>

<img 
src="<?php echo !empty($profilePhoto) 
    ? '/' . htmlspecialchars($profilePhoto) 
    : 'assets/default-profile.png'; ?>"
id="profilePreview">

<?php else: ?>

<img 
src="assets/default-profile.png"
id="profilePreview">

<?php endif; ?>
    <!-- Camera Button -->
    <label for="profileImage" class="change-photo">
        <i class="bi bi-camera-fill"></i>
    </label>


</div>

                <div class="profile-info">
                    <h3>Hello!</h3>
                  <h5>
<?php 
echo $user['first_name']." ".$user['last_name'];
?>
</h5>
                </div>

            </div>

            <!-- Profile Details -->

            <div class="profile-details">

                <div class="info-box">
                    <strong>ID:</strong> 000123
                </div>

                <div class="info-box">
                    <strong>Truck No.:</strong> T-45
                </div>

                <div class="info-box">
                    <strong>Contact:</strong> 0912 345 6789
                </div>

            </div>

            <!-- Buttons -->

            <div class="profile-actions">

           

             <button class="logout-btn">
    <i class="bi bi-box-arrow-right"></i>
    <span>Log out</span>
</button>
            </div>

        </div>

    </div>

</main>
<!-- Bottom Navigation -->
<nav class="bottom-nav">

    <a href="collector-home.php" class="nav-item">
        <i class="bi bi-house-fill"></i>
        <span>Home</span>
    </a>

    <a href="collector-route-map.php" class="nav-item">
        <img src="assets/location.png" alt="">
        <span>Route Map</span>
    </a>

    <a href="collector-profile.php" class="nav-item active">
        <i class="bi bi-person-fill"></i>
        <span>Profile</span>
    </a>

</nav>
<!-- LOGOUT MODAL -->
<div class="modal fade" id="logoutModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered confirm-modal">

        <div class="modal-content">

            <div class="modal-body">
                Are you sure you want to log out?
            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Cancel
                </button>

                <button
                    type="button"
                    class="btn btn-danger"
                    id="confirmLogout">
                    Yes
                </button>

            </div>

        </div>

    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const chatBtn=document.querySelector(".chat-btn");

if(chatBtn){

    chatBtn.addEventListener("click",function(){

        window.location.href="collector-message.php";

    });

}
const logoutBtn = document.querySelector(".logout-btn");
const logoutModal = new bootstrap.Modal(
    document.getElementById("logoutModal")
);

logoutBtn.addEventListener("click", function () {
    logoutModal.show();
});

document.getElementById("confirmLogout").addEventListener("click", function () {
    window.location.href = "login.php";
});

const fullName = "Juan Dela Cruz";

// Kunin ang first letter ng first name at last name
const parts = fullName.trim().split(" ");

const firstLetter = parts[0][0].toUpperCase();
const lastLetter = parts[parts.length - 1][0].toUpperCase();

document.getElementById("avatarInitials").textContent =
    firstLetter + lastLetter;

const profileImage = document.getElementById("profileImage");
const profilePreview = document.getElementById("profilePreview");
const avatarInitials = document.getElementById("avatarInitials");

profileImage.addEventListener("change", function(){

    const file = this.files[0];

    if(file){

        const imageURL = URL.createObjectURL(file);

        profilePreview.src = imageURL;
        profilePreview.style.display = "block";

        avatarInitials.style.display = "none";


        const formData = new FormData();

        formData.append("photo", file);


        fetch("./collector-profile-upload.php", {
            method:"POST",
            body:formData
        })

        .then(response => response.json())

        .then(data => {

            console.log(data);

            if(data.success){

           profilePreview.src = "/" + data.path + "?v=" + Date.now();
                Swal.fire({
                    icon:"success",
                    title:"Profile Updated!",
                    text:"Your profile is updated successfully!",
                    confirmButtonColor:"#1e5631"
                });


            }else{

                Swal.fire({
                    icon:"error",
                    title:"Upload Failed",
                    text:data.message,
                    confirmButtonColor:"#1e5631"
                });

            }

        })

        .catch(error=>{

            console.log(error);

            Swal.fire({
                icon:"error",
                title:"Error",
                text:"Something went wrong."
            });

        });

    }

});
const profileCard = document.getElementById("profileCard");
const profileHandle = document.getElementById("profileHandle");

profileHandle.addEventListener("click", function(){

    profileCard.classList.toggle("closed");

});


// DRAG UP / DOWN MOBILE

let startY = 0;


profileCard.addEventListener("touchstart", function(e){

    startY = e.touches[0].clientY;

});


profileCard.addEventListener("touchend", function(e){

    let endY = e.changedTouches[0].clientY;


    // swipe up = expand
if(startY - endY > 50){
    profileCard.classList.remove("closed");
}

// swipe down = collapse
if(endY - startY > 50){
    profileCard.classList.add("closed");
}
});

</script>
</body>