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

<title>EnviroManage Admin - Resident Complaints</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
  font-family:"Segoe UI",Tahoma,Geneva,Verdana,sans-serif;
  background:#f8f9fa;
  margin:0;
  padding-top:70px;
}

.navbar{
  background:#1e5631 !important;
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
.sidebar {
  position: fixed;
  top: 70px;
  left: 0;
  width: 70px;
  height: 100%;
  background-color: #fff;
  border-right: 1px solid #dee2e6;
  padding-top: 15px;
  overflow-y: auto;
  transition: width 0.3s ease, transform 0.3s ease;
  z-index: 1000;
}

.sidebar.expanded {
  width: 220px;
}

.sidebar.hidden {
  transform: translateX(-100%);
}

.sidebar .nav-link {
  color: #495057;
  padding: 10px;
  display: flex;
  align-items: center;
  gap: 10px;
  justify-content: center;
}

.sidebar .nav-link span {
  display: none;
}

.sidebar.expanded .nav-link {
  justify-content: flex-start;
}

.sidebar.expanded .nav-link span {
  display: inline;
}

.sidebar .nav-link:hover,
.sidebar .nav-link.active {
  background-color: #1e5631;
  color: #fff;
  border-radius: 5px;
}

/* EDGE BUTTONS */
#sidebarControls {
  position: fixed;
  top: 80px;
  left: 70px;
  z-index: 1000;
  display: flex;
  flex-direction: column;
  gap: 5px;
  transition: left 0.3s ease;
}

.sidebar.expanded + #sidebarControls {
  left: 220px;
}

#sidebarControls button {
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  cursor: pointer;
  border-radius: 0 5px 5px 0;
  color: #fff;
  font-size: 1rem;
}

#toggleBtn {
  background-color: #1e5631;
}

#closeBtn {
  background-color: #dc3545;
}

#sidebarControls.hidden {
  display: none;
}

.main-content {
  margin-left: 0;
  transition: margin-left 0.3s;
  padding: 20px;
}

#hamburger {
  display: none;
  background-color: #1e5631;
  color: #fff;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 5px;
  align-items: center;
  justify-content: center;
  z-index: 1200;
}

/* MOBILE */
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

  #newsTable{
    font-size:11px;
  }

  #newsTable th,
  #newsTable td{
    padding:5px;
    white-space:nowrap;
  }
}

/* DESKTOP */
@media (min-width: 769px) {

  .sidebar {
    width: 220px !important;
    transform: none !important;
  }

  .sidebar .nav-link {
    justify-content: flex-start !important;
    padding-left: 20px;
  }

  .sidebar .nav-link span {
    display: inline !important;
    margin-left: 10px;
  }

  .main-content {
    margin-left: 220px !important;
  }

  #sidebarControls,
  #toggleBtn,
  #closeBtn,
  #hamburger {
    display: none !important;
  }

}

.image-preview{
  width:100%;
  height:250px;
  object-fit:cover;
  border-radius:10px;
  border:1px solid #ddd;
}

.news-content-preview{
  max-height:80px;
  overflow:hidden;
  text-overflow:ellipsis;
}

.table td{
  vertical-align:middle;
}

@media(max-width:768px){

  .sidebar{
    transform: translateX(-100%);
    width: 70px;
    display:block;
  }

  .sidebar:not(.hidden){
    transform: translateX(0);
  }

  .main-content{
    margin-left:0;
  }

  #newsTable{
    font-size:11px;
  }

  #newsTable th,
  #newsTable td{
    padding:5px;
    white-space:nowrap;
  }

}

.sidebar.hidden{
  transform: translateX(-100%);
}

#searchInput{
  flex:1;
  min-width:0;
}



.news-controls {
  width: 100%;
  max-width: 100%;
}

.search-row {
  display: flex;
  gap: 10px;
  align-items: center;
}

.search-row #searchInput {
  flex: 1;
  min-width: 0;
}

.search-row .btn {
  white-space: nowrap;
}

/* MOBILE FIX */
@media (max-width: 768px) {
  .search-row {
    flex-direction: row;
  }

  .search-row #searchInput {
    font-size: 12px;
    height: 38px;
  }

  .search-row .btn {
    font-size: 12px;
    padding: 6px 10px;
  }
}

/* mobile default (unchanged behavior) */
.news-title {
  font-size: 16px;
}

/* desktop only */
@media (min-width: 769px) {
  .news-title {
    font-size: 22px;
  }
}

/* ROW spacing consistency (same feel as announcements) */
.news-controls {
  margin-top: 8px !important;
  gap: 8px;
  align-items: center;
}

/* search input sizing consistency */
.news-search {
  padding: 6px 10px;
  font-size: 13px;
}

/* add button consistency */
.news-add-btn {
  font-size: 12px;
  padding: 5px 10px;
  border-radius: 6px;
  white-space: nowrap;
}

/* desktop enhancement */
@media (min-width: 769px) {
  .news-search {
    font-size: 14px;
    padding: 8px 12px;
  }

  .news-add-btn {
    font-size: 13px;
    padding: 8px 14px;
  }
}

#pagination {
  flex-wrap: wrap;
}

#pagination .page-link {
  cursor: pointer;
}

.image-preview{
  width:100%;
  height:150px;
  object-fit:cover;
  border-radius:10px;
  border:1px solid #ddd;
}

.preview-wrapper{
  position:relative;
}

.preview-label{
  position:absolute;
  top:5px;
  left:5px;
  background:rgba(0,0,0,.6);
  color:#fff;
  font-size:11px;
  padding:2px 6px;
  border-radius:4px;
}

.image-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
}

.image-item {
  position: relative;
  width: 100%;
  aspect-ratio: 1 / 1;
  overflow: hidden;
  border-radius: 10px;
  background: #f1f1f1;
}

.image-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-actions {
  position: absolute;
  top: 5px;
  right: 5px;
  display: flex;
  gap: 4px;
}

.image-actions button {
  width: 22px;
  height: 22px;
  border: none;
  border-radius: 50%;
  background: rgba(0,0,0,0.6);
  color: #fff;
}

.fullscreen-btn {
  position: absolute;
  top: 5px;
  left: 5px;
  width: 26px;
  height: 26px;
  border: none;
  border-radius: 50%;
  background: rgba(0,0,0,0.6);
  color: #fff;
  z-index: 10;
}

.spinner-border {
  vertical-align: middle;
}

.view-slider-btn{
    width:42px;
    height:42px;
    border:none;
    border-radius:50%;
    background:rgba(240,240,240,.9);
    color:#555;
    transition:.2s;
}

.view-slider-btn:hover{
    background:#fff;
    color:#222;
    transform:translateY(-50%) scale(1.08);
}


/* Standard Facebook-style landscape */
.preview-landscape{
    height:300px;
}

/* Standard portrait (phone photo/poster) */
.preview-portrait{
    height:480px;
}

/* ---------- NEWS IMAGE PREVIEW ---------- */

#newsPreviewContainer{
    width:100%;
    max-width:420px;
    margin:0 auto;
    background:#f8f9fa;
    border-radius:12px;
    overflow:hidden;
    border:1px solid #ddd;
    position:relative;
}

/* Landscape preview */
#newsPreviewContainer.landscape{
    height:240px;
}

/* Portrait preview */
#newsPreviewContainer.portrait{
    height:500px;
}

#newsPreviewImage{
    width:100%;
    height:100%;
    display:block;
    object-fit:cover;
    object-position:center;
}

#editnewsPreviewContainer{
    width:80%;
    max-width:380px;
    margin:auto;
    background:#f8f9fa;
    border-radius:12px;
    overflow:hidden;
    position:relative;
}

#editnewsPreviewContainer.landscape{
    aspect-ratio:16/9;
}

#editnewsPreviewContainer.portrait{
    aspect-ratio:4/5;
}

#editnewsPreviewImage{
    width:100%;
    height:100%;
    object-fit:cover;
    object-position:center;
    display:block;
}

/* ================= FULLSCREEN IMAGE MODAL ================= */

#imageFullscreenModal{
    z-index: 3000 !important;
}

#imageFullscreenModal + .modal-backdrop{
    z-index: 2999 !important;
}

#imageFullscreenModal .modal-dialog{
    margin:0;
    width:100vw;
    max-width:none;
    height:100vh;
}

#imageFullscreenModal .modal-content{
    background:#1b1b1b;
    border:none;
    border-radius:0;
    width:100%;
    height:100vh;
}

#imageFullscreenModal .modal-header{
    border:none;
    position:absolute;
    top:0;
    right:0;
    width:100%;
    z-index:10;
    background:transparent;
    justify-content:flex-end;
    padding:15px;
}

#imageFullscreenModal .btn-close{
    filter:invert(1);
    opacity:1;
    width:20px;
    height:20px;
}

#imageFullscreenModal .modal-body{
    display:flex;
    justify-content:center;
    align-items:center;
    width:100%;
    height:100vh;
    padding:20px;
}

#fullscreenImage{
    max-width:100%;
    max-height:100%;
    object-fit:contain;
    display:block;
}

</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="height:70px;">
 <div class="container-fluid">

  <button id="hamburger" class="d-flex d-lg-none">
    <i class="bi bi-list"></i>
  </button>

    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="assets/enviromanage-logo.png" style="height:40px;">
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
    <a class="nav-link active" href="admin-resident-complaints.php"><i class="bi bi-file-earmark-text-fill"></i> <span>Resident Complaints</span></a>
    <a class="nav-link" href="#"><i class="bi bi-bar-chart-fill"></i> <span>Analytics</span></a>

    <a class="nav-link" href="admin-announcements.php">
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

<!-- MAIN -->
<div class="main-content">

  <div class="card shadow-sm p-4">

  <!-- HEADER (COMPACT + INFO TOGGLE) -->
<div class="d-flex align-items-center gap-2 mb-2">

<h5 class="fw-semibold mb-0 news-title">
<i class="bi bi-file-earmark-text-fill text-success me-1"></i>
Resident Complaints
</h5>

  <!-- INFO BUTTON -->
  <button type="button"
    id="complaintInfoBtn"
    class="btn btn-sm btn-light border rounded-circle"
    style="width:24px;height:24px;padding:0;">
    <i class="bi bi-info-circle text-success" style="font-size:11px;"></i>
  </button>

</div>

<!-- INFO CARD (HIDDEN BY DEFAULT) -->
<div id="complaintInfoCard"
class="alert mt-2 d-none"
style="
background:#e9f7ef;
border:1px solid #1e5631;
color:#1e5631;
font-size:12px;
border-radius:8px;
line-height:1.4;
">


This module displays resident complaints that have been reviewed and validated by Barangay Secretaries.

<br><br>

<strong>Features:</strong><br>

• View validated resident complaints<br>
• Monitor garbage collection issues<br>
• Track complaint resolution status<br>
• Update complaint progress after action taken


</div>

<div class="search-row">

<input type="text"
id="complaintSearch"
class="form-control news-search"
placeholder="Search complaints...">


<select id="statusFilter"
class="form-select news-search"
style="max-width:180px;">

<option value="">
All Status
</option>

<option value="Pending">
Pending
</option>

<option value="In Progress">
In Progress
</option>

<option value="Resolved">
Resolved
</option>

</select>

</div>


    <div class="table-responsive mt-3">

      <table class="table table-bordered" id="complaintTable">

<thead>

<tr>

<th>ID</th>

<th>Resident</th>

<th>Barangay</th>

<th>Complaint</th>

<th>Validated By</th>

<th>Validation Date</th>

<th>Status</th>

<th>Actions</th>

</tr>

</thead>


<tbody id="complaintTableBody">
    <tr id="noComplaintsRow">
        <td colspan="8" class="text-center text-muted py-4">
            No resident complaints found
        </td>
    </tr>
</tbody>


</table>

    </div>

    <!-- COMPLAINT PAGINATION -->
<div id="complaintPagination"
     class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">

  <div class="text-center text-md-start">
    <small id="complaintPaginationInfo" class="text-muted">
      Showing 0 to 0 of 0 complaints
    </small>
  </div>

  <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">

    <button id="complaintPrevBtn"
            class="btn btn-sm btn-outline-success">
      Previous
    </button>

    <span id="complaintPageNumber"
          class="fw-semibold px-2">
      Page 1 of 1
    </span>

    <button id="complaintNextBtn"
            class="btn btn-sm btn-outline-success">
      Next
    </button>

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

function isMobile() {
    return window.innerWidth <= 768;
}

function updateContentMargin() {
    if (!isMobile()) {
        mainContent.style.marginLeft = '220px';
    } else {
        mainContent.style.marginLeft =
            sidebar.classList.contains('hidden') ? '0' : '70px';
    }
}

toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('expanded');
    updateSidebarUI();
});

closeBtn.addEventListener('click', () => {
    sidebar.classList.add('hidden');
    sidebar.classList.remove('expanded');
    updateSidebarUI();
});

hamburger.addEventListener('click', () => {
    sidebar.classList.remove('hidden');
    updateSidebarUI();
});

function updateSidebarUI() {

    const isHidden = sidebar.classList.contains('hidden');

    if (isHidden) {
        sidebarControls.classList.add('hidden');
        hamburger.style.display = 'flex';
    } else {
        sidebarControls.classList.remove('hidden');
        hamburger.style.display = 'none';
    }

    updateContentMargin();
}

window.addEventListener('resize', updateContentMargin);
updateContentMargin();

/* =========================
   INFO CARD TOGGLE
========================= */

const complaintInfoBtn = document.getElementById('complaintInfoBtn');
const complaintInfoCard = document.getElementById('complaintInfoCard');

if (complaintInfoBtn && complaintInfoCard) {
    complaintInfoBtn.addEventListener('click', () => {
        complaintInfoCard.classList.toggle('d-none');
    });
}

/* =========================
   COMPLAINT PAGE VARIABLES
========================= */

let complaints = [];
let currentPage = 1;
const ITEMS_PER_PAGE = 10;

const tbody = document.getElementById("complaintTableBody");

if (complaints.length === 0) {
    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center text-muted py-4">
                No resident complaints found
            </td>
        </tr>
    `;
    return;
}
</script>

</body>
</html>