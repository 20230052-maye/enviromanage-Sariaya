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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Announcements</title>

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

.navbar .container-fluid{

    display:flex;
    align-items:center;
    justify-content:space-between;

}

.navbar-brand img{

    width:45px;
    height:45px;
    object-fit:contain;

}

.navbar-actions{

    display:flex;
    align-items:center;
    gap:10px;

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

}

.notification-badge{

    position:absolute;

    top:4px;
    right:2px;

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

#hamburger{

    display:none;

    width:40px;
    height:40px;

    border:none;
    background:transparent;

    color:#fff;

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


/* ===========================
   SIDEBAR BUTTONS
=========================== */

#sidebarControls{

    position:fixed;

    top:85px;
    left:270px;

    display:none;

    flex-direction:column;
    gap:8px;

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

    color:#fff;

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

/* ===========================
   COLLAPSED SIDEBAR
=========================== */

.sidebar.collapsed{

    width:70px;

}

.sidebar.collapsed .nav-link{

    justify-content:center;
    padding:12px 10px;

}

.sidebar.collapsed .nav-link span{

    display:none;

}

.sidebar.collapsed~#sidebarControls{

    left:70px;

}

.sidebar.collapsed~.main-content{

    margin-left:70px;

}

/* ===========================
   MAIN CONTENT
=========================== */

.main-content{
    margin-left:270px;
    padding:85px 25px 30px;
    transition:.3s ease;
    min-width:0;
}

/* ===========================
   PAGE HEADER
=========================== */

.main-content h2{

    color:#1b5e20;
    font-weight:700;

}

.main-content p{

    color:#6c757d;

}

/* ===========================
   SUMMARY CARDS
=========================== */

.summary-card{

    background:linear-gradient(135deg,#43a047,#66bb6a);

    color:#fff;

    border-radius:20px;

    padding:25px;

    display:flex;
    justify-content:space-between;
    align-items:center;

    box-shadow:0 12px 25px rgba(0,0,0,.08);

    transition:.3s;

}

.summary-card.green{

    background:linear-gradient(135deg,#2e7d32,#43a047);

}

.summary-card:hover{

    transform:translateY(-5px);

}

.summary-card h2{

    font-size:38px;
    font-weight:700;

}

.summary-card span{

    opacity:.9;

}

.summary-card i{

    font-size:55px;
    opacity:.25;

}

/* ===========================
   SEARCH CARD
=========================== */

.search-card{

    border:none;

    border-radius:18px;

    box-shadow:0 6px 15px rgba(0,0,0,.05);

}

.search-card .card-body{

    padding:25px;

}

.form-control{

    height:50px;
    border-radius:12px;

}

.form-control:focus{

    border-color:#2e7d32;

    box-shadow:0 0 0 .15rem rgba(46,125,50,.2);

}
/* ===========================
   ANNOUNCEMENT TABLE CARD
=========================== */

.table-card{

    border:none;
    border-radius:18px;
    overflow:hidden;

    box-shadow:0 8px 18px rgba(0,0,0,.05);

}

.table-card .card-header{

    background:#fff;

    padding:20px 25px;

    border-bottom:1px solid #eee;

}



.table-card h5{
font-size:16px;
    margin:0;

    color:#1b5e20;

    font-weight:600;

}



.table{

    margin:0;

}



.table thead{

    background:#f1f8f4;

}

/* ===========================
   ANNOUNCEMENT CARD
=========================== */

.announcement-card{

    background:#fff;

    border:1px solid #e9ecef;

    border-radius:16px;

    padding:22px;

    margin-bottom:18px;

    display:grid;

    grid-template-columns:1fr auto;

    gap:20px;

    align-items:flex-start;

    transition:.3s;

}

.announcement-card:hover{

    transform:translateY(-3px);

    box-shadow:0 8px 20px rgba(0,0,0,.08);

}

.announcement-title{

    margin-bottom:6px;

    color:#1b5e20;

    font-size:20px;

    font-weight:600;

}

.announcement-date{

    margin:0;

    color:#6c757d;

    font-size:14px;

}

.announcement-content{

    grid-column:1 / -1;

    margin-top:18px;

    padding:18px;

    border-radius:12px;

    background:#f8fbf8;

    border-left:5px solid #2e7d32;

    color:#555;

    white-space:pre-line;

    line-height:1.8;

}

/* ===========================
   BUTTONS
=========================== */

.btn{

    border-radius:10px;

    font-weight:500;

}

.btn-success{

    background:#2e7d32;

    border:none;

}

.btn-success:hover{

    background:#1b5e20;

}

.btn-primary{

    background:#388e3c;

    border:none;

}

.btn-primary:hover{

    background:#2e7d32;

}

.btn-danger{

    border:none;

}


/* ===========================
   MODALS
=========================== */

.modal{

    z-index:2000;

}

.modal-content{

    border:none;

    border-radius:20px;

    overflow:hidden;

}

.modal-header{

    background:linear-gradient(135deg,#2e7d32,#43a047);

    color:#fff;

    border:none;

    padding:20px 30px;

}

.modal-header .btn-close{

    filter:brightness(0) invert(1);

}

.modal-body{

    padding:30px;

}

.modal-footer{

    border:none;

    padding:20px 30px;

    gap:10px;

}

textarea.form-control{

    min-height:220px;

    resize:none;

}

textarea.form-control:focus{

    border-color:#2e7d32;

    box-shadow:0 0 0 .15rem rgba(46,125,50,.2);

}

/* ===========================
   CONFIRM MODAL
=========================== */

.confirm-modal{

    max-width:520px;

}

.confirm-modal .modal-content{

    border-radius:20px;

}

.confirm-modal .modal-body{

    color:#555;

    line-height:1.7;

}
/* ===========================
   SCROLLBAR
=========================== */

::-webkit-scrollbar{

    width:8px;

}


::-webkit-scrollbar-thumb{

    background:#b0b0b0;
    border-radius:20px;

}


::-webkit-scrollbar-thumb:hover{

    background:#8a8a8a;

}


::-webkit-scrollbar-track{

    background:#f5f5f5;

}
/* ===========================
   TABLET
=========================== */
@media(max-width:992px){

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

#hamburger{
    display:flex;
}

/* DEFAULT SIDEBAR */
.sidebar{
    width:70px;
}

.sidebar .nav-link{
    justify-content:center;
    padding:12px 10px;
}

.sidebar .nav-link span{
    display:none;
}

/* BUTTON POSITION */
#sidebarControls{
    display:flex;
    left:70px;
}

/* EXPANDED */
.sidebar.expanded{
    width:270px;
     box-shadow:8px 0 20px rgba(0,0,0,.15);
    z-index:1200;
}

.sidebar.expanded .nav-link{
    justify-content:flex-start;
}

.sidebar.expanded .nav-link span{
    display:inline;
}

.sidebar.expanded ~ #sidebarControls{
    left:270px;
}

.sidebar.expanded ~ #sidebarControls #toggleBtn{
    display:flex;
}

.sidebar.expanded ~ #sidebarControls #closeBtn{
    display:flex;
}

/* MAIN CONTENT */
.main-content{
    margin-left:70px;
    padding:85px 15px 20px;
}

.sidebar.expanded ~ .main-content{
    margin-left:70px;
}

/* HIDE SIDEBAR */
.sidebar.hide-sidebar{
    transform:translateX(-100%);
}
.sidebar.hide-sidebar ~ .main-content{

    margin-left:0;

}

.sidebar.hide-sidebar ~ #sidebarControls{
    left:0;
    display:none;
}

/* COLLAPSED */
.sidebar:not(.expanded):not(.hide-sidebar){
    width:70px;
}

.sidebar:not(.expanded):not(.hide-sidebar) .nav-link{
    justify-content:center;
}

.sidebar:not(.expanded):not(.hide-sidebar) .nav-link span{
    display:none;
}

}
/* ===========================
   MOBILE
=========================== */
@media(max-width:576px){

 .navbar{

        padding:0 15px;

    }
#announcementInfo{
    width:100%;
    text-align:center;
    margin-bottom:10px;
}

#announcementPagination{
    width:100%;
    display:flex;
    justify-content:center;
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

.main-content{

    margin-left:70px;
    padding:95px 15px 20px;
    transition:.3s ease;

}
.sidebar.hide-sidebar ~ .main-content{

    margin-left:0;

}
.sidebar{
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

.sidebar.expanded .nav-link{
    justify-content:flex-start;
}

.sidebar.expanded .nav-link span{
    display:inline;
}

.sidebar.hide-sidebar{
    transform:translateX(-100%);
}

.announcement-card{
    grid-template-columns:1fr;
}

.announcement-card>div:last-child{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
}

.announcement-card .btn{
    margin-left:0;
}/* MOBILE VIEW ANNOUNCEMENT MODAL */
#viewAnnouncementModal .modal-dialog{

    width:90%;
    max-width:none;

    margin:45px auto 15px;

}

#viewAnnouncementModal .modal-content{

    border-radius:15px;

}

#viewAnnouncementModal .modal-header{

    padding:15px 18px;

}

#viewAnnouncementModal .modal-header h4{

    font-size:18px;

}

#viewAnnouncementModal .modal-body{

    padding:18px;

    font-size:14px;

    max-height:65vh;

    overflow-y:auto;

}

#viewAnnouncementModal #viewDate{

    font-size:14px;

}

#viewAnnouncementModal #viewContent{

    font-size:14px;

    line-height:1.7;

}

/* SUMMARY CARDS - 2 PER ROW */
.row.g-4{
    --bs-gutter-x:12px;
    --bs-gutter-y:12px;
}

.row.g-4 > .col-lg-6{
    width:50%;
    flex:0 0 50%;
}
/* FIX SUMMARY CARD ICON WHEN SIDEBAR IS VISIBLE */
.summary-card{
    overflow:hidden;
    position:relative;
}

.summary-card i{
    font-size:30px;
    opacity:.22;
    margin-left:6px;
    margin-right:4px;   /* ilipat pakaliwa */
    flex-shrink:0;
}

.summary-card > div{
    flex:1;
    min-width:0;
    padding-right:8px;
}

.summary-card h6{
    font-size:13px;
    margin-bottom:8px;
}

.summary-card h2{
    font-size:34px;
    margin-bottom:8px;
    line-height:1;
}

.summary-card span{
    font-size:11px;
    display:block;
}

.summary-card i{
    font-size:34px;
    opacity:.25;
    margin-left:10px;
    align-self:center;
}
.announcement-title{
    font-size:16px;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
    max-width:100%;
}
.announcement-card{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:10px;
    padding:14px;
}

.announcement-card > div:first-child{
    flex:1;
    min-width:0;
}
.announcement-card > div:last-child{
    display:flex;
    flex-wrap:nowrap;
    gap:6px;
    align-items:center;
    flex-shrink:0;
}

.announcement-card .btn{
    padding:.28rem .55rem;
    font-size:12px;
    white-space:nowrap;
}
.table-card{
    overflow:hidden;
}

.card-body{
    overflow-x:hidden;
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
/* MOBILE DELETE CONFIRM MODAL */
#deleteAnnouncementModal .modal-dialog{
    width:320px;
    max-width:90%;
    margin:auto;
}

#deleteAnnouncementModal .modal-content{
    border-radius:14px;
}

#deleteAnnouncementModal .modal-body{
    padding:20px 18px 10px;
    text-align:center;
    font-size:14px;
    color:#555;
}

#deleteAnnouncementModal .modal-footer{
    padding:10px 18px 18px;
    justify-content:center;
    gap:10px;
}

#deleteAnnouncementModal .btn{
    flex:1;
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
   VIEW MODAL CONTENT
=========================== */

#viewTitle{

    color:#fff;

    font-weight:600;

}

#viewDate{

    font-weight:500;

    color:#2e7d32;

}

#viewContent{

    font-size:15px;

    color:#555;

    line-height:1.9;
text-align:center;
}
#viewDate{
    font-weight:500;
    color:#2e7d32;
    text-align:center;
}
@media (min-width:768px){

.search-card .btn{
    width:auto !important;
    min-width:210px;
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
/* ===========================
   SWEETALERT2 ABOVE SIDEBAR
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
/* ===========================
   DELETE MODAL OUTLINE BUTTONS
=========================== */

#deleteAnnouncementModal .btn-secondary{
    background:transparent !important;
    border:2px solid #6c757d !important;
    color:#6c757d !important;
}

#deleteAnnouncementModal .btn-secondary:hover{
    background:#6c757d !important;
    color:#fff !important;
}

#deleteAnnouncementModal .btn-danger{
    background:transparent !important;
    border:2px solid #dc3545 !important;
    color:#dc3545 !important;
}

#deleteAnnouncementModal .btn-danger:hover{
    background:#dc3545 !important;
    color:#fff !important;
}


#announcementPagination .page-item.active .page-link{
    background:#1e5631;
    border-color:#1e5631;
    color:#fff;
}


#announcementInfo{
    font-size:14px;
    color:#6c757d;
}

#announcementPagination .pagination{
    margin-bottom:0;
}



#announcementPagination .page-item.active .page-link{
    background:#1e5631;
    border-color:#1e5631;
    color:#fff;
}

#viewDate{
    color:#2e7d32;
    font-size:14px;
    font-weight:500;
}


#announcementImages img{

    width:100%;
    max-height:350px;
    object-fit:contain;
    border-radius:15px;

}


#announcementCarousel{

    background:#f8f9fa;
    border-radius:15px;
    padding:10px;

}


#viewContent{

    white-space:pre-line;
    line-height:1.8;
    text-align:center;
    color:#555;

}


/* kapag walang image */
#announcementImageContainer:empty{

    display:none;

}
/* BLACK CAROUSEL ARROWS */
#announcementCarousel .carousel-control-prev-icon,
#announcementCarousel .carousel-control-next-icon{
    filter: invert(1);
}
/* SEARCH INSIDE HEADER */

.table-card{
    margin-top:15px;
}
.table-card .card-header{
    padding:18px 22px;
}

.table-card .card-header > div{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:16px;
}

.search-wrapper{
    width:380px;
}

.search-wrapper .form-control{
    height:45px;
    border-radius:10px;
}

@media (max-width:768px){

    .table-card .card-header > div{
        flex-direction:column;
        align-items:stretch;
    }

    .search-wrapper{
        width:100%;
    }

}
/* ===========================
   ANNOUNCEMENT HEADER
=========================== */

.table-card .card-header h5{
    font-size:22px;
    font-weight:700;
    color:#1b5e20;
    margin:0;
}

/* MOBILE */
@media (max-width:768px){

    .table-card .card-header > div{
        flex-direction:column;
        align-items:flex-start;   /* hindi naka-center */
        gap:12px;
    }

    .table-card .card-header h5{
        width:100%;
        text-align:left;          /* left align */
        font-size:20px;
    }

    .search-wrapper{
        width:100%;
    }

}
</style>
</head>

<body>

<!-- ===========================
     NAVBAR
=========================== -->

<nav class="navbar navbar-dark fixed-top">

<div class="container-fluid">

    <button id="hamburger">
        <i class="bi bi-list"></i>
    </button>

    <a class="navbar-brand">
        <img src="assets/enviromanage-logo.png" alt="Logo">
    </a>

    <div class="navbar-actions">

     <a href="barangay-secretary-notification.php"
   class="text-decoration-none">

    <button class="nav-icon-btn position-relative">

        <i class="bi bi-bell-fill"></i>

     

    </button>

</a>

    <div class="dropdown"> <button class="nav-icon-btn dropdown-toggle" data-bs-toggle="dropdown"> <i class="bi bi-person-circle"></i> </button> <ul class="dropdown-menu dropdown-menu-end shadow"> <li> <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"> <i class="bi bi-box-arrow-right me-2"></i> Logout </a> </li>

            </ul>

        </div>

    </div>

</div>

</nav>

<!-- ===========================
     SIDEBAR
=========================== -->

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

<a class="nav-link active" href="barangay-secretary-announcements.php">
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

<!-- ===========================
     MAIN CONTENT
=========================== -->

<main class="main-content">

<div class="card table-card mt-3">

    <div class="card-header">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <h5 class="mb-0">
                Announcements
            </h5>

            <div class="search-wrapper">
                <input
                    type="text"
                    class="form-control"
                    id="searchAnnouncement"
                    placeholder="Search announcement title...">
            </div>

        </div>

    </div>

    <div class="card-body">

        <div id="announcementContainer"></div>
        <div id="announcementData" style="display:none;"></div>

<div id="noAnnouncement" class="text-center text-muted py-4" style="display:none;">
    <i class="bi bi-megaphone fs-3 d-block mb-2"></i>
    No announcement found.
</div>

<div class="d-flex justify-content-between align-items-center flex-wrap mt-4">
    <div id="announcementInfo" class="text-muted mb-2">
        Showing 0 to 0 of 0 entries
    </div>

    <div id="announcementPagination"></div>

</div>

<!-- ===========================
     VIEW ANNOUNCEMENT MODAL
=========================== -->

<div class="modal fade" id="viewAnnouncementModal" tabindex="-1">

<div class="modal-dialog modal-lg modal-dialog-centered">

<div class="modal-content">

<div class="modal-header">

<h4 id="viewTitle">

Announcement

</h4>

<button
class="btn-close"
data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<div class="d-flex justify-content-end mb-3">

    <span id="viewDate" class="text-muted"></span>

</div>


<div id="announcementImageContainer"
class="mb-3"
style="display:none;">


<div id="announcementCarousel"
class="carousel slide"
data-bs-ride="false">


<div class="carousel-inner"
id="announcementImages">


</div>


<button class="carousel-control-prev"
type="button"
data-bs-target="#announcementCarousel"
data-bs-slide="prev">

<span class="carousel-control-prev-icon"></span>

</button>


<button class="carousel-control-next"
type="button"
data-bs-target="#announcementCarousel"
data-bs-slide="next">

<span class="carousel-control-next-icon"></span>

</button>


</div>


</div>



<hr>


<div id="viewContent">

</div>


</div>



</div>

</div>

</div>

<!-- ===========================
     DELETE CONFIRMATION
=========================== -->

<div class="modal fade" id="deleteAnnouncementModal">

<div class="modal-dialog modal-dialog-centered confirm-modal">

<div class="modal-content">

<div class="modal-header">

<h5>

Delete Announcement

</h5>

</div>

<div class="modal-body">

Are you sure you want to delete this announcement?

</div>

<div class="modal-footer">

<button
class="btn btn-secondary"
data-bs-dismiss="modal">

Cancel

</button>

<button
class="btn btn-danger"
id="confirmDelete">

Delete

</button>

</div>

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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>


// ======================================
// BOOTSTRAP MODALS
// ======================================

const viewModal =
new bootstrap.Modal(
document.getElementById("viewAnnouncementModal")
);

const deleteModal =
new bootstrap.Modal(
document.getElementById("deleteAnnouncementModal")
);


// ======================================
// CURRENT ANNOUNCEMENT
// ======================================

let currentCard = null;
// ======================================
// SEARCH + PAGINATION
// ======================================

const rowsPerPage = 10;

let currentPage = 1;
const start = (currentPage - 1) * rowsPerPage;
const end = start + rowsPerPage;
function escapeHtml(text){

    const div = document.createElement("div");

    div.textContent = text;

    return div.innerHTML;

}

function formatDate(date){

    let d = new Date(date);

    return d.toLocaleString("en-US",{
        year:"numeric",
        month:"long",
        day:"numeric",
        hour:"numeric",
        minute:"2-digit"
    })
    .replace(" at ", " | ");

}

async function loadAnnouncements() {

    const search = document.getElementById("searchAnnouncement").value.trim();

    const response = await fetch(
        `barangay-secretary-announcement-search.php?page=${currentPage}&search=${encodeURIComponent(search)}`
    );

    const data = await response.json();

    if (!data.success) return;

  const container = document.getElementById("announcementContainer");
const noAnnouncement = document.getElementById("noAnnouncement");

container.innerHTML = "";


if(data.rows.length === 0){

    const search = document
        .getElementById("searchAnnouncement")
        .value
        .trim();

    if(search !== ""){

        container.innerHTML = `
            <div class="text-center py-4">
                <i class="bi bi-search fs-3 d-block mb-2 text-muted"></i>

                <h6 class="fw-semibold mb-1">
                    No announcement found for "${escapeHtml(search)}".
                </h6>

                <p class="text-muted mb-0">
                    Try changing your search.
                </p>
            </div>
        `;

    }else{

        container.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="bi bi-megaphone fs-3 d-block mb-2"></i>
                No announcement available.
            </div>
        `;

    }


    document.getElementById("announcementInfo").textContent =
        "Showing 0 to 0 of 0 entries";

    document.getElementById("announcementPagination").innerHTML="";

    return;

}


data.rows.forEach(row => {
        container.innerHTML += `
        <div class="announcement-card"
data-id="${row.id}"
data-images='${JSON.stringify(row.announcement_images || [])}'>
                <div>

                    <h5 class="announcement-title">
                        ${escapeHtml(row.title)}
                    </h5>

                    <p class="announcement-date">
                        ${formatDate(row.created_at)}
                    </p>

                </div>

               <div class="d-flex gap-2">

    <button class="btn btn-success btn-sm viewBtn">
        View
    </button>

    <button class="btn btn-danger btn-sm deleteBtn">
        Delete
    </button>

</div>

                <div class="announcement-content d-none">
                    ${escapeHtml(row.message)}
                </div>

            </div>
        `;

    });

    refreshButtons();

    renderAnnouncementPagination(
        Math.ceil(data.total / data.limit)
    );

    const start = data.total === 0 ? 0 : ((currentPage - 1) * data.limit) + 1;

    const end = Math.min(
        currentPage * data.limit,
        data.total
    );

    document.getElementById("announcementInfo").textContent =
        `Showing ${start} to ${end} of ${data.total} entries`;

    document.getElementById("noAnnouncement").style.display =
        data.total === 0 ? "block" : "none";

}
function renderAnnouncementPagination(totalPages){

    const pagination =
        document.getElementById("announcementPagination");


    pagination.innerHTML = "";


    if(totalPages <= 1){

        if(totalPages === 1){

            pagination.innerHTML = `
                <div class="d-flex justify-content-center align-items-center gap-2">

                    <button class="btn btn-outline-success btn-sm" disabled>
                        Previous
                    </button>


                    <span class="fw-semibold">
                        Page 1 of 1
                    </span>


                    <button class="btn btn-outline-success btn-sm" disabled>
                        Next
                    </button>

                </div>
            `;

        }

        return;

    }



    pagination.innerHTML = `

    <div class="d-flex justify-content-center align-items-center gap-2">


        <button 
            class="btn btn-outline-success btn-sm"
            id="prevAnnouncementPage"
            ${currentPage === 1 ? "disabled" : ""}>
            Previous
        </button>



        <span class="fw-semibold">
            Page ${currentPage} of ${totalPages}
        </span>



        <button 
            class="btn btn-outline-success btn-sm"
            id="nextAnnouncementPage"
            ${currentPage === totalPages ? "disabled" : ""}>
            Next
        </button>


    </div>

    `;



    document
    .getElementById("prevAnnouncementPage")
    .onclick = function(){

        if(currentPage > 1){

            currentPage--;

            loadAnnouncements();

        }

    };



    document
    .getElementById("nextAnnouncementPage")
    .onclick = function(){

        if(currentPage < totalPages){

            currentPage++;

            loadAnnouncements();

        }

    };


}

let searchTimer;

document
.getElementById("searchAnnouncement")
.addEventListener("input", function(){

    clearTimeout(searchTimer);

    searchTimer = setTimeout(()=>{

        currentPage = 1;

        loadAnnouncements();

    },400);

});

// ======================================
// VIEW BUTTON
// ======================================

function attachViewButtons(){

document.querySelectorAll(".viewBtn").forEach(btn=>{

btn.onclick=function(){

currentCard=this.closest(".announcement-card");

const title=
currentCard.querySelector(".announcement-title").textContent;

const date=
currentCard.querySelector(".announcement-date").textContent;

const content =
currentCard.querySelector(".announcement-content").textContent;


const images =
JSON.parse(
currentCard.dataset.images || "[]"
);



document.getElementById("viewTitle").textContent = title;

document.getElementById("viewDate").textContent = date;

document.getElementById("viewContent").textContent = content;



const imageContainer =
document.getElementById("announcementImageContainer");

const imageWrapper =
document.getElementById("announcementImages");


imageWrapper.innerHTML="";


if(images.length > 0){


    imageContainer.style.display="block";


  images.forEach((img,index)=>{


imageWrapper.innerHTML += `

<div class="carousel-item ${index===0 ? 'active':''}">

<img src="${img}">

</div>

`;

});

    // hide arrows if only one image
    document.querySelectorAll(
    "#announcementCarousel .carousel-control-prev, #announcementCarousel .carousel-control-next"
    )
    .forEach(btn=>{

        btn.style.display =
        images.length > 1 ? "flex" : "none";

    });



}else{


    imageContainer.style.display="none";


}


viewModal.show();

};

});

}


// ======================================
// DELETE BUTTON
// ======================================

function attachDeleteButtons(){

document.querySelectorAll(".deleteBtn").forEach(btn=>{

btn.onclick=function(){

currentCard=this.closest(".announcement-card");

deleteModal.show();

};

});

}

attachViewButtons();

attachDeleteButtons();


// ======================================
// DELETE ANNOUNCEMENT
// ======================================

document
.getElementById("confirmDelete")
.addEventListener("click", async () => {

    if (!currentCard) return;

    const id = currentCard.dataset.id;

    const form = new FormData();
    form.append("id", id);

try {

    const response = await fetch(
        "barangay-secretary-delete-announcement.php",
        {
            method: "POST",
            body: form
        }
    );

    const text = await response.text();

    console.log(text);

    const result = JSON.parse(text);

    deleteModal.hide();

    if(result.success){

      Swal.fire({
    icon:"success",
    title:"Deleted Successfully",
    text:"The announcement has been removed.",
    confirmButtonColor:"#2e7d32",
    timer:3000,
    showConfirmButton:false
});

        loadAnnouncements();

    }else{

      Swal.fire({
    icon:"error",
    title:"Delete Failed",
    text:result.message,
    timer:3000,
    showConfirmButton:false
});

    }

}catch(err){

    console.error(err);

    Swal.fire({
        icon:"error",
        title:"Server Error",
        text:err.message
    });

}

});

// ======================================
// SIDEBAR
// ======================================

const sidebar = document.getElementById("sidebar");
const toggleBtn = document.getElementById("toggleBtn");
const closeBtn = document.getElementById("closeBtn");
const hamburger = document.getElementById("hamburger");

function isTabletOrMobile() {
    return window.innerWidth <= 992;
}


// ======================================
// TOGGLE SIDEBAR
// ======================================

toggleBtn.addEventListener("click", () => {

    if (!isTabletOrMobile()) return;

    const icon = toggleBtn.querySelector("i");

    sidebar.classList.toggle("expanded");

    if (sidebar.classList.contains("expanded")) {

        icon.classList.remove("bi-chevron-right");
        icon.classList.add("bi-chevron-left");

    } else {

        icon.classList.remove("bi-chevron-left");
        icon.classList.add("bi-chevron-right");

    }

});


// ======================================
// CLOSE SIDEBAR
// ======================================

closeBtn.addEventListener("click", () => {

    if (!isTabletOrMobile()) return;

    sidebar.classList.add("hide-sidebar");
    sidebar.classList.remove("expanded");

    const icon = toggleBtn.querySelector("i");

    icon.classList.remove("bi-chevron-left");
    icon.classList.add("bi-chevron-right");

});


// ======================================
// HAMBURGER
// ======================================

hamburger.addEventListener("click", () => {

    if (!isTabletOrMobile()) return;

    sidebar.classList.remove("hide-sidebar");
    sidebar.classList.remove("expanded");

    const icon = toggleBtn.querySelector("i");

    icon.classList.remove("bi-chevron-left");
    icon.classList.add("bi-chevron-right");

});


// ======================================
// RESET WHEN DESKTOP
// ======================================

window.addEventListener("resize", () => {

    if (window.innerWidth > 992) {

        sidebar.classList.remove("expanded");
        sidebar.classList.remove("hide-sidebar");

    }

});


// ======================================
// CARD HOVER EFFECT
// ======================================

document.querySelectorAll(".summary-card").forEach(card => {

    card.addEventListener("mouseenter", () => {

        card.style.transform = "translateY(-5px)";

    });

    card.addEventListener("mouseleave", () => {

        card.style.transform = "translateY(0)";

    });

});


// ======================================
// REATTACH EVENTS
// ======================================

function refreshButtons() {

    attachViewButtons();
  
    attachDeleteButtons();

}

// ======================================
// SORT ANNOUNCEMENTS (LATEST FIRST)
// ======================================
function sortAnnouncements() {

    const data =
        document.getElementById("announcementData");

    const cards = [
        ...data.querySelectorAll(".announcement-card")
    ];

    cards.sort((a, b) => {

        const dateA = new Date(
            a.querySelector(".announcement-date")
             .textContent.replace(" • ", " ")
        );

        const dateB = new Date(
            b.querySelector(".announcement-date")
             .textContent.replace(" • ", " ")
        );

        return dateB - dateA;

    });

    cards.forEach(card => data.appendChild(card));

}
document.getElementById("confirmLogout").addEventListener("click", function () {
    window.location.href = "login.php";
});
// ======================================
// INITIALIZE
// ======================================

sortAnnouncements();
refreshButtons();
loadAnnouncements();
</script>