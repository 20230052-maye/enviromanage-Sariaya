<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EnviroManage | Collection Schedules</title>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; padding-top: 70px; }
a { text-decoration: none; }
h4, h5, h6 { color: #1e5631; }
.navbar { background-color: #1e5631 !important; }
.navbar {min-height:70px;}

.navbar .container-fluid{
    height:70px; display:flex; align-items:center;
}

.navbar-nav{
    display:flex;
    flex-direction:row;
    align-items:center;
    height:70px;
    margin-bottom:0;
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

.navbar-nav > .nav-item > .nav-link > i{
    display:block;
    line-height:1;
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
  z-index: 1050;
}

.sidebar.expanded {
  width: 220px;
}

.sidebar.hidden {
  transform: translateX(-100%);
}
.sidebar .nav-link { color: #495057; padding: 10px; display: flex; align-items: center; gap: 10px; justify-content: center; }
.sidebar .nav-link span { display: none; }
.sidebar.expanded .nav-link { justify-content: flex-start; }
.sidebar.expanded .nav-link span { display: inline; }
.sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #1e5631; color: #fff; border-radius: 5px; }

/* EDGE BUTTONS */
#sidebarControls {
  position: fixed;
  top: 80px;
  left: 70px;
  z-index: 1040;
  display: flex;
  flex-direction: column;
  gap: 5px;
  transition: left 0.3s ease, opacity 0.2s ease;
  opacity: 1;
  visibility: visible;
}

/* hidden state */
#sidebarControls.hidden {
  opacity: 0;
  visibility: hidden;
  pointer-events: none;
}
.sidebar.expanded + #sidebarControls { left: 220px; }
#sidebarControls button { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; border-radius: 0 5px 5px 0; color: #fff; font-size:1rem; }
#toggleBtn { background-color: #1e5631; }
#closeBtn { background-color: #dc3545; }

.sidebar {
  overflow-x: hidden;
}

.sidebar.expanded {
  width: 220px;
}

.main-content {
  margin-left: 70px;   /* default = icon-only sidebar */
  padding: 15px;
  transition: margin-left 0.3s ease;
}

/* when sidebar collapsed/hidden */
body.sidebar-collapsed .main-content {
  margin-left: 70px;
}

body.sidebar-hidden .main-content {
  margin-left: 0;
}

#hamburger { display: none; background-color: #1e5631; color: #fff; border: none; width:40px; height:40px; border-radius:5px; align-items:center; justify-content:center; z-index:1200; }


/* Mobile */
@media (max-width: 768px) {
  #hamburger { display: flex; }
}

@media (max-width:768px){

    .navbar .container-fluid{
        position:relative;
        display:flex;
        justify-content:space-between;
        align-items:center;
        height:70px;
        padding:0 12px;
    }

    #hamburger{
        display:flex !important;
        align-items:center;
        justify-content:center;
        width:40px;
        height:40px;
        z-index:2;
    }

    .navbar-brand{
        position:absolute;
        left:50%;
        top:50%;
        transform:translate(-50%,-50%);
        margin:0;
    }

    .navbar-brand img{
      border-radius:5px;
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

    #sidebarControls{
        display:flex;
    }
}

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

@media (max-width: 768px) {
  .table {
    font-size: 12px;
  }

  .table th,
  .table td {
    padding: 6px !important;
    white-space: nowrap;
  }

  .card {
    padding: 12px !important;
  }

  .table-responsive {
    overflow-x: auto;
  }
}

@media (max-width: 768px) {
  .header-title {
    font-size: 16px;
  }

  .header-subtitle {
    font-size: 11px;
  }

  .btn-sm {
    padding: 4px 8px;
    font-size: 12px;
  }

  h4.header-title {
    line-height: 1.2;
  }
}

/* BARANGAY SUGGESTIONS */
#barangaySuggestions .list-group-item {
  cursor: pointer;
  font-size: 14px;
}

#barangaySuggestions .list-group-item:hover {
  background-color: #198754;
  color: #fff;
}

/* TRUCK SUGGESTIONS */
#truckSuggestions .list-group-item {
  cursor: pointer;
  font-size: 14px;
}

#truckSuggestions .list-group-item:hover {
  background-color: #198754;
  color: #fff;
}

/* MOBILE FILTER LAYOUT */
@media (max-width: 768px) {

  .row.g-2.mb-3 {
    display: flex;
    flex-wrap: nowrap;
    gap: 4px;
    overflow-x: auto;
  }

  .row.g-2.mb-3 > div {
    flex: 1;
    min-width: 90px;
    padding: 0;
  }

  .row.g-2.mb-3 .form-select,
  .row.g-2.mb-3 .form-control {
    font-size: 11px;
    padding: 4px 6px;
    height: 32px;
  }

  #filterDay,
  #filterBarangay,
  #filterType,
  #filterTruck {
    min-width: 0;
  }

  #barangaySuggestions,
  #truckSuggestions {
    font-size: 11px;
  }
}

/* Default (Desktop) */
#mobileFilterContainer {
  display: none;
}

#desktopFilters {
  display: block;
}

#desktopFilters .form-select,
#desktopFilters .form-control {
    height: 34px;
    font-size: 13px;
    padding: 5px 10px;
}

/* MOBILE FILTER DROPDOWN MODE */
@media (max-width: 768px) {

  #mobileFilterContainer {
    display: block !important;
  }

  #desktopFilters {
    display: none !important;
  }

  .mobile-filter-box {
    margin-bottom: 10px;
  }

  .mobile-filter-box .form-select,
  .mobile-filter-box .form-control {
    font-size: 12px;
    height: 34px;
    padding: 5px 8px;
  }
}

@media (max-width: 768px) {

  #mobileBarangaySuggestions,
  #mobileTruckSuggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    z-index: 2000;
  }

}

/* =========================
   INFO ICON + CARD
========================= */

.info-wrapper {
  position: relative;
}

.info-icon-btn {
  width: 24px;
  height: 24px;
  border: none;
  border-radius: 50%;
  background: #e9f7ef;
  color: #198754;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  cursor: pointer;
  transition: 0.2s;
}

.info-icon-btn:hover {
  background: #198754;
  color: #fff;
}

.info-card {
  display: none;
  background: #e9f7ef;
  border: 1px solid #1e5631;
  color: #1e5631;
  font-size: 13px;
  border-radius: 8px;
  padding: 12px 14px;
  line-height: 1.5;
  margin-top: 10px;
}

.info-card.show {
  display: block;
}

@media (max-width: 768px) {
  .header-title {
    font-size: 16px;
  }

  .add-schedule-btn {
    font-size: 12px;
    padding: 4px 10px;
  }
}


@media (max-width: 768px) {
  .header-title {
    font-size: 15px;
  }

  .add-schedule-btn {
    margin-left: auto;
  }

  .d-flex.justify-content-between {
    flex-wrap: nowrap !important;
  }
}

/* =========================
   SCHEDULE SETTINGS
========================= */

.schedule-actions{
    display:flex;
    align-items:center;
    gap:8px;
}

.settings-btn{
    width:34px;
    height:34px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:6px;
}

.preview-table-wrapper{
    max-height:320px;
    overflow-y:auto;
    border:1px solid #dee2e6;
    border-radius:8px;
}

.preview-table thead th{
    position:sticky;
    top:0;
    background:#fff;
    z-index:2;
}

.preview-summary{
    background:#f8f9fa;
    border-radius:8px;
    padding:12px;
    margin-bottom:15px;
}

.preview-summary strong{
    color:#198754;
}

@media(max-width:768px){

    .preview-table-wrapper{
        max-height:260px;
    }

    .settings-btn{
        width:32px;
        height:32px;
    }

}

/* =========================
   HEADER MOBILE STRUCTURE FIX
========================= */

.schedule-header {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* keeps top row clean */
.header-top {
  width: 100%;
}

@media (max-width: 768px) {

  .header-top {
    display: flex;
    flex-wrap: nowrap !important;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
  }

  .schedule-header .d-flex.align-items-center.gap-2 {
    flex-wrap: nowrap;
    min-width: 0;
  }

  .header-title {
    font-size: 14px;
    white-space: nowrap;   /* 🔥 prevents 2-line title */
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .info-icon-btn {
    width: 24px;
    height: 24px;
    flex-shrink: 0; /* 🔥 prevents pushing away */
  }

  .add-schedule-btn {
    font-size: 11px;
    padding: 3px 8px;
    white-space: nowrap;
    flex-shrink: 0;
  }
}

/* ===========================
   CUSTOM TIME PICKER
=========================== */

.custom-time-picker{
    position:relative;
}

.time-input{
    cursor:pointer;
    background:#fff !important;
}

.time-dropdown{

    position:absolute;

    top:100%;
    left:0;
    right:0;

    background:#fff;

    border:1px solid #ced4da;

    border-radius:10px;

    box-shadow:0 10px 25px rgba(0,0,0,.15);

    max-height:260px;

    overflow-y:auto;

    display:none;

    z-index:99999;

    margin-top:4px;
}

.time-dropdown.show{
    display:block;
}

.time-option{

    padding:10px 14px;

    cursor:pointer;

    transition:.15s;

    font-size:14px;
}

.time-option:hover{

    background:#198754;

    color:white;

}

.time-option.selected{

    background:#198754;

    color:white;

    font-weight:600;

}

.time-dropdown::-webkit-scrollbar{
    width:8px;
}

.time-dropdown::-webkit-scrollbar-thumb{
    background:#bbb;
    border-radius:10px;
}

.time-dropdown::-webkit-scrollbar-track{
    background:#f3f3f3;
}

 

</style>

<script>
// AUTH CHECK
const loggedIn = localStorage.getItem("logged_in");
const role = localStorage.getItem("role");

if (loggedIn !== "true") {
    if (navigator.onLine) {
        window.location.href = "login.php";
    } else {
        document.body.innerHTML = "<h3 style='text-align:center;margin-top:50px;'>Offline login required</h3>";
    }
}

if (role !== "admin") {
    window.location.href = "login.php";
}
</script>

</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="height:70px;">
    <div class="container-fluid">

        <!-- Hamburger -->
        <button id="hamburger">
            <i class="bi bi-list"></i>
        </button>

        <!-- Logo -->
        <a class="navbar-brand" href="#">
            <img src="assets/enviromanage-logo.png" style="height:40px;">
        </a>

        <!-- Right Icons -->
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
    <a class="nav-link active" href="admin-collection-schedules.php"><i class="bi bi-map-fill"></i> <span>Collection Schedules</span></a>
    <a class="nav-link" href="admin-trucks-collectors.php"><i class="bi bi-truck-front-fill"></i> <span>Trucks & Collectors</span></a>
    <a class="nav-link" href="admin-collection-records.php"><i class="bi bi-trash-fill"></i> <span>Collection Records</span></a>
    <a class="nav-link" href="#"><i class="bi bi-exclamation-circle-fill"></i> <span>Pickup Requests</span></a>
    <a class="nav-link" href="admin-resident-complaints.php"><i class="bi bi-file-earmark-text-fill"></i> <span>Resident Complaints</span></a>
    <a class="nav-link" href="#"><i class="bi bi-bar-chart-fill"></i> <span>Analytics & Reports</span></a>
    <a class="nav-link" href="admin-announcements.php"><i class="bi bi-megaphone-fill"></i> <span>Announcements</span></a>
    <a class="nav-link" href="admin-news.php"><i class="bi bi-newspaper"></i> <span>News & Articles</span></a>
    <a class="nav-link" href="admin-usermanagement.php"><i class="bi bi-people-fill"></i> <span>User Management</span></a>
    <a class="nav-link" href="#"><i class="bi bi-gear-fill"></i> <span>Settings</span></a>
  </nav>
</div>

<div id="sidebarControls">
  <button id="closeBtn"><i class="bi bi-x-lg"></i></button>
  <button id="toggleBtn"><i class="bi bi-chevron-right"></i></button>
</div>

<div class="main-content">

  <div class="card shadow-sm">

  <!-- CARD BODY -->
  <div class="p-3 p-md-4">

<!-- HEADER (MOBILE FIXED LAYOUT) -->
<div class="schedule-header mb-2">

  <div class="header-top d-flex align-items-center justify-content-between">

    <!-- TITLE + INFO -->
    <div class="d-flex align-items-center gap-2">
      <h4 class="mb-0 header-title d-flex align-items-center">
        <i class="bi bi-calendar-week me-2"></i>
        Collection Schedules
      </h4>

      <button id="scheduleInfoBtn" class="info-icon-btn">
        <i class="bi bi-info-circle"></i>
      </button>
    </div>

   <div class="schedule-actions">

    <button
        class="btn btn-outline-secondary btn-sm settings-btn"
        id="scheduleSettingsBtn"
        data-bs-toggle="modal"
        data-bs-target="#scheduleSettingsModal"
        title="Schedule Settings">

        <i class="bi bi-clock-history"></i>

    </button>

    <button
        class="btn btn-success btn-sm add-schedule-btn"
        data-bs-toggle="modal"
        data-bs-target="#addScheduleModal">

        <i class="bi bi-plus-lg"></i>
        Add

    </button>

</div>

  </div>

</div>

<!-- INFO CARD (BELOW HEADER) -->
<div id="scheduleInfoCard" class="info-card mb-3">

  <h6 class="mb-2">Collection Schedules Info</h6>

  This module is used to manage waste collection schedules.

  <br><br>

  <strong>Features:</strong>
  <ul class="mb-0">
    <li>Add barangay schedules</li>
    <li>Assign trucks per route</li>
    <li>Filter by day, barangay, type, truck</li>
    <li>Edit and delete schedules</li>
  </ul>

</div>

</div>

<!-- DESKTOP FILTERS -->
<div id="desktopFilters" class="mb-3 px-3 d-block">

    <!-- SEARCH -->
    <div class="position-relative mb-2 w-100">

        <input
            type="text"
            id="filterSearch"
            class="form-control"
            placeholder="Search schedules..."
            autocomplete="off">

        <div
            id="searchSuggestions"
            class="list-group position-absolute w-100 shadow-sm"
            style="z-index:1050;max-height:220px;overflow-y:auto;display:none;">
        </div>

    </div>


    <!-- DAY + TYPE SIDE BY SIDE -->
    <div class="row g-2">

        <!-- DAY -->
        <div class="col-6">

            <select id="filterDay" class="form-select">
                <option value="">All Days</option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
            </select>

        </div>


        <!-- TYPE -->
        <div class="col-6">

            <select id="filterType" class="form-select">
                <option value="">All Garbage Types</option>
                <option value="Biodegradable">Biodegradable</option>
                <option value="Non-Biodegradable">Non-Biodegradable</option>
            </select>

        </div>

    </div>

</div>

<!-- MOBILE FILTER -->
<div id="mobileFilterContainer" class="mobile-filter-box mb-3">

  <select id="mobileFilterSelector" class="form-select mb-2">
    <option value="">Select Filter</option>
    <option value="day">Day</option>
    <option value="barangay">Barangay</option>
    <option value="type">Garbage Type</option>
    <option value="truck">Truck</option>
  </select>

  <!-- DAY -->
  <div id="mobileDayFilter" style="display:none;">
    <select id="mobileFilterDay" class="form-select">
      <option value="">All Days</option>
      <option value="Monday">Monday</option>
      <option value="Tuesday">Tuesday</option>
      <option value="Wednesday">Wednesday</option>
      <option value="Thursday">Thursday</option>
      <option value="Friday">Friday</option>
      <option value="Saturday">Saturday</option>
    </select>
  </div>

  <!-- BARANGAY -->
  <div id="mobileBarangayFilter"
       class="position-relative"
       style="display:none;">

    <input type="text"
           id="mobileFilterBarangay"
           class="form-control"
           placeholder="Search Barangay">

    <div id="mobileBarangaySuggestions"
         class="list-group position-absolute w-100 shadow-sm"
         style="z-index:1050; max-height:200px; overflow-y:auto; display:none;">
    </div>

  </div>

  <!-- GARBAGE TYPE -->
  <div id="mobileTypeFilter" style="display:none;">
    <select id="mobileFilterType" class="form-select">
      <option value="">All Garbage Types</option>
      <option value="Biodegradable">Biodegradable</option>
      <option value="Non-Biodegradable">Non-Biodegradable</option>
    </select>
  </div>

  <!-- TRUCK -->
  <div id="mobileTruckFilter"
       class="position-relative"
       style="display:none;">

    <input type="text"
           id="mobileFilterTruck"
           class="form-control"
           placeholder="Search Truck">

    <div id="mobileTruckSuggestions"
         class="list-group position-absolute w-100 shadow-sm"
         style="z-index:1050; max-height:200px; overflow-y:auto; display:none;">
    </div>

  </div>

</div>

    <!-- TABLE -->
    <div class="table-responsive">
      <table class="table table-bordered text-center align-middle mb-0">
        <thead class="table-light">
        <tr>
  <th>ID</th>
  <th>Barangay</th>
  <th>Day</th>
  <th>Time</th>
  <th>Garbage Type</th>
  <th>Truck</th>
  <th>Created At</th>
  <th>Actions</th>
</tr>
        </thead>

        <tbody id="scheduleTable">
          <tr>
            <td colspan="8" class="text-muted py-4">
              No schedules available
            </td>
          </tr>
        </tbody>

      </table>
    </div>


    
    <div id="schedulePagination"
     class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">

    <div class="text-center text-md-start px-4 py-2">

    <small id="schedulePaginationInfo" class="text-muted">
        Showing 0 to 0 of 0 records
    </small>

</div>

   <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap px-4 py-3">

    <button id="schedulePrevBtn"
            class="btn btn-sm btn-outline-success px-3 py-2">
        Previous
    </button>

    <span id="schedulePageNumber"
          class="fw-semibold px-3 py-2">
        Page 1 of 1
    </span>

    <button id="scheduleNextBtn"
            class="btn btn-sm btn-outline-success px-3 py-2">
        Next
    </button>

</div>

</div>

  </div>
</div>

<!-- ADD SCHEDULE MODAL -->
<div class="modal fade" id="addScheduleModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">

      <form id="addScheduleForm">

        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-calendar-plus me-2"></i> Add Schedule
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

      <div class="modal-body">

<div class="mb-3 position-relative">
  <label class="form-label">Barangay</label>
  <input type="text" id="scheduleBarangay" class="form-control" autocomplete="off" required>

  <div id="scheduleBarangaySuggestions"
       class="list-group position-absolute w-100 shadow-sm"
       style="z-index:2000; max-height:200px; overflow-y:auto; display:none;">
  </div>
</div>

  <div class="mb-3">
    <label class="form-label">Day</label>
   <select id="scheduleDay" class="form-select" required>
  <option value="">Select Day</option>
  <option value="Monday">Monday</option>
  <option value="Tuesday">Tuesday</option>
  <option value="Wednesday">Wednesday</option>
  <option value="Thursday">Thursday</option>
  <option value="Friday">Friday</option>
  <option value="Saturday">Saturday</option>
</select>
  </div>

<div class="mb-3">
    <label class="form-label">Start Time</label>

    <div class="custom-time-picker">

        <input
            type="text"
            id="scheduleStartTime"
            class="form-control time-input"
            placeholder="Select Start Time"
            required>

        <div
            id="scheduleStartDropdown"
            class="time-dropdown">
        </div>

    </div>

</div>

<div class="mb-3">
    <label class="form-label">End Time</label>

    <div class="custom-time-picker">

        <input
            type="text"
            id="scheduleEndTime"
            class="form-control time-input"
            placeholder="Select End Time"
          
            required>

        <div
            id="scheduleEndDropdown"
            class="time-dropdown">
        </div>

    </div>

</div>

  <!-- NEW: Garbage Type -->
  <div class="mb-3">
    <label class="form-label">Garbage Type</label>
    <select id="scheduleType" class="form-select" required>
      <option value="">Select Type</option>
      <option value="Biodegradable">Biodegradable</option>
      <option value="Non-Biodegradable">Non-Biodegradable</option>
    </select>
  </div>

  <!-- NEW: Assigned Truck -->
<div class="mb-3">
  <label class="form-label">Assign Truck</label>

  <select id="scheduleTruck" class="form-select" required>
    <option value="">Select a truck</option>
  </select>
</div>

</div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save Schedule</button>
        </div>

      </form>

    </div>
  </div>
</div>

<!-- EDIT SCHEDULE MODAL -->
<div class="modal fade" id="editScheduleModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">

      <form id="editScheduleForm">

        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-pencil-square me-2"></i> Edit Schedule
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <!-- hidden ID -->
          <input type="hidden" id="editScheduleId">

          <div class="mb-3">
            <label class="form-label">Barangay</label>
            <input type="text" id="editScheduleBarangay" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Day</label>
            <select id="editScheduleDay" class="form-select" required>
              <option value="">Select Day</option>
              <option value="Monday">Monday</option>
              <option value="Tuesday">Tuesday</option>
              <option value="Wednesday">Wednesday</option>
              <option value="Thursday">Thursday</option>
              <option value="Friday">Friday</option>
              <option value="Saturday">Saturday</option>
            </select>
          </div>

         <div class="mb-3">

    <label class="form-label">
        Start Time
    </label>

    <div class="custom-time-picker">

        <input
            type="text"
            id="editScheduleStartTime"
            class="form-control time-input"
            placeholder="Select Start Time"
            
            required>

        <div
            id="editScheduleStartDropdown"
            class="time-dropdown">
        </div>

    </div>

</div>

<div class="mb-3">

    <label class="form-label">
        End Time
    </label>

    <div class="custom-time-picker">

        <input
            type="text"
            id="editScheduleEndTime"
            class="form-control time-input"
            placeholder="Select End Time"
            
            required>

        <div
            id="editScheduleEndDropdown"
            class="time-dropdown">
        </div>

    </div>

</div>

          <div class="mb-3">
            <label class="form-label">Garbage Type</label>
            <select id="editScheduleType" class="form-select" required>
              <option value="">Select Type</option>
              <option value="Biodegradable">Biodegradable</option>
              <option value="Non-Biodegradable">Non-Biodegradable</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Assign Truck</label>
            <select id="editScheduleTruck" class="form-select" required>
              <option value="">Loading trucks...</option>
            </select>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            Update Schedule
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<div class="modal fade"
     id="scheduleSettingsModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">

                    <i class="bi bi-clock-history me-2"></i>

                    Schedule Settings

                </h5>

                <button
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="mb-3">

                    <label class="form-label">

                        Maximum Schedule Duration

                    </label>

                    <div class="input-group">

                        <input
    type="number"
    min="1"
    max="24"
    step="1"
    inputmode="numeric"
    class="form-control"
    id="maxScheduleHours"
    onkeydown="return !['.','e','E','+','-',','].includes(event.key);"
    oninput="
        this.value=this.value.replace(/[^0-9]/g,'');
        if(this.value !== ''){
            let v=parseInt(this.value,10);
            if(v>24) this.value=24;
            if(v<1) this.value=1;
        }
    ">

                        <span class="input-group-text">
                            Hours
                        </span>

                    </div>

                </div>

                <div
                    class="alert alert-light border mb-0">

                    Current Maximum:
                    <strong id="currentMaxHours">

                        --

                    </strong>

                </div>

            </div>

            <div class="modal-footer">

                <button
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Cancel

                </button>

                <button
                    class="btn btn-success"
                    id="saveScheduleSettings">

                    Save

                </button>

            </div>

        </div>

    </div>

</div>

<div class="modal fade"
     id="schedulePreviewModal"
     tabindex="-1">

<div class="modal-dialog modal-xl modal-dialog-centered">

<div class="modal-content">

<div class="modal-header">

<h5 class="modal-title">

<i class="bi bi-eye me-2"></i>

Schedule Adjustment Preview

</h5>

<button
class="btn-close"
data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<div class="preview-summary">

<div class="row">

<div class="col-md-4">

<strong id="previewNewLimit">

--

</strong>

<br>

<small>New Maximum</small>

</div>

<div class="col-md-4">

<strong id="previewAffectedCount">

0

</strong>

<br>

<small>Affected Schedules</small>

</div>

<div class="col-md-4">

<strong id="previewTotalReduced">

0 Hours

</strong>

<br>

<small>Total Hours Reduced</small>

</div>

</div>

</div>

<div class="preview-table-wrapper">

<table
class="table table-bordered table-hover preview-table mb-0">

<thead>

<tr>

<th>ID</th>

<th>Barangay</th>

<th>Day</th>

<th>Truck</th>

<th>Old Time</th>

<th>New Time</th>

<th>Reduced</th>

</tr>

</thead>

<tbody id="previewScheduleTable">

<tr>

<td colspan="7" class="text-center text-muted">

No affected schedules.

</td>

</tr>

</tbody>

</table>

</div>

<div class="small text-muted mt-2">

Showing all affected schedules.

Scroll to see more.

</div>

</div>

<div class="modal-footer">

<button
class="btn btn-secondary"
data-bs-dismiss="modal">

Cancel

</button>

<button
class="btn btn-outline-success"
id="keepExistingSchedules">

Keep Existing

</button>

<button
class="btn btn-success"
id="applyScheduleChanges">

Apply Changes

</button>

</div>

</div>

</div>

</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

 const ROWS_PER_PAGE = 10;

let currentPage = 1;
let filteredSchedules = [];
let allSchedules = [];

document.addEventListener('DOMContentLoaded', function () {

  const infoBtn = document.getElementById('scheduleInfoBtn');
  const infoCard = document.getElementById('scheduleInfoCard');

  if (!infoBtn || !infoCard) return;

  infoBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    infoCard.classList.toggle('show');
  });

  document.addEventListener('click', function (e) {
    if (!infoBtn.contains(e.target) && !infoCard.contains(e.target)) {
      infoCard.classList.remove('show');
    }
  });

});

const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleBtn');
const closeBtn = document.getElementById('closeBtn');
const hamburger = document.getElementById('hamburger');
const controls = document.getElementById('sidebarControls');

let sidebarClosed = false;

function isMobile() {
  return window.innerWidth <= 768;
}

function updateLayout() {
  if (!isMobile()) {
    sidebar.classList.remove('hidden');

    if (sidebar.classList.contains('expanded')) {
      document.body.classList.add('sidebar-expanded');
      document.body.classList.remove('sidebar-collapsed');
    } else {
      document.body.classList.add('sidebar-collapsed');
      document.body.classList.remove('sidebar-expanded');
    }

    document.body.classList.remove('sidebar-hidden');
    return;
  }

  if (sidebarClosed) {
    sidebar.classList.add('hidden');
    document.body.classList.add('sidebar-hidden');
    document.body.classList.remove('sidebar-expanded', 'sidebar-collapsed');
    return;
  }

  sidebar.classList.remove('hidden');

  if (sidebar.classList.contains('expanded')) {
    document.body.classList.add('sidebar-expanded');
    document.body.classList.remove('sidebar-collapsed', 'sidebar-hidden');
  } else {
    document.body.classList.add('sidebar-collapsed');
    document.body.classList.remove('sidebar-expanded', 'sidebar-hidden');
  }
}

toggleBtn.onclick = () => {
  if (!isMobile()) {
    sidebar.classList.toggle('expanded');
    document.body.classList.toggle('sidebar-expanded');
    return;
  }

  sidebar.classList.toggle('expanded');
  updateLayout();
};

closeBtn.onclick = () => {
  if (!isMobile()) return;

  sidebarClosed = true;
  sidebar.classList.add('hidden');
  controls.classList.add('hidden');

  updateLayout();
};

hamburger.onclick = () => {
  sidebarClosed = false;
  sidebar.classList.remove('hidden');
  controls.classList.remove('hidden');
  updateLayout();
};

window.addEventListener('resize', updateLayout);

/* =========================
   LOAD TRUCKS
========================= */
async function loadTrucks() {
  try {
    const select = document.getElementById('scheduleTruck');
    const editSelect = document.getElementById('editScheduleTruck');

    // show loading state
    select.innerHTML = '<option value="">Loading trucks...</option>';
    editSelect.innerHTML = '<option value="">Loading trucks...</option>';

    const res = await fetch('admin-fetch-trucks.php');
    const result = await res.json();

    if (!result.success) {
      select.innerHTML = '<option value="">No trucks available</option>';
      editSelect.innerHTML = '<option value="">No trucks available</option>';
      return;
    }

    // reset proper default
    select.innerHTML = '<option value="">Select a truck</option>';
    editSelect.innerHTML = '<option value="">Select a truck</option>';

    result.data.forEach(truck => {
      const label = truck.collector_name
        ? `${truck.plate_no} - ${truck.collector_name}`
        : truck.plate_no;

      const option = document.createElement('option');
      option.value = truck.id;
      option.textContent = label;

      select.appendChild(option.cloneNode(true));
      editSelect.appendChild(option);
    });

  } catch (err) {
    console.error(err);
  }
}

let maxScheduleHours = 3;
let pendingNewLimit = 3;

/* =========================
   FETCH SCHEDULES
========================= */
async function fetchSchedules() {

    try{

        const res = await fetch("admin-fetch-schedules.php");
        const result = await res.json();

        console.log("FETCH RESULT:", result);

        if(!result.success){

            console.error(result.message);

            renderSchedules([]);

            return;

        }

        allSchedules = result.data;

        currentPage = 1;

        renderSchedules(allSchedules);

    }catch(err){

        console.error(err);

    }

}

/* =========================
   RENDER TABLE
========================= */
function renderSchedules(data) {

    filteredSchedules = data;

    const table = document.getElementById("scheduleTable");
    table.innerHTML = "";

    const isFiltering =
        document.getElementById("filterDay").value ||
        document.getElementById("filterSearch").value.trim() ||
        document.getElementById("filterType").value;

    if(data.length===0){

        if(isFiltering){

            table.innerHTML=`
            <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                    <i class="bi bi-search"></i><br>
                    <strong>No schedules found</strong><br>
                    <small>Try adjusting your filters</small>
                </td>
            </tr>`;

        }else{

            table.innerHTML=`
            <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                    <i class="bi bi-calendar-x"></i><br>
                    <strong>No schedules created</strong><br>
                    <small>Add your first collection schedule</small>
                </td>
            </tr>`;

        }

        updatePagination();

        return;
    }

    const start=(currentPage-1)*ROWS_PER_PAGE;
    const end=start+ROWS_PER_PAGE;

    data.slice(start,end).forEach(s=>{

        table.innerHTML+=`
        <tr>
            <td>${s.id}</td>
            <td>${s.barangay}</td>
            <td>${s.day_of_week}</td>
            <td>${s.start_time} - ${s.end_time}</td>
            <td>${s.garbage_type}</td>
            <td>${s.truck}</td>
            <td>${s.created_at}</td>
            <td>

                <button class="btn btn-sm btn-primary"
                    onclick='openEditModal(${JSON.stringify(s)})'>
                    <i class="bi bi-pencil"></i>
                </button>

                <button class="btn btn-sm btn-danger"
                    onclick="deleteSchedule(${s.id})">
                    <i class="bi bi-trash"></i>
                </button>

            </td>
        </tr>`;
    });

    updatePagination();

}

function updatePagination(){

    const totalRecords = filteredSchedules.length;

    const totalPages =
        Math.max(1, Math.ceil(totalRecords / ROWS_PER_PAGE));

    if(currentPage > totalPages){
        currentPage = totalPages;
    }

    const start =
        totalRecords === 0
        ? 0
        : ((currentPage - 1) * ROWS_PER_PAGE) + 1;

    const end =
        Math.min(currentPage * ROWS_PER_PAGE, totalRecords);

    document.getElementById("schedulePaginationInfo").textContent =
        `Showing ${start} to ${end} of ${totalRecords} records`;

    document.getElementById("schedulePageNumber").textContent =
        `Page ${currentPage} of ${totalPages}`;

    document.getElementById("schedulePrevBtn").disabled =
        currentPage === 1;

    document.getElementById("scheduleNextBtn").disabled =
        currentPage === totalPages;

}

document.getElementById("schedulePrevBtn").addEventListener("click",()=>{

    if(currentPage > 1){

        currentPage--;

        renderSchedules(filteredSchedules);

    }

});

document.getElementById("scheduleNextBtn").addEventListener("click",()=>{

    const totalPages =
        Math.ceil(filteredSchedules.length / ROWS_PER_PAGE);

    if(currentPage < totalPages){

        currentPage++;

        renderSchedules(filteredSchedules);

    }

});

function openEditModal(s) {

  document.getElementById('editScheduleId').value = s.id;
  document.getElementById('editScheduleBarangay').value = s.barangay;
  document.getElementById('editScheduleDay').value = s.day_of_week;
// Populate the custom picker
editStartPicker.select(s.start_time);

// Limit the end-time options
limitEndPicker(editStartPicker, editEndPicker);

// Populate the end picker
editEndPicker.select(s.end_time);


document.getElementById("editScheduleEndTime").value =
    s.end_time;
  document.getElementById('editScheduleEndTime').value = s.end_time;
  document.getElementById('editScheduleType').value = s.garbage_type;

  // ✅ IMPORTANT: use truck_id ONLY
  document.getElementById('editScheduleTruck').value = s.truck_id;

  const modal = new bootstrap.Modal(document.getElementById('editScheduleModal'));
  modal.show();
}

async function deleteSchedule(id) {

  const confirm = await Swal.fire({
    title: 'Delete Schedule?',
    text: "This action cannot be undone.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, delete it'
  });

  if (!confirm.isConfirmed) return;

  try {
    const res = await fetch('admin-delete-schedule.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id })
    });

    const data = await res.json();

    if (data.success) {
      Swal.fire('Deleted', data.message, 'success');
      fetchSchedules(); // refresh table
    } else {
      Swal.fire('Error', data.message, 'error');
    }

  } catch (err) {
    Swal.fire('Error', 'Server error', 'error');
  }
}

/* =========================
   FILTER FUNCTION
========================= */
function applyFilters() {

  const day = document
    .getElementById('filterDay')
    .value
    .toLowerCase();

const search = document
    .getElementById("filterSearch")
    .value
    .trim()
    .toLowerCase();

  const type = document
    .getElementById('filterType')
    .value
    .toLowerCase();


  const filtered = allSchedules.filter(s => {

    const matchDay =
      !day ||
      s.day_of_week.toLowerCase().includes(day);


    const matchSearch =
    !search ||
    String(s.id).includes(search) ||
    s.barangay.toLowerCase().includes(search) ||
    s.day_of_week.toLowerCase().includes(search) ||
    s.garbage_type.toLowerCase().includes(search) ||
    s.truck.toLowerCase().includes(search) ||
    s.start_time.toLowerCase().includes(search) ||
    s.end_time.toLowerCase().includes(search) ||
    s.created_at.toLowerCase().includes(search);


    const matchType =
    !type ||
    s.garbage_type.trim().toLowerCase() === type.trim().toLowerCase();


   return (
    matchDay &&
    matchSearch &&
    matchType
);
    

  });


 currentPage = 1;
renderSchedules(filtered);
}

/* =========================
   BARANGAY SUGGESTIONS
========================= */

let barangayList = [];

/* Load barangays.json */
async function loadBarangays() {

  try {

    const res = await fetch('barangays.json');
    const data = await res.json();

    barangayList = Object.keys(data);

  } catch(err) {
    console.error('Failed to load barangays:', err);
  }
}

const scheduleBarangayInput = document.getElementById('scheduleBarangay');
const scheduleBarangaySuggestions = document.getElementById('scheduleBarangaySuggestions');

scheduleBarangayInput.addEventListener('input', function () {

  const value = this.value.trim().toLowerCase();
  scheduleBarangaySuggestions.innerHTML = '';

  if (!value) {
    scheduleBarangaySuggestions.style.display = 'none';
    return;
  }

  const matches = barangayList.filter(brgy =>
    brgy.toLowerCase().includes(value)
  );

  if (matches.length === 0) {
    scheduleBarangaySuggestions.innerHTML = `
      <div class="list-group-item text-muted text-center">
        No barangay found
      </div>
    `;
    scheduleBarangaySuggestions.style.display = 'block';
    return;
  }

  matches.slice(0, 8).forEach(brgy => {

    const item = document.createElement('button');
    item.type = 'button';
    item.className = 'list-group-item list-group-item-action';
    item.textContent = brgy;

    item.onclick = () => {
      scheduleBarangayInput.value = brgy;
      scheduleBarangaySuggestions.style.display = 'none';
    };

    scheduleBarangaySuggestions.appendChild(item);
  });

  scheduleBarangaySuggestions.style.display = 'block';
});

/* hide when clicking outside */
document.addEventListener('click', function (e) {
  if (
    !scheduleBarangayInput.contains(e.target) &&
    !scheduleBarangaySuggestions.contains(e.target)
  ) {
    scheduleBarangaySuggestions.style.display = 'none';
  }
});
const locationInput =
  document.getElementById('filterSearch');

const locationSuggestions =
  document.getElementById('searchSuggestions');


locationInput.addEventListener('input', function(){

  const value = this.value.trim().toLowerCase();

  locationSuggestions.innerHTML = "";


  if(!value){
    locationSuggestions.style.display="none";
    applyFilters();
    return;
  }


  let results = [];


  // barangay matches
  barangayList.forEach(brgy=>{
    if(brgy.toLowerCase().includes(value)){
      results.push({
        type:"Barangay",
        text:brgy
      });
    }
  });


  // truck matches
  truckList.forEach(truck=>{
    if(truck.text.toLowerCase().includes(value)){
      results.push({
        type:"Truck",
        text:truck.text
      });
    }
  });



  if(results.length === 0){

    locationSuggestions.innerHTML=`
      <div class="list-group-item text-muted text-center">
        No result found
      </div>
    `;

    locationSuggestions.style.display="block";
    return;
  }



  results.slice(0,10).forEach(item=>{

    const btn=document.createElement('button');

    btn.type="button";
    btn.className=
      "list-group-item list-group-item-action";

    btn.textContent =
      `${item.text} (${item.type})`;


    btn.onclick=()=>{

      locationInput.value=item.text;

      locationSuggestions.style.display="none";

      applyFilters();
    };


    locationSuggestions.appendChild(btn);

  });


  locationSuggestions.style.display="block";

});



document.addEventListener('click',function(e){

 if(
   !locationInput.contains(e.target) &&
   !locationSuggestions.contains(e.target)
 ){

   locationSuggestions.style.display="none";

 }

});
/* =========================
   MOBILE FILTER SWITCHER
========================= */

const mobileSelector =
  document.getElementById('mobileFilterSelector');

const mobileDay =
  document.getElementById('mobileDayFilter');

const mobileBarangay =
  document.getElementById('mobileBarangayFilter');

const mobileType =
  document.getElementById('mobileTypeFilter');

const mobileTruck =
  document.getElementById('mobileTruckFilter');

mobileSelector.addEventListener('change', function () {

  mobileDay.style.display = 'none';
  mobileBarangay.style.display = 'none';
  mobileType.style.display = 'none';
  mobileTruck.style.display = 'none';

  if (this.value === 'day') {
    mobileDay.style.display = 'block';
  }

  if (this.value === 'barangay') {
    mobileBarangay.style.display = 'block';
  }

  if (this.value === 'type') {
    mobileType.style.display = 'block';
  }

  if (this.value === 'truck') {
    mobileTruck.style.display = 'block';
  }

});

/* =========================
   FILTER EVENTS
========================= */

document.getElementById('filterDay')
  .addEventListener('change', applyFilters);

document.getElementById('filterType')
  .addEventListener('change', applyFilters);

  document.getElementById('filterSearch')
  .addEventListener('input', applyFilters);

  /* =========================
   MOBILE BARANGAY SUGGESTIONS
========================= */

const mobileBarangayInput =
  document.getElementById('mobileFilterBarangay');

const mobileBarangaySuggestions =
  document.getElementById('mobileBarangaySuggestions');

mobileBarangayInput.addEventListener('input', function () {

  const value = this.value.trim().toLowerCase();

  mobileBarangaySuggestions.innerHTML = '';

  if (!value) {
    mobileBarangaySuggestions.style.display = 'none';
    return;
  }

  const matches = barangayList.filter(brgy =>
    brgy.toLowerCase().includes(value)
  );

 if (matches.length === 0) {
  mobileBarangaySuggestions.innerHTML = `
    <div class="list-group-item text-muted text-center">
      No barangay found
    </div>
  `;
  mobileBarangaySuggestions.style.display = 'block';
  return;
}

  matches.slice(0, 8).forEach(brgy => {

    const item = document.createElement('button');

    item.type = 'button';
    item.className = 'list-group-item list-group-item-action';
    item.textContent = brgy;

    item.onclick = () => {

      mobileBarangayInput.value = brgy;

      mobileBarangaySuggestions.style.display = 'none';

      document.getElementById('filterSearch').value = brgy;

      applyFilters();
    };

    mobileBarangaySuggestions.appendChild(item);
  });

  mobileBarangaySuggestions.style.display = 'block';
});

/* =========================
   MOBILE TRUCK SUGGESTIONS
========================= */

const mobileTruckInput =
  document.getElementById('mobileFilterTruck');

const mobileTruckSuggestions =
  document.getElementById('mobileTruckSuggestions');

mobileTruckInput.addEventListener('input', function () {

  const value = this.value.trim().toLowerCase();

  mobileTruckSuggestions.innerHTML = '';

  if (!value) {
    mobileTruckSuggestions.style.display = 'none';
    return;
  }

  const matches = truckList.filter(truck =>
    truck.text.toLowerCase().includes(value)
  );

 if (matches.length === 0) {
  mobileTruckSuggestions.innerHTML = `
    <div class="list-group-item text-muted text-center">
      No trucks found
    </div>
  `;
  mobileTruckSuggestions.style.display = 'block';
  return;
}

  matches.slice(0, 8).forEach(truck => {

    const item = document.createElement('button');

    item.type = 'button';
    item.className = 'list-group-item list-group-item-action';
    item.textContent = truck.text;

    item.onclick = () => {

      mobileTruckInput.value = truck.text;

      mobileTruckSuggestions.style.display = 'none';

      document.getElementById('filterSearch').value = truck.text;

      applyFilters();
    };

    mobileTruckSuggestions.appendChild(item);
  });

  mobileTruckSuggestions.style.display = 'block';
});

/* =========================
   HIDE MOBILE SUGGESTIONS
========================= */

document.addEventListener('click', function(e){

  if (
    !mobileBarangayInput.contains(e.target) &&
    !mobileBarangaySuggestions.contains(e.target)
  ) {
    mobileBarangaySuggestions.style.display = 'none';
  }

  if (
    !mobileTruckInput.contains(e.target) &&
    !mobileTruckSuggestions.contains(e.target)
  ) {
    mobileTruckSuggestions.style.display = 'none';
  }

});

/* =========================
   MOBILE FILTER EVENTS
========================= */

document.getElementById('mobileFilterDay')
  .addEventListener('change', function () {

    document.getElementById('filterDay').value = this.value;

    applyFilters();
});

document.getElementById('mobileFilterType')
  .addEventListener('change', function () {

    document.getElementById('filterType').value = this.value;

    applyFilters();
});

/* =========================
   ADD SCHEDULE
========================= */
document.getElementById('addScheduleForm').addEventListener('submit', async function(e){
  e.preventDefault();

  const barangay = document.getElementById('scheduleBarangay').value.trim();
  const day_of_week = document.getElementById('scheduleDay').value;
const start_time =
document.getElementById("scheduleStartTime").dataset.value;

const end_time =
document.getElementById("scheduleEndTime").dataset.value;
  const garbage_type = document.getElementById('scheduleType').value;
  const truck_id = document.getElementById('scheduleTruck').value;

  console.log({
  barangay,
  day_of_week,
  start_time,
  end_time,
  garbage_type,
  truck_id
});

if (start_time >= end_time) {
  Swal.fire('Invalid Time', 'Start time must be earlier than end time.', 'warning');
  return;
}

  if(!barangay || !day_of_week || !start_time || !end_time || !garbage_type || !truck_id){
    Swal.fire('Missing Fields', 'Please complete all fields.', 'warning');
    return;
  }

  try {
    const res = await fetch('admin-add-schedule.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    barangay,
    day_of_week,
    start_time,
    end_time,
    garbage_type,
    truck_id
  })
});

    const data = await res.json();

    if(data.success){
      Swal.fire('Success', 'Schedule added successfully.', 'success');

      document.getElementById('addScheduleForm').reset();

      bootstrap.Modal.getInstance(
        document.getElementById('addScheduleModal')
      ).hide();

      fetchSchedules(); // refresh table
    } else {
      Swal.fire('Error', data.message, 'error');
    }

  } catch(err){
    Swal.fire('Error', 'Server error.', 'error');
  }
});

/* =========================
   UPDATE SCHEDULE
========================= */

document.getElementById('editScheduleForm').addEventListener('submit', async function(e){
  e.preventDefault();

  const id = document.getElementById('editScheduleId').value;
  const barangay = document.getElementById('editScheduleBarangay').value.trim();
  const day_of_week = document.getElementById('editScheduleDay').value;
const start_time =
document.getElementById("editScheduleStartTime").dataset.value;

const end_time =
document.getElementById("editScheduleEndTime").dataset.value;
  const garbage_type = document.getElementById('editScheduleType').value;
  const truck_id = document.getElementById('editScheduleTruck').value;

  if (start_time >= end_time) {
    Swal.fire('Invalid Time', 'Start time must be earlier than end time.', 'warning');
    return;
  }

  try {
    const res = await fetch('admin-update-schedule.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        id,
        barangay,
        day_of_week,
        start_time,
        end_time,
        garbage_type,
        truck_id
      })
    });

    const data = await res.json();

    if (data.success) {
      Swal.fire('Updated', 'Schedule updated successfully.', 'success');

      bootstrap.Modal.getInstance(
        document.getElementById('editScheduleModal')
      ).hide();

      fetchSchedules();
    } else {
      Swal.fire('Error', data.message || 'Update failed', 'error');
    }

  } catch (err) {
    Swal.fire('Error', 'Server error.', 'error');
  }
});

/* =========================
   SCHEDULE SETTINGS
========================= */

async function loadScheduleSettings(){

    try{

        const res =
            await fetch(
                "admin-fetch-schedule-settings.php"
            );

        const data =
            await res.json();

        if(!data.success){

            Swal.fire(
                "Error",
                data.message,
                "error"
            );

            return;
        }

        maxScheduleHours =
            parseInt(
                data.max_schedule_hours
            );

        document.getElementById(
            "currentMaxHours"
        ).textContent =
            maxScheduleHours + " Hours";

        document.getElementById(
            "maxScheduleHours"
        ).value =
            maxScheduleHours;

    }
    catch(err){

        console.error(err);

        Swal.fire(
            "Error",
            "Unable to load schedule settings.",
            "error"
        );

    }

}

//time manipulation function

function addHoursToTime(time, hours){

    let parts = time.split(":");

    let h = parseInt(parts[0]);
    let m = parseInt(parts[1]);

    let date = new Date();

    date.setHours(h);
    date.setMinutes(m);

    date.setHours(date.getHours()+hours);

    let hh = String(date.getHours()).padStart(2,"0");
    let mm = String(date.getMinutes()).padStart(2,"0");

    return hh+":"+mm;

}

//duration calculation function
function getDuration(start,end){

    const s = start.split(":");
    const e = end.split(":");

    let startMinutes =
        parseInt(s[0])*60+
        parseInt(s[1]);

    let endMinutes =
        parseInt(e[0])*60+
        parseInt(e[1]);

    return (endMinutes-startMinutes)/60;

}

/* =========================
   PREVIEW SCHEDULE UPDATES
========================= */

async function showSchedulePreview(newLimit){

    pendingNewLimit = newLimit;

    try{

        const res = await fetch(
            "admin-preview-schedule-updates.php",
            {
                method:"POST",
                headers:{
                    "Content-Type":"application/json"
                },
                body:JSON.stringify({
                    max_schedule_hours:newLimit
                })
            }
        );

        const data = await res.json();

        if(!data.success){

            Swal.fire(
                "Error",
                data.message,
                "error"
            );

            return;

        }

        document.getElementById(
            "previewNewLimit"
        ).textContent =
            data.new_limit + " Hours";

        document.getElementById(
            "previewAffectedCount"
        ).textContent =
            data.affected_count;

        document.getElementById(
            "previewTotalReduced"
        ).textContent =
            data.total_hours_reduced + " Hours";

        const tbody =
            document.getElementById(
                "previewScheduleTable"
            );

        tbody.innerHTML = "";

        if(data.schedules.length===0){

            tbody.innerHTML = `
            <tr>
                <td colspan="7"
                    class="text-center text-muted py-4">

                    <i class="bi bi-check-circle fs-3 text-success"></i>

                    <br>

                    No schedules exceed
                    the selected maximum duration.

                </td>
            </tr>`;

        }else{

            data.schedules.forEach(s=>{

                tbody.innerHTML += `
                <tr>

                    <td>${s.id}</td>

                    <td>${s.barangay}</td>

                    <td>${s.day_of_week}</td>

                    <td>${s.truck}</td>

                    <td>

                        ${s.start_time}

                        -

                        ${s.old_end_time}

                    </td>

                    <td>

                        ${s.start_time}

                        -

                        <span class="text-danger fw-bold">

                            ${s.new_end_time}

                        </span>

                    </td>

                    <td>

                        -${s.hours_reduced} hr

                    </td>

                </tr>
                `;

            });

        }

        bootstrap.Modal.getInstance(
            document.getElementById(
                "scheduleSettingsModal"
            )
        ).hide();

        new bootstrap.Modal(
            document.getElementById(
                "schedulePreviewModal"
            )
        ).show();

    }
    catch(err){

        console.error(err);

        Swal.fire(
            "Error",
            "Unable to generate preview.",
            "error"
        );

    }

}

//save new limit
document
.getElementById("saveScheduleSettings")
.addEventListener("click", async function(){

    const input =
        document.getElementById(
            "maxScheduleHours"
        );

    const newLimit =
        Number(input.value);

    if(
        !Number.isInteger(newLimit) ||
        newLimit < 1 ||
        newLimit > 24
    ){

        Swal.fire(
            "Invalid Duration",
            "Please enter a whole number between 1 and 24.",
            "warning"
        );

        input.focus();

        return;

    }

    await showSchedulePreview(newLimit);

});

//keep existing schedules
document
.getElementById("keepExistingSchedules")
.addEventListener("click", async function(){

    try{

        const res = await fetch(
            "admin-save-schedule-settings.php",
            {
                method:"POST",
                headers:{
                    "Content-Type":"application/json"
                },
                body:JSON.stringify({
                    max_schedule_hours:pendingNewLimit
                })
            }
        );

        const data = await res.json();

        if(!data.success){

            Swal.fire(
                "Error",
                data.message,
                "error"
            );

            return;
        }

        maxScheduleHours = pendingNewLimit;

        document.getElementById(
            "currentMaxHours"
        ).textContent =
            maxScheduleHours + " Hours";

        bootstrap.Modal.getInstance(
            document.getElementById(
                "schedulePreviewModal"
            )
        ).hide();

        Swal.fire(
            "Saved",
            "Only future schedules will use the new maximum duration.",
            "success"
        );

    }catch(err){

        console.error(err);

        Swal.fire(
            "Error",
            "Unable to save settings.",
            "error"
        );

    }

});

//apply schedule changes
document
.getElementById("applyScheduleChanges")
.addEventListener("click", async function(){

    const confirm = await Swal.fire({

        title:"Apply Changes?",

        text:
        "All schedules exceeding the new maximum duration will be shortened automatically.",

        icon:"warning",

        showCancelButton:true,

        confirmButtonText:"Apply",

        confirmButtonColor:"#198754"

    });

    if(!confirm.isConfirmed)
        return;

    try{

        const res = await fetch(
            "admin-apply-schedule-updates.php",
            {

                method:"POST",

                headers:{
                    "Content-Type":"application/json"
                },

                body:JSON.stringify({

                    max_schedule_hours:
                    pendingNewLimit

                })

            }
        );

        const data = await res.json();

        if(!data.success){

            Swal.fire(
                "Error",
                data.message,
                "error"
            );

            return;

        }

        maxScheduleHours =
            pendingNewLimit;

        bootstrap.Modal.getInstance(
            document.getElementById(
                "schedulePreviewModal"
            )
        ).hide();

        document.getElementById(
            "currentMaxHours"
        ).textContent =
            maxScheduleHours + " Hours";

        fetchSchedules();

        Swal.fire({

            icon:"success",

            title:"Completed",

            html:`
                <b>${data.updated}</b>
                schedules were updated.
            `

        });

    }
    catch(err){

        console.error(err);

        Swal.fire(
            "Error",
            "Unable to update schedules.",
            "error"
        );

    }

});

/* =========================
   MAX SCHEDULE HOURS INPUT
========================= */

const maxHoursInput = document.getElementById("maxScheduleHours");

maxHoursInput.addEventListener("input", function () {

    this.value = this.value.replace(/[^0-9]/g, "");

    if (this.value === "") return;

    let value = parseInt(this.value, 10);

    if (value > 24) value = 24;
    if (value < 1) value = 1;

    this.value = value;

});

maxHoursInput.addEventListener("keydown", function (e) {

    if ([".", ",", "e", "E", "+", "-"].includes(e.key)) {
        e.preventDefault();
    }

});

function timeToMinutes(time){

    const [h,m]=time.split(":").map(Number);

    return h*60+m;

}

function minutesToTime(minutes){

    const h=Math.floor(minutes/60);
    const m=minutes%60;

    return String(h).padStart(2,"0")+":"+
           String(m).padStart(2,"0");

}

function validateEndTime(startInput, endInput) {
    if (!startInput.value || !endInput.value) return;

    const start = new Date();
    const end = new Date();

    const [sh, sm] = startInput.value.split(':').map(Number);
    const [eh, em] = endInput.value.split(':').map(Number);

    start.setHours(sh, sm, 0, 0);
    end.setHours(eh, em, 0, 0);

    // End must be after start
    if (end <= start) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid End Time',
            text: 'End time must be later than the start time.'
        });
        endInput.value = '';
        return;
    }

    // Check maximum duration
    const durationHours = (end - start) / (1000 * 60 * 60);

    if (durationHours > maxScheduleHours) {
        Swal.fire({
            icon: 'warning',
            title: 'Maximum Duration Exceeded',
            text: `The schedule cannot exceed ${maxScheduleHours} hour(s).`
        });
        endInput.value = '';
    }
}

/* ===========================================
   CUSTOM TIME PICKER ENGINE
=========================================== */

class TimePicker {

    constructor(inputId, dropdownId){

        this.input =
            document.getElementById(inputId);

        this.dropdown =
            document.getElementById(dropdownId);

        this.selected = null;

        this.build();

        this.events();

    }

    build(){

        this.dropdown.innerHTML = "";

        for(let h=0;h<24;h++){

            for(let m=0;m<60;m++){

                const value =
                    String(h).padStart(2,"0")
                    +":"
                    +
                    String(m).padStart(2,"0");

                const option =
                    document.createElement("div");

                option.className="time-option";

                option.dataset.value=value;

                option.textContent=
                    this.format(value);

                option.onclick=()=>{

                    this.select(value);

                };

                this.dropdown.appendChild(option);

            }

        }

    }

    format(value){

        const [h,m]=value.split(":");

        let hour=parseInt(h);

        const ampm=
            hour>=12?"PM":"AM";

        hour=hour%12||12;

        return hour+":"+m+" "+ampm;

    }

    select(value){

        this.selected=value;

        this.input.value = this.format(value);

        this.input.dataset.value = value;

        this.close();

        this.highlight(value);

        this.input.dispatchEvent(
            new Event("change")
        );

    }

    highlight(value){

        this.dropdown
            .querySelectorAll(".time-option")
            .forEach(opt=>{

                opt.classList.remove("selected");

                if(opt.dataset.value===value){

                    opt.classList.add("selected");

                }

            });

    }

   search(keyword){

    // Allow only numbers, colon and spaces
    keyword = keyword
        .replace(/[^0-9: ]/g, "")
        .trim()
        .toLowerCase();

    this.dropdown
        .querySelectorAll(".time-option")
        .forEach(option => {

            // Example: "1:00 am"
            const label = option.textContent.toLowerCase();

            // Remove am/pm for searching
            const searchable = label
                .replace(/\s?(am|pm)/, "")
                .trim();

            option.style.display =
                keyword === "" || searchable === keyword
                    ? "block"
                    : "none";

        });

}
    open(){

        document
            .querySelectorAll(".time-dropdown.show")
            .forEach(d=>d.classList.remove("show"));

        this.dropdown.classList.add("show");

        if(this.selected){

            const item=
                this.dropdown.querySelector(
                    `[data-value="${this.selected}"]`
                );

            if(item){

                item.scrollIntoView({
                    block:"center"
                });

            }

        }

    }

    close(){

        this.dropdown.classList.remove("show");

    }

    events(){

        this.input.addEventListener("focus", () => {
    this.open();
});

this.input.addEventListener("click", () => {
    this.open();
});

this.input.addEventListener("input", () => {

    // Allow only digits and colon
    this.input.value = this.input.value.replace(/[^0-9:]/g, "");

    // Only allow one colon
    const parts = this.input.value.split(":");
    if (parts.length > 2) {
        this.input.value = parts[0] + ":" + parts.slice(1).join("");
    }

    // Limit HH to 2 digits and MM to 2 digits
    if (this.input.value.includes(":")) {
        let [hh, mm] = this.input.value.split(":");

        hh = hh.substring(0, 2);
        mm = mm.substring(0, 2);

        this.input.value = hh + ":" + mm;
    } else {
        this.input.value = this.input.value.substring(0, 2);
    }

    this.search(this.input.value);
});

this.input.addEventListener("keypress", function(e) {

    const allowed = /[0-9:]/;

    if (!allowed.test(e.key)) {
        e.preventDefault();
    }

});

this.input.addEventListener("keydown", (e) => {

    if (
        e.key.length === 1 &&
        !/[0-9:]/.test(e.key)
    ) {
        e.preventDefault();
        return;
    }

    if (e.key === "Escape") {
        this.close();
    }

    if (e.key === "Enter") {

        const first = this.dropdown.querySelector(
            ".time-option:not([style*='display: none'])"
        );

        if (first) {
            first.click();
        }

        e.preventDefault();
    }

});

    }

    filter(min,max){

    const minMinutes = toMinutes(min);
    const maxMinutes = toMinutes(max);

    this.dropdown
        .querySelectorAll(".time-option")
        .forEach(opt=>{

            const value =
                toMinutes(opt.dataset.value);

            if(
                value >= minMinutes &&
                value <= maxMinutes
            ){

                opt.style.display="block";

            }else{

                opt.style.display="none";

            }

        });

    this.close();

    this.input.value="";

    this.selected=null;

}

    reset(){

        this.dropdown
            .querySelectorAll(".time-option")
            .forEach(opt=>{

                opt.style.display="block";

            });

    }

}

function toMinutes(time){

    const [h,m]=time
        .split(":")
        .map(Number);

    return h*60+m;

}

function toTime(minutes){

    const h=
        Math.floor(minutes/60);

    const m=
        minutes%60;

    return String(h)
        .padStart(2,"0")
        +":"
        +
        String(m)
        .padStart(2,"0");

}

const startPicker =
    new TimePicker(
        "scheduleStartTime",
        "scheduleStartDropdown"
);

const endPicker =
    new TimePicker(
        "scheduleEndTime",
        "scheduleEndDropdown"
);

const editStartPicker =
    new TimePicker(
        "editScheduleStartTime",
        "editScheduleStartDropdown"
);

const editEndPicker =
    new TimePicker(
        "editScheduleEndTime",
        "editScheduleEndDropdown"
);

function limitEndPicker(startPicker,endPicker){

    if(!startPicker.input.value){

        endPicker.reset();

        return;

    }

   const startValue = startPicker.input.dataset.value;

if (!startValue) {
    endPicker.reset();
    return;
}

const startMinutes = toMinutes(startValue);

    const min =
        startMinutes + 1;

    const max =
        startMinutes +
        (maxScheduleHours * 60);

    endPicker.filter(

        toTime(min),

        toTime(max)

    );

}

startPicker.input.addEventListener(

    "change",

    ()=>{

        limitEndPicker(
            startPicker,
            endPicker
        );

    }

);

editStartPicker.input.addEventListener(

    "change",

    ()=>{

        limitEndPicker(
            editStartPicker,
            editEndPicker
        );

    }

);

document.addEventListener("click",function(e){

    if(!e.target.closest(".custom-time-picker")){

        document
            .querySelectorAll(".time-dropdown")
            .forEach(d=>{

                d.classList.remove("show");

            });

    }

});


/* INIT */
loadBarangays();

loadTrucks();

fetchSchedules();

loadScheduleSettings();

updateLayout();
</script>

</body>
</html>