<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title> Notification</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


<style>
    body{
    background:#f4f7f9;
    padding-top:70px;
}

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

.navbar .container-fluid{
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.navbar-actions{
    display:flex;
    align-items:center;
    gap:10px;
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
    padding:15px 0;
    overflow-y:auto;
    transition:.3s ease;
    z-index:1100;

    /* Hidden by default */
    display:none;
}
.sidebar .nav-link{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 20px;
    margin-bottom:8px;
    color:#495057;
    text-decoration:none;
}

.sidebar .nav-link i{
    width:25px;
    font-size:20px;
}

.sidebar .nav-link:hover,
.sidebar .nav-link.active{
    background:#1e5631;
    color:#fff;
}
#sidebarControls{
    position:fixed;
    top:85px;
    left:270px;
    display:none;
    flex-direction:column;
    gap:8px;
    z-index:1300;
    transition:.3s;
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
    display:none;
    background:#dc3545;
     border-radius:0 8px 8px 0;
}
.main-content,
main{
    flex:1;
    padding:35px;
    transition:.3s;
    overflow-x:hidden;
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
    font-size:22px;
    border-radius:50%;
    transition:.3s;
}

.nav-icon-btn:hover{
    background:rgba(255,255,255,.15);
    color:#fff;
}

.notification-badge{
    position:absolute;
    top:2px;
    right:-2px;
    min-width:20px;
    height:20px;
    padding:0 5px;
    background:#dc3545;
    color:#fff;
    border:2px solid #1f5d2f;
    border-radius:50px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:11px;
    font-weight:600;
    line-height:1;
}
.dropdown-toggle::after{
    display:none;
}

/* LEFT HAMBURGER */
#hamburger{
    display:flex;
    align-items:center;
    justify-content:center;
    position:relative;
    width:40px;
    height:40px;
    border:none;
    background:transparent;
    color:#fff;
    font-size:22px;
    z-index:1001;
}
/* BACK ARROW */
.back-arrow{
    display:flex;
    align-items:center;
}


    /* ==========================================================
   NOTIFICATION PAGE
========================================================== */

.page-title{
    font-size:1.8rem;
    font-weight:700;
    color:#198754;
}

.category-tabs{
    display:flex;
    flex-wrap:wrap;
    gap:12px;
    margin-bottom:25px;
    align-items:center;
}


.notification-list{
    width:100%;
    display:flex;
    flex-direction:column;
    gap:18px;
}

/* ==========================================================
   NOTIFICATION CARD
========================================================== */
.notification-card{
    width:100%;
    background:#fff;
    border:none;
    border-radius:18px;
    padding:18px 22px;
    display:flex;
    align-items:center;
    gap:18px;
    box-shadow:0 8px 20px rgba(0,0,0,.06);
    transition:.25s;
    cursor:pointer;
}

.notification-card:hover{
    transform:translateY(-2px);
}

.notification-left{
    display:flex;
    align-items:center;
    gap:16px;
    flex:1;
}

.notification-left div{
    flex:1;
}

.notification-card .bi-chevron-right{
    font-size:18px;
    color:#999;
    margin-left:auto;
}
.notification-left h6{
    margin:0;
    font-size:17px;
    font-weight:600;
}

.notification-content p{
    margin:6px 0 0;
    color:#777;
    font-size:14px;
}

.notification-time{
    font-size:13px;
    color:#999;
}.notification-card.unread{
    border-left:6px solid #198754;
    background:#f8fff9;
}

.notification-card.unread h6{
    color:#198754;
}
/* ==========================================================
   STATUS DOT
========================================================== */

.status-dot{
    width:13px;
    height:13px;
    border-radius:50%;
    background:#198754;
    flex-shrink:0;
}

.status-dot.read{
    background:#bdbdbd;
}

/* ==========================================================
   UNREAD CARD
========================================================== */

.notification-card.unread{
    border-left:6px solid #198754;
    background:#f8fff9;
}

.notification-card.unread h6{
    color:#198754;
}

/* ==========================================================
   BADGE
========================================================== */


.notification-info{
    background:#fff;
    border:1px solid #e8e8e8;
    border-radius:15px;
    padding:25px;
}

.notification-info label{
    display:block;
    color:#198754;
    font-size:13px;
    font-weight:600;
    margin-bottom:5px;
}
.notification-info p{
    font-size:15px;
    color:#444;
    margin-bottom:20px;
}
/* ==========================================================
   MODAL
========================================================== */

.modal-content{
    border:none;
    border-radius:18px;
    overflow:hidden;
    max-height:85vh;
    display:flex;
    flex-direction:column;
}

.modal.show{
    display:flex !important;
    align-items:center;
    justify-content:center;
}
.modal-header{
    background:#198754;
    color:#fff;
    border:none;
    position:sticky;
    top:0;
    z-index:10;
}

.modal-title{
    font-weight:600;
}

.btn-close{
    filter:invert(1);
}

.modal-body{
    padding:20px;
    overflow-y:auto;
    flex:1;
}
.modal-body label{
    color:#198754;
    display:block;
    margin-bottom:4px;
    font-weight:600;
}

.modal-body p{
   margin-bottom:10px;
    font-size:14px;
    color:#555;
}

.modal-footer{
    border:none;
    padding:18px 25px;
    background:#fff;
}
#hamburger{
    display:none;
}

#sidebarControls{
    display:none;
}
/* ==========================================================
   BUTTONS
========================================================== */

.btn-success{
    background:#198754;
    border:none;
}

.btn-success:hover{
    background:#157347;
}

/* ==========================================================
   RESPONSIVE
========================================================== */
.category-btn{
    display:flex;
    align-items:center;
    gap:8px;
    background:#fff;
    border:2px solid #e8e8e8;
    color:#666;
    border-radius:30px;
    padding:8px 15px;
    font-size:14px;
    font-weight:600;
    transition:.3s;
}

.category-btn:hover,
.category-btn.active{
    background:#198754;
    color:#fff;
    border-color:#198754;
}
@media (max-width:992px){


.notification-card{
    padding:16px 20px;
}

.page-title{
    font-size:1.7rem;
}
.modal-dialog{
    max-width:760px;
    margin:2rem auto;
}

.modal-content{
    max-height:88vh;
}
}
@media (max-width:768px){

main{
    padding:20px 15px!important;
}

.page-title{
    font-size:1.45rem;
}

.category-tabs{
    overflow-x:auto;
    flex-wrap:nowrap;
    padding-bottom:6px;
    scrollbar-width:none;
}

.category-tabs::-webkit-scrollbar{
    display:none;
}

.category-btn{
    white-space:nowrap;
    flex-shrink:0;
        padding:7px 13px;
    font-size:13px;
    gap:5px;
}

.notification-card{
    padding:15px;
}

.notification-left{
    gap:12px;
}

.notification-left h6{
    font-size:15px;
}

.notification-left small{
    font-size:12px;
}

.modal{
    padding:15px;
}

.modal-dialog{
    display:flex;
    align-items:center;
    min-height:calc(100% - 30px);
    margin:15px auto;
    max-width:680px;
}

.modal-content{
    width:100%;
    max-height:88vh;
    border-radius:16px;
}

.modal-body{
    padding:20px;
}

.notification-info{
    padding:18px;
}
}

@media (max-width:576px){


 #hamburger{
        display:flex !important;
    }
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
/* RIGHT SIDE ICONS */
.navbar-actions{
    position:absolute;
    right:12px;
    top:50%;
    transform:translateY(-50%);
    display:flex;
    align-items:center;
    gap:10px;   /* dagdag space */
    z-index:10;
}

/* LEFT HAMBURGER */
#hamburger{
    position: relative;

}

/* CENTER LOGO */
.navbar-brand{
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    margin: 0;
}

/* Sidebar */

.sidebar{
    display:block;
    width:70px;
    transform:translateX(0);
    box-shadow:5px 0 15px rgba(0,0,0,.1);
}

.sidebar .nav-link{
    justify-content:center;
}

.sidebar .nav-link span{
    display:none;
}

.sidebar.expanded{
    width:270px;
}
.sidebar.expanded ~ #sidebarControls{
    left:270px;
}
.sidebar.expanded .nav-link{
    justify-content:flex-start;
}

.sidebar.expanded .nav-link span{
    display:inline;
}

.sidebar.hide-sidebar{
    transform:translateX(-100%);
}

#sidebarControls{
    display:flex;
    left:70px;
}

#closeBtn{
    display:flex;
}


.main-content{

    margin-left:70px;
    padding:95px 15px 20px;
    transition:.3s ease;

}

.notification-card{
    padding:14px;
}

.notification-left h6{
    font-size:14px;
}

.notification-left small{
    font-size:11px;
}


.modal{
    padding:12px;
}

.modal-dialog{
    display:flex;
    align-items:center;
    justify-content:center;

    min-height:calc(100% - 24px);

    margin:12px auto;
    max-width:100%;
}

.modal-content{
    width:100%;
    max-height:88vh;
    border-radius:15px;
}

.modal-header{
    padding:12px 15px;
}

.modal-title{
    font-size:16px;
}

.modal-body{
    padding:15px;
}


.notification-info{
    padding:12px;
}

.notification-info label{
    font-size:12px;
    margin-bottom:2px;
}

.notification-info p{
    font-size:14px;
    margin-bottom:12px;
}
.category-tabs{

    gap:8px;

}

.category-btn{

    padding:6px 12px;
    font-size:12px;
    gap:4px;

}
  main{
        margin-left:70px;
        width:calc(100% - 70px);
        transition:.3s ease;
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


    .back-arrow{
        display:none;
    }
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

</style>
<body>



<div class="sidebar" id="sidebar">

    <div class="nav flex-column">

        <a class="nav-link" href="barangay-secretary-home.php">
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

        <a class="nav-link" href="barangay-secretary-settings.php">
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

<nav class="navbar navbar-dark fixed-top">
    <div class="container-fluid">

        <!-- Left -->
        <button id="hamburger">
            <i class="bi bi-list"></i>
        </button>

     <a href="barangay-secretary-home.php" class="navbar-brand">
    <img src="assets/enviromanage-logo.png" alt="Logo">
</a>
        <!-- Right -->
        <div class="navbar-actions">

            <!-- Notification -->
            <a href="barangay-secretary-notification.php"
               class="nav-icon-btn position-relative text-decoration-none">

                <i class="bi bi-bell-fill"></i>

              

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
    <div class="row">

        <!-- ===========================
             Notification Page
        ============================ -->

      <main class="px-4 py-4">
    <!-- Page Header -->
<div class="d-flex align-items-center mb-4">

  <a href="barangay-secretary-home.php" 
   class="text-success me-3 fs-3 text-decoration-none back-arrow">
    <i class="bi bi-arrow-left"></i>
</a>

    <div>
        <h3 class="page-title mb-1">Notifications</h3>
        <p class="text-muted mb-0">
            View all recent notifications.
        </p>
    </div>

</div>

            <!-- Categories -->

            <div class="category-tabs mb-4">

                <button class="category-btn active"
                        data-category="all">

                    All

                </button>

                <button class="category-btn"
                        data-category="applications">
<i class="bi bi-person-check-fill">  </i>
                    Applications

                </button>

                <button class="category-btn"
                        data-category="complaints">
    <i class="bi bi-chat-left-text-fill">  </i>
                    Complaints    
                </button>

                <button class="category-btn"
                        data-category="announcements">
<i class="bi bi-megaphone-fill"> </i>
                 Announcements

                </button>

                <button class="category-btn"
                        data-category="system">
<i class="bi bi-gear-fill"> </i>
                  System

                </button>

            </div>

            <!-- Notification List -->

            <div class="notification-list">

                <!-- Notification 1 -->

          <div class="notification-card unread"
     data-category="applications"
     data-heading="Collection Request Details"
     data-reference="REQ-001"
     data-date="July 10, 2026 | 9:30 AM"
     data-status="Pending"
     data-value1="Juan Dela Cruz"
     data-value2="Poblacion"
     data-value3="Special Pickup"
     data-value4="Normal"
     data-description="A resident submitted a Special Pickup request for bulky household waste.">

    <div class="notification-left">

        <span class="status-dot"></span>

        <div>
            <h6>New Resident Account Request</h6>
            <small>10 minutes ago</small>
        </div>

    </div>

    <i class="bi bi-chevron-right"></i>

</div>

                <!-- Notification 2 -->

                <div class="notification-card unread"

                     data-category="complaints"

                 data-heading="Complaint Details"
data-reference="CMP-024"
data-date="July 10, 2026 | 8:45 AM"
data-status="Pending Review"

data-value1="Maria Santos"
data-value2="Sampaloc"
data-value3="Missed Collection"
data-value4="High"

data-description="The resident reported that garbage was not collected during the scheduled collection.">

<div class="notification-left">
                        <span class="status-dot"></span>

                        <div>

                            <h6>

                            New Resident Complaint

                            </h6>

                            <small>

                                35 minutes ago

                            </small>

                        </div>

                    </div>

                    <i class="bi bi-chevron-right"></i>

                </div>

                <!-- Notification 3 -->

                <div class="notification-card"

                     data-category="announcements"

                  data-heading="Announcement Details"
data-reference="ANN-015"
data-date="July 9, 2026"
data-status="Published"

data-value1="MENRO"
data-value2="All Residents"
data-value3="Collection Schedule"
data-value4="Public"

data-description="Waste collection schedule has been adjusted due to road maintenance.">

<div class="notification-left">
                        <span class="status-dot read"></span>

                        <div>

                            <h6>

                                New Announcement Published

                            </h6>

                            <small>

                                Yesterday

                            </small>

                        </div>

                    </div>

                    <i class="bi bi-chevron-right"></i>

                </div>

                <!-- Notification 4 -->

                <div class="notification-card"

                     data-category="system"

                  data-heading="System Notification"
data-reference="SYS-007"
data-date="July 8, 2026"
data-status="Scheduled"

data-value1="Maintenance"
data-value2="Resident Portal"
data-value3="10:00 PM - 12:00 AM"
data-value4="Scheduled"
data-description="EnviroManage will undergo scheduled maintenance.">

<div class="notification-left">
                        <span class="status-dot read"></span>

                        <div>

                            <h6>

                                System Maintenance Reminder

                            </h6>

                            <small>

                                2 days ago

                            </small>

                        </div>

                    </div>

                    <i class="bi bi-chevron-right"></i>

                </div>

            </div>

        </main>

    </div>

</div>


<!-- ==================================
     Notification Details Modal
=================================== -->

<div class="modal fade"

     id="notificationModal"

     tabindex="-1">

<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">

                    Notification Details

                </h5>

                <button class="btn-close"

                        data-bs-dismiss="modal">

                </button>

            </div>

       <div class="modal-body">

    <div class="notification-info">

        <div class="d-flex justify-content-between align-items-start mb-3">

            <div>
                <h5 id="modalHeading" class="fw-bold text-success mb-1">
                    Collection Request Details
                </h5>

                <small class="text-muted" id="modalSubtitle">
                    View complete notification information.
                </small>
            </div>

            <span class="badge bg-success px-3 py-2" id="modalStatus">
                Pending
            </span>

        </div>

        <hr>

      <div class="row g-2">

    <div class="col-6">
        <label>Reference No.</label>
        <p id="modalReference"></p>
    </div>

    <div class="col-6">
        <label>Date</label>
        <p id="modalDate"></p>
    </div>

    <div class="col-6">
        <label id="fieldOneLabel">Resident Name</label>
        <p id="fieldOneValue"></p>
    </div>

    <div class="col-6">
        <label id="fieldTwoLabel">Barangay</label>
        <p id="fieldTwoValue"></p>
    </div>

    <div class="col-6">
        <label id="fieldThreeLabel">Type</label>
        <p id="fieldThreeValue"></p>
    </div>

    <div class="col-6">
        <label id="fieldFourLabel">Priority</label>
        <p id="fieldFourValue"></p>
    </div>

</div>
        <hr>

        <label>Description</label>

        <p id="modalDescription" class="mb-0"></p>

    </div>

</div>

        <div class="modal-footer d-none"></div>

        </div>

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

<script >

// =====================================
// Bootstrap Modal
// =====================================

const notificationModal = new bootstrap.Modal(
    document.getElementById("notificationModal")
);

// =====================================
// Elements
// =====================================

const cards = document.querySelectorAll(".notification-card");
const categoryButtons = document.querySelectorAll(".category-btn");
const notificationCount = document.getElementById("notificationCount") || {
    textContent:"",
    style:{
        display:""
    }
};// Modal Fields

const modalDate = document.getElementById("modalDate");
const modalDescription = document.getElementById("modalDescription");
const modalReference = document.getElementById("modalReference");
const modalStatus = document.getElementById("modalStatus");
const modalHeading=document.getElementById("modalHeading");

const fieldOneLabel=document.getElementById("fieldOneLabel");
const fieldTwoLabel=document.getElementById("fieldTwoLabel");
const fieldThreeLabel=document.getElementById("fieldThreeLabel");
const fieldFourLabel=document.getElementById("fieldFourLabel");

const fieldOneValue=document.getElementById("fieldOneValue");
const fieldTwoValue=document.getElementById("fieldTwoValue");
const fieldThreeValue=document.getElementById("fieldThreeValue");
const fieldFourValue=document.getElementById("fieldFourValue");

const sidebar = document.getElementById("sidebar");
const toggleBtn = document.getElementById("toggleBtn");
const closeBtn = document.getElementById("closeBtn");
const hamburger = document.getElementById("hamburger");
const sidebarControls = document.getElementById("sidebarControls");
function isTabletOrMobile(){
    return window.innerWidth <= 576;
}

toggleBtn.addEventListener("click", ()=>{

    if(!isTabletOrMobile()) return;

    const icon = toggleBtn.querySelector("i");

    sidebar.classList.toggle("expanded");

    if(sidebar.classList.contains("expanded")){

        icon.classList.remove("bi-chevron-right");
        icon.classList.add("bi-chevron-left");

    }else{

        icon.classList.remove("bi-chevron-left");
        icon.classList.add("bi-chevron-right");

    }

});
closeBtn.addEventListener("click", ()=>{

    if(!isTabletOrMobile()) return;

    sidebar.classList.add("hide-sidebar");
    sidebar.classList.remove("expanded");
const mainContent = document.querySelector("main");

mainContent.style.marginLeft = "0";
mainContent.style.width = "100%";
    sidebarControls.style.display = "none";

    const icon = toggleBtn.querySelector("i");

    icon.classList.remove("bi-chevron-left");
    icon.classList.add("bi-chevron-right");

});

hamburger.addEventListener("click", ()=>{

    if(!isTabletOrMobile()) return;

    sidebar.classList.remove("hide-sidebar");
    sidebar.classList.remove("expanded");

    sidebarControls.style.display = "flex";

    const icon = toggleBtn.querySelector("i");

    icon.classList.remove("bi-chevron-left");
    icon.classList.add("bi-chevron-right");

});
window.addEventListener("resize", ()=>{

    if(window.innerWidth > 576){

        sidebar.classList.remove("expanded");
        sidebar.classList.remove("show");

        sidebarControls.style.display = "none";

    }

});

let currentCategory = "";
let currentReference = "";

// =====================================
// Update Unread Count
// =====================================

function updateNotificationCount() {

    const unread = document.querySelectorAll(".notification-card.unread").length;

    notificationCount.textContent = unread;

    if (unread === 0) {

        notificationCount.style.display = "none";

    } else {

        notificationCount.style.display = "inline-block";

    }

}

updateNotificationCount();

// =====================================
// Open Notification
// =====================================

cards.forEach(card => {

    card.addEventListener("click", function () {

        currentCategory = this.dataset.category;
        currentReference = this.dataset.reference;

        modalHeading.textContent = this.dataset.heading;

     fieldOneValue.textContent = this.dataset.value1;
fieldTwoValue.textContent = this.dataset.value2;
fieldThreeValue.textContent = this.dataset.value3;
fieldFourValue.textContent = this.dataset.value4;

        modalDate.textContent = this.dataset.date;
        modalDescription.textContent = this.dataset.description;
        modalReference.textContent = this.dataset.reference;
      modalStatus.textContent=this.dataset.status;

modalStatus.className="badge rounded-pill px-3 py-2 fs-6";

switch(this.dataset.status){

case "Pending":
modalStatus.classList.add("bg-warning","text-dark");
break;

case "Resolved":
modalStatus.classList.add("bg-success");
break;

case "Published":
modalStatus.classList.add("bg-primary");
break;

case "Scheduled":
modalStatus.classList.add("bg-secondary");
break;

default:
modalStatus.classList.add("bg-success");

}
        // Mark as Read

        this.classList.remove("unread");

        const dot = this.querySelector(".status-dot");

        if (dot) {

            dot.classList.add("read");

        }

        updateNotificationCount();
switch(this.dataset.category){

case "applications":

fieldOneLabel.textContent="Resident Name";
fieldTwoLabel.textContent="Barangay";
fieldThreeLabel.textContent="Collection Type";
fieldFourLabel.textContent="Priority";

break;

case "complaints":

fieldOneLabel.textContent="Resident Name";
fieldTwoLabel.textContent="Barangay";
fieldThreeLabel.textContent="Complaint Type";
fieldFourLabel.textContent="Priority";

break;

case "announcements":

fieldOneLabel.textContent="Published By";
fieldTwoLabel.textContent="Target Audience";
fieldThreeLabel.textContent="Announcement";
fieldFourLabel.textContent="Visibility";

break;

case "system":

fieldOneLabel.textContent="Type";
fieldTwoLabel.textContent="Affected Module";
fieldThreeLabel.textContent="Maintenance";
fieldFourLabel.textContent="Status";

break;

}
        notificationModal.show();

    });

});

// =====================================
// Category Filter
// =====================================

categoryButtons.forEach(button => {

    button.addEventListener("click", function () {

        categoryButtons.forEach(btn =>
            btn.classList.remove("active")
        );

        this.classList.add("active");

        const category = this.dataset.category;

        cards.forEach(card => {

            if (
                category === "all" ||
                card.dataset.category === category
            ) {

                card.style.display = "flex";

            } else {

                card.style.display = "none";

            }

        });

    });

});
document.getElementById("confirmLogout").addEventListener("click", function () {
    window.location.href = "login.php";
});
</script>

</body>