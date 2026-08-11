<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
    
}
date_default_timezone_set('Asia/Manila');

require_once 'db.php';

$assignmentPersonnel = [
    'collectors' => [],
    'secretaries' => []
];

try {

    /*
    |--------------------------------------------------------------------------
    | FETCH APPROVED COLLECTORS
    |--------------------------------------------------------------------------
    */

    $collectorStmt = $conn->prepare("
    SELECT
        id,
        first_name,
        middle_initial,
        last_name,
        barangay
    FROM users
    WHERE role = 'collector'
    ORDER BY first_name ASC, last_name ASC
");

    if ($collectorStmt) {

        $collectorStmt->execute();

        $collectorResult = $collectorStmt->get_result();

        while ($row = $collectorResult->fetch_assoc()) {

            $middleInitial = trim($row['middle_initial'] ?? '');

            $row['name'] =
                trim($row['first_name']) .
                ($middleInitial !== ''
                    ? ' ' . $middleInitial . '.'
                    : '') .
                ' ' .
                trim($row['last_name']);

            $assignmentPersonnel['collectors'][] = $row;
        }

        $collectorStmt->close();
    }


    /*
    |--------------------------------------------------------------------------
    | FETCH APPROVED BARANGAY SECRETARIES
    |--------------------------------------------------------------------------
    */

    $secretaryStmt = $conn->prepare("
    SELECT
        id,
        first_name,
        middle_initial,
        last_name,
        barangay
    FROM users
    WHERE role = 'barangay_secretary'
    ORDER BY barangay ASC, first_name ASC
");

    if ($secretaryStmt) {

        $secretaryStmt->execute();

        $secretaryResult = $secretaryStmt->get_result();

        while ($row = $secretaryResult->fetch_assoc()) {

            $middleInitial = trim($row['middle_initial'] ?? '');

            $row['name'] =
                trim($row['first_name']) .
                ($middleInitial !== ''
                    ? ' ' . $middleInitial . '.'
                    : '') .
                ' ' .
                trim($row['last_name']);

            $assignmentPersonnel['secretaries'][] = $row;
        }

        $secretaryStmt->close();
    }


} catch (Throwable $e) {

    error_log(
        "Assignment personnel loading error: " .
        $e->getMessage()
    );

    $assignmentPersonnel = [
        'collectors' => [],
        'secretaries' => []
    ];
}

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

.complaint-swal-popup {
    border-radius: 14px !important;
    padding: 24px !important;
}

.complaint-swal-popup .swal2-html-container {
    margin-top: 8px !important;
}

.complaint-swal-confirm {
    border-radius: 7px !important;
    padding: 8px 22px !important;
    font-size: 14px !important;
}

@media (max-width: 576px) {

    .complaint-swal-popup {
        width: calc(100% - 24px) !important;
        padding: 18px !important;
    }

    .complaint-swal-popup .swal2-html-container {
        padding: 0 !important;
    }

}

/* =========================
   COMPLAINT VIEW MODAL
========================= */

.complaint-view-popup {
    width: min(850px, calc(100% - 24px)) !important;
    max-height: 90vh !important;
    border-radius: 14px !important;
    padding: 0 !important;
    overflow: hidden !important;
}

.complaint-view-popup .swal2-html-container {
    margin: 0 !important;
    padding: 0 !important;
    overflow-y: auto !important;
    max-height: calc(90vh - 20px);
}

.complaint-modal-header {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 15px 20px;
    border-bottom: 1px solid #e5e5e5;
    color: #1e5631;
    font-size: 18px;
    font-weight: 600;
}

.complaint-modal-body {
    padding: 16px 20px 20px;
    text-align: left;
    font-size: 13px;
}

.complaint-ticket {
    background: #f0f8f3;
    border: 1px solid #d7eadf;
    border-radius: 8px;
    padding: 9px 12px;
    margin-bottom: 14px;
}

.complaint-section-title {
    color: #1e5631;
    font-weight: 600;
    font-size: 13px;
    margin-bottom: 7px;
}

.complaint-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 9px;
    margin-bottom: 14px;
}

.complaint-info-box {
    background: #f8f9fa;
    border-radius: 7px;
    padding: 9px 10px;
}

.complaint-info-label {
    color: #6c757d;
    font-size: 10px;
    margin-bottom: 2px;
}

.complaint-info-value {
    font-weight: 500;
    font-size: 13px;
    word-break: break-word;
}


/* =========================
   COMPLAINT DESCRIPTION
========================= */

.complaint-description-box {
    background: #f8f9fa;
    border-radius: 7px;
    padding: 10px 12px;
    margin-bottom: 14px;
    text-align: left;
}

/* Fixed header */
.complaint-description-box .complaint-info-label {
    display: block;
    margin: 0 0 6px 0;
    padding: 0;
    line-height: 1.2;
    text-align: left;
}

/* SCROLLABLE DESCRIPTION */
.complaint-description-text {
    height: 150px;
    max-height: 150px;

    overflow-y: scroll;
    overflow-x: hidden;

    padding: 0 6px 0 0;
    margin: 0;

    white-space: pre-wrap;
    word-break: break-word;
    line-height: 1.5;
    text-align: left;
}

/* Scrollbar */
.complaint-description-text::-webkit-scrollbar {
    width: 6px;
}

.complaint-description-text::-webkit-scrollbar-track {
    background: #eeeeee;
    border-radius: 10px;
}

.complaint-description-text::-webkit-scrollbar-thumb {
    background: #bdbdbd;
    border-radius: 10px;
}

.complaint-description-text::-webkit-scrollbar-thumb:hover {
    background: #999;
}

/* Firefox */
.complaint-description-text {
    scrollbar-width: thin;
    scrollbar-color: #bdbdbd #eeeeee;
}



/* =========================
   COMPLAINT REMARKS
========================= */

.complaint-remarks {
    background: #fff9e6;
    border: 1px solid #f1df9b;
    border-radius: 7px;
    padding: 10px;

    height: 130px;
    max-height: 130px;
    overflow-y: auto;

    white-space: pre-wrap;
    word-break: break-word;
    line-height: 1.5;

    margin: 0;
}


.complaint-assignment {
    background: #f8f9fa;
    border: 1px solid #e5e5e5;
    border-radius: 9px;
    padding: 12px;
    margin-top: 4px;
}

.assignment-type-row {
    display: flex;
    gap: 6px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.assignment-type-btn {
    border: 1px solid #ced4da;
    background: #fff;
    color: #495057;
    border-radius: 6px;
    padding: 5px 12px;
    font-size: 12px;
    cursor: pointer;
    transition: .15s;
}

.assignment-type-btn:hover {
    border-color: #1e5631;
    color: #1e5631;
}

.assignment-type-btn.active {
    background: #1e5631;
    border-color: #1e5631;
    color: #fff;
}

.assignment-field {
    display: none;
}

.assignment-field.active {
    display: block;
}

.assignment-field .form-label {
    font-size: 11px;
    color: #6c757d;
    margin-bottom: 4px;
}

.assignment-field .form-select,
.assignment-field .form-control {
    font-size: 13px;
    min-height: 36px;
}

.assignment-loading {
    font-size: 12px;
    color: #6c757d;
    padding: 8px 0;
}

.assignment-current {
    font-size: 11px;
    color: #6c757d;
    margin-top: 6px;
}

.complaint-status-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 14px;
}


.complaint-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid #e5e5e5;
}

.complaint-modal-footer button {
    font-size: 12px;
    padding: 6px 16px;
    border-radius: 6px;
}

@media (max-width: 576px) {

    .complaint-view-popup {
        width: calc(100% - 16px) !important;
        max-height: 94vh !important;
    }

    .complaint-view-popup .swal2-html-container {
        max-height: calc(94vh - 10px);
    }

    .complaint-modal-header {
    position: sticky;
    top: 0;
    z-index: 100;

    padding: 13px 50px 13px 15px;

    font-size: 16px;

    background: #fff;
}

    .complaint-modal-body {
        padding: 13px 15px 15px;
        font-size: 12px;
    }

    .complaint-info-grid {
        grid-template-columns: 1fr;
    }

    .assignment-type-btn {
        flex: 1;
        padding: 6px 8px;
    }
}

/* =========================
   STICKY COMPLAINT HEADER
========================= */

.complaint-modal-header {
    position: sticky;
    top: 0;
    z-index: 100;
    display: flex;
    align-items: center;
    gap: 9px;

    padding: 15px 52px 15px 20px;

    border-bottom: 1px solid #e5e5e5;
    background: #fff;

    color: #1e5631;
    font-size: 18px;
    font-weight: 600;
}

/* FIXED/VISIBLE EXIT BUTTON */
.complaint-modal-close {
    position: absolute;

    top: 50%;
    right: 12px;

    transform: translateY(-50%);

    width: 32px;
    height: 32px;

    border: none;
    background: transparent;

    color: #6c757d;

    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    cursor: pointer;

    font-size: 16px;

    z-index: 101;

    transition: background .15s, color .15s;
}

.complaint-modal-close:hover {
    background: #f1f1f1;
    color: #dc3545;
}

.complaint-modal-close:focus {
    outline: none;
    box-shadow: none;
}

.complaint-modal-close:hover {
    background: #f1f1f1;
    color: #dc3545;
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
    <th>Location</th>
    <th>Complaint</th>
    <th>Validated By</th>
    <th>Validated Date</th>
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
   COMPLAINT PAGE
========================= */

let complaints = [];
let filteredComplaints = [];

let currentPage = 1;
const ITEMS_PER_PAGE = 10;

const tbody = document.getElementById("complaintTableBody");

const searchInput = document.getElementById("complaintSearch");
const statusFilter = document.getElementById("statusFilter");

const prevBtn = document.getElementById("complaintPrevBtn");
const nextBtn = document.getElementById("complaintNextBtn");
const pageNumber = document.getElementById("complaintPageNumber");
const paginationInfo = document.getElementById("complaintPaginationInfo");

/* =========================================================
   ASSIGNMENT PERSONNEL FROM DATABASE
   ========================================================= */

const assignmentPersonnel = <?= json_encode(
    $assignmentPersonnel,
    JSON_HEX_TAG |
    JSON_HEX_APOS |
    JSON_HEX_AMP |
    JSON_HEX_QUOT
) ?>;


/* =========================
   FETCH VALID COMPLAINTS
========================= */

async function fetchValidComplaints() {

    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center py-4">
                <div class="spinner-border text-success"
                     role="status"
                     style="width:1.5rem;height:1.5rem;">
                </div>

                <div class="small text-muted mt-2">
                    Loading complaints...
                </div>
            </td>
        </tr>
    `;

    try {

        const response = await fetch("admin-fetch-valid-complaints.php", {
            method: "GET",
            headers: {
                "Accept": "application/json"
            },
            cache: "no-store"
        });

        if (!response.ok) {
            throw new Error(
                "Server returned HTTP " + response.status
            );
        }

        const data = await response.json();

        console.log("Valid complaints response:", data);

        if (!data.success) {
            throw new Error(
                data.message || "Failed to load complaints."
            );
        }

        complaints = Array.isArray(data.complaints)
            ? data.complaints
            : [];

        filteredComplaints = [...complaints];

        currentPage = 1;

        renderComplaints();

    } catch (error) {

        console.error(
            "Error fetching complaints:",
            error
        );

        tbody.innerHTML = `
            <tr>
                <td colspan="8"
                    class="text-center text-danger py-4">

                    <i class="bi bi-exclamation-triangle-fill me-1"></i>

                    Failed to load resident complaints.

                    <br>

                    <small class="text-muted">
                        ${escapeHtml(error.message)}
                    </small>

                    <br>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-success mt-2"
                        onclick="fetchValidComplaints()">

                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Retry

                    </button>

                </td>
            </tr>
        `;

        updatePagination();
    }
}


/* =========================
   RENDER COMPLAINTS
========================= */

function renderComplaints() {

    tbody.innerHTML = "";

    if (filteredComplaints.length === 0) {

        tbody.innerHTML = `
            <tr>
                <td colspan="8"
                    class="text-center text-muted py-4">

                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>

                    No resident complaints found

                </td>
            </tr>
        `;

        updatePagination();

        return;
    }


    const totalPages = Math.ceil(
        filteredComplaints.length / ITEMS_PER_PAGE
    );


    if (currentPage > totalPages) {
        currentPage = totalPages;
    }

    if (currentPage < 1) {
        currentPage = 1;
    }


    const startIndex =
        (currentPage - 1) * ITEMS_PER_PAGE;

    const endIndex =
        startIndex + ITEMS_PER_PAGE;


    const pageItems =
        filteredComplaints.slice(
            startIndex,
            endIndex
        );


    pageItems.forEach(complaint => {

        const row = document.createElement("tr");

        const status = complaint.status || "Pending";

        row.innerHTML = `

            <td>
                ${escapeHtml(complaint.id)}
            </td>

            <td>
                ${escapeHtml(
                    complaint.resident_name ||
                    "Unknown Resident"
                )}
            </td>

            <td>
    ${escapeHtml(
        complaint.complaint_location ||
        complaint.location ||
        "Not specified"
    )}
</td>

            <td>
                <div style="
                    max-width:280px;
                    white-space:normal;
                    word-break:break-word;
                ">
                    ${escapeHtml(
                        complaint.complaint ||
                        complaint.description ||
                        "No complaint description"
                    )}
                </div>
            </td>

            <td>
                ${escapeHtml(
                    complaint.validated_by_name ||
                    "Unknown"
                )}
            </td>

            <td>
                ${formatDate(
                    complaint.validation_date ||
                    complaint.validated_at
                )}
            </td>

            <td>
                ${getStatusBadge(status)}
            </td>

            <td>
                <div class="d-flex gap-1 justify-content-center">

    <button
        type="button"
        class="btn btn-sm btn-outline-primary"
        onclick="viewComplaint(${Number(complaint.id)})">

        View

    </button>

</div>
            </td>

        `;

        tbody.appendChild(row);
    });


    updatePagination();
}


/* =========================
   STATUS BADGE
========================= */

function getStatusBadge(status) {

    let badgeClass = "bg-secondary";

    switch (status) {

        case "Pending":
            badgeClass = "bg-warning text-dark";
            break;

        case "In Progress":
            badgeClass = "bg-primary";
            break;

        case "Resolved":
            badgeClass = "bg-success";
            break;
    }

    return `
        <span class="badge ${badgeClass}">
            ${escapeHtml(status)}
        </span>
    `;
}


/* =========================
   SEARCH + FILTER
========================= */

function filterComplaints() {

    const keyword =
        searchInput.value
            .trim()
            .toLowerCase();

    const selectedStatus =
        statusFilter.value
            .trim()
            .toLowerCase();


    filteredComplaints =
        complaints.filter(complaint => {

            const resident =
                (
                    complaint.resident_name || ""
                ).toLowerCase();

            const barangay =
                (
                    complaint.barangay || ""
                ).toLowerCase();

            const complaintText =
                (
                    complaint.complaint ||
                    complaint.description ||
                    ""
                ).toLowerCase();

            const location =
                (
                    complaint.complaint_location ||
                    ""
                ).toLowerCase();

            const validatedBy =
                (
                    complaint.validated_by_name ||
                    ""
                ).toLowerCase();


            const matchesSearch =
                keyword === "" ||

                resident.includes(keyword) ||
                barangay.includes(keyword) ||
                complaintText.includes(keyword) ||
                location.includes(keyword) ||
                validatedBy.includes(keyword);


            const complaintStatus =
                (
                    complaint.status || ""
                ).toLowerCase();


            const matchesStatus =
                selectedStatus === "" ||
                complaintStatus === selectedStatus;


            return (
                matchesSearch &&
                matchesStatus
            );
        });


    currentPage = 1;

    renderComplaints();
}


/* =========================
   SEARCH EVENTS
========================= */

searchInput.addEventListener(
    "input",
    filterComplaints
);

statusFilter.addEventListener(
    "change",
    filterComplaints
);


/* =========================
   PAGINATION
========================= */

function updatePagination() {

    const total =
        filteredComplaints.length;

    const totalPages =
        total === 0
            ? 1
            : Math.ceil(
                total / ITEMS_PER_PAGE
            );


    const start =
        total === 0
            ? 0
            : (
                (currentPage - 1) *
                ITEMS_PER_PAGE
            ) + 1;


    const end =
        total === 0
            ? 0
            : Math.min(
                currentPage * ITEMS_PER_PAGE,
                total
            );


    paginationInfo.textContent =
        `Showing ${start} to ${end} of ${total} complaints`;


    pageNumber.textContent =
        `Page ${currentPage} of ${totalPages}`;


    prevBtn.disabled =
        currentPage <= 1;


    nextBtn.disabled =
        currentPage >= totalPages ||
        total === 0;
}


prevBtn.addEventListener(
    "click",
    () => {

        if (currentPage > 1) {

            currentPage--;

            renderComplaints();

        }
    }
);


nextBtn.addEventListener(
    "click",
    () => {

        const totalPages =
            Math.ceil(
                filteredComplaints.length /
                ITEMS_PER_PAGE
            );

        if (currentPage < totalPages) {

            currentPage++;

            renderComplaints();

        }
    }
);


/* =========================
   VIEW COMPLAINT
========================= */

/* =========================
   VIEW COMPLAINT
========================= */

async function viewComplaint(id) {

    const complaint = complaints.find(
        item => Number(item.id) === Number(id)
    );

    if (!complaint) {
        Swal.fire(
            "Error",
            "Complaint information could not be found.",
            "error"
        );
        return;
    }

    const resident =
        complaint.resident_name ||
        "Unknown Resident";

        const location =
    complaint.complaint_location ||
    complaint.location ||
    "Not specified";

    const ticketNo =
        complaint.ticket_no ||
        `#${complaint.id}`;

    const category =
        complaint.category ||
        "Not specified";

    const description =
        complaint.complaint ||
        complaint.description ||
        "No complaint description.";

    const validatedBy =
        complaint.validated_by_name ||
        "Unknown";

    const validationDate =
        formatDate(
            complaint.validation_date ||
            complaint.validated_at
        );

    const status =
        complaint.action_status ||
        complaint.status ||
        "Pending";

    const remarks =
        complaint.remarks ||
        "";

    /*
     * Existing assignment information.
     */
    const assignedType =
        complaint.assigned_personnel_type ||
        complaint.assignment_type ||
        "";

    const assignedPersonnel =
        complaint.assigned_personnel_name ||
        complaint.assigned_to_name ||
        "";

    const assignedMenro =
        complaint.assigned_menro ||
        complaint.menro_personnel ||
        "";

    Swal.fire({

        title: "",

        html: `

            <!-- HEADER -->
<div class="complaint-modal-header">

    <i class="bi bi-file-earmark-text-fill"></i>

    <span>Resident Complaint</span>

    <button
        type="button"
        class="complaint-modal-close"
        onclick="Swal.close()"
        aria-label="Close">

        <i class="bi bi-x-lg"></i>

    </button>

</div>


            <div class="complaint-modal-body">

                <!-- TOP INFORMATION -->
                <div class="complaint-info-grid">

                    <!-- TICKET -->
                    <div class="complaint-ticket">

                        <div class="complaint-info-label">
                            TICKET NUMBER
                        </div>

                        <div style="
                            font-weight:600;
                            color:#1e5631;
                            font-size:13px;
                        ">
                            ${escapeHtml(ticketNo)}
                        </div>

                    </div>


                    <!-- STATUS -->
                    <div class="complaint-ticket">

                        <div class="complaint-info-label">
                            CURRENT STATUS
                        </div>

                        <div>
                            ${getStatusBadge(status)}
                        </div>

                    </div>

                </div>


                <!-- RESIDENT INFORMATION -->
                <div class="complaint-section-title">
                    Resident Information
                </div>

                <div class="complaint-info-grid">

                    <div class="complaint-info-box">

                        <div class="complaint-info-label">
                            Resident
                        </div>

                        <div class="complaint-info-value">
                            ${escapeHtml(resident)}
                        </div>

                    </div>


                    <div class="complaint-info-box">

                        <div class="complaint-info-label">
                            Location
                        </div>

                        <div class="complaint-info-value">
                            ${escapeHtml(location)}
                        </div>

                    </div>

                </div>


                <!-- COMPLAINT INFORMATION -->
              
<div class="complaint-section-title">
    Complaint Information
</div>

<div class="complaint-info-grid">

    <div class="complaint-info-box">

        <div class="complaint-info-label">
            Category
        </div>

        <div class="complaint-info-value">
            ${escapeHtml(category)}
        </div>

    </div>

</div>

<div class="complaint-description-box">

    <div class="complaint-info-label">
        Complaint Description
    </div>

    <div class="complaint-description-text">
        ${escapeHtml(description)}
    </div>

</div>



                <!-- VALIDATION -->
                <div class="complaint-section-title">
                    Validation Details
                </div>

                <div class="complaint-info-grid">

                    <div class="complaint-info-box">

                        <div class="complaint-info-label">
                            Validated By
                        </div>

                        <div class="complaint-info-value">
                            ${escapeHtml(validatedBy)}
                        </div>

                    </div>


                    <div class="complaint-info-box">

                        <div class="complaint-info-label">
                            Validation Date
                        </div>

                        <div class="complaint-info-value">
                            ${escapeHtml(validationDate)}
                        </div>

                    </div>

                </div>


                <!-- ASSIGN PERSONNEL -->
                <div class="complaint-section-title">
                    Assign Personnel
                </div>

                <div class="complaint-assignment">

                    <!-- TYPE -->
                    <div class="assignment-type-row">

                        <button
                            type="button"
                            class="assignment-type-btn ${
                                assignedType.toLowerCase() === "collector"
                                    ? "active"
                                    : ""
                            }"
                            onclick="selectAssignmentType('collector')">

                            <i class="bi bi-person-fill me-1"></i>
                            Collector

                        </button>


                        <button
                            type="button"
                            class="assignment-type-btn ${
                                assignedType.toLowerCase() === "secretary"
                                    ? "active"
                                    : ""
                            }"
                            onclick="selectAssignmentType('secretary')">

                            <i class="bi bi-person-badge-fill me-1"></i>
                            Secretary

                        </button>


                        <button
                            type="button"
                            class="assignment-type-btn ${
                                assignedType.toLowerCase() === "menro"
                                    ? "active"
                                    : ""
                            }"
                            onclick="selectAssignmentType('menro')">

                            <i class="bi bi-building me-1"></i>
                            MENRO

                        </button>

                    </div>


                    <!-- COLLECTOR -->
                    <div
                        id="assignmentCollectorField"
                        class="assignment-field ${
                            assignedType.toLowerCase() === "collector"
                                ? "active"
                                : ""
                        }">

                        <label class="form-label">
                            Available Collector
                        </label>

                        <select
                            id="collectorSelect"
                            class="form-select">

                            <option value="">
                                Loading collectors...
                            </option>

                        </select>

                        <div class="assignment-current">
                            ${
                                assignedType.toLowerCase() === "collector" &&
                                assignedPersonnel
                                    ? `Currently assigned: <strong>${escapeHtml(assignedPersonnel)}</strong>`
                                    : ""
                            }
                        </div>

                    </div>


                    <!-- SECRETARY -->
                    <div
                        id="assignmentSecretaryField"
                        class="assignment-field ${
                            assignedType.toLowerCase() === "secretary"
                                ? "active"
                                : ""
                        }">

                        <label class="form-label">
                            Barangay Secretary
                        </label>

                        <select
                            id="secretarySelect"
                            class="form-select">

                            <option value="">
                                Loading secretary...
                            </option>

                        </select>

                        <div class="assignment-current">
                            Based on complaint location:
                            <strong>${escapeHtml(location)}</strong>
                        </div>

                    </div>


                    <!-- MENRO -->
                    <div
                        id="assignmentMenroField"
                        class="assignment-field ${
                            assignedType.toLowerCase() === "menro"
                                ? "active"
                                : ""
                        }">

                        <label class="form-label">
                            MENRO Personnel / Contact
                        </label>

                      
<input
    type="text"
    id="menroInput"
    class="form-control"
    placeholder="Enter external MENRO personnel"
    value="${escapeHtml(assignedMenro || assignedPersonnel)}"
    oninput="this.value = this.value.replace(/[0-9]/g, '')">


                        <div class="assignment-current">
                            MENRO personnel is entered manually because this is an external assignment.
                        </div>

                    </div>


                    ${
                        assignedType
                            ? `
                                <div class="assignment-current mt-2">
                                    Current assignment:
                                    <strong>
                                        ${escapeHtml(
                                            assignedType.charAt(0).toUpperCase() +
                                            assignedType.slice(1)
                                        )}
                                    </strong>
                                </div>
                            `
                            : `
                                <div class="assignment-current mt-2">
                                    No personnel assigned yet.
                                </div>
                            `
                    }

                </div>


                ${
                    remarks
                        ? `

                            <!-- REMARKS -->
                            <div class="complaint-section-title mt-3">
                                Remarks
                            </div>

                            <div class="complaint-remarks">
                                ${escapeHtml(remarks)}
                            </div>

                        `
                        : ""
                }


                <!-- FOOTER -->
                <div class="complaint-modal-footer">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        onclick="Swal.close()">

                        Cancel

                    </button>


                    <button
                        type="button"
                        class="btn btn-success"
                        id="saveAssignmentBtn"
                        onclick="saveComplaintAssignment(${Number(complaint.id)})">

                        <i class="bi bi-person-check-fill me-1"></i>
                        Assign Personnel

                    </button>

                </div>

            </div>

        `,

        width: "850px",

        showCloseButton: false,

        showConfirmButton: false,

        customClass: {
            popup: "complaint-view-popup"
        },

        allowOutsideClick: true,

        didOpen: () => {

    loadAssignmentPersonnel(
        location,
        assignedPersonnel,
        assignedType
    );

}

    });

}

/* =========================
   ASSIGNMENT TYPE
========================= */

let selectedAssignmentType = "";


function selectAssignmentType(type) {

    selectedAssignmentType = type;

    const collectorField =
        document.getElementById("assignmentCollectorField");

    const secretaryField =
        document.getElementById("assignmentSecretaryField");

    const menroField =
        document.getElementById("assignmentMenroField");

    if (!collectorField || !secretaryField || !menroField) {
        return;
    }

    collectorField.classList.remove("active");
    secretaryField.classList.remove("active");
    menroField.classList.remove("active");


    document
        .querySelectorAll(".assignment-type-btn")
        .forEach(button => {
            button.classList.remove("active");
        });


    const buttons =
        document.querySelectorAll(".assignment-type-btn");

    buttons.forEach(button => {

        const buttonText =
            button.textContent
                .trim()
                .toLowerCase();

        if (
            (type === "collector" &&
                buttonText.includes("collector")) ||

            (type === "secretary" &&
                buttonText.includes("secretary")) ||

            (type === "menro" &&
                buttonText.includes("menro"))
        ) {
            button.classList.add("active");
        }

    });


    if (type === "collector") {
        collectorField.classList.add("active");
    }

    else if (type === "secretary") {
        secretaryField.classList.add("active");
    }

    else if (type === "menro") {
        menroField.classList.add("active");
    }

}


/* =========================================================
   LOAD ASSIGNMENT PERSONNEL
   Uses personnel already fetched from the database by PHP.
   No AJAX/fetch requests are needed.
   ========================================================= */

function loadAssignmentPersonnel(
    location,
    assignedPersonnel,
    assignedType
) {

    const collectorSelect =
        document.getElementById("collectorSelect");

    const secretarySelect =
        document.getElementById("secretarySelect");


    /*
     * Set current assignment type.
     */
    selectedAssignmentType =
        assignedType
            ? assignedType.toLowerCase()
            : "";


    /* =====================================================
       COLLECTORS
       ===================================================== */

    if (collectorSelect) {

        collectorSelect.innerHTML = `
            <option value="">
                Select collector
            </option>
        `;


        const collectors =
            Array.isArray(assignmentPersonnel.collectors)
                ? assignmentPersonnel.collectors
                : [];


        if (collectors.length === 0) {

            collectorSelect.innerHTML = `
                <option value="">
                    No approved collectors available
                </option>
            `;

        } else {

            collectors.forEach(collector => {

                const option =
                    document.createElement("option");


                option.value =
                    collector.id;


                option.textContent =
                    collector.name ||
                    (
                        `${collector.first_name || ""} ` +
                        `${collector.middle_initial ? collector.middle_initial + ". " : ""}` +
                        `${collector.last_name || ""}`
                    ).trim() ||
                    "Unnamed Collector";


                /*
                 * Select existing assignment.
                 */
                if (
                    assignedType &&
                    assignedType.toLowerCase() === "collector" &&
                    assignedPersonnel &&
                    option.textContent.trim() ===
                        String(assignedPersonnel).trim()
                ) {

                    option.selected = true;

                }


                collectorSelect.appendChild(option);

            });

        }

    }


    /* =====================================================
       SECRETARIES
       ===================================================== */

    if (secretarySelect) {

        secretarySelect.innerHTML = `
            <option value="">
                Select secretary
            </option>
        `;


        const secretaries =
            Array.isArray(assignmentPersonnel.secretaries)
                ? assignmentPersonnel.secretaries
                : [];


        /*
         * Get complaint barangay/location.
         *
         * The secretary records contain a barangay field.
         * We first try to match the complaint location
         * against that barangay.
         */

        const normalizedLocation =
            String(location || "")
                .trim()
                .toLowerCase();


        let matchingSecretaries =
            secretaries.filter(secretary => {

                const secretaryBarangay =
                    String(secretary.barangay || "")
                        .trim()
                        .toLowerCase();

                return (
                    normalizedLocation !== "" &&
                    secretaryBarangay !== "" &&
                    (
                        normalizedLocation === secretaryBarangay ||
                        normalizedLocation.includes(secretaryBarangay) ||
                        secretaryBarangay.includes(normalizedLocation)
                    )
                );

            });


        /*
         * If no location match was found,
         * show all approved secretaries.
         *
         * This prevents the dropdown from becoming
         * unusable if the complaint location format
         * differs from the users.barangay value.
         */

        if (matchingSecretaries.length === 0) {

            matchingSecretaries = secretaries;

        }


        if (matchingSecretaries.length === 0) {

            secretarySelect.innerHTML = `
                <option value="">
                    No approved secretaries available
                </option>
            `;

        } else {

            matchingSecretaries.forEach(secretary => {

                const option =
                    document.createElement("option");


                option.value =
                    secretary.id;


                option.textContent =
                    secretary.name ||
                    (
                        `${secretary.first_name || ""} ` +
                        `${secretary.middle_initial ? secretary.middle_initial + ". " : ""}` +
                        `${secretary.last_name || ""}`
                    ).trim() ||
                    "Unnamed Secretary";


                /*
                 * Select existing assignment.
                 */
                if (
                    assignedType &&
                    assignedType.toLowerCase() === "secretary" &&
                    assignedPersonnel &&
                    option.textContent.trim() ===
                        String(assignedPersonnel).trim()
                ) {

                    option.selected = true;

                }


                secretarySelect.appendChild(option);

            });

        }

    }

}

/* =========================
   SAVE COMPLAINT ASSIGNMENT
========================= */

async function saveComplaintAssignment(complaintId) {

    const saveButton =
        document.getElementById("saveAssignmentBtn");


    let personnelType =
        selectedAssignmentType;


    /*
     * If the user opened the modal and
     * immediately clicked save without
     * selecting a type.
     */
    if (!personnelType) {

        const activeButton =
            document.querySelector(
                ".assignment-type-btn.active"
            );

        if (activeButton) {

            const text =
                activeButton.textContent
                    .trim()
                    .toLowerCase();

            if (text.includes("collector")) {
                personnelType = "collector";
            }

            else if (text.includes("secretary")) {
                personnelType = "secretary";
            }

            else if (text.includes("menro")) {
                personnelType = "menro";
            }

        }

    }


    if (!personnelType) {

        Swal.showValidationMessage(
            "Please select personnel type."
        );

        return;
    }


    let personnelId = "";
    let personnelName = "";


    /* =========================
       COLLECTOR
    ========================= */

    if (personnelType === "collector") {

        const select =
            document.getElementById(
                "collectorSelect"
            );


        if (!select || !select.value) {

            Swal.showValidationMessage(
                "Please select a collector."
            );

            return;
        }


        personnelId =
            select.value;


        personnelName =
            select.options[
                select.selectedIndex
            ].text;

    }


    /* =========================
       SECRETARY
    ========================= */

    else if (
        personnelType === "secretary"
    ) {

        const select =
            document.getElementById(
                "secretarySelect"
            );


        if (!select || !select.value) {

            Swal.showValidationMessage(
                "No secretary is available for this location."
            );

            return;
        }


        personnelId =
            select.value;


        personnelName =
            select.options[
                select.selectedIndex
            ].text;

    }


    /* =========================
       MENRO
    ========================= */

    else if (
        personnelType === "menro"
    ) {

        const input =
            document.getElementById(
                "menroInput"
            );


        personnelName =
            input
                ? input.value.trim()
                : "";


        if (!personnelName) {

            Swal.showValidationMessage(
                "Please enter the MENRO personnel or contact."
            );

            return;
        }

    }


    /*
     * Disable button to prevent
     * multiple submissions.
     */

    if (saveButton) {

        saveButton.disabled = true;

        saveButton.innerHTML = `
            <span
                class="spinner-border spinner-border-sm me-1"
                role="status">
            </span>
            Assigning...
        `;

    }


    try {

        const response =
            await fetch(
                "admin-assign-complaint.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type":
                            "application/json",

                        "Accept":
                            "application/json"
                    },

                    body: JSON.stringify({

                        complaint_id:
                            complaintId,

                        personnel_type:
                            personnelType,

                        personnel_id:
                            personnelId,

                        personnel_name:
                            personnelName

                    })
                }
            );


        if (!response.ok) {

            throw new Error(
                "Server returned HTTP " +
                response.status
            );

        }


        const data =
            await response.json();


        if (!data.success) {

            throw new Error(
                data.message ||
                "Failed to assign personnel."
            );

        }


        /*
         * Update local complaint data.
         */

        const complaint =
            complaints.find(
                item =>
                    Number(item.id) ===
                    Number(complaintId)
            );


        if (complaint) {

            complaint.assigned_personnel_type =
                personnelType;

            complaint.assigned_personnel_name =
                personnelName;

            if (
                personnelType === "menro"
            ) {

                complaint.assigned_menro =
                    personnelName;

            }

        }


        Swal.fire({

            icon: "success",

            title: "Personnel Assigned",

            text:
                `${personnelName} has been assigned to this complaint.`,

            confirmButtonColor:
                "#1e5631",

            customClass: {
                popup:
                    "complaint-swal-popup",

                confirmButton:
                    "complaint-swal-confirm"
            }

        });


    } catch (error) {

        console.error(
            "Assignment error:",
            error
        );


        Swal.showValidationMessage(
            error.message ||
            "Failed to assign personnel."
        );


        if (saveButton) {

            saveButton.disabled = false;

            saveButton.innerHTML = `
                <i class="bi bi-person-check-fill me-1"></i>
                Assign Personnel
            `;

        }

    }

}


/* =========================
   DATE FORMAT
========================= */

function formatDate(dateValue) {

    if (!dateValue) {
        return "—";
    }

    const date =
        new Date(
            dateValue.replace(" ", "T")
        );

    if (isNaN(date.getTime())) {
        return escapeHtml(dateValue);
    }

    return date.toLocaleString(
        "en-PH",
        {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "numeric",
            minute: "2-digit",
            hour12: true
        }
    );
}


/* =========================
   HTML ESCAPE
========================= */

function escapeHtml(value) {

    if (value === null || value === undefined) {
        return "";
    }

    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}


/* =========================
   INITIAL LOAD
========================= */

fetchValidComplaints();

</script>

</body>
</html>