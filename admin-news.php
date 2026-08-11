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

<title>EnviroManage Admin - News & Articles</title>

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
    <a class="nav-link" href="admin-resident-complaints.php"><i class="bi bi-file-earmark-text-fill"></i> <span>Resident Complaints</span></a>
    <a class="nav-link" href="#"><i class="bi bi-bar-chart-fill"></i> <span>Analytics</span></a>

    <a class="nav-link" href="admin-announcements.php">
      <i class="bi bi-megaphone-fill"></i> <span>Announcements</span>
    </a>

    <a class="nav-link active" href="admin-news.php"><i class="bi bi-newspaper"></i> <span>News & Articles</span></a>
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
  <i class="bi bi-newspaper text-success me-1"></i>
  News & Articles
</h5>

  <!-- INFO BUTTON -->
  <button type="button"
    id="newsPageInfoBtn"
    class="btn btn-sm btn-light border rounded-circle"
    style="width:24px;height:24px;padding:0;">
    <i class="bi bi-info-circle text-success" style="font-size:11px;"></i>
  </button>

</div>

<!-- INFO CARD (HIDDEN BY DEFAULT) -->
<div id="newsPageInfoCard"
  class="alert mt-2 d-none"
  style="
    background:#e9f7ef;
    border:1px solid #1e5631;
    color:#1e5631;
    font-size:12px;
    border-radius:8px;
    line-height:1.4;
  ">

  This module is used to publish and manage news articles for residents.

  <br><br>

  <strong>Features:</strong><br>
  • Create and publish news articles<br>
  • Save drafts for later editing<br>
  • Upload featured images<br>
  • Categorize content by topic<br>

</div>

<div class="news-controls mt-2">

  <div class="search-row">

    <input type="text"
           id="searchInput"
           class="form-control news-search"
           placeholder="Search news...">

    <button class="btn btn-success news-add-btn"
            data-bs-toggle="modal"
            data-bs-target="#addNewsModal">
      <i class="bi bi-plus-circle"></i> Add
    </button>

  </div>

</div>



    <div class="table-responsive mt-3">

      <table class="table table-bordered" id="newsTable">

        <thead>

          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Category</th>
            <th>Status</th>
            <th>Created At</th>
<th>Updated At</th>
            <th>Actions</th>
          </tr>

        </thead>

        <tbody></tbody>

      </table>

    </div>

    <!-- NEWS PAGINATION -->
<div id="newsPagination"
     class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">

  <div class="text-center text-md-start">
    <small id="newsPaginationInfo" class="text-muted">
      Showing 0 to 0 of 0 articles
    </small>
  </div>

  <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">

    <button id="newsPrevBtn"
            class="btn btn-sm btn-outline-success">
      Previous
    </button>

    <span id="newsPageNumber"
          class="fw-semibold px-2">
      Page 1 of 1
    </span>

    <button id="newsNextBtn"
            class="btn btn-sm btn-outline-success">
      Next
    </button>

  </div>
</div>

  </div>

</div>

<!-- ADD NEWS MODAL -->
<div class="modal fade" id="addNewsModal" tabindex="-1">

  <div class="modal-dialog modal-lg modal-dialog-centered">

    <div class="modal-content">

      <form id="addNewsForm">

        <div class="modal-header">

          <h5 class="modal-title">
            Add News Article
          </h5>

          <button type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"></button>

        </div>

        <div class="modal-body">

          <div class="mb-3">

            <label>Title</label>

            <input type="text"
                   id="title"
                   class="form-control"
                   required>

          </div>

          <div class="mb-3">

            <label>Category</label>

            <select id="category"
                    class="form-select"
                    required>

              <option value="">Select Category</option>
              <option>Environment</option>
              <option>Waste Management</option>
              <option>Community</option>
              <option>Health</option>
              <option>Events</option>

            </select>

          </div>

          <div class="mb-3">

            <label>Article Content</label>

            <textarea id="content"
                      rows="8"
                      class="form-control"
                      required></textarea>

          </div>

       <input
    type="file"
    id="newsImages"
    name="newsImages[]"
    class="form-control"
    accept="image/*"
    multiple
>

<div id="newsImageGrid" style="display:none;"></div>

<!-- Orientation -->
<div class="mt-3">
    <label class="form-label">Image Orientation</label>

    <div class="form-check form-check-inline">
        <input class="form-check-input"
               type="radio"
               name="newsImageOrientation"
               value="landscape"
               checked>

        <label class="form-check-label">
            Landscape
        </label>
    </div>

    <div class="form-check form-check-inline">
        <input class="form-check-input"
               type="radio"
               name="newsImageOrientation"
               value="portrait">

        <label class="form-check-label">
            Portrait
        </label>
    </div>
</div>

<div id="newsPreviewWrapper" class="mt-2 d-none">

    <div id="newsPreviewContainer"
         class="position-relative rounded overflow-hidden border">

        <img id="newsPreviewImage"
             class="w-100 preview-image">

        <button type="button"
                id="newsPreviewFullscreen"
                class="fullscreen-btn">
            <i class="bi bi-arrows-fullscreen"></i>
        </button>

        <div class="image-actions">

            <button type="button"
                    id="newsPreviewReplace"
                    class="btn-warning">
                <i class="bi bi-arrow-repeat"></i>
            </button>

            <button type="button"
                    id="newsPreviewDelete"
                    class="btn-delete">
                <i class="bi bi-trash"></i>
            </button>

        </div>

        <button id="newsPreviewPrev"
                type="button"
                class="view-slider-btn position-absolute top-50 start-0 translate-middle-y ms-3">
            <i class="bi bi-chevron-left"></i>
        </button>

        <button id="newsPreviewNext"
                type="button"
                class="view-slider-btn position-absolute top-50 end-0 translate-middle-y me-3">
            <i class="bi bi-chevron-right"></i>
        </button>

    </div>

    <div id="newsPreviewCounter"
         class="text-center mt-2 small text-muted"></div>

</div>
          <div class="mt-3">

            <label>Status</label>

            <select id="status" class="form-select">

              <option value="published">Publish</option>
              <option value="draft">Save as Draft</option>

            </select>

          </div>

        </div>

        <div class="modal-footer">

          <button type="submit"
                  class="btn btn-success">

            Publish News

          </button>

        </div>

      </form>

    </div>

  </div>

</div>

<!-- VIEW MODAL -->
<div class="modal fade" id="viewNewsModal" tabindex="-1">

  <div class="modal-dialog modal-lg modal-dialog-centered">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title">News Article</h5>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="modal"></button>

      </div>

      <div class="modal-body" id="viewNewsContent"></div>

    </div>

  </div>

</div>

<!-- EDIT MODAL -->

<div class="modal fade" id="editNewsModal" tabindex="-1">

  <div class="modal-dialog modal-lg modal-dialog-centered">

    <div class="modal-content">

      <form id="editNewsForm">

        <div class="modal-header">
          <h5 class="modal-title">Edit News Article</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input type="hidden" id="edit_id">

          <div class="mb-3">
            <label>Title</label>
            <input type="text" id="edit_title" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Category</label>
            <select id="edit_category" class="form-select" required>
              <option>Environment</option>
              <option>Waste Management</option>
              <option>Community</option>
              <option>Health</option>
              <option>Events</option>
            </select>
          </div>

          <div class="mb-3">
            <label>Content</label>
            <textarea id="edit_content" rows="8" class="form-control" required></textarea>
          </div>

          <div class="mb-3">
            <label>Status</label>
            <select id="edit_status" class="form-select">
              <option value="published">Publish</option>
              <option value="draft">Draft</option>
            </select>
          </div>

          <div class="mb-3">
  <label>Current Images</label>
  <div id="editNewsImageGrid" class="image-grid mt-2"></div>
</div>

<div class="mb-3">
  <label>Upload New Images</label>
  <input type="file"
         id="editNewsImages"
         class="form-control"
         accept="image/*"
         multiple>
</div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-success" type="submit">Update</button>
        </div>

      </form>

    </div>

  </div>

</div>

<div class="modal fade"
     id="imageFullscreenModal"
     tabindex="-1"
     data-bs-backdrop="true"
     data-bs-keyboard="true">
 <div class="modal-dialog modal-fullscreen m-0">

    <div class="modal-content">

        <div class="modal-header">

            <button
                type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal">
            </button>

        </div>

        <div class="modal-body">

            <img id="fullscreenImage">

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

function isMobile(){
  return window.innerWidth <= 768;
}

function updateContentMargin(){

  if(!isMobile()){

    mainContent.style.marginLeft = '220px';

  } else {

    mainContent.style.marginLeft =
      sidebar.classList.contains('hidden')
      ? '0'
      : '70px';

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

function updateSidebarUI(){

  const isHidden = sidebar.classList.contains('hidden');

  if(isHidden){

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

let news = [];
let currentPage = 1;
const ITEMS_PER_PAGE = 10;

/* INFO CARD TOGGLE */
document.getElementById('newsPageInfoBtn').addEventListener('click', () => {
  const card = document.getElementById('newsPageInfoCard');
  card.classList.toggle('d-none');
});


/* FETCH NEWS */
async function fetchNews() {
  try {
    const res = await fetch('admin-fetch-news.php');

    const text = await res.text(); // 👈 debug safe

    console.log("RAW RESPONSE:", text); // 👈 check in console

    const data = JSON.parse(text);

    news = Array.isArray(data) ? data : [];

    renderTable();

  } catch (err) {
    console.error("FETCH ERROR:", err);
  }
}

function renderPagination(
  currentPage,
  totalPages,
  totalItems
) {

  if (totalPages <= 0) {
    totalPages = 1;
  }

  const prevBtn =
    document.getElementById('newsPrevBtn');

  const nextBtn =
    document.getElementById('newsNextBtn');

  const pageNumber =
    document.getElementById('newsPageNumber');

  const paginationInfo =
    document.getElementById('newsPaginationInfo');

  const startItem = totalItems === 0
    ? 0
    : ((currentPage - 1) * ITEMS_PER_PAGE) + 1;

  const endItem = Math.min(
    currentPage * ITEMS_PER_PAGE,
    totalItems
  );

  paginationInfo.textContent =
    `Showing ${startItem} to ${endItem} of ${totalItems} articles`;

  pageNumber.textContent =
    `Page ${currentPage} of ${totalPages}`;

  prevBtn.disabled = currentPage === 1;
  nextBtn.disabled = currentPage === totalPages;

  prevBtn.replaceWith(prevBtn.cloneNode(true));
  nextBtn.replaceWith(nextBtn.cloneNode(true));

  const newPrevBtn =
    document.getElementById('newsPrevBtn');

  const newNextBtn =
    document.getElementById('newsNextBtn');

  newPrevBtn.addEventListener('click', () => {
    if (currentPage > 1) {
      currentPage--;
      renderTable();
    }
  });

  newNextBtn.addEventListener('click', () => {
    if (currentPage < totalPages) {
      currentPage++;
      renderTable();
    }
  });
}

function formatDate(dt) {
  if (!dt) return '-';

  return new Date(dt).toLocaleString('en-PH', {
    timeZone: 'Asia/Manila',
    year: 'numeric',
    month: 'short',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  });
}

/* TABLE */
function renderTable(){

  const tbody = document.querySelector('#newsTable tbody');

  const search = document.getElementById('searchInput')
    .value
    .toLowerCase();

  const filtered = news.filter(n =>
    n.title?.toLowerCase().includes(search)
  );

const totalPages = Math.max(
  1,
  Math.ceil(filtered.length / ITEMS_PER_PAGE)
);

if (currentPage > totalPages) {
  currentPage = totalPages;
}

 const start = (currentPage - 1) * ITEMS_PER_PAGE;
const end = start + ITEMS_PER_PAGE;

const paginated = filtered.slice(start, end);

  tbody.innerHTML = '';

 if(paginated.length === 0){

  tbody.innerHTML = `
    <tr>
      <td colspan="7" class="text-center text-muted py-4">
        No news articles found
      </td>
    </tr>
  `;

 renderPagination(
  1,
  1,
  0
);
  return;
}

  paginated.forEach(n => {

    tbody.innerHTML += `
      <tr>
        <td>${n.id}</td>
        <td>${n.title}</td>

        <td>
          <span class="badge bg-primary">${n.category}</span>
        </td>

        <td>
          ${
            n.status === 'published'
            ? '<span class="badge bg-success">Published</span>'
            : '<span class="badge bg-warning text-dark">Draft</span>'
          }
        </td>

  <td>
  <small class="text-muted">${formatDate(n.created_at)}</small>
</td>

<td>
  <small class="text-warning">${formatDate(n.updated_at)}</small>
</td>

        <td>
          <button class="btn btn-sm btn-info" onclick="viewNews(${n.id})">
            View
          </button>

            <button class="btn btn-sm btn-warning" onclick="editNews(${n.id})">
    Edit
  </button>

          <button class="btn btn-sm btn-danger" onclick="deleteNews(${n.id})">
            Delete
          </button>
        </td>
      </tr>
    `;
  });



 renderPagination(
  currentPage,
  totalPages,
  filtered.length
);
}

let selectedNewsImages = [];
let newsPreviewIndex = 0
let selectedEditImages = [];
let existingEditImages = [];
let deletedEditImages = [];
let currentViewImages = [];
let currentViewIndex = 0;

/* ADD NEWS */
document.getElementById('addNewsForm')
.addEventListener('submit', async function(e){

  e.preventDefault();

  const submitBtn = this.querySelector('button[type="submit"]');

  if (submitBtn.disabled) return;

  // save original text
  const originalText = submitBtn.innerHTML;

  submitBtn.disabled = true;
  submitBtn.innerHTML = `
    <span class="spinner-border spinner-border-sm me-2"></span>
    Publishing...
  `;

  try {

   const formData = new FormData();

formData.append('title', title.value);
formData.append('category', category.value);
formData.append('content', content.value);
formData.append('status', status.value);

formData.append(
    'image_orientation',
    document.querySelector(
        'input[name="newsImageOrientation"]:checked'
    ).value
);

// Upload current preview images
selectedNewsImages.forEach(img => {
    formData.append('images[]', img.file);
});

const res = await fetch('admin-add-news.php', {
    method: 'POST',
    body: formData
});

    const data = await res.json();

    if(data.success){

      Swal.fire('Success', 'News article saved', 'success');

      bootstrap.Modal
        .getInstance(document.getElementById('addNewsModal'))
        .hide();

      this.reset();
      selectedNewsImages = [];
      document.getElementById('newsImageGrid').innerHTML = '';

      fetchNews();

    } else {
      Swal.fire('Error', data.message || 'Failed', 'error');
    }

  } catch (err) {
    console.error(err);
    Swal.fire('Error', 'Something went wrong', 'error');
  }

  // restore button
  submitBtn.disabled = false;
  submitBtn.innerHTML = originalText;
});

/* VIEW */
function viewNews(id){

    const n = news.find(x => x.id == id);
    if(!n) return;

    currentViewImages = n.images || [];
    currentViewIndex = 0;

    const orientation = (n.image_orientation || "landscape").toLowerCase();

    const imageStyle =
        orientation === "portrait"
        ? "width:auto;max-width:100%;height:520px;object-fit:cover;"
        : "width:100%;height:350px;object-fit:cover;";

    document.getElementById("viewNewsContent").innerHTML = `

        ${
            currentViewImages.length
            ? `
            <div class="position-relative mb-3 text-center">

                <img
                    id="viewSliderImage"
                    src="${currentViewImages[0]}"
                    class="rounded border"
                    style="${imageStyle}">

                ${
                    currentViewImages.length > 1
                    ? `
                    <button
                        class="btn view-slider-btn position-absolute top-50 start-0 translate-middle-y ms-3 shadow-sm"
                        onclick="changeViewImage(-1)">
                        <i class="bi bi-chevron-left"></i>
                    </button>

                    <button
                        class="btn view-slider-btn position-absolute top-50 end-0 translate-middle-y me-3 shadow-sm"
                        onclick="changeViewImage(1)">
                        <i class="bi bi-chevron-right"></i>
                    </button>

                    <div class="text-center mt-2">
                        <small id="viewImageCounter">
                            1 / ${currentViewImages.length}
                        </small>
                    </div>
                    `
                    : ""
                }

            </div>
            `
            : ""
        }

        <h3>${n.title}</h3>

        <div class="mb-2">
            <span class="badge bg-primary">${n.category}</span>
            <span class="badge ${
                n.status === "published"
                    ? "bg-success"
                    : "bg-warning text-dark"
            }">
                ${n.status}
            </span>
        </div>

        <div style="white-space:pre-wrap;line-height:1.7;">
            ${n.content}
        </div>
    `;

    new bootstrap.Modal(
        document.getElementById("viewNewsModal")
    ).show();
}

function changeViewImage(direction){

    currentViewIndex += direction;

    if(currentViewIndex < 0){
        currentViewIndex = currentViewImages.length - 1;
    }

    if(currentViewIndex >= currentViewImages.length){
        currentViewIndex = 0;
    }

    document.getElementById('viewSliderImage').src =
        currentViewImages[currentViewIndex];

    document.getElementById('viewImageCounter').textContent =
        `${currentViewIndex + 1} / ${currentViewImages.length}`;
}

/* DELETE */
async function deleteNews(id){

  const confirm = await Swal.fire({
    title:'Delete article?',
    icon:'warning',
    showCancelButton:true
  });

  if(!confirm.isConfirmed) return;

  const res = await fetch('admin-delete-news.php', {
    method:'POST',
    headers:{
      'Content-Type':'application/json'
    },
    body:JSON.stringify({id})
  });

  const data = await res.json();

  if(data.success){

    Swal.fire(
      'Deleted',
      'Article removed',
      'success'
    );

    fetchNews();

  }

}

document.getElementById('searchInput')
.addEventListener('input', () => {
  currentPage = 1;
  renderTable();
});

fetchNews();


function syncNewsFileInput(){

  const dt = new DataTransfer();

  selectedNewsImages.forEach(item => {
    dt.items.add(item.file);
  });

  document.getElementById('newsImages').files = dt.files;
}

// Load files
document.getElementById('newsImages').addEventListener('change', async function(e) {

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

        selectedNewsImages.push({
            id: crypto.randomUUID(),
            file: compressed,
            preview: URL.createObjectURL(compressed)
        });
    }
    newsPreviewIndex = 0;
    syncNewsFileInput();
    renderNewsImagePreview();
});


let draggedIndex = null;

function renderNewsImages(){

    const grid = document.getElementById('newsImageGrid');

    grid.innerHTML = '';

    selectedNewsImages.forEach(item => {

        const div = document.createElement('div');

        div.className = 'image-item';

        div.innerHTML = `
            <img src="${item.preview}">

            <button class="fullscreen-btn">
                <i class="bi bi-arrows-fullscreen"></i>
            </button>

            <div class="image-actions">
                <button class="btn-replace">↻</button>
                <button class="btn-delete">×</button>
            </div>
        `;

        /* FULLSCREEN */
        div.querySelector('.fullscreen-btn').onclick = () => {
            openFullscreen(item.preview);
        };

        /* DELETE */
        div.querySelector('.btn-delete').onclick = () => {

            URL.revokeObjectURL(item.preview);

            selectedNewsImages =
                selectedNewsImages.filter(x => x.id !== item.id);

            syncNewsFileInput();
            renderNewsImages();
        };

        /* REPLACE */
        div.querySelector('.btn-replace').onclick = () => {

            const input = document.createElement('input');

            input.type = 'file';
            input.accept = 'image/*';

            input.onchange = (e) => {

                const newFile = e.target.files[0];

                if(!newFile) return;

                const target =
                    selectedNewsImages.find(x => x.id === item.id);

                URL.revokeObjectURL(target.preview);

                target.file = newFile;
                target.preview = URL.createObjectURL(newFile);

                syncNewsFileInput();
                renderNewsImages();
            };

            input.click();
        };

        grid.appendChild(div);

    });

}

function editNews(id){

  const n = news.find(x => x.id == id);
  if(!n) return;

  document.getElementById('edit_id').value = n.id;
  document.getElementById('edit_title').value = n.title;
  document.getElementById('edit_category').value = n.category;
  document.getElementById('edit_content').value = n.content;

  const statusSelect = document.getElementById('edit_status');
  const status = (n.status || '').toLowerCase().trim();

  statusSelect.value = status;
  statusSelect.disabled = (status === 'published');

  // ✅ STORE EXISTING IMAGES
  existingEditImages = (n.images || []).map((img, i) => ({
  id: i,               // temporary index
  path: img
}));

deletedEditImages = [];
  selectedEditImages = [];

  renderEditImages();

  new bootstrap.Modal(
    document.getElementById('editNewsModal')
  ).show();
}



document.getElementById('editNewsForm')
.addEventListener('submit', async function(e){

  e.preventDefault();

  const submitBtn = this.querySelector('button[type="submit"]');

  if (submitBtn.disabled) return;

  const originalText = submitBtn.innerHTML;

  submitBtn.disabled = true;
  submitBtn.innerHTML = `
    <span class="spinner-border spinner-border-sm me-2"></span>
    Updating...
  `;

  try {

    const formData = new FormData();

    formData.append('id', document.getElementById('edit_id').value);
    formData.append('title', document.getElementById('edit_title').value);
    formData.append('category', document.getElementById('edit_category').value);
    formData.append('content', document.getElementById('edit_content').value);
    formData.append('status', document.getElementById('edit_status').value);

    // KEEP images that remain
    formData.append(
      'existing_images',
      JSON.stringify(existingEditImages)
    );

    // DELETE images marked for removal (server + DB handled in PHP)
    formData.append(
      'deleted_images',
      JSON.stringify(deletedEditImages)
    );

    // ADD new uploads
    if (selectedEditImages.length) {
     selectedEditImages.forEach(item => {
    formData.append('new_images[]', item.file);
});
    }

    const res = await fetch('admin-update-news.php', {
      method: 'POST',
      body: formData
    });

    const data = await res.json();

    if (data.success) {

  Swal.fire('Updated', 'News updated successfully', 'success');

  bootstrap.Modal.getInstance(
    document.getElementById('editNewsModal')
  ).hide();

  // Reset edit state
  selectedEditImages = [];
  existingEditImages = [];
  deletedEditImages = [];

  document.getElementById('editNewsImages').value = '';
  document.getElementById('editNewsImageGrid').innerHTML = '';

  fetchNews();

} else {

  Swal.fire('Error', data.message || 'Update failed', 'error');
}
  } catch (err) {

    console.error(err);
    Swal.fire('Error', 'Something went wrong', 'error');

  }

  submitBtn.disabled = false;
  submitBtn.innerHTML = originalText;
});

function renderEditImages(){

  const grid = document.getElementById('editNewsImageGrid');
  grid.innerHTML = '';

  // EXISTING IMAGES (FROM DB)
  existingEditImages.forEach((imgObj, index) => {

    const div = document.createElement('div');
    div.className = 'image-item';

    div.innerHTML = `
      <img src="${imgObj.path}">

      <button class="fullscreen-btn">
        <i class="bi bi-arrows-fullscreen"></i>
      </button>

      <div class="image-actions">
        <button class="btn-delete">×</button>
      </div>
    `;

    div.querySelector('.fullscreen-btn').onclick = () => {
      openFullscreen(imgObj.path);
    };

    div.querySelector('.btn-delete').onclick = () => {

      // mark for deletion
      if (!deletedEditImages.includes(imgObj.path)) {
  deletedEditImages.push(imgObj.path);
}

      // remove from UI list
      existingEditImages.splice(index, 1);

      renderEditImages();
    };

    grid.appendChild(div);
  });

  // NEW UPLOADS
  selectedEditImages.forEach(item => {

    const div = document.createElement('div');
    div.className = 'image-item';

    div.innerHTML = `
      <img src="${item.preview}">

      <button class="fullscreen-btn">
        <i class="bi bi-arrows-fullscreen"></i>
      </button>

      <div class="image-actions">
        <button class="btn-delete">×</button>
      </div>
    `;

    div.querySelector('.fullscreen-btn').onclick = () => {
      openFullscreen(item.preview);
    };

    div.querySelector('.btn-delete').onclick = () => {
      URL.revokeObjectURL(item.preview);
      selectedEditImages =
        selectedEditImages.filter(x => x.id !== item.id);

      renderEditImages();
    };

    grid.appendChild(div);
  });
}

document.getElementById('editNewsImages')
.addEventListener('change', function(e){

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

        selectedEditImages.push({
            id: crypto.randomUUID(),
            file,
            preview: URL.createObjectURL(file)
        });
    }

    renderEditImages();
});

function renderNewsImagePreview(){

    const wrapper = document.getElementById("newsPreviewWrapper");

    if(selectedNewsImages.length === 0){

        wrapper.classList.add("d-none");
        newsPreviewIndex = 0;
        return;

    }

    wrapper.classList.remove("d-none");
   updateNewsPreviewOrientation();
   
    if(newsPreviewIndex >= selectedNewsImages.length){
        newsPreviewIndex = selectedNewsImages.length - 1;
    }

    if(newsPreviewIndex < 0){
        newsPreviewIndex = 0;
    }

    const current = selectedNewsImages[newsPreviewIndex];

    document.getElementById("newsPreviewImage").src = current.preview;

    document.getElementById("newsPreviewCounter").textContent =
        `${newsPreviewIndex + 1} / ${selectedNewsImages.length}`;

    document.getElementById("newsPreviewFullscreen").onclick = () => {
        openFullscreen(current.preview);
    };

    document.getElementById("newsPreviewReplace").onclick = () => {

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

            syncNewsFileInput()
renderNewsImagePreview()

        };

        input.click();

    };

    document.getElementById("newsPreviewDelete").onclick = () => {

        URL.revokeObjectURL(current.preview);

        selectedNewsImages.splice(newsPreviewIndex,1);

        if(newsPreviewIndex >= selectedNewsImages.length){
            newsPreviewIndex = selectedNewsImages.length - 1;
        }

        syncNewsFileInput();
        renderNewsImagePreview();

    };

    const prevBtn = document.getElementById("newsPreviewPrev");
const nextBtn = document.getElementById("newsPreviewNext");
const counter = document.getElementById("newsPreviewCounter");

const showNav = selectedNewsImages.length > 1;

prevBtn.style.display = showNav ? "" : "none";
nextBtn.style.display = showNav ? "" : "none";
counter.style.display = showNav ? "" : "none";

// Previous
prevBtn.onclick = () => {

    newsPreviewIndex--;

    if (newsPreviewIndex < 0) {
        newsPreviewIndex = selectedNewsImages.length - 1;
    }

    renderNewsImagePreview();
};

// Next
nextBtn.onclick = () => {

    newsPreviewIndex++;

    if (newsPreviewIndex >= selectedNewsImages.length) {
        newsPreviewIndex = 0;
    }

    renderNewsImagePreview();
};

}


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

function updateNewsPreviewOrientation(){

    const img = document.getElementById("newsPreviewImage");
    const container = document.getElementById("newsPreviewContainer");

    container.classList.remove("landscape","portrait");

    const newsImageOrientation = document.querySelector(
        'input[name="newsImageOrientation"]:checked'
    ).value;

    if (newsImageOrientation === "portrait") {
        container.classList.add("portrait");
    } else {
        container.classList.add("landscape");
    }
}

let fullscreenModal = null;
let fullscreenOpen = false;


function openFullscreen(src){

    document.getElementById("fullscreenImage").src = src;

    const modalEl = document.getElementById("imageFullscreenModal");

    if(!fullscreenModal){

        fullscreenModal = new bootstrap.Modal(modalEl,{
            backdrop:true,
            keyboard:true,
            focus:false
        });

    }

    fullscreenModal.show();

    // Keep fullscreen modal above Add/Edit modal
    setTimeout(()=>{

        modalEl.style.zIndex = "3000";

        const backdrops =
            document.querySelectorAll(".modal-backdrop");

        if(backdrops.length){

            backdrops[backdrops.length-1].style.zIndex="2999";

        }

    },10);

}

document.querySelectorAll(
    'input[name="newsImageOrientation"]'
).forEach(radio => {
    radio.addEventListener("change", updateNewsPreviewOrientation);
});



</script>

</body>
</html>