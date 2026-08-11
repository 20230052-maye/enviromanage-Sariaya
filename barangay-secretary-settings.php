<?php
session_start();

date_default_timezone_set('Asia/Manila');


if(
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'barangay_secretary'
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
    die("Database connection failed");
}

$conn->set_charset("utf8mb4");


$userID = $_SESSION['user_id'];


// FETCH SECRETARY DATA
$stmt = $conn->prepare("
    SELECT 
        first_name,
        last_name,
        barangay,
        employee_id,
        phone,
        email,
        username,
        profile_photo
    FROM users
    WHERE id=? 
    AND role='barangay_secretary'
");

$stmt->bind_param("i",$userID);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();


if(!$user){
    die("User not found");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Settings</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    /* ===========================
   GOOGLE FONT
=========================== */

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');


*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}


body{
    background:#f4f7f9;
}


/* ===========================
   NAVBAR
=========================== */


.navbar{

    height:70px;
    background:#1f5d2f;
    z-index:1200;
    padding:0 20px;

}

.navbar-brand img{

    height:45px;
    width:45px;
    object-fit:contain;

}
.navbar-actions{
    display:flex;
    align-items:center;
    gap:10px;
    margin-left:auto;
}

.nav-icon-btn{
    width:42px;
    height:42px;
    border:none;
    background:transparent;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
    font-size:22px;
    transition:.25s;
}

.nav-icon-btn:hover{
    background:rgba(255,255,255,.15);
}

.notification-badge{
    position:absolute;
    top:4px;
    right:3px;
    width:18px;
    height:18px;
    border-radius:50%;
    background:#dc3545;
    color:#fff;
    font-size:10px;
    display:flex;
    align-items:center;
    justify-content:center;
}

.dropdown-toggle::after{
    display:none;
}.navbar-actions{
    display:flex;
    align-items:center;
    gap:10px;
    margin-left:auto;
}

.nav-icon-btn{
    width:42px;
    height:42px;
    border:none;
    background:transparent;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
    font-size:22px;
    transition:.25s;
}

.nav-icon-btn:hover{
    background:rgba(255,255,255,.15);
}

.notification-badge{
    position:absolute;
    top:4px;
    right:3px;
    width:18px;
    height:18px;
    border-radius:50%;
    background:#dc3545;
    color:#fff;
    font-size:10px;
    display:flex;
    align-items:center;
    justify-content:center;
}

.dropdown-toggle::after{
    display:none;
}
.navbar .container-fluid{

    display:flex;
    align-items:center;
    justify-content:space-between;

}


#hamburger{

    display:none;

    width:40px;
    height:40px;

    border:none;
    background:transparent;

    color:white;

    align-items:center;
    justify-content:center;

    font-size:22px;

}


/* ===========================
   SIDEBAR
=========================== */


.sidebar{
    position:fixed;
    top:70px;
    left:0;
    width:270px;
    height:calc(100vh - 70px);
    background:#fff;
    border-right:1px solid #dee2e6;
    padding:15px 0; /* tanggalin side gap */
    overflow-y:auto;
    transition:.3s ease;
    z-index:1100;
}
.sidebar .nav-link{

    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 20px;
    margin-bottom:8px;
    border-radius:0;
    color:#495057;
    text-decoration:none;
    white-space:nowrap;

}



.sidebar .nav-link i{

    font-size:20px;

    width:25px;

}


.sidebar .nav-link:hover,
.sidebar .nav-link.active{

    background:#1e5631;
    color:white;
}

/* COLLAPSED SIDEBAR */

.sidebar.collapsed{
    width:70px;
}


.sidebar.collapsed span{
    display:none;
}


.sidebar.collapsed .nav-link{

    justify-content:center;
    padding:12px 10px;
    width:100%;
    height:auto;
    margin-bottom:8px;
    border-radius:0;

}


/* MAIN CONTENT */

.main-content{

    margin-left:270px;

    width:calc(100% - 270px);

    padding:90px 25px 30px;

    transition:.3s ease;

}

/* SIDEBAR BUTTON */

#sidebarControls{

    position:fixed;

    top:85px;

    left:70px;

    display:none;

    flex-direction:column;

    gap:8px; /* space between X and >/< */

    z-index:1300;

    transition:.3s ease;

}

#sidebarControls button{

    width:32px;

    height:32px;

    border:none;

    display:flex;

    align-items:center;

    justify-content:center;

    color:white;

    cursor:pointer;

}


#toggleBtn{

    background:#1e5631;

    border-radius:0 8px 8px 0;

}

#closeBtn{

    display:flex;

    background:#dc3545;

    border-radius:0 8px 8px 0;

}


/* ==========================================
   CARD
========================================== */

.card{

    border:none;

    border-radius:20px;

    box-shadow:0 8px 18px rgba(0,0,0,.05);

    transition:.3s;

}

.card:hover{

    transform:translateY(-4px);

    box-shadow:0 12px 25px rgba(0,0,0,.08);

}

.card-header{

    background:#fff;

    border-bottom:1px solid #eee;

    padding:18px 25px;

}

.card-header h5{

    color:#2e7d32;

    margin:0;

    font-weight:600;

}

.card-body{

    padding:30px;

}

/* ==========================================
   PROFILE CARD
========================================== */

.profile-card{

    overflow:hidden;
       

}

.profile-image{
    width:clamp(100px, 18vw, 170px);
    height:clamp(100px, 18vw, 170px);
    border-radius:50%;
    object-fit:cover;
    border:5px solid #e8f5e9;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    transition:.3s;
}

.profile-image:hover{
    transform:scale(1.05);
}

.profile-card h3{
    color:#1b5e20;
    font-weight:700;
    font-size:clamp(1.4rem,2.5vw,2rem);
    margin-bottom:8px;
}

.profile-card hr{

    margin:20px 0;

}

/* ==========================================
   LABELS
========================================== */

label{

    font-size:14px;

    font-weight:500;

    color:#555;

    margin-bottom:8px;

    display:block;

}

/* ==========================================
   INPUTS
========================================== */

.form-control{

    height:48px;

    border-radius:12px;

    border:1px solid #ddd;

    transition:.3s;

}

.form-control:focus{

    border-color:#2e7d32;

    box-shadow:0 0 0 .15rem rgba(46,125,50,.15);

}

textarea.form-control{

    height:auto;

    resize:none;

}
/* ==========================================
   BUTTONS
========================================== */

.btn{
    border-radius:12px;
    font-weight:500;
    transition:.3s ease;
}

.btn-success{
    background:linear-gradient(135deg,#2e7d32,#43a047);
    border:none;
    color:#fff;
}

.btn-success:hover{
    background:linear-gradient(135deg,#1b5e20,#2e7d32);
    transform:translateY(-2px);
    box-shadow:0 8px 18px rgba(46,125,50,.25);
}

.btn-outline-danger{
    border:2px solid #dc3545;
    color:#dc3545;
}

.btn-outline-danger:hover{
    background:#dc3545;
    color:#fff;
}

/* ==========================================
   BADGES
========================================== */

.badge{
    padding:8px 14px;
    border-radius:20px;
    font-size:13px;
    font-weight:500;
}

/* ===========================
   NOTIFICATION SETTINGS
=========================== */

.notification-item{

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:16px 0;

    border-bottom:1px solid #ececec;

}

.notification-item:last-child{

    border-bottom:none;

}

.notification-item .form-check-label{

    flex:1;

    margin:0;

    font-size:15px;

    color:#555;

}

.notification-item .form-check-input{

    margin-left:20px;

    cursor:pointer;

}
.form-check-label{
    font-weight:500;
    color:#555;
    cursor:pointer;
}

.form-check-input{
    width:45px;
    height:24px;
    cursor:pointer;
}

.form-check-input:checked{
    background-color:#2e7d32;
    border-color:#2e7d32;
}

.form-check-input:focus{
    box-shadow:0 0 0 .15rem rgba(46,125,50,.2);
}


/* ==========================================
   PROFILE BUTTON
========================================== */
.profile-header{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
}
.profile-card .btn{
    min-width:170px;
}

/* ==========================================
   ICONS
========================================== */

.card-header i{
    margin-right:8px;
    color:#2e7d32;
}

.topbar i{
    color:#2e7d32;
}


/* ==========================================
   SCROLLBAR
========================================== */

::-webkit-scrollbar{
    width:8px;
}

::-webkit-scrollbar-track{
    background:#f1f1f1;
}

::-webkit-scrollbar-thumb{
    background:#43a047;
    border-radius:20px;
}

::-webkit-scrollbar-thumb:hover{
    background:#2e7d32;
}

/* ==========================================
   ANIMATIONS
========================================== */

.profile-image{
    transition:.3s;
}

.profile-image:hover{
    transform:scale(1.05);
}

.form-control,
.btn,
.card{
    transition:.3s;
}

/* ==========================================
   ACTION CARD
========================================== */

.card:last-child .card-body{
    display:flex;
    justify-content:flex-end;
    align-items:center;
    flex-direction:row;
    flex-wrap:nowrap;
    gap:10px;
}



/* ===========================
   TABLET
=========================== */

@media(max-width:1200px){


    .main-content{

        margin-left:240px;

        width:calc(100% - 240px);

    }


}




/* ===========================
   SMALL LAPTOP / TABLET
=========================== */


@media(max-width:992px){


    .sidebar{

        width:70px;

    }
.sidebar.expanded{
    width:270px;
      box-shadow:8px 0 20px rgba(0,0,0,.15);
    z-index:1200;
}

.sidebar .nav-link{

    justify-content:center;
    padding:12px 0;
    width:100%;
    height:auto;
    margin-bottom:8px;
    border-radius:0;

}

    .sidebar .nav-link span{

        display:none;

    }

.sidebar.hide-sidebar{
    transform:translateX(-100%);
}
.sidebar.hide-sidebar ~ .main-content{

    margin-left:0;
      width:100%;

}

 .main-content{

    margin-left:70px;
    width:calc(100% - 70px);
    padding:120px 30px 30px;
    transition:.3s ease;

}
.navbar .container-fluid{
    position:relative;
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.navbar-brand{
    position:absolute;
    left:50%;
    transform:translateX(-50%);
    margin:0;
}

.navbar-actions{
    margin-left:auto;
}

    .sidebar.expanded ~ .main-content{

    margin-left:70px;
      width:calc(100% - 70px);


}


.sidebar.expanded .nav-link{
    width:100%;
    height:auto;
    justify-content:flex-start;
    gap:12px;
    padding:12px 15px;
    margin-bottom:8px;
}

.sidebar.expanded .nav-link span{
    display:block;
}



    /* BUTTON POSITION */

    #sidebarControls{

    
        left:70px;

    }


    .sidebar.expanded ~ #sidebarControls{

        left:270px;

    }



    /* kapag icon only */

    #toggleBtn{

        display:flex;

    }


    #closeBtn{

        display:flex;

    }

.navbar .container-fluid{

    justify-content:space-between;

}

.navbar-brand{

    margin-left:auto;

}

#hamburger{

    display:flex;

}
.profile-card .row:last-of-type{
    text-align:left;
}

.profile-card .row:last-of-type label{
    text-align:left;
}
.profile-card .col-lg-9{
    margin-top:25px;
}

.profile-card h3{
    font-size:1.7rem;
}

.profile-image{
    width:140px;
    height:140px;
}

.profile-card .badge{
    display:inline-block;
    margin-top:5px;
}
.profile-card .col-lg-3{
    display:flex;
    flex-direction:column;
    align-items:center;
}

#changePhotoBtn{

    min-width:170px;
    margin-top:15px;

}
}



/* ===========================
   MOBILE
=========================== */
@media(max-width:576px){


  .navbar{

        padding:0 15px;

    }


/* CENTER LOGO SA MOBILE */
.navbar .container-fluid{
    position: relative;
    justify-content: flex-start;
}

.navbar-brand{
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    margin: 0;
}
.nav-icon-btn{
    width:38px;
    height:38px;
    font-size:20px;
}

.notification-badge{
    width:16px;
    height:16px;
    font-size:9px;
}

.navbar-brand img{
    width:38px;
    height:38px;
}

.notification-badge{
    width:16px;
    height:16px;
    font-size:9px;
}
    .sidebar{

        width:70px;

        transform:translateX(0);

    }



 .sidebar .nav-link{

    justify-content:center;
    width:100%;
    border-radius:0;

}


    .sidebar .nav-link span{

        display:none;

    }



    #sidebarControls{

        display:flex;

        left:70px;

    }



    .sidebar.expanded .nav-link{

        justify-content:flex-start;

    }



    .sidebar.expanded .nav-link span{

        display:inline;

    }



.main-content{

    margin-left:70px;
    width:calc(100% - 70px);
    padding:50px 15px 20px;
    transition:.3s ease;

}
.sidebar.hide-sidebar ~ .main-content{

    margin-left:0;
       width:100%;

}
.profile-image{
    width:115px;
    height:115px;
    border-width:4px;
}

.profile-card h3{
    font-size:1.45rem;
}

.profile-card .btn{
    width:100%;
    max-width:220px;
}

.profile-card .card-body{
    padding:22px;
}

.profile-card .col-lg-3{
    display:flex;
    flex-direction:column;
    align-items:center;
}

#changePhotoBtn{
    display:block;
    width:100%;
    max-width:200px;
    margin:15px auto 0;
}

.profile-image{
    margin:0 auto;
}
.card:last-child .card-body{
    display:flex;
    flex-direction:row;
    justify-content:flex-end;
    align-items:center;
    flex-wrap:nowrap;
    gap:8px;
}

#saveSettingsBtn,
#logoutBtn{
    flex:1;
    width:auto;
    min-width:0;
    padding:8px 6px;
    font-size:12px;
    white-space:nowrap;
}


#saveSettingsBtn i,
#logoutBtn i{

    font-size:11px;

}


/* MOBILE LOGOUT MODAL */
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
     background:#fff;
}

#logoutModal .modal-body{
   padding:22px 24px 12px;
    text-align:center;
    font-size:16px;
    font-weight:500;
    color:#555;
      white-space:nowrap; /* one line lang */
}

#logoutModal .modal-footer{
      background:#fff;
    border:none;
    padding:12px 24px 20px;
    justify-content:center;
    gap:10px;
     flex-direction:row !important;
    display:flex;
}

#logoutModal .btn{
     width:auto !important;
    flex:1;
    flex:1;                    /* pantay ang width */
    min-width:110px;
    font-size:13px;
    padding:8px 12px;
}
  .swal2-popup{
        width:75% !important;
        max-width:300px !important;
        border-radius:15px !important;
        padding:20px !important;
    }

    .swal2-title{
        font-size:18px !important;
    }

    .swal2-html-container{
        font-size:13px !important;
    }

   .swal2-icon{
    transform:scale(.75);
    margin:5px auto !important;
}

.swal2-icon .swal2-icon-content{
    font-size:28px !important;
}

    .swal2-confirm,
    .swal2-cancel{
        font-size:12px !important;
        padding:8px 18px !important;
        border-radius:8px !important;
    }

}

/* ===========================
   VERY SMALL MOBILE
=========================== */


@media(max-width:380px){

  .card:last-child .card-body{
        flex-direction:row;
        gap:5px;
    }

    #saveSettingsBtn,
    #logoutBtn{
        font-size:11px;
        padding:7px 3px;
    }
    .main-content{

        padding-left:10px;

        padding-right:10px;

    }


    .btn-lg{

        width:100%;

    }

.profile-image{
    width:95px;
    height:95px;
}

.profile-card h3{
    font-size:1.25rem;
}

.profile-card .badge{
    font-size:11px;
}
#changePhotoBtn{
    max-width:170px;
    font-size:14px;
    padding:8px 12px;
}
}

.hide-sidebar{

    transform:translateX(-100%);

}

/* ACTION BUTTON SIZE */

#saveSettingsBtn,
#logoutBtn{

    padding:8px 14px;
    font-size:13px;
    min-width:120px;
    white-space:nowrap;

}


/* ===========================
   SIDEBAR SHADOW WHEN MODAL IS OPEN
=========================== */

body.modal-open .sidebar{
    box-shadow:0 0 0 9999px rgba(0,0,0,.45);
    z-index:1040;
}

body.modal-open #sidebarControls{
    z-index:1041;
}

body.modal-open .navbar{
    z-index:1040;
}
/* ===========================
   SWEETALERT ABOVE SIDEBAR
=========================== */

.swal2-container{
    z-index:3000 !important;
}


body.swal2-shown .sidebar{
    box-shadow:0 0 0 9999px rgba(0,0,0,.45);
    z-index:1040;
}


body.swal2-shown .navbar{
    z-index:1040;
}


body.swal2-shown #sidebarControls{
    z-index:1041;
}
/* SWEETALERT OUTLINE BUTTONS */
.swal-outline-green{
    background:#fff !important;
    color:#2e7d32 !important;
    border:2px solid #2e7d32 !important;
    border-radius:8px !important;
    padding:8px 20px !important;
    font-weight:500;
}

.swal-outline-green:hover{
    background:#2e7d32 !important;
    color:#fff !important;
}

.swal-outline-red{
    background:#fff !important;
    color:#dc3545 !important;
    border:2px solid #dc3545 !important;
    border-radius:8px !important;
    padding:8px 20px !important;
    font-weight:500;
}

.swal-outline-red:hover{
    background:#dc3545 !important;
    color:#fff !important;
}
/* Space between SweetAlert buttons */
.swal2-actions{
    gap:12px !important;
}

.swal-outline-green,
.swal-outline-red{
    min-width:110px;
}
</style>
</head>


<body>


<!-- NAVBAR -->

   <nav class="navbar navbar-dark fixed-top">

    <div class="container-fluid">

        <!-- Left -->
        <button id="hamburger">
            <i class="bi bi-list"></i>
        </button>

        <!-- Center Logo -->
        <a class="navbar-brand">
            <img src="assets/enviromanage-logo.png" alt="Logo">
        </a>

        <!-- Right -->
        <div class="navbar-actions">

    <a href="barangay-secretary-notification.php"
   class="text-decoration-none">

    <button class="nav-icon-btn position-relative">

        <i class="bi bi-bell-fill"></i>

     

    </button>

</a>
            <!-- Profile -->
            <div class="dropdown">

                <button
                    class="nav-icon-btn dropdown-toggle"
                    data-bs-toggle="dropdown">

                    <i class="bi bi-person-circle"></i>

                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow">

                    <li><hr class="dropdown-divider"></li>

                  <li>
    <a
    class="dropdown-item text-danger"
    href="#"
    data-bs-toggle="modal"
    data-bs-target="#logoutModal">

        <i class="bi bi-box-arrow-right me-2"></i>

        Logout

    </a>
</li>

                </ul>

            </div>

        </div>

    </div>


</nav>



<!-- SIDEBAR -->

<div class="sidebar" id="sidebar">


    <div class="nav flex-column">

        <a class="nav-link " href="barangay-secretary-home.php">
            <i class="bi bi-person-check"></i>
            <span>User Applications</span>
        </a>


        <a class="nav-link" href="barangay-secretary-complaints.php">
            <i class="bi bi-chat-left-text"></i>
            <span>Resident Complaints</span>
        </a>
     <a class="nav-link" href="barangay-secretary-announcements.php">
    <i class="bi bi-megaphone-fill"></i>
    <span>Announcements</span>
</a>

        <a class="nav-link active" href="barangay-secretary-settings.php">
            <i class="bi bi-gear"></i>
            <span>Settings</span>
        </a>


    </div>


</div>


<div id="sidebarControls">

      <button id="closeBtn">
        <i class="bi bi-x-lg"></i>
    </button>
 
    <button id="toggleBtn">
        <i class="bi bi-chevron-right"></i>
    </button>


  

</div>





<!-- MAIN CONTENT -->

<main class="main-content">


        <!-- PROFILE CARD -->

        <div class="card profile-card mt-4">

            <div class="card-body">

  <div class="text-center mb-4 profile-header">

  <img 
src="<?= !empty($user['profile_photo']) 
? 'uploads/profile/'.$user['profile_photo'] 
: '' ?>"
class="profile-image"
id="profileImage">
    <div class="mt-3">
        <button id="changePhotoBtn" class="btn btn-success">
            <i class="bi bi-camera-fill"></i>
            Change Photo
        </button>
    </div>

  <h3 id="profileName" class="mt-3">
<?= htmlspecialchars($user['first_name']." ".$user['last_name']) ?>
</h3>
 <span class="badge bg-success">
        Barangay Secretary
    </span>

</div>

<hr>

<div class="row">
                            <div class="col-md-6">

                                <label>Assigned Barangay</label>

                                <input

                                type="text"

                                class="form-control"

                              value="<?= htmlspecialchars($user['barangay']) ?>"
                                readonly>

                            </div>

                            <div class="col-md-6">

                                <label>Employee ID</label>

                                <input

                                type="text"

                                class="form-control"

                                value="<?= htmlspecialchars($user['employee_id']) ?>"
                                readonly>

                            </div>

                            <div class="col-md-6 mt-3">

                                <label>Contact Number</label>

                                <input

                                type="text"

                                class="form-control"
value="<?= htmlspecialchars($user['phone']) ?>"
                            </div>

                            <div class="col-md-6 mt-3">

                                <label>Email Address</label>

                                <input

                                type="email"

                                class="form-control"

                    value="<?= htmlspecialchars($user['email']) ?>"
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- ACCOUNT SETTINGS -->

        <div class="card mt-4">

            <div class="card-header">

                <h5>

                    <i class="bi bi-shield-lock-fill"></i>

                    Account Settings

                </h5>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6">

                        <label>Username</label>

                        <input

                        class="form-control"

                     value="<?= htmlspecialchars($user['username']) ?>"
                    </div>

                    <div class="col-md-6">

                        <label>Current Password</label>

                        <input

                        type="password"

                        class="form-control">

                    </div>

                    <div class="col-md-6 mt-3">

                        <label>New Password</label>

                        <input

                        type="password"

                        class="form-control">

                    </div>

                    <div class="col-md-6 mt-3">

                        <label>Confirm Password</label>

                        <input

                        type="password"

                        class="form-control">

                    </div>

                </div>

            </div>

        </div>
                <!-- ================= NOTIFICATION SETTINGS ================= -->

        <div class="card mt-4">

            <div class="card-header">

                <h5>

                    <i class="bi bi-bell-fill"></i>

                    Notification Settings

                </h5>

            </div>

            <div class="card-body">

                <div class="form-check form-switch mb-3">

                    <input
                    class="form-check-input"
                    type="checkbox"
                    checked>

                    <label class="form-check-label">

                        Notify me for new resident applications

                    </label>

                </div>

        
                          <div class="form-check form-switch mb-3">

                    <input
                    class="form-check-input"
                    type="checkbox"
                    checked>

                    <label class="form-check-label">

                        Receive email notifications

                    </label>

                </div>

            </div>

        </div>

 

        <!-- ================= ACTION BUTTONS ================= -->

       <div class="card mt-4">

    <div class="card-body d-flex justify-content-end flex-wrap gap-2">

        <button id="saveSettingsBtn" class="btn btn-success">
            <i class="bi bi-check-circle-fill"></i>
            Save Changes
        </button>

        <button
        class="btn btn-outline-danger"
        id="logoutBtn">

            <i class="bi bi-box-arrow-right"></i>
            Logout

        </button>

    </div>

</div>

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



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ===============================
// SIDEBAR LOGIC
// ===============================

const sidebar = document.getElementById("sidebar");
const toggleBtn = document.getElementById("toggleBtn");
const closeBtn = document.getElementById("closeBtn");
const hamburger = document.getElementById("hamburger");
const sidebarControls = document.getElementById("sidebarControls");



function isTabletOrMobile(){

    return window.innerWidth <= 992;

}



// ===============================
// ICON ONLY STATE
// ===============================

function iconOnly(){


    sidebar.classList.remove("hide-sidebar");

    sidebar.classList.remove("expanded");


    sidebarControls.style.display="flex";


    toggleBtn.style.display="flex";

    closeBtn.style.display="flex";


    hamburger.style.display="flex";



    const icon = toggleBtn.querySelector("i");

    icon.className="bi bi-chevron-right";


}



// ===============================
// EXPANDED STATE
// ===============================

function expandedSidebar(){


    sidebar.classList.remove("hide-sidebar");


    sidebar.classList.add("expanded");


    sidebarControls.style.display="flex";


    toggleBtn.style.display="flex";

    closeBtn.style.display="flex";


    hamburger.style.display="flex";



    const icon = toggleBtn.querySelector("i");

    icon.className="bi bi-chevron-left";


}




// ===============================
// CLOSED STATE
// ===============================

function closeSidebar(){


    sidebar.classList.add("hide-sidebar");


    sidebar.classList.remove("expanded");


    sidebarControls.style.display="none";


    hamburger.style.display="flex";


}




// ===============================
// CLICK >
// ===============================

toggleBtn.addEventListener("click",()=>{


    if(!isTabletOrMobile()) return;


    if(sidebar.classList.contains("expanded")){


        iconOnly();


    }else{


        expandedSidebar();


    }


});




// ===============================
// CLICK X
// ===============================

closeBtn.addEventListener("click",()=>{


    if(!isTabletOrMobile()) return;


    closeSidebar();


});




hamburger.addEventListener("click",()=>{


    if(!isTabletOrMobile()) return;


    sidebar.classList.remove("hide-sidebar");

    sidebar.classList.remove("expanded");


    sidebarControls.style.display="flex";


    toggleBtn.style.display="flex";

    closeBtn.style.display="flex";


   // keep hamburger visible
hamburger.style.display="flex";


    const icon = toggleBtn.querySelector("i");

    icon.className="bi bi-chevron-right";


});
// ===============================
// RESPONSIVE
// ===============================

function checkResponsive(){

    if(isTabletOrMobile()){


        // kapag papasok sa tablet/mobile
        if(sidebar.classList.contains("hide-sidebar")){

            closeSidebar();

        }else{

            iconOnly();

        }


        // IMPORTANT
        // show hamburger agad sa mobile/tablet
        hamburger.style.display="flex";


    }else{


        // desktop mode

        sidebar.classList.remove("hide-sidebar");

        sidebar.classList.remove("expanded");


        sidebarControls.style.display="none";


        // desktop hide hamburger
        hamburger.style.display="none";


    }

}



window.addEventListener("resize", checkResponsive);


// run once on page load
checkResponsive();



// ===============================
// CHANGE PROFILE PHOTO
// ===============================

const changePhotoButton = document.getElementById("changePhotoBtn");

const fileInput = document.createElement("input");

fileInput.type = "file";
fileInput.accept = "image/*";


// store temporary image
let selectedImage = "";



if(changePhotoButton){


    changePhotoButton.addEventListener("click", function(){


        fileInput.click();


    });


}



fileInput.addEventListener("change", function(e){


    const file = e.target.files[0];


    if(file){


        const reader = new FileReader();


    reader.onload = function(event){

    selectedImage = event.target.result;

    profileImage.src = selectedImage;

};


        reader.readAsDataURL(file);


    }


});






// ===============================
// PASSWORD VALIDATION
// ===============================


const passwordInputs = document.querySelectorAll(
    "input[type='password']"
);


let currentPassword = passwordInputs[0];

let newPassword = passwordInputs[1];

let confirmPassword = passwordInputs[2];



if(confirmPassword){


    confirmPassword.addEventListener("input", function(){


        if(confirmPassword.value === ""){


            confirmPassword.style.borderColor = "";


        }


        else if(newPassword.value === confirmPassword.value){


            confirmPassword.style.borderColor = "green";


        }


        else{


            confirmPassword.style.borderColor = "red";


        }


    });


}





// ===============================
// SAVE SETTINGS
// ===============================

const saveButton = document.getElementById("saveSettingsBtn");

const originalValues = [];

document.querySelectorAll(".form-control, .form-check-input").forEach(input => {

    originalValues.push({
        element: input,
        value: input.type === "checkbox" ? input.checked : input.value.trim()
    });

});

if(saveButton){

    saveButton.addEventListener("click", function(){


     let hasChanges = false;


originalValues.forEach(item => {

    let currentValue = item.element.type === "checkbox"
        ? item.element.checked
        : item.element.value.trim();


    if(currentValue !== item.value){

        hasChanges = true;

    }

});


     // CHECK IF NO CHANGES
if(!hasChanges){

    Swal.fire({

        icon:"info",

        title:"No Changes Detected",

        text:"Please make changes first before saving.",

        confirmButtonColor:"#2e7d32",

        customClass:{
            container:"settings-alert"
        }

    });

    return;

}



        // PASSWORD CHECKING

        if(newPassword.value !== "" || confirmPassword.value !== ""){


            if(currentPassword.value === ""){


                Swal.fire({

                    icon:"warning",

                    title:"Current Password Required",

                    text:"Please enter your current password first.",

                    confirmButtonColor:"#2e7d32"

                });

                return;


            }



            if(newPassword.value !== confirmPassword.value){


                Swal.fire({

                    icon:"error",

                    title:"Password Mismatch",

                    text:"New password and confirm password do not match.",

                    confirmButtonColor:"#2e7d32"

                });

                return;


            }


        }


Swal.fire({

    icon:"success",

    title:"Settings Saved",

    text:"Your changes have been saved successfully.",

    confirmButtonColor:"#2e7d32",

    customClass:{
        container:"settings-alert"
    }

})
      
      .then(()=>{


            // UPDATE ORIGINAL VALUES AFTER SAVE

         originalValues.forEach(item => {

    item.value = item.element.type === "checkbox"
        ? item.element.checked
        : item.element.value.trim();

});


        });



    });

}
// ===============================
// LOGOUT
// ===============================


const logoutButton = document.getElementById("logoutBtn");


if(logoutButton){


    logoutButton.addEventListener("click", function(){



    Swal.fire({

    title:"Logout?",

    text:"Are you sure you want to logout?",

    icon:"warning",

    showCancelButton:true,

    confirmButtonText:"Yes",
    cancelButtonText:"Cancel",

    buttonsStyling:false,

    customClass:{
        container:"settings-alert",
        confirmButton:"swal-outline-green",
        cancelButton:"swal-outline-red"
    }

})
.then((result)=>{


            if(result.isConfirmed){


                window.location.href="login.php";


            }


        });


    });


}

const profileName = document.getElementById("profileName");

function createAvatar(name){

    const canvas = document.createElement("canvas");

    canvas.width = 300;
    canvas.height = 300;

    const ctx = canvas.getContext("2d");

    ctx.fillStyle = "#2e7d32";
    ctx.fillRect(0,0,300,300);

    const initials = name
        .trim()
        .split(" ")
        .map(word => word[0])
        .slice(0,2)
        .join("")
        .toUpperCase();

    ctx.fillStyle = "#fff";
    ctx.font = "bold 120px Arial";
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.fillText(initials,150,160);

    profileImage.src = canvas.toDataURL();

}

createAvatar(profileName.textContent);
document.getElementById("confirmLogout").addEventListener("click", function () {
    window.location.href = "login.php";
});

document.getElementById("saveSettingsBtn")
.addEventListener("click",function(){

fetch("barangay-secretary-update-settings.php",{

method:"POST",

headers:{
"Content-Type":"application/json"
},

body:JSON.stringify({

phone:document.querySelector("input[value*='09']").value,
email:document.querySelector("input[type='email']").value,
username:document.querySelector("input").value

})

})
.then(res=>res.json())
.then(data=>{

Swal.fire({
icon:data.success?"success":"error",
title:data.success?"Saved":"Error",
text:data.message,
confirmButtonColor:"#2e7d32"
});

});


});
</script>

</body>
</html>