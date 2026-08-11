<?php
session_start();
// Redirect to login page if user is not logged in
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
<title>EnviroManage Admin - User Management</title>

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

/* NAVBAR */
.navbar-nav{
    display:flex;
    flex-direction:row;
    align-items:center;
    height:70px;
    margin-bottom:0;
    margin-left:auto;
    gap:0;
}

.navbar-nav .nav-item{
    position:relative;
    display:flex;
    align-items:center;
    justify-content:center;
    height:70px;
}

.navbar-nav .nav-link{
    display:flex;
    align-items:center;
    justify-content:center;
    height:100%;
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
.sidebar { position: fixed; top: 70px; left: 0; width: 70px; height: 100%; background-color: #fff; border-right: 1px solid #dee2e6; padding-top: 15px; overflow-y: auto; transition: width 0.3s ease, transform 0.3s ease; z-index: 1050; }
.sidebar.expanded { width: 220px; }
.sidebar.hidden { transform: translateX(-100%); }
.sidebar .nav-link { color: #495057; padding: 10px; display: flex; align-items: center; gap: 10px; justify-content: center; }
.sidebar .nav-link span { display: none; }
.sidebar.expanded .nav-link { justify-content: flex-start; }
.sidebar.expanded .nav-link span { display: inline; }
.sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #1e5631; color: #fff; border-radius: 5px; }

/* EDGE BUTTONS */
#sidebarControls { position: fixed; top: 80px; left: 70px; z-index: 1100; display: flex; flex-direction: column; gap: 5px; transition: left 0.3s ease; }
.sidebar.expanded + #sidebarControls { left: 220px; }
#sidebarControls button { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; border-radius: 0 5px 5px 0; color: #fff; font-size:1rem; }
#toggleBtn { background-color: #1e5631; }
#closeBtn { background-color: #dc3545; }
#sidebarControls.hidden { display: none; }

.main-content { margin-left: 0; transition: margin-left 0.3s; padding: 15px; }

#hamburger { display: none; background-color: #1e5631; color: #fff; border: none; width: 40px; height: 40px; border-radius: 5px; align-items: center; justify-content: center; z-index: 1200; }

.strength-bar-container { width:100%; height:8px; background:#e0e0e0; border-radius:4px; overflow:hidden; }
.strength-bar-fill { height:100%; width:0; background:red; transition: width 0.3s, background 0.3s; border-radius:4px; }
.requirements-list { text-align:left; font-size:13px; color:#6c757d; }
.requirements-list li { margin-bottom:3px; }

/* ==========================
   MOBILE NAVBAR
========================== */
@media (max-width:768px){

    .navbar{
        height:70px;
    }

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
        left:auto !important;
        top:100%;
        margin-top:8px !important;
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

/* CENTER MODALS ON MOBILE */
@media (max-width: 768px) {
  .modal { padding-top: 0 !important; }
  .modal-dialog { min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0 auto; }
  .modal-content { width: 95%; margin: 0 auto; }
}

/* =========================
   ACTION BUTTONS
========================= */

/* Desktop */
.action-buttons{
    display:grid;
    grid-template-columns:repeat(2, minmax(65px,1fr));
    gap:4px;
    min-width:140px;
}

.action-buttons .btn{
    width:100%;
    font-size:11px;
    padding:4px 6px;
}

.action-buttons .btn i{
    font-size:10px;
}

/* If only one button exists */
.action-buttons .btn:only-child{
    grid-column:1;
    width:65px;
    justify-self:center;
}

/* Mobile */
@media (max-width:768px){

    .action-buttons{
        grid-template-columns:repeat(2,1fr);
        gap:3px;
        min-width:120px;
    }

    .action-buttons .btn{
        font-size:9px;
        padding:2px 4px;
        line-height:1.1;
    }

    .action-buttons .btn i{
        font-size:9px;
    }
}

.address-cell{
    max-width:20ch;   /* approximately 20 characters */
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

@media (max-width:768px){
    .address-cell{
        width:120px;
        min-width:120px;
        max-width:120px;
    }
}

/* =========================
   AUTOCOMPLETE DROPDOWN FIX
========================= */
.suggestion-box {
  display: none;              /* hidden by default */
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;

  z-index: 9999;              /* IMPORTANT: stay above modal */
  background: #fff;
  border: 1px solid #dee2e6;
  border-top: none;
  max-height: 200px;
  overflow-y: auto;

  box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}

/* Each item */
.suggestion-box .list-group-item {
  cursor: pointer;
  font-size: 14px;
  padding: 8px 10px;
  border: none;
  border-bottom: 1px solid #f1f1f1;
}

.suggestion-box .list-group-item:hover {
  background-color: #1e5631;
  color: #fff;
}

/* Fix positioning context */
.position-relative {
  position: relative;
}

/* TOOLBAR LAYOUT */
.user-controls {
  display: flex;
  gap: 10px;
  align-items: center;
}

/* SEARCH expands */
.search-box {
  min-width: 250px;
  flex: 1;
}

.role-box {
  flex: 0 0 150px;   /* prevents shrinking */
  min-width: 120px;
}

.role-box select {
  width: 100%;
  padding-right: 2rem; /* space for caret */
}


/* ADD button fixed */
.add-box {
  width: 140px;
}


@media (max-width:768px){

    .user-controls{
        flex-wrap:nowrap !important;
        gap:6px;
        align-items:center;
    }

    .search-box{
        flex:2;
        min-width:0;
    }

    .role-box{
        flex:0 0 70px;
        min-width:70px;
        max-width:90px;
    }

    .role-box select{
        font-size:11px;
        height:32px;
        padding:4px 6px;
    }

    .add-box{
        flex:0 0 auto;
        width:auto;
    }

    .add-box .btn{
        font-size:11px;
        padding:5px 8px;
        white-space:nowrap;
        height:32px;
        display:inline-flex;
        align-items:center;
    }

    .form-control-sm,
    .form-select-sm{
        height:32px;
        font-size:12px;
        padding:4px 8px;
    }
}

  /* ADD button smallest */
  .add-box {
    flex: 0 0 auto;
    width: auto;
  }

  /* make button compact and aligned */
  .add-box .btn {
    font-size: 11px;
    padding: 5px 8px;
    white-space: nowrap;
    height: 32px; /* aligns with inputs */
    display: inline-flex;
    align-items: center;
  }

  /* keep inputs same height for alignment */
  .form-control-sm,
  .form-select-sm {
    height: 32px;
    font-size: 12px;
    padding: 4px 8px;
  }


/* SEARCH takes remaining space */
.search-box {
  flex: 1;
  min-width: 120px;
}

/* ADD button fixed */
.add-box {
  white-space: nowrap;
}

/* 📱 MOBILE: just shrink sizes, NOT layout */
@media (max-width: 768px) {

  .form-control,
  .form-select {
    font-size: 12px;
    padding: 4px 8px;
  }

  .add-box .btn {
    font-size: 12px;
    padding: 4px 8px;
    white-space: nowrap;
  }

  .role-box {
    width: 120px;
  }
}

/* =========================
   MOBILE FIX: SEARCH + ROLE
========================= */
@media (max-width: 768px) {

  /* make row tighter */
  .user-controls {
    gap: 6px;
  }

  /* SEARCH BAR SMALLER */
  .search-box {
    flex: 2;
    min-width: 0;
  }

  .search-box input {
    font-size: 12px;
    height: 32px;
    padding: 4px 8px;
  }

  /* ROLE BOX BIGGER (fix caret overlap) */
  .role-box {
  flex: 0 0 70px;
  min-width: 70px;
}

  .role-box select {
    width: 100%;
    height: 32px;
    font-size: 12px;
    padding-right: 28px;  /* IMPORTANT: space for dropdown arrow */
    text-overflow: ellipsis;
  }

  /* ADD BUTTON stays compact */
  .add-box .btn {
    height: 32px;
    font-size: 11px;
    padding: 5px 8px;
    white-space: nowrap;
  }
}

/* MAIN CONTENT HEADER MOBILE */
@media (max-width: 768px) {

  .main-content h4 {
    font-size: 1rem;
  }

  .main-content h4 i {
    font-size: 0.95rem;
  }

}

/* mobile default */
.user-title {
  font-size: 16px;
}

/* desktop */
@media (min-width: 769px) {
  .user-title {
    font-size: 22px;
  }
}

/* Single action button */
.action-buttons-single{
    display:flex;
    justify-content:center;   /* horizontal */
    align-items:center;       /* vertical */
    min-width:140px;
    width:100%;
    height:100%;
}

.action-buttons-single .btn{
    width:65px;
    font-size:11px;
    padding:4px 6px;
}

@media (max-width:768px){
    .action-buttons-single{
        min-width:120px;
    }

    .action-buttons-single .btn{
        width:58px;
        font-size:9px;
        padding:2px 4px;
    }
}

#usersTable td:last-child{
    vertical-align: middle;
    text-align: center;
}

</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="height:70px;">
  <div class="container-fluid">
    <button id="hamburger" class="d-flex d-lg-none"><i class="bi bi-list"></i></button>
    <a class="navbar-brand d-flex align-items-center" href="#"><img src="assets/enviromanage-logo.png" alt="Logo" style="height:40px;"></a>
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
    <a class="nav-link" href="admin-announcements.php"><i class="bi bi-megaphone-fill"></i> <span>Announcements</span></a>
    <a class="nav-link" href="admin-news.php"><i class="bi bi-newspaper"></i> <span>News</span></a>
    <a class="nav-link active" href="admin-usermanagement.php"><i class="bi bi-people-fill"></i> <span>User Management</span></a>
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
 <!-- HEADER (COMPACT + INFO TOGGLE) -->
<div class="d-flex align-items-center gap-2 mb-2">

  <h5 class="fw-semibold mb-0 user-title">
    <i class="bi bi-people-fill text-success me-1"></i>
    User Management
  </h5>

  <!-- INFO BUTTON -->
  <button type="button"
    id="userPageInfoBtn"
    class="btn btn-sm btn-light border rounded-circle"
    style="width:24px;height:24px;padding:0;">

    <i class="bi bi-info-circle text-success"
       style="font-size:11px;"></i>

  </button>

</div>

<!-- INFO CARD (HIDDEN BY DEFAULT) -->
<div id="userPageInfoCard"
  class="alert mt-2 d-none"
  style="
    background:#e9f7ef;
    border:1px solid #1e5631;
    color:#1e5631;
    font-size:12px;
    border-radius:8px;
    line-height:1.4;
  ">

  This module is used to manage user accounts and system access.

  <br><br>

  <strong>Features:</strong><br>
  • Create and manage admin accounts<br>
  • Edit user information and roles<br>
  • Activate or deactivate accounts<br>
  • Search and filter users quickly<br>
  • View complete resident information<br>

</div>

<!-- SEARCH + ROLE + ADD (SAME ROW ALWAYS) -->
<div class="user-controls d-flex align-items-center gap-2 mt-3 mb-3 flex-nowrap">

  <!-- SEARCH -->
  <div class="search-box flex-grow-1">
    <input type="text" id="searchInput" class="form-control form-control-sm"
      placeholder="Search by name, email, username...">
  </div>

  <!-- ROLE -->
  <div class="role-box">
    <select id="roleFilter" class="form-select form-select-sm">
      <option value="">All Roles</option>
      <option value="admin">Admin</option>
      <option value="collector">Collector</option>
      <option value="resident">Resident</option>
      <option value="barangay_secretary">Barangay Secretary</option>
    </select>
  </div>

  <!-- ADD BUTTON -->
  <div class="add-box">
    <button class="btn btn-success btn-sm"
      data-bs-toggle="modal"
      data-bs-target="#addUserModal">
      <i class="bi bi-person-plus-fill"></i> Add User
    </button>
  </div>

</div>

    <div class="table-responsive">
      <table class="table table-bordered" id="usersTable">
       <thead>
  <tr>
    <th>ID</th><th>Name</th><th>Email</th><th>Username</th><th>Phone</th>
    <th>Address</th><th>Role</th><th>Status</th><th>Account Status</th><th>Actions</th>
  </tr>
</thead>
        <tbody></tbody>
      </table>
</div>

<!-- PAGINATION -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">

  <div class="text-center text-md-start">
    <small id="paginationInfo" class="text-muted"></small>
  </div>

  <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">

    <button id="prevPageBtn" class="btn btn-sm btn-outline-success">
      Previous
    </button>

    <span id="pageNumber" class="fw-semibold px-2">
      Page 1
    </span>

    <button id="nextPageBtn" class="btn btn-sm btn-outline-success">
      Next
    </button>

  </div>

</div>

</div>
  </div>
</div>

<!-- ADD USER MODAL -->
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addUserForm">
        <div class="modal-header">
          <h5 class="modal-title">Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2"><label>First Name</label><input type="text" id="addFirstName" class="form-control" required></div>
          <div class="mb-2"><label>Last Name</label><input type="text" id="addLastName" class="form-control" required></div>
          <div class="mb-2"><label>Middle Initial</label><input type="text" id="addMI" class="form-control" maxlength="1" placeholder="Optional"><small class="text-muted">Enter Middle Initial (optional)</small></div>
          <div class="mb-2"><label>Email</label><input type="email" id="addEmail" class="form-control" required></div>
          <div class="mb-2"><label>Username</label><input type="text" id="addUsername" class="form-control" required></div>
          <div class="mb-2 position-relative">
            <label>Phone Number</label>
            <div class="input-group">
              <span class="input-group-text">+63</span>
              <input type="text" id="addPhone" class="form-control" placeholder="9123456789" maxlength="10" pattern="[9][0-9]{9}" required>
            </div>
            <small class="text-muted">Enter 10-digit mobile number starting with 9</small>
          </div>
          <div class="mb-2">
  <label>Gender</label>
  <select id="addGender" class="form-select" required>
    <option value="">Select Gender</option>
    <option value="Male">Male</option>
    <option value="Female">Female</option>
    <option value="Others">Others</option>
  </select>
</div>

<div class="mb-2">
  <label>Birthdate</label>
  <input type="date" id="addBirthdate" class="form-control" required>
</div>

<div class="position-relative">
  <label>Barangay</label>
  <input type="text" id="addBarangay" class="form-control barangay-input" autocomplete="off" required>
  <div class="suggestion-box list-group position-absolute w-100"></div>
</div>

<div class="position-relative">
  <label>Street</label>
  <input type="text" id="addStreet" class="form-control house-street-input" autocomplete="off" required>
  <div class="suggestion-box list-group position-absolute w-100"></div>
</div>

<div class="mb-2">
  <label>House No.</label>
  <input type="text" id="addHouseNo" class="form-control" required>
</div>

<div class="mb-2">
  <label>Postal Code</label>
  <input type="text" id="addPostal" class="form-control" value="4322" readonly>
</div>

          <div class="mb-2 position-relative">
            <label>Password</label>
            <input type="password" id="addPassword" class="form-control" required>
            <i class="bi bi-eye-slash-fill toggle-password" style="position:absolute; top:70%; right:10px; transform:translateY(-50%); cursor:pointer;"></i>
          </div>

          <!-- Password Strength Bar -->
          <div class="strength-bar-container mb-2">
            <div class="strength-bar-fill"></div>
          </div>

          <!-- Password Requirements -->
          <ul class="requirements-list mb-2">
            <li>At least 8 characters</li>
            <li>Uppercase letter</li>
            <li>Lowercase letter</li>
            <li>Number</li>
          </ul>

          <div class="mb-2">
            <label>Role</label>
           <select id="addRole" class="form-select" required>
  <option value="admin">Admin</option>
  <option value="collector">Collector</option>
  <option value="barangay_secretary">Barangay Secretary</option>
</select>
          </div>
        </div>
        <!-- PROFILE PHOTO -->
<div class="mb-2">
  <label>Profile Photo</label>
  <input type="file" id="addPhoto" class="form-control" accept="image/*">

  <!-- Preview -->
  <div class="mt-2 text-center">
    <img id="photoPreview"
         src="https://via.placeholder.com/120x120?text=Preview"
         alt="Preview"
         class="img-thumbnail"
         style="width:120px; height:120px; object-fit:cover;">
  </div>

  <small class="text-muted">
    Accepted formats: JPG, JPEG, PNG
  </small>
</div>
        <div class="modal-footer"><button type="submit" class="btn btn-success">Add User</button></div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT USER MODAL -->
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form id="editUserForm" enctype="multipart/form-data">

        <div class="modal-header">
          <h5 class="modal-title">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input type="hidden" id="editUserId">

          <!-- PROFILE PHOTO -->
          <div class="mb-3 text-center">

            <label class="form-label fw-semibold">Profile Photo</label>

            <div class="mb-2">
              <img
                id="editPhotoPreview"
                src="assets/default-user.png"
                alt="Profile Photo"
                class="img-thumbnail"
                style="
                  width:120px;
                  height:120px;
                  object-fit:cover;
                  border-radius:50%;
                "
              >
            </div>

            <input
              type="file"
              id="editPhoto"
              class="form-control"
              accept="image/jpeg,image/jpg,image/png"
            >

            <small class="text-muted">
              Leave blank to keep current photo.
            </small>

          </div>

          <div class="mb-2">
            <label>First Name</label>
            <input type="text" id="editFirstName" class="form-control" required>
          </div>

          <div class="mb-2">
            <label>Last Name</label>
            <input type="text" id="editLastName" class="form-control" required>
          </div>

          <div class="mb-2">
            <label>Middle Initial</label>
            <input
              type="text"
              id="editMI"
              class="form-control"
              maxlength="1"
              placeholder="Optional"
            >
            <small class="text-muted">
              Enter Middle Initial (optional)
            </small>
          </div>

          <div class="mb-2">
            <label>Email</label>
            <input type="email" id="editEmail" class="form-control" required>
          </div>

          <div class="mb-2 position-relative">

            <label>Phone Number</label>

            <div class="input-group">
              <span class="input-group-text">+63</span>

              <input
                type="text"
                id="editPhone"
                class="form-control"
                placeholder="9123456789"
                maxlength="10"
                pattern="[9][0-9]{9}"
                required
              >
            </div>

            <small class="text-muted">
              Enter 10-digit mobile number starting with 9
            </small>

          </div>

          <div class="mb-2">
            <label>Gender</label>

            <select id="editGender" class="form-select" required>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Others">Others</option>
            </select>
          </div>

          <div class="mb-2">
            <label>Birthdate</label>
            <input type="date" id="editBirthdate" class="form-control" required>
          </div>
<div class="position-relative">
  <label>Barangay</label>
  <input type="text" id="editBarangay" class="form-control barangay-input" autocomplete="off" required>
  <div class="suggestion-box list-group position-absolute w-100"></div>
</div>

<div class="position-relative">
  <label>Street</label>
  <input type="text" id="editStreet" class="form-control house-street-input" autocomplete="off" required>
  <div class="suggestion-box list-group position-absolute w-100"></div>
</div>

<div class="mb-2">
  <label>House No.</label>
  <input type="text" id="editHouseNo" class="form-control" required>
</div>

         <div class="mb-2">
  <label>Postal Code</label>
  <input type="text" id="editPostal" class="form-control" value="4322" readonly>
</div>

          <div class="mb-2">

            <label>Role</label>

            <select id="editRole" class="form-select" required>
              <option value="admin">Admin</option>
              <option value="collector">Collector</option>
              <option value="barangay_secretary">Barangay Secretary</option>
            </select>

          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            Save Changes
          </button>
        </div>

      </form>

    </div>
  </div>
</div>


<!-- VIEW ALL MODAL -->
<div class="modal fade" id="viewUserModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">User Full Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <table class="table table-bordered">
          <tbody id="viewUserBody"></tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<!-- DEACTIVATION REASON MODAL -->
<div class="modal fade" id="deactivateModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title text-danger">
          Deactivate User Account
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <input type="hidden" id="deactivateUserId">

        <div class="mb-2">
          <label class="form-label">Reason</label>
          <select id="deactivateReason" class="form-select" required>
            <option value="">Select reason</option>
            <option value="Violation of rules">Violation of rules</option>
            <option value="Suspicious activity">Suspicious activity</option>
            <option value="User request">User request</option>
            <option value="System cleanup">System cleanup</option>
            <option value="Other">Other</option>
          </select>
        </div>

        <div class="mb-2">
          <label class="form-label">Description</label>
          <textarea id="deactivateDescription" class="form-control" rows="3"
            placeholder="Add additional details..."></textarea>
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" id="confirmDeactivateBtn">
          Confirm Deactivation
        </button>
      </div>

    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

 /* INFO CARD TOGGLE */
document.getElementById('userPageInfoBtn').addEventListener('click', () => {

  const card = document.getElementById('userPageInfoCard');

  card.classList.toggle('d-none');

});

 let barangayData = {};

// Load JSON
fetch('barangays.json')
  .then(res => res.json())
  .then(data => {
    barangayData = data;
  })
  .catch(err => console.error('Error loading barangays:', err));

function initAddressAutocomplete(scope) {

  const barangayInput = scope.querySelector('.barangay-input');
  const streetInput = scope.querySelector('.house-street-input');

  if (!barangayInput || !streetInput) return;

  const barangayBox = barangayInput.parentElement.querySelector('.suggestion-box');
  const streetBox = streetInput.parentElement.querySelector('.suggestion-box');

  // ===========================
  // BARANGAY AUTOCOMPLETE
  // ===========================
  barangayInput.addEventListener('input', function () {

    const value = this.value.toLowerCase().trim();
    barangayBox.innerHTML = '';

    if (!value) {
      barangayBox.style.display = 'none';
      return;
    }

    const matches = Object.keys(barangayData)
      .filter(b => b.toLowerCase().includes(value))
      .slice(0, 8);

    if (matches.length === 0) {
      barangayBox.style.display = 'none';
      return;
    }

    matches.forEach(barangay => {
      const item = document.createElement('div');
      item.className = 'list-group-item list-group-item-action';
      item.textContent = barangay;

      item.addEventListener('click', () => {
        barangayInput.value = barangay;

        streetInput.value = '';
        streetBox.innerHTML = '';
        streetBox.style.display = 'none';

        barangayBox.style.display = 'none';
      });

      barangayBox.appendChild(item);
    });

    barangayBox.style.display = 'block';
  });

  // ===========================
  // STREET AUTOCOMPLETE
  // ===========================
  streetInput.addEventListener('input', function () {

    const selectedBarangay = barangayInput.value;
    const value = this.value.toLowerCase().trim();

    streetBox.innerHTML = '';

    if (!selectedBarangay || !barangayData[selectedBarangay]) {
      streetBox.style.display = 'none';
      return;
    }

    const matches = barangayData[selectedBarangay]
      .filter(s => s.toLowerCase().includes(value))
      .slice(0, 8);

    if (matches.length === 0) {
      streetBox.style.display = 'none';
      return;
    }

    matches.forEach(street => {
      const item = document.createElement('div');
      item.className = 'list-group-item list-group-item-action';
      item.textContent = street;

      item.addEventListener('click', () => {
        streetInput.value = street;
        streetBox.innerHTML = '';
        streetBox.style.display = 'none';
      });

      streetBox.appendChild(item);
    });

    streetBox.style.display = 'block';
  });

  // ===========================
  // CLICK OUTSIDE CLOSE
  // ===========================
  document.addEventListener('click', function (e) {
    if (!scope.contains(e.target)) {
      barangayBox.style.display = 'none';
      streetBox.style.display = 'none';
    }
  });
}

// ----------------------
// SIDEBAR LOGIC
// ----------------------
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleBtn');
const closeBtn = document.getElementById('closeBtn');
const sidebarControls = document.getElementById('sidebarControls');
const hamburger = document.getElementById('hamburger');
const mainContent = document.querySelector('.main-content');

function isMobile(){ return window.innerWidth <= 768; }

function updateContentMargin(){
  if(!isMobile()){
    mainContent.style.marginLeft='220px';
    sidebarControls.style.display='none';
    hamburger.style.display='none';
    sidebar.classList.remove('expanded','hidden');
  } else {
    mainContent.style.marginLeft = sidebar.classList.contains('hidden') ? '0' : '70px';
  }
}

toggleBtn.addEventListener('click', ()=>{
  if(!isMobile()) return;
  sidebar.classList.toggle('expanded');
  toggleBtn.querySelector('i').className = sidebar.classList.contains('expanded') ? 'bi bi-chevron-left' : 'bi bi-chevron-right';
  updateContentMargin();
});

closeBtn.addEventListener('click', ()=>{
  if(!isMobile()) return;
  sidebar.classList.add('hidden');
  sidebarControls.classList.add('hidden');
  hamburger.style.display='flex';
  updateContentMargin();
});

hamburger.addEventListener('click', ()=>{
  if(!isMobile()) return;
  sidebar.classList.remove('hidden');
  sidebarControls.classList.remove('hidden');
  sidebar.classList.remove('expanded');
  toggleBtn.querySelector('i').className='bi bi-chevron-right';
  hamburger.style.display='none';
  updateContentMargin();
});

window.addEventListener('resize', updateContentMargin);
updateContentMargin();

// GLOBAL USER STORAGE
let allUsers = [];

// PAGINATION
let currentPage = 1;
const rowsPerPage = 10;

// FETCH USERS
async function fetchUsers() {
  try {
    const res = await fetch('admin-fetch-user.php');
    const data = await res.json();

    console.log("FETCHED USERS:", data);

    // ✅ FIX: ensure array is assigned properly
    allUsers = Array.isArray(data)
      ? data
      : (data.users || []);

    renderUsers();

  } catch (err) {
    console.error("Fetch error:", err);
    allUsers = [];
    renderUsers();
  }
}

function getFilteredUsers() {
  const search = document.getElementById('searchInput').value.toLowerCase();
  const roleFilter = document.getElementById('roleFilter').value;

  return allUsers.filter(user => {
    const name = (user.display_name || '').toLowerCase();
    const email = (user.email || '').toLowerCase();
    const username = (user.username || '').toLowerCase();

    const matchesSearch =
      name.includes(search) ||
      email.includes(search) ||
      username.includes(search);

    const matchesRole =
      roleFilter === '' || user.role === roleFilter;

    return matchesSearch && matchesRole;
  });
}

function renderUsers() {
  const tbody = document.querySelector('#usersTable tbody');

  tbody.innerHTML = '';

  let filtered = getFilteredUsers();

  const totalPages = Math.ceil(filtered.length / rowsPerPage);

  if (currentPage > totalPages && totalPages > 0) {
    currentPage = totalPages;
  }

  const startIndex = (currentPage - 1) * rowsPerPage;
  const endIndex = startIndex + rowsPerPage;

  const paginatedUsers = filtered.slice(startIndex, endIndex);

  if (filtered.length === 0) {
    tbody.innerHTML = '<tr><td colspan="10" class="text-center">No users found.</td></tr>';
    return;
  }

  paginatedUsers.forEach(user => {
    const accountStatus = user.is_logged_in == 1 ? 'Activated' : 'Deactivated';
    const status = user.is_logged_in == 1 ? 'Active' : 'Inactive';

    const statusBadge = `
      <span class="badge ${status === 'Active' ? 'bg-success' : 'bg-secondary'}">
        ${status}
      </span>`;

    const accountStatusBadge = `
      <span class="badge ${accountStatus === 'Activated' ? 'bg-success' : 'bg-secondary'}">
        ${accountStatus}
      </span>`;

   let buttonCount = 1; // View button

if (user.role !== 'resident') buttonCount++; // Activate/Deactivate
if (
  user.role === 'admin' ||
  user.role === 'collector' ||
  user.role === 'barangay_secretary'
) buttonCount++; // Delete
if (user.role !== 'resident') buttonCount++; // Edit

let actionButtons = `
<div class="${buttonCount === 1 ? 'action-buttons-single' : 'action-buttons'}">
`;

/* VIEW */
actionButtons += `
  <button class="btn btn-sm btn-info" onclick="viewAllUser(${user.id})">
    View All
  </button>
`;

/* ENABLE / DISABLE (not for residents) */
if (user.role !== 'resident') {
  actionButtons += `
    <button class="btn btn-sm ${accountStatus === 'Activated' ? 'btn-warning' : 'btn-success'}"
      onclick="toggleUserStatus(${user.id}, '${accountStatus}')">
      ${accountStatus === 'Activated' ? 'Deactivate' : 'Activate'}
    </button>
  `;
}

/* DELETE (only for admin/collector/barangay_secretary) */
if (
  user.role === 'admin' ||
  user.role === 'collector' ||
  user.role === 'barangay_secretary'
) {
  actionButtons += `
    <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">
      Delete
    </button>
  `;
}

/* EDIT (not for residents) */
if (user.role !== 'resident') {
  actionButtons += `
    <button class="btn btn-sm btn-primary" onclick="editUser(${user.id})">
      <i class="bi bi-pencil-fill"></i> Edit
    </button>
  `;
}

/* CLOSE WRAPPER */
actionButtons += `</div>`;

    const displayName = (user.display_name === 'Pending')
      ? `<span class="badge bg-warning text-dark">Pending</span>`
      : (user.display_name || '-');

    const address = user.display_address || '';

    const displayAddress = (address === 'Pending')
  ? `<span class="badge bg-warning text-dark">Pending</span>`
  : address;

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${user.id}</td>
      <td>${displayName}</td>
      <td>${user.email || '-'}</td>
      <td>${user.username || '-'}</td>
      <td>${user.phone || '-'}</td>
      <td class="address-cell" title="${address}">
    ${displayAddress}
</td>
      <td>${user.role || '-'}</td>
      <td>${statusBadge}</td>
      <td>${accountStatusBadge}</td>
      <td>${actionButtons}</td>
    `;

    tbody.appendChild(tr);
  });

  document.getElementById('pageNumber').textContent =
    `Page ${currentPage} of ${totalPages || 1}`;

  document.getElementById('paginationInfo').textContent =
    `Showing ${filtered.length === 0 ? 0 : startIndex + 1}
     to ${Math.min(endIndex, filtered.length)}
     of ${filtered.length} users`;

  document.getElementById('prevPageBtn').disabled = currentPage === 1;
  document.getElementById('nextPageBtn').disabled = currentPage === totalPages || totalPages === 0;
}


function viewAllUser(id){
  const user = allUsers.find(u => u.id == id);
  if(!user) return;

  const tbody = document.getElementById('viewUserBody');

  // ✅ Use backend computed values (BEST PRACTICE)
  const fullName = user.display_name || 'Pending';
  const fullAddress = user.display_address || 'Pending';

  tbody.innerHTML = `

  <tr>
    <th>Profile Photo</th>
    <td class="text-center">
      <img
        src="${user.profile_photo ? user.profile_photo : 'assets/default-user.png'}"
        alt="Profile Photo"
        class="img-thumbnail"
        style="
          width:150px;
          height:150px;
          object-fit:cover;
          border-radius:50%;
        "
      >
    </td>
  </tr>

  <tr><th>ID</th><td>${user.id ?? '-'}</td></tr>
  <tr><th>Name</th><td>${fullName}</td></tr>
  <tr><th>Username</th><td>${user.username ?? '-'}</td></tr>
  <tr><th>Email</th><td>${user.email ?? '-'}</td></tr>
  <tr><th>Role</th><td>${user.role ?? '-'}</td></tr>
  <tr><th>Gender</th><td>${user.gender ?? '-'}</td></tr>
  <tr><th>Birthdate</th><td>${user.birthdate ?? '-'}</td></tr>
  <tr><th>Phone</th><td>${user.phone ?? '-'}</td></tr>
  <tr><th>Address</th><td>${fullAddress}</td></tr>
  <tr><th>Status</th><td>${user.is_logged_in == 1 ? 'Activated' : 'Deactivated'}</td></tr>
  <tr><th>Last Activity</th><td>${user.last_activity ?? '-'}</td></tr>
  <tr><th>Created At</th><td>${user.created_at ?? '-'}</td></tr>

`;
  new bootstrap.Modal(document.getElementById('viewUserModal')).show();
}

// TOGGLE STATUS
async function toggleUserStatus(id, status) {

  const user = allUsers.find(u => u.id == id);
  if (!user) return;

  const currentUserId = <?php echo $_SESSION['user_id']; ?>;

  // ❌ BLOCK: admin cannot deactivate self
  if (id == currentUserId && status === 'Activated') {
    Swal.fire('Not Allowed', 'You cannot deactivate your own account.', 'error');
    return;
  }

  const isActivating = status !== 'Activated';

  // =========================
  // ACTIVATE USER (no reason)
  // =========================
  if (isActivating) {

    const result = await Swal.fire({
      title: 'Activate User?',
      text: 'Do you want to activate this user?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, Activate'
    });

    if (!result.isConfirmed) return;

    await updateUserStatus(id, 1, null, null);
    return;
  }

  // =========================
  // DEACTIVATION FLOW
  // =========================
  const confirm = await Swal.fire({
    title: 'Deactivate User?',
    text: 'This user will lose access to the system.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Continue'
  });

  if (!confirm.isConfirmed) return;

  // =========================
  // RESIDENT → SHOW REASON MODAL
  // =========================
  if (user.role === 'resident') {

    document.getElementById('deactivateUserId').value = id;
    document.getElementById('deactivateReason').value = '';
    document.getElementById('deactivateDescription').value = '';

    new bootstrap.Modal(
      document.getElementById('deactivateModal')
    ).show();

    return;
  }

  // =========================
  // OTHER ROLES → DIRECT DEACTIVATE
  // =========================
  await updateUserStatus(id, 0, null, null);
}

  // CONFIRM DEACTIVATION
document.getElementById('confirmDeactivateBtn').addEventListener('click', async function () {

  const id = document.getElementById('deactivateUserId').value;
  const reason = document.getElementById('deactivateReason').value;
  const description = document.getElementById('deactivateDescription').value;

  if (!reason) {
    Swal.fire('Required', 'Please select a reason.', 'warning');
    return;
  }

  await updateUserStatus(id, 0, reason, description);

  bootstrap.Modal.getInstance(document.getElementById('deactivateModal')).hide();
});

async function updateUserStatus(id, status, reason, description) {

  try {

    const response = await fetch('admin-toggle-user.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        id,
        is_logged_in: status,
        reason: status == 0
          ? (reason ? reason + (description ? " - " + description : "") : null)
          : null
      })
    });

    const data = await response.json();

    if (data.success) {

      Swal.fire({
        icon: 'success',
        title: 'Updated',
        text: status == 1 ? 'User activated' : 'User deactivated'
      });

      fetchUsers();

    } else {
      Swal.fire('Error', data.message, 'error');
    }

  } catch (err) {
    console.error(err);
    Swal.fire('Error', 'Server error', 'error');
  }
}

// DELETE USER
async function deleteUser(id){
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: 'Do you want to delete this user?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    cancelButtonText: 'Cancel'
  });

  if(!result.isConfirmed) return;

  const res = await fetch('admin-delete-user.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify({id})
  });
  const data = await res.json();
  if(data.success) fetchUsers();
  else Swal.fire('Error', data.message, 'error');
}

// ADD USER
document.getElementById('addUserForm').addEventListener('submit', async function(e){
  e.preventDefault();

  let first = document.getElementById('addFirstName').value.trim();
  let last = document.getElementById('addLastName').value.trim();
  let mi = document.getElementById('addMI').value.trim().toUpperCase();
  let phone = document.getElementById('addPhone').value.trim();

  const phoneRegex = /^9\d{9}$/;
  if(!phoneRegex.test(phone)){
    Swal.fire('Invalid', "Phone number must be 10 digits and start with 9.", 'error');
    return;
  }
const password = document.getElementById('addPassword').value;

const formData = new FormData();

formData.append('first', first);
formData.append('last', last);
formData.append('mi', mi);
formData.append('email', document.getElementById('addEmail').value);
formData.append('username', document.getElementById('addUsername').value);
formData.append('password', password);
formData.append('role', document.getElementById('addRole').value);

formData.append('phone', phone);

formData.append('gender', document.getElementById('addGender').value);
formData.append('birthdate', document.getElementById('addBirthdate').value);
formData.append('house_no', document.getElementById('addHouseNo').value);
formData.append('street', document.getElementById('addStreet').value);
formData.append('barangay', document.getElementById('addBarangay').value);
formData.append('postal_code', '4322');

// PHOTO
const photoFile = document.getElementById('addPhoto').files[0];

if(photoFile){
  formData.append('photo', photoFile);
}

  try {
 const res = await fetch('admin-add-user.php', {
  method:'POST',
  body: formData
});

  const text = await res.text();
  console.log("RAW RESPONSE:", text);

  let data;
  try {
    data = JSON.parse(text);
  } catch (err) {
    console.error("Invalid JSON response:", text);
    Swal.fire('Error', 'Server returned invalid response', 'error');
    return;
  }

  if(data.success){
    document.getElementById('addUserForm').reset();
    bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
    fetchUsers();
    Swal.fire('Success','User added successfully.','success');
  } else {
    Swal.fire('Error', data.message, 'error');
  }

} catch(err){
  console.error(err);
  Swal.fire('Error','Error adding user.','error');
}
});

// EDIT USER (Open Modal + Populate Data)
function editUser(id){

  const user = allUsers.find(u => u.id == id);

  if(!user) return;

  document.getElementById('editUserId').value = user.id ?? '';

  // ----------------------
  // NAME
  // ----------------------

  document.getElementById('editFirstName').value =
    user.first_name ?? '';

  document.getElementById('editLastName').value =
    user.last_name ?? '';

  document.getElementById('editMI').value =
    user.middle_initial ?? '';

  // ----------------------
  // BASIC INFO
  // ----------------------

  document.getElementById('editEmail').value =
    user.email ?? '';

  document.getElementById('editRole').value =
    user.role ?? '';

  // ----------------------
  // PHONE
  // ----------------------

  let phone = user.phone ?? '';

  phone = phone.replace('+63', '').replace(/\D/g, '');

  document.getElementById('editPhone').value = phone;

  // ----------------------
  // OTHER FIELDS
  // ----------------------

  document.getElementById('editGender').value =
    user.gender ?? '';

  document.getElementById('editBirthdate').value =
    user.birthdate ?? '';

    document.getElementById('editBarangay').value =
  user.barangay ?? '';

 document.getElementById('editStreet').value =
  user.street ?? '';

document.getElementById('editHouseNo').value =
  user.house_no ?? '';

document.getElementById('editPostal').value = '4322';
document.getElementById('editPostal').setAttribute('readonly', true);
  // ----------------------
  // PROFILE PHOTO
  // ----------------------

  document.getElementById('editPhotoPreview').src =
    user.profile_photo
      ? user.profile_photo
      : 'assets/default-user.png';

  // CLEAR FILE INPUT
  document.getElementById('editPhoto').value = '';

  // ----------------------
  // OPEN MODAL
  // ----------------------

  new bootstrap.Modal(
    document.getElementById('editUserModal')
  ).show();
}

// SAVE EDITED USER
document.getElementById('editUserForm').addEventListener('submit', async function(e){

  e.preventDefault();

  const phone =
    document.getElementById('editPhone').value.trim();

  const phoneRegex = /^9\d{9}$/;

  if(!phoneRegex.test(phone)){

    Swal.fire(
      'Invalid',
      'Phone number must be 10 digits and start with 9.',
      'error'
    );

    return;
  }

  const formData = new FormData();

  formData.append(
    'id',
    document.getElementById('editUserId').value
  );

  formData.append(
    'first',
    document.getElementById('editFirstName').value.trim()
  );

  formData.append(
    'last',
    document.getElementById('editLastName').value.trim()
  );

  formData.append(
    'mi',
    document.getElementById('editMI').value.trim()
  );

  formData.append(
    'email',
    document.getElementById('editEmail').value.trim()
  );

  formData.append(
    'role',
    document.getElementById('editRole').value
  );

  formData.append('phone', phone);

  formData.append(
    'gender',
    document.getElementById('editGender').value
  );

  formData.append(
    'birthdate',
    document.getElementById('editBirthdate').value
  );

formData.append(
  'barangay',
  document.getElementById('editBarangay').value.trim()
);

formData.append(
  'street',
  document.getElementById('editStreet').value.trim()
);

formData.append(
  'house_no',
  document.getElementById('editHouseNo').value.trim()
);

formData.append(
  'postal_code',
  '4322'
);

  // PHOTO
  const photoFile =
    document.getElementById('editPhoto').files[0];

  if(photoFile){
    formData.append('photo', photoFile);
  }

  try {

    const res = await fetch('admin-update-user.php', {
      method: 'POST',
      body: formData
    });

    const data = await res.json();

    if(data.success){

      bootstrap.Modal
        .getInstance(document.getElementById('editUserModal'))
        .hide();

      fetchUsers();

      Swal.fire(
        'Success',
        'User updated successfully.',
        'success'
      );

    } else {

      Swal.fire(
        'Error',
        data.message,
        'error'
      );

    }

  } catch(err){

    console.error(err);

    Swal.fire(
      'Error',
      'Server error.',
      'error'
    );

  }

});

//Password toggle
const addPassword = document.getElementById('addPassword');
const togglePassword = document.querySelector('#addUserModal .toggle-password');

togglePassword.addEventListener('click', function () {

  if (addPassword.type === 'password') {
    addPassword.type = 'text';
    this.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
  } else {
    addPassword.type = 'password';
    this.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
  }

});

// Password strength bar
const addPasswordInput = document.getElementById('addPassword');
const addStrengthBar = document.querySelector('#addUserModal .strength-bar-fill');
const addReqs = document.querySelectorAll('#addUserModal .requirements-list li');

addPasswordInput.addEventListener('input', ()=>{
  const val = addPasswordInput.value;
  let score = 0;

  if(val.length >= 8){ score++; addReqs[0].style.color='green'; } else addReqs[0].style.color='#6c757d';
  if(/[A-Z]/.test(val)){ score++; addReqs[1].style.color='green'; } else addReqs[1].style.color='#6c757d';
  if(/[a-z]/.test(val)){ score++; addReqs[2].style.color='green'; } else addReqs[2].style.color='#6c757d';
  if(/\d/.test(val)){ score++; addReqs[3].style.color='green'; } else addReqs[3].style.color='#6c757d';

  addStrengthBar.style.width = (score*25) + '%';
  addStrengthBar.style.backgroundColor = score < 2 ? "#dc3545" : score < 4 ? "#ffc107" : "#28a745";
});

// PHOTO PREVIEW
document.getElementById('addPhoto').addEventListener('change', function(e){

  const file = e.target.files[0];

  if(!file) return;

  const allowed = ['image/jpeg', 'image/jpg', 'image/png'];

  if(!allowed.includes(file.type)){
    Swal.fire('Invalid File', 'Only JPG, JPEG, and PNG are allowed.', 'error');
    e.target.value = '';
    return;
  }

  const reader = new FileReader();

  reader.onload = function(event){
    document.getElementById('photoPreview').src = event.target.result;
  };

  reader.readAsDataURL(file);

});

// PHOTO PREVIEW (ADD USER)
document.getElementById('addPhoto').addEventListener('change', function(e){

  const file = e.target.files[0];
  if(!file) return;

  const allowed = ['image/jpeg', 'image/jpg', 'image/png'];

  if(!allowed.includes(file.type)){
    Swal.fire('Invalid File', 'Only JPG, JPEG, and PNG are allowed.', 'error');
    e.target.value = '';
    return;
  }

  const reader = new FileReader();

  reader.onload = function(event){
    document.getElementById('photoPreview').src = event.target.result;
  };

  reader.readAsDataURL(file);
});


// EDIT PHOTO PREVIEW
document.getElementById('editPhoto').addEventListener('change', function(e){

  const file = e.target.files[0];

  if(!file) return;

  const allowed = ['image/jpeg', 'image/jpg', 'image/png'];

  if(!allowed.includes(file.type)){

    Swal.fire(
      'Invalid File',
      'Only JPG, JPEG, and PNG are allowed.',
      'error'
    );

    e.target.value = '';
    return;
  }

  const reader = new FileReader();

  reader.onload = function(event){
    document.getElementById('editPhotoPreview').src = event.target.result;
  };

  reader.readAsDataURL(file);

});

// SEARCH FILTER
document.getElementById('searchInput')
  .addEventListener('input', () => {

    currentPage = 1;
    renderUsers();

});

// ROLE FILTER
document.getElementById('roleFilter')
  .addEventListener('change', () => {

    currentPage = 1;
    renderUsers();

});

  // PREVIOUS PAGE
document.getElementById('prevPageBtn').addEventListener('click', () => {
  if (currentPage > 1) {
    currentPage--;
    renderUsers();
  }
});

// NEXT PAGE
document.getElementById('nextPageBtn').addEventListener('click', () => {
  const filtered = getFilteredUsers();
  const totalPages = Math.ceil(filtered.length / rowsPerPage);

  if (currentPage < totalPages) {
    currentPage++;
    renderUsers();
  }
});

fetchUsers();
// ----------------------
// HIDE SIDEBAR CONTROLS WHEN MODAL OPEN
// ----------------------
document.querySelectorAll('.modal').forEach(modalEl => {
  modalEl.addEventListener('show.bs.modal', () => {
    if(isMobile()){ sidebarControls.style.display = 'none'; }
  });

  modalEl.addEventListener('hidden.bs.modal', () => {
    if(isMobile()){
      sidebarControls.style.display = sidebar.classList.contains('hidden') ? 'none' : 'flex';
    }
  });
});

document.getElementById('logoutBtn').addEventListener('click', function() {
    window.location.href = 'logout.php';
});

initAddressAutocomplete(document.getElementById('addUserModal'));
initAddressAutocomplete(document.getElementById('editUserModal'));
</script>
</body>
</html>