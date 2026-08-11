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
<title>EnviroManage | Trucks & Collectors</title>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; padding-top: 70px; }
a { text-decoration: none; }
h4, h5, h6 { color: #1e5631; }
.navbar { background-color: #1e5631 !important; }

.navbar .container-fluid{
    height:70px;
    display:flex;
    align-items:center;
}

.navbar{
    min-height:70px;
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

/* Desktop */
@media (min-width: 769px) {
  .sidebar { width: 220px !important; transform: none !important; }
  .sidebar .nav-link { justify-content: flex-start !important; padding-left: 20px; }
  .sidebar .nav-link span { display: inline !important; margin-left: 10px; }
  .main-content { margin-left: 220px !important; }
  #sidebarControls, #toggleBtn, #closeBtn, #hamburger { display: none !important; }
}

/* Mobile */
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

    #sidebarControls{
        display:flex;
    }

}
/* Page Specific */
.card { background-color: #fff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.1); }
.badge-active { background:#198754; }
.badge-inactive { background:#dc3545; }


/* ===================== MOBILE MODAL FIX ===================== */
@media (max-width: 768px) {
  /* Keep modal width responsive, but don't touch select */
  #addTruckModal .modal-dialog {
    max-width: 400px;      /* reasonable width on small devices */
    margin: 1.5rem auto;   /* center vertically */
  }

  /* Modal content & body */
  #addTruckModal .modal-content {
    padding: 0;           /* let Bootstrap handle spacing */
    overflow-x: hidden;   /* prevent horizontal scroll */
  }

  #addTruckModal .modal-body {
    padding: 1rem;        /* default Bootstrap padding */
  }

  /* Remove all custom width / font-size rules for select */
  #addTruckModal .form-select {
    width: auto !important;   /* let it size naturally */
    max-width: 100%;          /* prevent overflow outside modal */
    font-size: 1rem;          /* same as desktop */
    box-sizing: border-box;   /* consistent sizing */
  }

  #addTruckModal .form-select option {
    white-space: normal;      /* allow normal wrapping if needed */
    overflow: visible;
    text-overflow: initial;
  }
}

/* Disabled option style */
select option:disabled {
  background-color: #e9ecef;
  color: #6c757d;
}

/* ================= TABLE MOBILE RESPONSIVE ================= */
@media (max-width: 768px) {

  .table {
    font-size: 0.75rem;
  }

  .table th,
  .table td {
    padding: 0.4rem;
    white-space: nowrap;
    vertical-align: middle;
  }

  .table .btn {
    font-size: 0.7rem;
    padding: 0.2rem 0.45rem;
  }

  .card {
    padding: 0.8rem !important;
  }

  h4 {
    font-size: 1rem;
  }

  h6 {
    font-size: 0.85rem;
  }

  .pagination {
    justify-content: center;
    flex-wrap: wrap;
  }

  .pagination .page-link {
    padding: 0.3rem 0.55rem;
    font-size: 0.75rem;
  }
}

</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="height:70px;">
  <div class="container-fluid">
    <button id="hamburger" class="d-flex d-lg-none"><i class="bi bi-list"></i></button>
    <a class="navbar-brand d-flex align-items-center" href="#"><img src="assets/enviromanage-logo.png" style="height:40px;"></a>
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
                <button class="dropdown-item text-center" id="logoutBtn">
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
    <a class="nav-link active" href="admin-trucks-collectors.php"><i class="bi bi-truck-front-fill"></i> <span>Trucks & Collectors</span></a>
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

<!-- MAIN CONTENT -->
<div class="main-content p-4">

  <!-- TRUCK CARD -->
<div class="card p-4 shadow-sm mt-2 mb-4">
  <h4 class="fw-semibold mb-4">
    <i class="bi bi-truck-front-fill me-2 text-success"></i>Truck Management
  </h4>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="fw-semibold text-primary mb-0">Truck List</h6>

    <button class="btn btn-success btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#addTruckModal">
      <i class="bi bi-plus-lg me-1"></i>Add Truck
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle text-center" id="truckTable">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Plate No.</th>
          <th>Collector Assigned</th>
          <th width="160">Actions</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <!-- TRUCK PAGINATION -->
  <div id="truckPagination"
       class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">

    <div class="text-center text-md-start">
      <small id="truckPaginationInfo" class="text-muted">
        Showing 0 to 0 of 0 trucks
      </small>
    </div>

    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">

      <button id="truckPrevBtn" class="btn btn-sm btn-outline-success">
        Previous
      </button>

      <span id="truckPageNumber" class="fw-semibold px-2">
        Page 1 of 1
      </span>

      <button id="truckNextBtn" class="btn btn-sm btn-outline-success">
        Next
      </button>

    </div>
  </div>
</div>

<!-- COLLECTOR CARD -->
<div class="card p-4 shadow-sm">
  <h4 class="fw-semibold mb-4">
    <i class="bi bi-people-fill me-2 text-success"></i>Collector Management
  </h4>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="fw-semibold text-success mb-0">
      Collectors / Drivers List
    </h6>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle text-center"
           id="collectorTable">

     <thead class="table-light">
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Contact</th>
    <th>Assigned Truck ID</th>
  </tr>
</thead>

      <tbody></tbody>
    </table>
  </div>

  <!-- COLLECTOR PAGINATION -->
  <div id="collectorPagination"
       class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">

    <div class="text-center text-md-start">
      <small id="collectorPaginationInfo" class="text-muted">
        Showing 0 to 0 of 0 collectors
      </small>
    </div>

    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">

      <button id="collectorPrevBtn"
              class="btn btn-sm btn-outline-success">
        Previous
      </button>

      <span id="collectorPageNumber"
            class="fw-semibold px-2">
        Page 1 of 1
      </span>

      <button id="collectorNextBtn"
              class="btn btn-sm btn-outline-success">
        Next
      </button>

    </div>
  </div>
</div>

<!-- ADD TRUCK MODAL -->
<div class="modal fade" id="addTruckModal" tabindex="-1" aria-labelledby="addTruckModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm"> <!-- Centered & smaller on mobile -->
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="addTruckModalLabel"><i class="bi bi-plus-lg me-2"></i>Add Truck</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addTruckForm">
        <div class="modal-body">
          <div class="mb-3">
            <label for="plate_no" class="form-label">Plate Number</label>
            <input type="text" class="form-control" id="plate_no" name="plate_no" required>
          </div>
          <div class="mb-3">
            <label for="collector_id" class="form-label">Assign Collector</label>
            <select class="form-select" id="collector_id" name="collector_id">
              <option value="">-- None --</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Add Truck</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT TRUCK MODAL -->
<div class="modal fade" id="editTruckModal" tabindex="-1" aria-labelledby="editTruckModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="editTruckModalLabel"><i class="bi bi-pencil-fill me-2"></i>Edit Truck</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editTruckForm">
        <div class="modal-body">
          <input type="hidden" id="editTruckId">
          <div class="mb-3">
            <label for="editPlateNo" class="form-label">Plate Number</label>
            <input type="text" class="form-control" id="editPlateNo" name="plate_no" required>
          </div>
          <div class="mb-3">
            <label for="editCollectorId" class="form-label">Assign Collector</label>
            <select class="form-select" id="editCollectorId" name="collector_id">
              <option value="">-- None --</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT COLLECTOR MODAL -->
<div class="modal fade" id="editCollectorModal" tabindex="-1" aria-labelledby="editCollectorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="editCollectorModalLabel"><i class="bi bi-pencil-fill me-2"></i>Edit Collector</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editCollectorForm">
        <div class="modal-body">
          <input type="hidden" id="editCollectorId">
          <div class="mb-3">
  <label for="editCollectorFirstName" class="form-label">First Name</label>
  <input type="text" class="form-control" id="editCollectorFirstName" required>
</div>

<div class="mb-3">
  <label for="editCollectorMiddleInitial" class="form-label">Middle Initial</label>
  <input type="text" class="form-control" id="editCollectorMiddleInitial" maxlength="1">
</div>

<div class="mb-3">
  <label for="editCollectorLastName" class="form-label">Last Name</label>
  <input type="text" class="form-control" id="editCollectorLastName" required>
</div>
          <div class="mb-3">
            <label for="editCollectorTruck" class="form-label">Assigned Truck</label>
            <select class="form-select" id="editCollectorTruck" name="truck_id">
              <option value="">-- None --</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="editCollectorPhone" class="form-label">Contact</label>
            <input type="text" class="form-control" id="editCollectorPhone" name="phone" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Wait for the DOM to load
document.addEventListener('DOMContentLoaded', () => {

  // ===== SIDEBAR LOGIC =====
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('toggleBtn');
  const closeBtn = document.getElementById('closeBtn');
  const sidebarControls = document.getElementById('sidebarControls');
  const hamburger = document.getElementById('hamburger');
  const mainContent = document.querySelector('.main-content');

  function isMobile() { return window.innerWidth <= 768; }

  function updateContentMargin() {
    if (!isMobile()) {
      mainContent.style.marginLeft = '220px';
      sidebarControls.style.display = 'none';
      hamburger.style.display = 'none';
      sidebar.classList.remove('expanded', 'hidden');
    } else {
      mainContent.style.marginLeft = sidebar.classList.contains('hidden') ? '0' : '70px';
    }
  }

  toggleBtn.addEventListener('click', () => {
    if (!isMobile()) return;
    sidebar.classList.toggle('expanded');
    toggleBtn.querySelector('i').className = sidebar.classList.contains('expanded') ? 'bi bi-chevron-left' : 'bi bi-chevron-right';
    updateContentMargin();
  });

  closeBtn.addEventListener('click', () => {
    if (!isMobile()) return;
    sidebar.classList.add('hidden');
    sidebarControls.classList.add('hidden');
    hamburger.style.display = 'flex';
    updateContentMargin();
  });

  hamburger.addEventListener('click', () => {
    if (!isMobile()) return;
    sidebar.classList.remove('hidden');
    sidebarControls.classList.remove('hidden');
    sidebar.classList.remove('expanded');
    toggleBtn.querySelector('i').className = 'bi bi-chevron-right';
    hamburger.style.display = 'none';
    updateContentMargin();
  });

  window.addEventListener('resize', updateContentMargin);
  updateContentMargin();

  // ===== PAGINATION VARIABLES =====
let truckPage = 1;
let collectorPage = 1;

const ITEMS_PER_PAGE = 10;

// ===== RENDER PAGINATION =====
function renderPagination(containerId, currentPage, totalPages, callback, totalItems) {

  if (totalPages <= 0) {
    totalPages = 1;
  }

  // ===== ELEMENTS =====
  const prevBtn = document.getElementById(
    containerId === 'truckPagination'
      ? 'truckPrevBtn'
      : 'collectorPrevBtn'
  );

  const nextBtn = document.getElementById(
    containerId === 'truckPagination'
      ? 'truckNextBtn'
      : 'collectorNextBtn'
  );

  const pageNumber = document.getElementById(
    containerId === 'truckPagination'
      ? 'truckPageNumber'
      : 'collectorPageNumber'
  );

  const paginationInfo = document.getElementById(
    containerId === 'truckPagination'
      ? 'truckPaginationInfo'
      : 'collectorPaginationInfo'
  );

  // ===== PAGE INFO =====
  const startItem = totalItems === 0
    ? 0
    : ((currentPage - 1) * ITEMS_PER_PAGE) + 1;

  const endItem = Math.min(
    currentPage * ITEMS_PER_PAGE,
    totalItems
  );

  const label = containerId === 'truckPagination'
    ? 'trucks'
    : 'collectors';

  paginationInfo.textContent =
    `Showing ${startItem} to ${endItem} of ${totalItems} ${label}`;

  pageNumber.textContent =
    `Page ${currentPage} of ${totalPages}`;

  // ===== BUTTON STATES =====
  prevBtn.disabled = currentPage === 1;
  nextBtn.disabled = currentPage === totalPages;

  // ===== REMOVE OLD EVENTS =====
  prevBtn.replaceWith(prevBtn.cloneNode(true));
  nextBtn.replaceWith(nextBtn.cloneNode(true));

  const newPrevBtn = document.getElementById(prevBtn.id);
  const newNextBtn = document.getElementById(nextBtn.id);

  // ===== EVENTS =====
  newPrevBtn.addEventListener('click', () => {
    if (currentPage > 1) {
      callback(currentPage - 1);
    }
  });

  newNextBtn.addEventListener('click', () => {
    if (currentPage < totalPages) {
      callback(currentPage + 1);
    }
  });
}

  // ===== FETCH & LOAD FUNCTIONS =====
  function loadCollectors(page = 1) {
  collectorPage = page;

  fetch('admin-fetch-collectors.php')
    .then(res => res.json())
    .then(data => {

      const tbody = document.querySelector('#collectorTable tbody');
      tbody.innerHTML = '';

      if (
        data.success &&
        Array.isArray(data.data) &&
        data.data.length > 0
      ) {

        const start = (page - 1) * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;

        const paginatedData = data.data.slice(start, end);
paginatedData.forEach(c => {

  const isAssigned = c.assigned_truck_id !== null && c.assigned_truck_id !== '';

  tbody.innerHTML += `
    <tr class="${isAssigned ? 'table-secondary' : ''}">
      <td>${c.id}</td>

      <td>
        <span style="${isAssigned ? 'opacity:0.6; pointer-events:none;' : ''}">
          ${c.display_name}
        </span>
      </td>

      <td>${c.phone || '-'}</td>
      <td>${c.assigned_truck_id || '-'}</td>
    </tr>
  `;
});
        const totalPages = Math.ceil(
          data.data.length / ITEMS_PER_PAGE
        );

       renderPagination(
  'collectorPagination',
  page,
  totalPages,
  loadCollectors,
  data.data.length
);
      

     } else {
  tbody.innerHTML = `
    <tr>
      <td colspan="4" class="text-center py-4 text-muted">
        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
        No collectors found.
      </td>
    </tr>
  `;

  renderPagination(
    'collectorPagination',
    1,
    1,
    loadCollectors,
    0
  );
}
    });
}
  
  function loadTrucks(page = 1) {
  truckPage = page;

  fetch('admin-fetch-trucks.php')
    .then(res => res.json())
    .then(data => {

      const tbody = document.querySelector('#truckTable tbody');
      tbody.innerHTML = '';

      if (
        data.success &&
        Array.isArray(data.data) &&
        data.data.length > 0
      ) {

        const start = (page - 1) * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;

        const paginatedData = data.data.slice(start, end);

       paginatedData.forEach(truck => {
  tbody.innerHTML += `
    <tr id="truckRow-${truck.id}">
      <td>${truck.id}</td>
      <td>${truck.plate_no}</td>
      <td>${truck.collector_name || '-'}</td>
      <td>
        <button class="btn btn-sm btn-primary mb-1"
          onclick="editTruck(${truck.id})">
          Edit
        </button>

        <button class="btn btn-sm btn-danger"
          onclick="deleteTruck(${truck.id})">
          Delete
        </button>
      </td>
    </tr>
  `;
});

        const totalPages = Math.ceil(
          data.data.length / ITEMS_PER_PAGE
        );

       renderPagination(
  'truckPagination',
  page,
  totalPages,
  loadTrucks,
  data.data.length
);

      } else {
  tbody.innerHTML = `
    <tr>
      <td colspan="4" class="text-center py-4 text-muted">
        <i class="bi bi-truck fs-3 d-block mb-2"></i>
        No trucks found.
      </td>
    </tr>
  `;

  renderPagination(
    'truckPagination',
    1,
    1,
    loadTrucks,
    0
  );
}
    });
}

  // ===== DELETE FUNCTIONS =====
  window.deleteCollector = function(id) {
    Swal.fire({
      title: 'Delete Collector?',
      text: "This action cannot be undone.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, delete it'
    }).then(result => {
      if (result.isConfirmed) {
        fetch("admin-delete-collector.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id })
        }).then(res => res.json()).then(data => {
          if (data.success) {
            Swal.fire('Deleted!', data.message, 'success');
           loadCollectors(collectorPage);
loadTrucks(truckPage);
          } else {
            Swal.fire('Error', data.message, 'error');
          }
        });
      }
    });
  }

window.deleteTruck = function(id) {

  // STEP 1: Ask user what to do
  Swal.fire({
    title: 'Delete Truck?',
    text: "Choose what happens to the assigned collector.",
    icon: 'warning',
    showCancelButton: true,
    showDenyButton: true,
    confirmButtonText: 'Delete Only',
    denyButtonText: 'Delete + Reassign',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#dc3545',
    denyButtonColor: '#198754'
  }).then(async (result) => {

    if (result.isDismissed) return;

    // =========================
    // OPTION 1: DELETE ONLY
    // =========================
    if (result.isConfirmed) {

      document.querySelector(`#truckRow-${id}`)?.remove();

      fetch('admin-delete-trucks.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      })
      .then(res => res.json())
      .then(data => {

        if (data.success) {
          Swal.fire({
            title: 'Deleted!',
            text: data.message,
            icon: 'success',
            timer: 1200,
            showConfirmButton: false
          });

          loadTrucks(truckPage);
          loadCollectors(collectorPage);

        } else {
          Swal.fire('Error', data.message, 'error');
          loadTrucks(truckPage);
        }
      });

    }

    // =========================
    // OPTION 2: REASSIGN COLLECTOR
    // =========================
    if (result.isDenied) {

      // load available trucks for reassignment
      const res = await fetch('admin-fetch-trucks.php');
      const data = await res.json();

      if (!data.success || !data.data.length) {
        Swal.fire('No trucks available', '', 'info');
        return;
      }

      const options = data.data
        .filter(t => t.id != id)
        .map(t => `<option value="${t.id}">${t.plate_no}</option>`)
        .join('');

      const { value: reassignTo } = await Swal.fire({
        title: 'Reassign Collector',
        html: `
          <select id="reassignSelect" class="swal2-input">
            ${options}
          </select>
        `,
        focusConfirm: false,
        showCancelButton: true,
        preConfirm: () => {
          return document.getElementById('reassignSelect').value;
        }
      });

      if (!reassignTo) return;

      fetch('admin-delete-trucks.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id,
          reassign_to: reassignTo
        })
      })
      .then(res => res.json())
      .then(data => {

        if (data.success) {
          Swal.fire('Reassigned & Deleted!', data.message, 'success');

          loadTrucks(truckPage);
          loadCollectors(collectorPage);
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      });
    }

  });
};

  // ===== DROPDOWN OPTIONS =====
 function loadCollectorOptions() {
  fetch('admin-fetch-collectors.php')
    .then(res => res.json())
    .then(data => {
      const select = document.getElementById('collector_id');
      select.innerHTML = '<option value="">-- None --</option>';

      if (data.success && Array.isArray(data.data)) {
        data.data.forEach(c => {

          const isAssigned = c.assigned_truck_id !== null && c.assigned_truck_id !== '';

          select.innerHTML += `
            <option value="${c.id}" ${isAssigned ? 'disabled' : ''}>
              ${c.display_name} ${isAssigned ? '(Already Assigned)' : ''}
            </option>
          `;
        });
      }
    });
}

 function loadEditCollectorOptions(currentCollectorId) {
  fetch('admin-fetch-collectors.php')
    .then(res => res.json())
    .then(data => {
      const select = document.getElementById('editCollectorId');
      select.innerHTML = '<option value="">-- None --</option>';

    if (data.success && Array.isArray(data.data) && data.data.length > 0) {
        data.data.forEach(c => {
          const selected = c.id == currentCollectorId ? 'selected' : '';

          select.innerHTML += `
            <option value="${c.id}" ${selected}>
              ${c.display_name}
            </option>
          `;
        });
      }
    });
}

  // ===== ADD TRUCK =====
  const addTruckForm = document.getElementById('addTruckForm');
  document.getElementById('addTruckModal').addEventListener('show.bs.modal', loadCollectorOptions);

  addTruckForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const plate_no = document.getElementById('plate_no').value.trim();
    let collector_id = document.getElementById('collector_id').value;
    if (collector_id === '') collector_id = null;

    if (!plate_no) {
      Swal.fire('Error', 'Please enter a plate number.', 'error');
      return;
    }

    fetch('admin-add-truck.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ plate_no, collector_id })
    })
    .then(async res => {
  const text = await res.text();
  console.log("RAW RESPONSE:", text);
  return JSON.parse(text);
})
      .then(data => {
        if (data.success) {
          Swal.fire('Success', data.message, 'success');
          addTruckForm.reset();
          bootstrap.Modal.getInstance(document.getElementById('addTruckModal')).hide();
         loadCollectors(collectorPage);
loadTrucks(truckPage);
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      }).catch(err => {
        Swal.fire('Error', 'Failed to communicate with server.', 'error');
        console.error(err);
      });
  });

  // ===== EDIT TRUCK =====
 window.editTruck = function(id) {
  fetch('admin-fetch-trucks.php')
    .then(res => res.json())
    .then(data => {

      const truck = data.data.find(t => t.id == id);
      if (!truck) return;

      document.getElementById('editTruckId').value = truck.id;
      document.getElementById('editPlateNo').value = truck.plate_no || '';

      // load dropdown first, then show modal
      loadEditCollectorOptions(truck.collector_id ?? null);

      setTimeout(() => {
        const modal = new bootstrap.Modal(document.getElementById('editTruckModal'));
        modal.show();
      }, 100);
    })
    .catch(err => console.error("Edit truck error:", err));
};

  document.getElementById('editTruckForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('editTruckId').value;
    const plate_no = document.getElementById('editPlateNo').value.trim();
    let collector_id = document.getElementById('editCollectorId').value;
    if (collector_id === '') collector_id = null;

    if (!plate_no) {
      Swal.fire('Error', 'Please enter a plate number.', 'error');
      return;
    }

    fetch('admin-update-trucks.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, plate_no, collector_id })
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          Swal.fire('Updated!', data.message, 'success');
          bootstrap.Modal.getInstance(document.getElementById('editTruckModal')).hide();
          loadCollectors(collectorPage);
loadTrucks(truckPage);
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      });
  });

// ----- Edit Collector -----
  window.editCollector = function(id) {
    Promise.all([
      fetch('admin-fetch-collectors.php').then(res => res.json()),
      fetch('admin-fetch-trucks.php').then(res => res.json())
    ])
    .then(([collectorData, truckData]) => {
      const collector = collectorData.data.find(c => c.id == id);
      if (!collector) return;

      document.getElementById('editCollectorId').value = collector.id;
     document.getElementById('editCollectorFirstName').value =
  collector.first_name || '';

document.getElementById('editCollectorMiddleInitial').value =
  collector.middle_initial || '';

document.getElementById('editCollectorLastName').value =
  collector.last_name || '';
      document.getElementById('editCollectorPhone').value = collector.phone || '';

      const truckSelect = document.getElementById('editCollectorTruck');
      truckSelect.innerHTML = '<option value="">-- None --</option>';

      if(truckData.success && truckData.data.length > 0){
        truckData.data.forEach(truck => {
          const disabled = truck.collector_id && truck.collector_id != collector.id ? 'disabled' : '';
          const selected = truck.id == collector.assigned_truck_id ? 'selected' : '';
         truckSelect.innerHTML += `
  <option value="${truck.id}" ${selected} ${disabled}>
    ${truck.plate_no}
    ${truck.collector_name && truck.collector_name != collector.display_name
      ? ' (Assigned)'
      : ''}
  </option>
`;
        });
      }

      const modal = new bootstrap.Modal(document.getElementById('editCollectorModal'));
      modal.show();
    });
  };

// Handle form submit
document.getElementById('editCollectorForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const id = document.getElementById('editCollectorId').value;
 const first_name =
  document.getElementById('editCollectorFirstName').value.trim();

const middle_initial =
  document.getElementById('editCollectorMiddleInitial').value.trim();

const last_name =
  document.getElementById('editCollectorLastName').value.trim();
  const phone = document.getElementById('editCollectorPhone').value.trim();
  let truck_id = document.getElementById('editCollectorTruck').value;
  if(truck_id === '') truck_id = null;

if (!first_name || !last_name || !phone) {
    Swal.fire('Error', 'Name and Contact are required.', 'error');
    return;
  }

  fetch('admin-update-collectors.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
   body: JSON.stringify({
  id,
  first_name,
  middle_initial,
  last_name,
  phone,
  truck_id
})
  })
  .then(res => res.json())
  .then(data => {
    if(data.success){
      Swal.fire('Updated!', data.message, 'success');
      bootstrap.Modal.getInstance(document.getElementById('editCollectorModal')).hide();
     loadCollectors(collectorPage);
loadTrucks(truckPage);
    } else {
      Swal.fire('Error', data.message, 'error');
    }
  })
  .catch(err => {
    Swal.fire('Error', 'Failed to communicate with server.', 'error');
    console.error(err);
  });
});


  // ===== INITIAL LOAD =====
loadCollectors(collectorPage);
loadTrucks(truckPage);
});

document.getElementById('logoutBtn').addEventListener('click', function() {
    window.location.href = 'logout.php';
});
</script>
</body>
</html>