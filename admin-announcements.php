<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
date_default_timezone_set('Asia/Manila');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EnviroManage Admin - Announcements</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; padding-top: 70px; }
.navbar{
    background-color:#1e5631 !important;
    min-height:70px;
}

.navbar .container-fluid{
    height:70px;
    display:flex;
    align-items:center;
}

.navbar-brand img{
    border-radius:5px;
}

.navbar-nav{
    display:flex;
    flex-direction:row;
    align-items:center;
    justify-content:center;
    height:70px;
    margin-bottom:0;
    margin-left:auto;
    gap:0;
}

.navbar-nav .nav-item{
    display:flex;
    align-items:center;
    justify-content:center;
    height:70px;
    position:relative;
}

.navbar-nav .nav-link{
    display:flex;
    align-items:center;
    justify-content:center;
    padding:0 .5rem !important;
    line-height:1;
}

.navbar-nav > .nav-item > .nav-link > i{
    display:block;
    line-height:1;
}

.dropdown-toggle::after{
    display:none;
}

.dropdown-menu{
    margin-top:8px !important;
    right:0;
    left:auto !important;
}

.nav-item.dropdown{
    position:relative;
}

.nav-item.dropdown .dropdown-menu{
    position:absolute !important;
    top:100% !important;
    right:0 !important;
    left:auto !important;
    margin-top:8px !important;
    transform:none !important;
}

/* SIDEBAR */
.sidebar{
    position:fixed;
    top:70px;
    left:0;
    width:70px;
    height:100%;
    background:#fff;
    border-right:1px solid #dee2e6;
    padding-top:15px;
    overflow-y:auto;
    transition:width .3s ease, transform .3s ease;
    z-index:1000;
}

.sidebar.expanded{width:220px;}
.sidebar.hidden{transform:translateX(-100%);}

.sidebar .nav-link{
    color:#495057;
    padding:10px;
    display:flex;
    align-items:center;
    gap:10px;
    justify-content:center;
}

.sidebar .nav-link span{display:none;}
.sidebar.expanded .nav-link{justify-content:flex-start;}
.sidebar.expanded .nav-link span{display:inline;}
.sidebar .nav-link:hover,
.sidebar .nav-link.active{
    background:#1e5631;
    color:#fff;
    border-radius:5px;
}

/* EDGE BUTTONS */
#sidebarControls{
    position:fixed;
    top:80px;
    left:70px;
    z-index:1000;
    display:flex;
    flex-direction:column;
    gap:5px;
    transition:left .3s ease;
}

.sidebar.expanded + #sidebarControls{
    left:220px;
}

#sidebarControls button{
    width:30px;
    height:30px;
    display:flex;
    align-items:center;
    justify-content:center;
    border:none;
    cursor:pointer;
    border-radius:0 5px 5px 0;
    color:#fff;
    font-size:1rem;
}

#toggleBtn{background:#1e5631;}
#closeBtn{background:#dc3545;}
#sidebarControls.hidden{display:none;}

.main-content{
    margin-left:0;
    transition:margin-left .3s;
    padding:15px;
}

#hamburger{
    display:none;
    background:#1e5631;
    color:#fff;
    border:none;
    width:40px;
    height:40px;
    border-radius:5px;
    align-items:center;
    justify-content:center;
    z-index:1200;
}

.strength-bar-container { width:100%; height:8px; background:#e0e0e0; border-radius:4px; overflow:hidden; }
.strength-bar-fill { height:100%; width:0; background:red; transition: width 0.3s, background 0.3s; border-radius:4px; }
.requirements-list { text-align:left; font-size:13px; color:#6c757d; }
.requirements-list li { margin-bottom:3px; }

.image-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
  margin-top: 10px;
  width: 100%;
}

@media (min-width: 769px) {
  .image-grid {
    grid-template-columns: repeat(2, 1fr) !important;
  }
}

/* IMAGE PREVIEW */
#previewWrapper{
    width:100%;
    margin-top:12px;
}

.modal-dialog {
  max-width: 700px;
  width: 95%;
}

.modal-body {
  padding: 12px !important;
}

#titleSuggestions{
    position:absolute;
    top:100%;
    left:0;
    right:0;
    margin-top:2px;

    background:#fff;
    border:1px solid #dee2e6;
    border-top:none;

    border-radius:0 0 8px 8px;

    max-height:220px;
    overflow-y:auto;

    z-index:1056;

    box-shadow:0 6px 18px rgba(0,0,0,.12);

    display:none;
}

.suggestion-item{
    padding:10px 14px;
    cursor:pointer;
    font-size:14px;
    transition:.15s;
    border-bottom:1px solid #f1f1f1;
}

.suggestion-item:last-child{
    border-bottom:none;
}

.suggestion-item:hover{
    background:#e9f7ef;
    color:#1e5631;
}

.suggestion-item.active{
    background:#1e5631;
    color:#fff;
}

/* Scrollbar */

#titleSuggestions::-webkit-scrollbar{
    width:6px;
}

#titleSuggestions::-webkit-scrollbar-thumb{
    background:#c8c8c8;
    border-radius:10px;
}

#titleSuggestions::-webkit-scrollbar-thumb:hover{
    background:#9d9d9d;
}

.image-item {
  position: relative;
  width: 100%;
  aspect-ratio: 1 / 1;
  overflow: hidden;
  border-radius: 10px;
  background: #f1f1f1;
  box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}

.image-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.2s ease;
}

.image-actions{
  position:absolute;
  top:8px;
  right:8px;
  display:flex !important;
  flex-direction:row !important;
  flex-wrap:nowrap !important;
  align-items:center !important;
  justify-content:flex-end !important;
  gap:6px !important;
  z-index:20;
}

.image-actions > button{
  width:30px;
  height:30px;
  min-width:30px;
  flex:0 0 30px;
  border:none;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
}

.image-actions button:hover{
  background:#1e5631;
}

.image-actions .btn-delete:hover{
  background:#dc3545;
}

.image-actions i{
  font-size:14px;
}

.image-item:hover img {
  transform: scale(1.05);
}

.fullscreen-btn {
  position: absolute;
  top: 5px;
  left: 5px;
  width: 26px;
  height: 26px;
  z-index: 10;
  border: none;
  border-radius: 50%;
  background: rgba(0,0,0,0.6);
  color: #fff;
  font-size: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.image-item {
  position: relative; /* ensure overlay works */
}

.fullscreen-body {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
  padding: 0;
  overflow: hidden;
}

#fullscreenImage {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

#imageFullscreenModal .modal-content {
  background: rgba(0,0,0,0.95);
}

/* FIX FULLSCREEN MODAL CENTERING */
.modal.fullscreen,
.modal.show .modal-dialog.modal-fullscreen {
  margin: 0 !important;
  display: flex !important;
  align-items: center;
  justify-content: center;
  width: 100vw;
  height: 100vh;
}

#imageFullscreenModal .modal-dialog {
  margin: 0;
  max-width: 100%;
}

#imageFullscreenModal .modal-content {
  width: 100vw;
  height: 100vh;
  border: 0;
  border-radius: 0;
}

/* Bootstrap fullscreen image modal ABOVE SweetAlert */
#imageFullscreenModal {
    z-index: 6000 !important;
}


.swal-announcement-container {
  z-index: 5000 !important;
}

.swal2-container {
  z-index: 5000 !important;
}

.swal2-popup {
  z-index: 5001 !important;
}


@media (max-width:768px){

    .navbar{
        height:70px;
    }

    .navbar .container-fluid{
        position:relative;
        height:70px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        padding:0 12px;
    }

    #hamburger{
        display:flex !important;
        align-items:center;
        justify-content:center;
        width:42px;
        height:42px;
        padding:0;
        flex-shrink:0;
        z-index:2;
    }

    .navbar-brand{
        position:absolute;
        left:50%;
        top:50%;
        transform:translate(-50%,-50%);
        margin:0;
        z-index:1;
    }

    .navbar-brand img{
        height:38px;
    }

    /* Right Icons */
    .navbar-nav{
        margin-left:auto;
        flex-direction:row !important;
        align-items:center;
        justify-content:center;
        gap:0;
        height:70px;
    }

    .navbar-nav .nav-item{
        height:70px;
    }

    .navbar-nav .nav-link{
        display:flex;
        align-items:center;
        justify-content:center;
        padding:0 .15rem !important;
    }

    .navbar-nav i{
        font-size:1.35rem;
    }

    .dropdown-menu{
        position:absolute !important;
        right:0;
        left:auto;
        top:100%;
        margin-top:8px;
    }

    #sidebarControls{
        display:flex;
    }
}

@media (min-width: 769px) {
  .sidebar { width: 220px !important; transform: none !important; }
  .sidebar .nav-link { justify-content: flex-start !important; padding-left: 20px; }
  .sidebar .nav-link span { display: inline !important; margin-left: 10px; }
  .main-content { margin-left: 220px !important; }
  #sidebarControls, #toggleBtn, #closeBtn, #hamburger { display: none !important; }
}

/* Compact table for mobile */
@media (max-width: 768px) {
  #usersTable { font-size: 12px; }
  #usersTable th, #usersTable td { padding: 4px 6px; white-space: nowrap; }
  #usersTable th:nth-child(3), #usersTable td:nth-child(3) { max-width: 120px; overflow: hidden; text-overflow: ellipsis; }
  #usersTable th:nth-child(5), #usersTable td:nth-child(5) { max-width: 120px; overflow: hidden; text-overflow: ellipsis; }
  #usersTable .btn { padding: 2px 6px; font-size: 11px; }
}


/* =========================
   ACTION BUTTONS 
========================= */
.action-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  justify-content: flex-start;
  align-items: center;
}

/* Desktop: inline, compact */
.action-buttons .btn {
  font-size: 12px;
  padding: 4px 8px;
  white-space: nowrap;
}

/* Mobile: 2 buttons per row */
@media (max-width: 768px) {
  .action-buttons {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
  }

  .action-buttons .btn {
    width: 100%;
    font-size: 11px;
    padding: 5px 6px;
  }
}

@media (max-width: 768px) {

  .main-content .card > .d-flex {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: flex-start;
    flex-wrap: nowrap;
    gap: 8px;
  }

  /* TITLE */
  .main-content .card > .d-flex h4 {
    font-size: 15px;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  /* BUTTON (if present in this row) */
  .main-content .card > .d-flex .btn {
    flex-shrink: 0;
    font-size: 11px;
    padding: 5px 8px;
    white-space: nowrap;
  }

  .main-content p {
    font-size: 12px;
    margin: 0;
    line-height: 1.3;
  }
}


/* Compact table for announcements (mobile) */
@media (max-width: 768px) {
  #annTable {
    font-size: 11px;
  }

  #annTable th,
  #annTable td {
    padding: 4px 5px;
    white-space: nowrap;
    vertical-align: middle;
  }

  /* ID column */
  #annTable th:nth-child(1),
  #annTable td:nth-child(1) {
    width: 35px;
    text-align: center;
  }

  /* Title column (shorten) */
  #annTable th:nth-child(2),
  #annTable td:nth-child(2) {
    max-width: 90px;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Message column (very compact) */
  #annTable th:nth-child(3),
  #annTable td:nth-child(3) {
    max-width: 110px;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Audience + Status */
  #annTable th:nth-child(4),
  #annTable td:nth-child(4),
  #annTable th:nth-child(5),
  #annTable td:nth-child(5) {
    max-width: 80px;
    text-align: center;
  }

  /* Date column */
  #annTable th:nth-child(6),
  #annTable td:nth-child(6) {
    display: none; /* optional: hides date on mobile */
  }

  /* Actions column */
  #annTable th:nth-child(7),
  #annTable td:nth-child(7) {
    min-width: 90px;
  }

  #annTable .btn {
    font-size: 10px;
    padding: 2px 5px;
  }
}

.search-action-bar{
  display: flex;
  align-items: stretch; /* IMPORTANT: makes children equal height */
  gap: 8px;
  flex-wrap: nowrap;
}

.search-action-bar .form-control{
  height: 38px;   /* force consistent height */
  font-size: 13px;
}

.btn-add-announcement{
  height: 38px;   /* MATCH search bar height */
  display: flex;
  align-items: center;
  justify-content: center;
  white-space: nowrap;
  padding: 0 14px; /* balanced width */
  flex-shrink: 0;
}

@media (max-width: 768px){
  .search-action-bar{
    flex-wrap: nowrap;
  }

  .search-action-bar .form-control{
    font-size: 12px;
    height: 34px;
  }

}

/* =========================
   INFO ICON + CARD
========================= */

.info-icon-btn{
  width:28px;
  height:28px;
  padding:0;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  flex-shrink:0;
}

.info-card{
  background:#e9f7ef;
  border:1px solid #1e5631;
  color:#1e5631;
  border-radius:8px;
  font-size:13px;
  line-height:1.5;
}

.info-card strong{
  color:#174526;
}

@media (max-width:768px){

  .info-card{
    font-size:11px;
    padding:10px;
    line-height:1.45;
  }

  .info-icon-btn{
    width:24px;
    height:24px;
  }

  .info-icon-btn i{
    font-size:11px !important;
  }

  /* MOBILE IMAGE PREVIEW */
#previewWrapper{
    width:100%;
}


}

.view-slider-btn{
    width:42px;
    height:42px;
    border:none;
    border-radius:50%;
    background:rgba(240,240,240,.9);
    color:#555;
    transition:all .2s ease;
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:10;
}

.view-slider-btn:hover{
    background:#fff;
    color:#222;
    transform:translateY(-50%) scale(1.08);
}

.carousel-control-prev,
.carousel-control-next{
    width:auto;
    opacity:1;
    background:none;
}

.carousel-control-prev-icon,
.carousel-control-next-icon{
    display:none;
}

.preview-image{
    width:100%;
    height:420px;      /* fixed preview height */
    object-fit:cover;
    display:block;
}

.preview-image{
    width:100%;
    display:block;
    object-fit:cover;
    object-position:center;
    border-radius:8px;
}

/* Standard Facebook-style landscape */
.preview-landscape{
    height:300px;
}

/* Standard portrait (phone photo/poster) */
.preview-portrait{
    height:480px;
}

#previewContainer{
    width:80%;          /* smaller container */
    max-width:380px;    /* prevents it from becoming too large */
    margin:0 auto;      /* center it */
}

#previewContainer.landscape{
    aspect-ratio:16/9;
}

#previewContainer.portrait{
    aspect-ratio:4/5;
}

.preview-image{
    width:100%;
    height:100%;
    object-fit:cover;
    object-position:center;
    display:block;
}

#editPreviewContainer{
    width:80%;
    max-width:380px;
    margin:auto;
    background:#f8f9fa;
    border-radius:12px;
    overflow:hidden;
    position:relative;
}

#editPreviewContainer.landscape{
    aspect-ratio:16/9;
}

#editPreviewContainer.portrait{
    aspect-ratio:4/5;
}

#editPreviewImage{
    width:100%;
    height:100%;
    object-fit:cover;
    object-position:center;
    display:block;
}


</style>

</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="height:70px;">
  <div class="container-fluid">
    <button id="hamburger" class="d-flex d-lg-none"><i class="bi bi-list"></i></button>

    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="assets/enviromanage-logo.png" alt="Logo" style="height:40px;">
    </a>

    <ul class="navbar-nav flex-row align-items-center ms-auto">

    <!-- Notification -->
    <li class="nav-item me-3">
        <a class="nav-link text-white p-0" href="#">
            <i class="bi bi-bell-fill fs-5"></i>
        </a>
    </li>

    <!-- Profile -->
    <li class="nav-item dropdown">
        <a
            class="nav-link text-white p-0"
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
                        onclick="window.location.href='logout.php'">
                    Logout
                    <i class="bi bi-box-arrow-right ms-1"></i>
                </button>
            </li>
        </ul>
    </li>

</ul>
  </div>
</nav>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <nav class="nav flex-column">
    <a class="nav-link" href="admin-home.php"><i class="bi bi-house-door-fill"></i> <span>Dashboard</span></a>
    <a class="nav-link" href="admin-collection-schedules.php"><i class="bi bi-map-fill"></i> <span>Collection Schedules</span></a>
    <a class="nav-link" href="admin-trucks-collectors.php"><i class="bi bi-truck-front-fill"></i> <span>Trucks & Collectors</span></a>
    <a class="nav-link" href="admin-collection-records.php"><i class="bi bi-trash-fill"></i> <span>Collection Records</span></a>
    <a class="nav-link" href="#"><i class="bi bi-exclamation-circle-fill"></i> <span>Pickup Requests</span></a>
    <a class="nav-link" href="admin-resident-complaints.php"><i class="bi bi-file-earmark-text-fill"></i> <span>Resident Complaints</span></a>
    <a class="nav-link" href="#"><i class="bi bi-bar-chart-fill"></i> <span>Analytics</span></a>

    <a class="nav-link active" href="admin-announcements.php">
      <i class="bi bi-megaphone-fill"></i> <span>Announcements</span>
    </a>

    <a class="nav-link" href="admin-news.php"><i class="bi bi-newspaper"></i> <span>News & Articles</span></a>
    <a class="nav-link" href="admin-usermanagement.php"><i class="bi bi-people-fill"></i> <span>User Management</span></a>
    <a class="nav-link" href="#"><i class="bi bi-gear-fill"></i> <span>Settings</span></a>
  </nav>
</div>

<div id="sidebarControls">
  <button id="closeBtn"><i class="bi bi-x-lg"></i></button>
  <button id="toggleBtn"><i class="bi bi-chevron-right"></i></button>
</div>

<!-- MAIN CONTENT -->
<div class="main-content p-4">

  <div class="card p-4 shadow-sm mt-2">

    <!-- ROW 1: TITLE ONLY -->
    <div class="d-flex align-items-center gap-2">

      <h4 class="fw-semibold text-dark mb-0">
        <i class="bi bi-megaphone-fill me-2 text-success"></i>
        Announcements
      </h4>

      <!-- (i) INFO ICON -->
     <button type="button"
  id="announcementPageInfoBtn"
  class="btn btn-sm btn-light border info-icon-btn">
  <i class="bi bi-info-circle text-success"></i>
</button>
    </div>

     <!-- ROW 2: INFO CARD -->
    <div id="announcementPageInfoCard"
      class="alert mt-3 d-none"
      style="
        background:#e9f7ef;
        border:1px solid #1e5631;
        color:#1e5631;
        font-size:13px;
        border-radius:8px;
        line-height:1.5;
      ">

  This module is used to manage all system-wide announcements that are sent to residents, staff, and other user groups.<br><br>

  <strong>Core Features:</strong><br>
  • Create announcements with title, message, images, and audience targeting<br>
  • Save drafts for later editing or review before publishing<br>
  • Publish announcements instantly to selected users<br>
  • Edit, update, or delete existing announcements anytime<br>
  • Track status: <b>Posted</b> or <b>Draft</b><br><br>

  <strong>Audience Targeting:</strong><br>
  • Residents – community users<br>
  • Barangay Staff – internal personnel<br>
  • All Users – system-wide broadcast<br><br>

  <strong>Best Practices:</strong><br>
  • Use short, clear titles for better visibility<br>
  • Avoid unnecessary images for faster loading<br>
  • Always verify audience before posting<br>
  • Use drafts for incomplete announcements


    </div>

    <!-- ROW 3: SEARCH + ADD -->
<div class="d-flex align-items-center gap-2 mt-2 search-action-bar">

  <!-- SEARCH -->
  <div class="flex-grow-1">
    <input type="text" id="searchInput" class="form-control"
      placeholder="Search announcements...">
  </div>

  <!-- ADD BUTTON -->
  <button class="btn btn-success btn-sm flex-shrink-0"
    data-bs-toggle="modal"
    data-bs-target="#addAnnouncementModal">
    <i class="bi bi-plus-circle"></i> Add
  </button>

</div>
   

    <!-- ROW 4: TABLE -->
    <div class="table-responsive mt-3">

      <table class="table table-bordered" id="annTable">

        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Message</th>
            <th>Audience</th>
            <th>Status</th>
            <th>Created</th>
            <th>Updated</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody></tbody>

      </table>

    </div>

  </div>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addAnnouncementModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <form id="addAnnouncementForm">

       <div class="modal-header d-flex align-items-center">

  <!-- LEFT: TITLE + INFO ICON -->
  <div class="d-flex align-items-center gap-2">
    <h5 class="modal-title mb-0">Add Announcement</h5>

    <!-- INFO ICON (NEXT TO TITLE) -->
    <button type="button"
      id="addInfoToggleBtn"
      class="btn btn-sm btn-light border rounded-circle"
      style="width:28px;height:28px;padding:0;">
      <i class="bi bi-info-circle text-dark"></i>
    </button>
  </div>

  <!-- RIGHT: CLOSE BUTTON -->
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>

</div>
        

        <!-- BODY -->
        <div class="modal-body">

         <!-- INFO CARD -->
<div id="addInfoCard" class="alert small d-none mb-3"
     style="background:#e9f7ef; border:1px solid #1e5631; color:#1e5631; border-radius:8px;">

  <strong style="color:#1e5631;">How this works:</strong><br><br>

  • <b>Title Suggestions</b> – When you type a title, the system will suggest predefined titles.<br>
  • <b>Auto-Generate Message</b> – Works ONLY if you select a suggested title.<br>
  &nbsp;&nbsp;If you type your own title, message generation will be disabled.<br><br>

  • <b>Images</b> – You can upload multiple images for one announcement.<br>
  • <b>Audience</b> – Select who will receive the announcement (Residents, Staff, or All Users).<br>

</div>
         <div class="mb-2 position-relative">
    <label>Title</label>

    <input
        type="text"
        id="title"
        class="form-control"
        autocomplete="off"
        required>

    <div id="titleSuggestions"></div>
</div>

          <div class="mb-2">
            <label>Message</label>
            <textarea id="message" class="form-control" rows="4" required></textarea>
          </div>

          <div class="mt-2">
            <button type="button" class="btn btn-sm btn-outline-success" id="generateMessageBtn">
              ✨ Auto-generate Message
            </button>
          </div>

          <div class="mb-2">
            <label>Images (Optional)</label>
            <input type="file" id="images" class="form-control" accept="image/*" multiple>

          
          </div>

          <div class="mb-3">

    <label class="form-label">Image Orientation</label>

    <div class="d-flex gap-3">

        <div class="form-check">
            <input
                class="form-check-input"
                type="radio"
                name="imageOrientation"
                id="orientationLandscape"
                value="landscape"
                checked>

            <label class="form-check-label" for="orientationLandscape">
                Landscape
            </label>
        </div>

        <div class="form-check">
            <input
                class="form-check-input"
                type="radio"
                name="imageOrientation"
                id="orientationPortrait"
                value="portrait">

            <label class="form-check-label" for="orientationPortrait">
                Portrait
            </label>
        </div>

    </div>

</div>

       <div id="previewWrapper" class="mt-3 d-none">

    <div id="previewContainer"
     class="position-relative rounded overflow-hidden border">

       <img
    id="previewImage"
    class="w-100 preview-image">

        <button
            type="button"
            id="previewFullscreen"
            class="fullscreen-btn">
            <i class="bi bi-arrows-fullscreen"></i>
        </button>

        <div class="image-actions">

    <button
        type="button"
        id="previewReplace"
        class="btn-warning"
        title="Replace">
        <i class="bi bi-arrow-repeat"></i>
    </button>

    <button
        type="button"
        id="previewDelete"
        class="btn-delete"
        title="Delete">
        <i class="bi bi-trash"></i>
    </button>

</div>

        <button
            id="previewPrev"
            type="button"
            class="view-slider-btn position-absolute top-50 start-0 translate-middle-y ms-3">
            <i class="bi bi-chevron-left"></i>
        </button>

        <button
            id="previewNext"
            type="button"
            class="view-slider-btn position-absolute top-50 end-0 translate-middle-y me-3">
            <i class="bi bi-chevron-right"></i>
        </button>

    </div>

    <div id="previewCounter" class="text-center mt-2 small text-muted"></div>

</div>

<div class="mb-2 mt-3">
            <label>Audience</label>
            <select id="audience" class="form-select" required>
              <option value="all">All Users</option>
              <option value="resident">Residents</option>
              <option value="Barangay Secretary">Barangay Secretary</option>
            </select>
          </div>

        </div>

        <!-- FOOTER -->
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" id="saveDraftBtn">
            Save as Draft
          </button>

          <button type="submit" class="btn btn-success">
            Post
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editAnnouncementModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <form id="editAnnouncementForm">

        <div class="modal-header d-flex align-items-center justify-content-between">
  
  <div class="d-flex align-items-center gap-2">
    <h5 class="modal-title mb-0">Edit Announcement</h5>

    <!-- INFO ICON -->
    <button type="button" id="infoToggleBtn" class="btn btn-sm btn-light border rounded-circle"
      style="width:30px;height:30px;padding:0;">
      <i class="bi bi-info-circle text-dark"></i>
    </button>
  </div>

  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

        <div class="modal-body">

      <!-- INFO CARD (EDIT MODE) -->
<div id="infoCard" class="alert small d-none mb-3"
     style="background:#e9f7ef; border:1px solid #1e5631; color:#1e5631; border-radius:8px;">

  <strong style="color:#1e5631;">Edit Announcement Guide:</strong><br><br>

  • <b>Title</b> – You can modify the existing title anytime.<br>
  • <b>Message</b> – Update or correct the announcement content as needed.<br><br>

  • <b>Images</b> – You can add, remove, or replace images.<br>
  &nbsp;&nbsp;Existing images will remain unless deleted.<br><br>

  • <b>Audience</b> – Change who will receive this announcement.<br>
  • <b>Status</b> – Set to <b>Posted</b> or <b>Draft</b>.<br><br>

  <em>Tip: Changes only apply after clicking “Save Changes”.</em>

</div>

          <!-- ID -->
          <input type="hidden" id="editId">

          <!-- TITLE -->
          <div class="mb-2">
            <label>Title</label>
            <input type="text" id="editTitle" class="form-control" required>
          </div>

          <!-- MESSAGE -->
          <div class="mb-2">
            <label>Message</label>
            <textarea id="editMessage" class="form-control" rows="4" required></textarea>
          </div>

          <!-- AUDIENCE -->
          <div class="mb-2">
            <label>Audience</label>
            <select id="editAudience" class="form-select">
              <option value="all">All Users</option>
              <option value="resident">Residents</option>
              <option value="Barangay Secretary">Barangay Secretary</option>
            </select>
          </div>

          <!-- STATUS -->
          <div class="mb-2">
            <label>Status</label>
            <select id="editStatus" class="form-select">
              <option value="posted">Posted</option>
              <option value="draft">Draft</option>
            </select>
          </div>

          <!-- AUDIT -->
          <div class="mb-2">
            <label>Last Edited</label>
            <input type="text" id="editUpdatedAt" class="form-control" disabled>
          </div>

          <div class="mb-3">
    <label class="form-label">Image Orientation</label>

    <div class="d-flex gap-3">

        <div class="form-check">
            <input class="form-check-input"
                   type="radio"
                   name="editImageOrientation"
                   id="editLandscape"
                   value="landscape"
                   checked>

            <label class="form-check-label" for="editLandscape">
                Landscape
            </label>
        </div>

        <div class="form-check">
            <input class="form-check-input"
                   type="radio"
                   name="editImageOrientation"
                   id="editPortrait"
                   value="portrait">

            <label class="form-check-label" for="editPortrait">
                Portrait
            </label>
        </div>

    </div>
</div>

          <!-- IMAGES -->
          <div class="mb-2 mt-3">
            <label>Images</label>
            <input type="file" id="editImages" class="form-control" accept="image/*" multiple>

            <div id="editPreviewWrapper" class="mt-3 d-none">

    <div id="editPreviewContainer"
         class="position-relative rounded overflow-hidden border">

        <img id="editPreviewImage"
             class="w-100 preview-image">

        <button
            type="button"
            id="editPreviewFullscreen"
            class="fullscreen-btn">
            <i class="bi bi-arrows-fullscreen"></i>
        </button>

        <div class="image-actions">

            <button
                type="button"
                id="editPreviewReplace"
                class="btn-warning"
                title="Replace">
                <i class="bi bi-arrow-repeat"></i>
            </button>

            <button
                type="button"
                id="editPreviewDelete"
                class="btn-delete"
                title="Delete">
                <i class="bi bi-trash"></i>
            </button>

        </div>

        <button
            id="editPreviewPrev"
            type="button"
            class="view-slider-btn position-absolute top-50 start-0 translate-middle-y ms-3">
            <i class="bi bi-chevron-left"></i>
        </button>

        <button
            id="editPreviewNext"
            type="button"
            class="view-slider-btn position-absolute top-50 end-0 translate-middle-y me-3">
            <i class="bi bi-chevron-right"></i>
        </button>

    </div>

    <div id="editPreviewCounter"
         class="text-center mt-2 small text-muted"></div>

</div>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-primary" type="submit">
            Save Changes
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<!-- FULLSCREEN IMAGE MODAL -->
<div class="modal fade" id="imageFullscreenModal" tabindex="-1">
  <div class="modal-dialog modal-fullscreen modal-dialog-centered">
    <div class="modal-content bg-dark">

      <div class="modal-header border-0">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body fullscreen-body">
        <img id="fullscreenImage"
             src=""
             style="max-width:100%; max-height:100vh; object-fit:contain;">
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

/* =========================
   SIDEBAR LOGIC
========================= */
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleBtn');
const closeBtn = document.getElementById('closeBtn');
const hamburger = document.getElementById('hamburger');
const mainContent = document.querySelector('.main-content');
const sidebarControls = document.getElementById('sidebarControls');

function isMobile(){ return window.innerWidth <= 768; }

function updateContentMargin(){
  if(!isMobile()){
    mainContent.style.marginLeft='220px';
  } else {
    mainContent.style.marginLeft = sidebar.classList.contains('hidden') ? '0' : '70px';
  }
}

toggleBtn.addEventListener('click', () => {
  sidebar.classList.toggle('expanded');
  updateSidebarUI();
});

closeBtn.addEventListener('click', () => {
  sidebar.classList.add('hidden');
  sidebar.classList.remove('expanded'); // important fix
  updateSidebarUI();
});

hamburger.addEventListener('click', () => {
  sidebar.classList.remove('hidden');
  updateSidebarUI();
});

function updateSidebarUI() {
  const isHidden = sidebar.classList.contains('hidden');
  const isExpanded = sidebar.classList.contains('expanded');

  // MAIN SIDEBAR VISIBILITY
  if (isHidden) {
    sidebarControls.classList.add('hidden');
  } else {
    sidebarControls.classList.remove('hidden');
  }

  // BUTTON VISIBILITY ON MOBILE
  if (isHidden) {
    hamburger.style.display = 'flex';
  } else {
    hamburger.style.display = 'none';
  }

  updateContentMargin();
}

window.addEventListener('resize', updateContentMargin);
updateContentMargin();

/* =========================
   GLOBAL VARIABLES
========================= */
let titleSelectedFromSuggestion = false;
let editSelectedImages = [];
let fullscreenOpen = false;
let deletedEditImages = [];
let currentAnnouncementImages = [];
let currentAnnouncementIndex = 0;
let editPreviewIndex = 0;


const editId = document.getElementById('editId');
const editTitle = document.getElementById('editTitle');
const editMessage = document.getElementById('editMessage');
const editAudience = document.getElementById('editAudience');
const editStatus = document.getElementById('editStatus');
const editUpdatedAt = document.getElementById('editUpdatedAt');

/* =========================
   ANNOUNCEMENTS LOGIC
========================= */

let announcements = [];

async function fetchAnnouncements(){
  const res = await fetch('admin-fetch-announcements.php');
  const data = await res.json();
  announcements = Array.isArray(data) ? data : [];
  renderTable();
}

/* =========================
   IMAGE COMPRESSION
========================= */

async function compressImage(file){

  return new Promise((resolve) => {

    const img = new Image();
    const reader = new FileReader();

    reader.onload = e => {
      img.src = e.target.result;
    };

    img.onload = () => {

      const canvas = document.createElement('canvas');

      const MAX_WIDTH = 1200;

      let width = img.width;
      let height = img.height;

      if(width > MAX_WIDTH){
        height *= MAX_WIDTH / width;
        width = MAX_WIDTH;
      }

      canvas.width = width;
      canvas.height = height;

      const ctx = canvas.getContext('2d');

      ctx.drawImage(img, 0, 0, width, height);

      canvas.toBlob(
        blob => {
          resolve(
            new File(
              [blob],
              file.name,
              {
                type: 'image/jpeg',
                lastModified: Date.now()
              }
            )
          );
        },
        'image/jpeg',
        0.7
      );
    };

    reader.readAsDataURL(file);
  });
}

function renderTable(){
  const tbody = document.querySelector('#annTable tbody');
  tbody.innerHTML = '';

  const search = document.getElementById('searchInput').value.toLowerCase();

  const filtered = announcements.filter(a =>
    (a.title || '').toLowerCase().includes(search) ||
    (a.message || '').toLowerCase().includes(search)
  );

  // ✅ EMPTY STATES
if (filtered.length === 0) {

  const isSearching = search.trim() !== '';

  const tr = document.createElement('tr');

  // SEARCH EMPTY STATE
  if (isSearching) {

    tr.innerHTML = `
      <td colspan="8" class="text-center py-4 text-muted">
        <i class="bi bi-search" style="font-size:20px;"></i><br>
        <strong>No results found</strong><br>
        <small>Try adjusting your search keyword</small>
      </td>
    `;

  } 
  
  // NO ANNOUNCEMENTS YET
  else {

    tr.innerHTML = `
      <td colspan="8" class="text-center py-4 text-muted">
        <i class="bi bi-megaphone" style="font-size:20px;"></i><br>
        <strong>No announcements created</strong><br>
        <small>Create your first announcement using the Add button</small>
      </td>
    `;

  }

  tbody.appendChild(tr);
  return;
}

  // ✅ RENDER RESULTS
  filtered.forEach(a => {

    const statusBadge = a.status === 'posted'
      ? `<span class="badge bg-success">Posted</span>`
      : `<span class="badge bg-warning text-dark">Draft</span>`;

    const tr = document.createElement('tr');

     const shortMsg =
    a.message.length > 50
        ? a.message.substring(0,50) + '...'
        : a.message;

    tr.innerHTML = `
      <td>${a.id}</td>
      <td>${a.title}</td>
      <td>${shortMsg}</td>
      <td>${a.audience}</td>
      <td>${statusBadge}</td>
      <td>${formatDateTime(a.created_at)}</td>
      <td>
  ${
    a.updated_at === '-'
      ? '-'
      : `<small class="text-warning fw-semibold">
          ${formatDateTime(a.updated_at)}
        </small>`
  }
</td>
      <td>
        <div class="action-buttons">

          <button class="btn btn-sm btn-info"
            onclick="viewAnn(${a.id})">
            View All
          </button>

          <button class="btn btn-sm btn-primary"
            onclick="editAnn(${a.id})">
            <i class="bi bi-pencil-fill"></i> Edit
          </button>

          <button class="btn btn-sm btn-danger"
            onclick="deleteAnn(${a.id})">
            Delete
          </button>

        </div>
      </td>
    `;

    tbody.appendChild(tr);
  });
}

function formatDateTime(dateString){
  if(!dateString) return '';

  const date = new Date(dateString);

  return date.toLocaleString('en-PH', {
    year: 'numeric',
    month: 'short',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  });
}

async function submitAnnouncement(status, button, loadingText, successTitle) {
  const form = document.getElementById('addAnnouncementForm');

  const postBtn = form.querySelector('button[type="submit"]');
  const draftBtn = document.getElementById('saveDraftBtn');

  // 👉 disable both immediately (prevents double submit)
  postBtn.disabled = true;
  draftBtn.disabled = true;

  const originalHTML = button.innerHTML;

  button.innerHTML = `
    <span class="spinner-border spinner-border-sm me-1"></span>
    ${loadingText}
  `;

  try {
    const formData = new FormData();

    formData.append('title', title.value.trim());
    formData.append('message', message.value.trim());
    formData.append('audience', audience.value);
    formData.append('status', status);

    formData.append(
    'image_orientation',
    document.querySelector('input[name="imageOrientation"]:checked').value
);

    selectedImages.forEach(img => {
      formData.append('images[]', img.file);
    });

    const res = await fetch('admin-add-announcement.php', {
      method: 'POST',
      body: formData
    });

    const data = await res.json();

    if (data.success) {

      Swal.fire({
        icon: 'success',
        title: successTitle,
        timer: 1500,
        showConfirmButton: false
      });

      bootstrap.Modal
        .getInstance(document.getElementById('addAnnouncementModal'))
        .hide();

      form.reset();

selectedImages = [];
previewIndex = 0;

document.getElementById('images').value = '';
renderImagePreview();

      fetchAnnouncements();

    } else {
      Swal.fire('Error', data.message || 'Failed', 'error');
    }

  } catch (err) {
    console.error(err);
    Swal.fire('Error', 'Server error occurred', 'error');

  } finally {
    // 👉 ALWAYS RESET BOTH BUTTONS
    postBtn.disabled = false;
    draftBtn.disabled = false;
    button.innerHTML = originalHTML;
  }
}

/* INFORMATION */
document.getElementById('infoToggleBtn').addEventListener('click', () => {
  const card = document.getElementById('infoCard');
  card.classList.toggle('d-none');
});

document.getElementById('addInfoToggleBtn').addEventListener('click', () => {
  const card = document.getElementById('addInfoCard');
  card.classList.toggle('d-none');
});

function toggleViewInfoCard(){
  const card = document.getElementById('viewInfoCard');
  if(!card) return;

  card.style.display = (card.style.display === 'none' || card.style.display === '')
    ? 'block'
    : 'none';
}

document.getElementById('announcementPageInfoBtn')
  .addEventListener('click', function () {
    const card = document.getElementById('announcementPageInfoCard');
    card.classList.toggle('d-none');
  });

/* ADD */
document.getElementById('addAnnouncementForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const btn = e.submitter || e.target.querySelector('button[type="submit"]');

  await submitAnnouncement(
    'posted',
    btn,
    'Posting...',
    'Posted!'
  );
});

/* DRAFT */
document.getElementById('saveDraftBtn').addEventListener('click', async () => {
  const form = document.getElementById('addAnnouncementForm');

  await submitAnnouncement(
    'draft',
    document.getElementById('saveDraftBtn'),
    'Saving...',
    'Saved as Draft'
  );
});

let selectedImages = [];
let previewIndex = 0;

function syncFileInput(){

  const dt = new DataTransfer();

  selectedImages.forEach(item => {
    dt.items.add(item.file);
  });

  document.getElementById('images').files = dt.files;
}

// Load files
document.getElementById('images').addEventListener('change', async function(e) {

    const files = Array.from(e.target.files);

    const unsupportedExtensions = [
        "heic", "heif",
        "tif", "tiff",
        "raw",
        "cr2", "cr3",
        "nef",
        "arw",
        "dng",
        "orf",
        "rw2"
    ];

    for (const file of files) {

        // Reject non-image files (PDF, DOCX, MP4, etc.)
        if (!file.type.startsWith("image/")) {
            Swal.fire({
                icon: "warning",
                title: "Unsupported File",
                width: window.innerWidth <= 768 ? "320px" : "450px",
                html: `
                    <div style="font-size:13px;text-align:left;line-height:1.45;">
                        Only image files can be uploaded.<br><br>

                        <strong>Supported:</strong><br>
                        JPG, JPEG, PNG, GIF, WebP
                    </div>
                `
            });
            continue;
        }

        const ext = file.name.split('.').pop().toLowerCase();

        // Reject unsupported image formats
        if (
            unsupportedExtensions.includes(ext) ||
            file.type === "image/heic" ||
            file.type === "image/heif"
        ) {

            Swal.fire({
                icon: "warning",
                title: "Unsupported Image",
                width: window.innerWidth <= 768 ? "320px" : "500px",
                html: `
                    <div style="font-size:13px;text-align:left;line-height:1.45;">
                        This image format isn't supported.<br><br>

                        <strong>Unsupported:</strong><br>
                        HEIC, HEIF, TIFF, RAW<br>
                        (CR2, CR3, NEF, ARW, DNG, ORF, RW2)
                        <br><br>

                        <strong>Supported:</strong><br>
                        JPG, JPEG, PNG, GIF, WebP
                    </div>
                `,
                confirmButtonText: "OK"
            });

            continue;
        }

        const compressed = await compressImage(file);

        selectedImages.push({
            id: crypto.randomUUID(),
            file: compressed,
            preview: URL.createObjectURL(compressed)
        });
    }
    previewIndex = 0;
    syncFileInput();
    renderImagePreview();
});


let draggedIndex = null;

/* TITLE SUGGESTIONS LOGIC */
const titleInput = document.getElementById("title");
const suggestionBox = document.getElementById("titleSuggestions");

titleInput.addEventListener("input", async function () {

    titleSelectedFromSuggestion = false;

    const keyword = this.value.trim();

    if (keyword.length < 2){
    suggestionBox.innerHTML = "";
    suggestionBox.style.display = "none";
    return;
}

 const res = await fetch(
    "announcement-title-suggest.php?query=" +
    encodeURIComponent(keyword)
);

    const suggestions = await res.json();

    suggestionBox.innerHTML = "";

if(suggestions.length === 0){
    suggestionBox.style.display = "none";
    return;
}

suggestionBox.style.display = "block";

suggestions.forEach(title => {

        const div = document.createElement("div");
        div.className = "suggestion-item";
        div.textContent = title;

        div.onclick = () => {

            titleInput.value = title;
suggestionBox.innerHTML = "";
suggestionBox.style.display = "none";
titleSelectedFromSuggestion = true;

        };

        suggestionBox.appendChild(div);

    });

});

/* MESSAGE GENERATION */

document.getElementById('generateMessageBtn').addEventListener('click', async () => {
  const titleInput = document.getElementById('title');
  const title = titleInput.value;
  const audience = document.getElementById('audience').value;

  if (!title) {
    Swal.fire('Missing Title', 'Please enter a title first', 'warning');
    return;
  }

  if (!titleSelectedFromSuggestion) {
    Swal.fire(
      'Not Allowed',
      'Message can only be auto-generated if you select a suggested title.',
      'warning'
    );
    return;
  }

  const res = await fetch('announcement-message-generator.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ title, audience })
  });

  const data = await res.json();

  if (data.message) {
    document.getElementById('message').value = data.message;
  }
});

function updatePreviewOrientation(){

    const img = document.getElementById("previewImage");
    const container = document.getElementById("previewContainer");

    container.classList.remove("landscape","portrait");

    const image_orientation = document.querySelector(
        'input[name="imageOrientation"]:checked'
    ).value;

    if (image_orientation === "portrait") {
        container.classList.add("portrait");
    } else {
        container.classList.add("landscape");
    }
}

function updateEditPreviewOrientation(){

    const container = document.getElementById("editPreviewContainer");

    container.classList.remove("landscape","portrait");

    const orientation = document.querySelector(
        'input[name="editImageOrientation"]:checked'
    ).value;

    container.classList.add(orientation);

}

document
.querySelectorAll('input[name="editImageOrientation"]')
.forEach(radio=>{

    radio.addEventListener("change",()=>{

        updateEditPreviewOrientation();

    });

});

document
.querySelectorAll('input[name="imageOrientation"]')
.forEach(radio=>{

    radio.addEventListener("change",()=>{

        updatePreviewOrientation();

    });

});

function renderImagePreview(){

    const wrapper = document.getElementById("previewWrapper");

    if(selectedImages.length === 0){

        wrapper.classList.add("d-none");
        previewIndex = 0;
        return;

    }

    wrapper.classList.remove("d-none");
    updatePreviewOrientation();

    if(previewIndex >= selectedImages.length){
        previewIndex = selectedImages.length - 1;
    }

    if(previewIndex < 0){
        previewIndex = 0;
    }

    const current = selectedImages[previewIndex];

    document.getElementById("previewImage").src = current.preview;

    document.getElementById("previewCounter").textContent =
        `${previewIndex + 1} / ${selectedImages.length}`;

    document.getElementById("previewFullscreen").onclick = () => {
        openFullscreen(current.preview);
    };

    document.getElementById("previewReplace").onclick = () => {

        const input = document.createElement("input");

        input.type = "file";
        input.accept = "image/*";

        input.onchange = async e => {

            const file = e.target.files[0];

            if(!file) return;

            const compressed = await compressImage(file);

            URL.revokeObjectURL(current.preview);

            current.file = compressed;
            current.preview = URL.createObjectURL(compressed);

            syncFileInput();
            renderImagePreview();

        };

        input.click();

    };

    document.getElementById("previewDelete").onclick = () => {

        URL.revokeObjectURL(current.preview);

        selectedImages.splice(previewIndex,1);

        if(previewIndex >= selectedImages.length){
            previewIndex = selectedImages.length - 1;
        }

        syncFileInput();
        renderImagePreview();

    };

    const prevBtn = document.getElementById("previewPrev");
const nextBtn = document.getElementById("previewNext");
const counter = document.getElementById("previewCounter");

const showNav = selectedImages.length > 1;

prevBtn.style.display = showNav ? "" : "none";
nextBtn.style.display = showNav ? "" : "none";
counter.style.display = showNav ? "" : "none";

// Previous
prevBtn.onclick = () => {

    previewIndex--;

    if (previewIndex < 0) {
        previewIndex = selectedImages.length - 1;
    }

    renderImagePreview();
};

// Next
nextBtn.onclick = () => {

    previewIndex++;

    if (previewIndex >= selectedImages.length) {
        previewIndex = 0;
    }

    renderImagePreview();
};

}


function viewAnn(id){

    const a = announcements.find(x => x.id == id);
    if(!a) return;

    currentAnnouncementImages = a.images || [];
    currentAnnouncementIndex = 0;

    let imagesHtml = "";

    if (currentAnnouncementImages.length > 0) {

        const orientationClass =
            a.image_orientation === "portrait"
                ? "preview-portrait"
                : "preview-landscape";

        imagesHtml = `
            <div class="mb-2">

                <div class="position-relative border rounded overflow-hidden mx-auto"
                     style="width:80%;max-width:380px;">

                    <img
                        id="viewAnnouncementImage"
                        src="${currentAnnouncementImages[0]}"
                        class="w-100 preview-image ${orientationClass}"
                        style="display:block;">

                    ${
                        currentAnnouncementImages.length > 1
                        ? `
                            <button
                                type="button"
                                class="view-slider-btn position-absolute top-50 start-0 translate-middle-y ms-2"
                                onclick="changeAnnouncementImage(-1)">
                                <i class="bi bi-chevron-left"></i>
                            </button>

                            <button
                                type="button"
                                class="view-slider-btn position-absolute top-50 end-0 translate-middle-y me-2"
                                onclick="changeAnnouncementImage(1)">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        `
                        : ""
                    }

                </div>

                ${
                    currentAnnouncementImages.length > 1
                    ? `
                        <div class="text-center mt-2">
                            <small id="announcementImageCounter">
                                1 / ${currentAnnouncementImages.length}
                            </small>
                        </div>
                    `
                    : ""
                }

            </div>
        `;
    }

    Swal.fire({
        width: 600,
        showCloseButton: true,
        showConfirmButton: false,
        background: "#f8f9fa",

        customClass:{
            container:"swal-announcement-container"
        },

        didOpen:()=>{
            const container = Swal.getContainer();
            const popup = Swal.getPopup();

            container.style.zIndex = "5000";
            popup.style.zIndex = "5001";
        },

        title:"",

        html:`
            <div style="text-align:left; margin-top:2px;">

                <div style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    margin-bottom:6px;">

                    <div style="display:flex; align-items:center; gap:6px;">

                        <div style="font-size:12px; color:#6c757d;">
                            Announcement ID: #${a.id}
                        </div>

                        <button
                            onclick="toggleViewInfoCard()"
                            style="
                                width:22px;
                                height:22px;
                                border-radius:50%;
                                border:1px solid #1e5631;
                                background:#e9f7ef;
                                color:#1e5631;
                                font-size:12px;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                cursor:pointer;">

                            <i class="bi bi-info-circle"></i>

                        </button>

                    </div>

                </div>

                <div id="viewInfoCard"
                     style="
                        display:none;
                        background:#e9f7ef;
                        border:1px solid #1e5631;
                        color:#1e5631;
                        padding:10px;
                        border-radius:8px;
                        font-size:12px;
                        margin-bottom:10px;
                        line-height:1.4;">

                    <strong>Announcement View Guide:</strong><br><br>

                    • This is a read-only view of the announcement.<br>
                    • Use the arrows to browse images.<br>
                    • Audience shows recipients of this announcement.<br>
                    • Status indicates posted or draft state.

                </div>

                <div style="
                    font-size:18px;
                    font-weight:700;
                    color:#1e5631;
                    margin:2px 0 6px 0;">

                    ${a.title}

                </div>

                ${imagesHtml}

                <div style="
                    display:flex;
                    gap:8px;
                    flex-wrap:wrap;
                    font-size:12px;
                    margin-bottom:8px;">

                    <span class="badge bg-primary">${a.audience}</span>

                    ${
                        a.status === "posted"
                        ? `<span class="badge bg-success">Posted</span>`
                        : `<span class="badge bg-warning text-dark">Draft</span>`
                    }

                    <span class="badge bg-secondary">${a.created_at}</span>

                </div>

                <div style="
                    background:#fff;
                    padding:12px;
                    border-radius:8px;
                    border:1px solid #dee2e6;">

                    <div style="
                        font-size:13px;
                        font-weight:600;
                        margin-bottom:4px;">

                        Message:

                    </div>

                    <div style="
                        font-size:14px;
                        white-space:pre-wrap;
                        line-height:1.5;">

                        ${a.message}

                    </div>

                </div>

            </div>
        `
    });

}

function changeAnnouncementImage(direction){

    currentAnnouncementIndex += direction;

    if(currentAnnouncementIndex < 0){
        currentAnnouncementIndex = currentAnnouncementImages.length - 1;
    }

    if(currentAnnouncementIndex >= currentAnnouncementImages.length){
        currentAnnouncementIndex = 0;
    }

    document.getElementById("viewAnnouncementImage").src =
        currentAnnouncementImages[currentAnnouncementIndex];

    document.getElementById("announcementImageCounter").textContent =
        `${currentAnnouncementIndex + 1} / ${currentAnnouncementImages.length}`;
}

let fullscreenModal = null;

function openFullscreen(src) {

    fullscreenOpen = true;

    if (document.activeElement) {
        document.activeElement.blur();
    }

    document.getElementById('fullscreenImage').src = src;

    const modalEl = document.getElementById('imageFullscreenModal');

    if (!fullscreenModal) {
        fullscreenModal = new bootstrap.Modal(modalEl, {
            backdrop: true,
            keyboard: true
        });
    }

    fullscreenModal.show();

    setTimeout(() => {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        if (backdrops.length) {
            backdrops[backdrops.length - 1].style.zIndex = "5999";
        }
    }, 0);
}

document.getElementById('imageFullscreenModal')
.addEventListener('hidden.bs.modal', () => {
    fullscreenOpen = false;
});


function togglePostDraftButtons(mode) {
  const postBtn = document.querySelector('#addAnnouncementForm button[type="submit"]');
  const draftBtn = document.getElementById('saveDraftBtn');

  if (mode === 'draft') {
    postBtn.disabled = true;
    draftBtn.disabled = false;
  } else if (mode === 'post') {
    postBtn.disabled = false;
    draftBtn.disabled = true;
  } else {
    // reset (default state)
    postBtn.disabled = false;
    draftBtn.disabled = false;
  }
}

/* EDIT */
function editAnn(id){
  const a = announcements.find(x => x.id == id);
  if(!a) return;

  editId.value = a.id;
  editTitle.value = a.title || '';
  editMessage.value = a.message || '';
  editAudience.value = a.audience || 'all';
  editStatus.value = a.status || 'draft';
 console.log("Announcement:", a);
console.log("image_orientation =", JSON.stringify(a.image_orientation));

const orientation = (a.image_orientation || "landscape").trim();

const radio = document.querySelector(
    `input[name="editImageOrientation"][value="${orientation}"]`
);

console.log("Found radio:", radio);

if (radio) {
    radio.checked = true;
}

updateEditPreviewOrientation();

updateEditPreviewOrientation();
  editUpdatedAt.value = a.updated_at || a.created_at || '';

  // reset properly
  editSelectedImages = [];
  deletedEditImages = [];

  document.getElementById('editImages').value = '';

  if (Array.isArray(a.images)) {
    editSelectedImages = a.images.map(img => ({
      id: crypto.randomUUID ? crypto.randomUUID() : Date.now() + Math.random(),
      file: null,
      preview: img,
      existing: true
    }));
  }

  editPreviewIndex = 0;
  renderEditImages();

  const modal = new bootstrap.Modal(
    document.getElementById('editAnnouncementModal')
  );
  modal.show();
}

function renderEditImages(){

    const wrapper = document.getElementById("editPreviewWrapper");

    if(editSelectedImages.length === 0){

        wrapper.classList.add("d-none");
        editPreviewIndex = 0;
        return;

    }

    wrapper.classList.remove("d-none");
    updateEditPreviewOrientation();

    if(editPreviewIndex >= editSelectedImages.length){
        editPreviewIndex = editSelectedImages.length - 1;
    }

    if(editPreviewIndex < 0){
        editPreviewIndex = 0;
    }

    const current = editSelectedImages[editPreviewIndex];

    document.getElementById("editPreviewImage").src = current.preview;

    document.getElementById("editPreviewCounter").textContent =
        `${editPreviewIndex + 1} / ${editSelectedImages.length}`;

    document.getElementById("editPreviewFullscreen").onclick = () => {
        openFullscreen(current.preview);
    };

    document.getElementById("editPreviewDelete").onclick = () => {

        if(current.existing &&
           !deletedEditImages.includes(current.preview)){

            deletedEditImages.push(current.preview);

        }

        if(!current.existing){
            URL.revokeObjectURL(current.preview);
        }

        editSelectedImages.splice(editPreviewIndex,1);

        if(editPreviewIndex >= editSelectedImages.length){
            editPreviewIndex = editSelectedImages.length - 1;
        }

        renderEditImages();

    };

    document.getElementById("editPreviewReplace").onclick = () => {

        const input = document.createElement("input");
        input.type = "file";
        input.accept = "image/*";

        input.onchange = async e => {

            const file = e.target.files[0];
            if(!file) return;

            const compressed = await compressImage(file);

            if(current.existing){
                deletedEditImages.push(current.preview);
            }else{
                URL.revokeObjectURL(current.preview);
            }

            current.file = compressed;
            current.preview = URL.createObjectURL(compressed);
            current.existing = false;

            renderEditImages();

        };

        input.click();

    };

    const showNav = editSelectedImages.length > 1;

    document.getElementById("editPreviewPrev").style.display =
        showNav ? "" : "none";

    document.getElementById("editPreviewNext").style.display =
        showNav ? "" : "none";

    document.getElementById("editPreviewCounter").style.display =
        showNav ? "" : "none";

    document.getElementById("editPreviewPrev").onclick = () => {

        editPreviewIndex--;

        if(editPreviewIndex < 0){
            editPreviewIndex = editSelectedImages.length - 1;
        }

        renderEditImages();

    };

    document.getElementById("editPreviewNext").onclick = () => {

        editPreviewIndex++;

        if(editPreviewIndex >= editSelectedImages.length){
            editPreviewIndex = 0;
        }

        renderEditImages();

    };

}


document.getElementById('editImages').addEventListener('change', async function(e){

  const files = Array.from(e.target.files);

  for(const file of files){

    if(!file.type.startsWith("image/")){
      continue;
    }

    const compressed = await compressImage(file);

    editSelectedImages.push({
      id: crypto.randomUUID ? crypto.randomUUID() : Date.now() + Math.random(),
      file: compressed,
      preview: URL.createObjectURL(compressed),
      existing: false
    });

  }

  renderEditImages();

  // clear input so same file can be selected again
  this.value = '';

});

document.getElementById('editAnnouncementForm').addEventListener('submit', async e=>{
  e.preventDefault();

  const submitBtn = e.target.querySelector('button[type="submit"]');

  // LOADING STATE START
  submitBtn.disabled = true;
  submitBtn.innerHTML = `
    <span class="spinner-border spinner-border-sm me-1"></span>
    Saving...
  `;

  try {

    const formData = new FormData();

    formData.append('id', editId.value);
    formData.append('title', editTitle.value.trim());
    formData.append('message', editMessage.value.trim());
    formData.append('audience', editAudience.value);
    formData.append('status', editStatus.value);

    formData.append(
    'image_orientation',
    document.querySelector(
        'input[name="editImageOrientation"]:checked'
    ).value
);

    formData.append(
  'deleted_images',
  JSON.stringify(deletedEditImages)
);

    editSelectedImages.forEach(img => {
      if (img.file) {
        formData.append('new_images[]', img.file);
      } else {
        formData.append('existing_images[]', img.preview);
      }
    });

    const res = await fetch('admin-update-announcement.php', {
      method: 'POST',
      body: formData
    });

    const data = await res.json();

    if(data.success){

      Swal.fire('Updated','Announcement updated','success');

      bootstrap.Modal.getInstance(
        document.getElementById('editAnnouncementModal')
      )?.hide();

      fetchAnnouncements();

    } else {
      Swal.fire('Error', data.message || 'Update failed', 'error');
    }

  } catch(err) {

    console.error(err);
    Swal.fire('Error','Server error occurred','error');

  } finally {

    // RESET BUTTON STATE
    submitBtn.disabled = false;
    submitBtn.innerHTML = `Save Changes`;
  }
});

document.getElementById('editAnnouncementModal')
.addEventListener('hidden.bs.modal', () => {

    editSelectedImages = [];
    deletedEditImages = [];

    document.getElementById('editPreviewWrapper').classList.add('d-none');
editPreviewIndex = 0;

});

/* DELETE */
async function deleteAnn(id){
  const confirm = await Swal.fire({
    title:'Delete?',
    icon:'warning',
    showCancelButton:true
  });

  if(!confirm.isConfirmed) return;

  const res = await fetch('admin-delete-announcement.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify({id})
  });

  const data = await res.json();

 if(data.success){

    Swal.fire(
        'Deleted',
        'Announcement removed successfully.',
        'success'
    );

    fetchAnnouncements();
}
}

/* SEARCH */
document.getElementById('searchInput').addEventListener('input', renderTable);

fetchAnnouncements();

document.getElementById('logoutBtn').addEventListener('click', ()=>{
  window.location.href='logout.php';
});

document.addEventListener("click", function(e){

    if(
        !titleInput.contains(e.target) &&
        !suggestionBox.contains(e.target)
    ){
        suggestionBox.style.display = "none";
    }

});

</script>

</body>
</html>