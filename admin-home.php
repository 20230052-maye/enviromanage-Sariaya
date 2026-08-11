<?php
session_start();

// Prevent browser from caching this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Session check
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: login.php");
    exit;
}

$isLoggedIn = true;

// DB CONNECTION
$conn = new mysqli(
    "localhost",
    "u823857209_enviromanage",
    "Enviromanage4322",
    "u823857209_enviromanage"
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/**
 * Returns collection progress
 */
function getCollectionProgress($conn, $garbageType = null)
{
    $typeFilter = "";

    if ($garbageType !== null) {
        $garbageType = $conn->real_escape_string($garbageType);
        $typeFilter = " AND s.garbage_type = '$garbageType' ";
    }

    /*
    -----------------------------------------
    TOTAL SCHEDULES
    -----------------------------------------
    Default:
        Counts every schedule row
        (Bio + Non-Bio = 2)

    Filtered:
        Counts only matching garbage type.
    -----------------------------------------
    */

    if ($garbageType === null) {

        $totalSql = "
            SELECT COUNT(*) AS total
            FROM schedules s
            WHERE s.day_of_week = DAYNAME(CURDATE())
        ";

    } else {

        $totalSql = "
            SELECT COUNT(*) AS total
            FROM schedules s
            WHERE s.day_of_week = DAYNAME(CURDATE())
            $typeFilter
        ";

    }

    $total = 0;

    if ($result = $conn->query($totalSql)) {
        $row = $result->fetch_assoc();
        $total = (int)$row['total'];
    }

    /*
    -----------------------------------------
    COMPLETED SCHEDULES
    -----------------------------------------
    One completed schedule =
    one barangay + one garbage type
    -----------------------------------------
    */

    $completedSql = "
        SELECT COUNT(*) AS completed
        FROM (

            SELECT
                s.barangay,
                s.garbage_type
            FROM schedules s

            INNER JOIN collection_progress cp
                ON cp.barangay = s.barangay

            WHERE
                s.day_of_week = DAYNAME(CURDATE())
                AND cp.collection_date = CURDATE()
                $typeFilter

            GROUP BY
                s.barangay,
                s.garbage_type

            HAVING SUM(cp.status <> 'Completed') = 0

        ) completed_tbl
    ";

    $completed = 0;

    if ($result = $conn->query($completedSql)) {
        $row = $result->fetch_assoc();
        $completed = (int)$row['completed'];
    }

    return [
        "completed" => $completed,
        "total" => $total
    ];

    
}

/**
 * Returns incomplete collections today
 */
function getIncompleteCollections($conn, $garbageType = null)
{
    $typeFilter = "";

    if ($garbageType !== null) {
        $garbageType = $conn->real_escape_string($garbageType);

        $typeFilter = "
            AND s.garbage_type = '$garbageType'
        ";
    }

    $sql = "
        SELECT COUNT(*) AS incomplete
        FROM collection_progress cp

        INNER JOIN schedules s
            ON s.id = cp.schedule_id

        WHERE
            cp.collection_date = CURDATE()
            AND cp.status = 'Incomplete'
            $typeFilter
    ";

    $incomplete = 0;

    if ($result = $conn->query($sql)) {
        if ($row = $result->fetch_assoc()) {
            $incomplete = (int)$row['incomplete'];
        }
    }

    return $incomplete;
}
// Progress data
$allProgress = getCollectionProgress($conn);
$bioProgress = getCollectionProgress($conn, "Biodegradable");
$nonBioProgress = getCollectionProgress($conn, "Non-Biodegradable");

// Default values (used by the dashboard card)
$completedBarangays = $allProgress['completed'];
$totalBarangays = $allProgress['total'];

$allIncomplete = getIncompleteCollections($conn);
$bioIncomplete = getIncompleteCollections($conn, "Biodegradable");
$nonBioIncomplete = getIncompleteCollections($conn, "Non-Biodegradable");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EnviroManage Admin Dashboard</title>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-oZ8c2HcYl1C6rVj0wWXaFzXSkXb8VvdzL3n0Bsn1g0o=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-o9N1j7k91lG8PCsNgF0LoiVgP5L9Ypjp8Z4kYQ2h1vY=" crossorigin=""></script>

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
.navbar-brand img { border-radius: 5px; }

.navbar-nav > .nav-item > .nav-link > i{
    display:block;
    line-height:1;
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

/* Dashboard Cards */
.card { background-color: #fff; border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.1); }
.dashboard-card { background: #fff; border-radius: 10px; padding: 15px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.08); transition: transform 0.2s; }
.dashboard-card:hover { transform: translateY(-3px); }
.dashboard-card h3 { margin-top: 10px; }
.garbage-bin { width: 40px; height: 80px; border: 1px solid #bbb; border-radius: 5px; margin: 5px auto; background-color: #e9ecef; position: relative; overflow: hidden; }
.garbage-fill { width: 100%; position: absolute; bottom: 0; text-align: center; font-size: 0.75rem; line-height: 1.2; color: #fff; font-weight: bold; }
.bg-success-fill { background-color: #28a745 !important; }
.bg-warning-fill { background-color: #ffc107 !important; color: #000 !important; }
.bg-danger-fill { background-color: #dc3545 !important; }
.table { background-color: #fff; border-radius: 10px; overflow: hidden; }
canvas { background-color: #fff; border-radius: 10px; padding: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.08); }
#truckMapContainer { border: 1px solid #dee2e6; border-radius: 10px; }
.dashboard-card{
    background:#fff;
    border-radius:10px;
    padding:15px;
    text-align:center;
    box-shadow:0 2px 5px rgba(0,0,0,.08);
    transition:transform .2s;
    height:100%;
}

.row.g-3 > [class*="col-"]{
    display:flex;
}

.row.g-3 > [class*="col-"] .dashboard-card{
    flex:1;
}

.truck-warning {
    font-size: 0.9rem;
    color: #000;
}


/* ==========================
   MOBILE NAVBAR
========================== */
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

    /* Hamburger */
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

    /* Center Logo */
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

    /* Profile dropdown */
    .dropdown-menu{
        position:absolute !important;
        right:0;
        left:auto;
        top:100%;
        margin-top:8px;
    }

    /* Sidebar */
    #sidebarControls{
        display:flex;
    }

}

/* Responsive Sidebar & Desktop */
@media (min-width: 769px) {
  .sidebar { width: 220px !important; transform: none !important; }
  .sidebar .nav-link { justify-content: flex-start !important; padding-left: 20px; }
  .sidebar .nav-link span { display: inline !important; margin-left: 10px; }
  .main-content { margin-left: 220px !important; }
  #sidebarControls, #toggleBtn, #closeBtn, #hamburger { display: none !important; }
}
</style>

<script>

// ==========================
// ONLINE/OFFLINE AUTH CHECK
// ==========================

const sessionLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;

if (navigator.onLine) {

    // ONLINE → trust PHP session

    if (!sessionLoggedIn) {

        localStorage.removeItem("logged_in");
        localStorage.removeItem("role");

        window.location.href = "login.php";

    } else {

        // Keep offline credentials updated

        localStorage.setItem("logged_in", "true");
        localStorage.setItem("role", "admin");

    }

} else {

    // OFFLINE → trust localStorage

    if (
        localStorage.getItem("logged_in") !== "true" ||
        localStorage.getItem("role") !== "admin"
    ) {

        document.body.innerHTML = `
        <div style="
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
            font-family:sans-serif;
            text-align:center;
            padding:20px;
        ">
            <div>
                <h2>Offline Access Unavailable</h2>
                <p>You must login online first before using offline mode.</p>
            </div>
        </div>
        `;

    }

}

</script>

</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="height:70px;">
  <div class="container-fluid">
    <button id="hamburger" class="d-flex d-lg-none">
      <i class="bi bi-list"></i>
    </button>

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
    <a class="nav-link active" href="admin-home.php"><i class="bi bi-house-door-fill"></i> <span>Dashboard</span></a>
    <a class="nav-link" href="admin-collection-schedules.php"><i class="bi bi-map-fill"></i> <span>Collection Schedules</span></a>
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

<!-- MAIN CONTENT -->
<div class="main-content p-4">
  <div class="card p-4 shadow-sm mt-2">
    <h4 class="fw-semibold text-dark"><i class="bi bi-speedometer2 me-2 text-success"></i>Dashboard Overview</h4>

    <!-- Dashboard Overview Cards -->
   <div class="row g-3 mt-3">

    <!-- Collection Progress -->
    <div class="col-md-4">
    <div class="dashboard-card"
         id="collectionCard"
         style="cursor:pointer;">

        <h6 id="collectionTitle">
            <i class="bi bi-check2-square me-2 text-success"></i>
            All Schedules Today
        </h6>

        <h3 class="mt-2" id="collectionValue">
    <?= $allProgress['completed'] ?>/<?= $allProgress['total'] ?> COMPLETED
</h3>

        <small class="text-muted">
            Click to change schedule type
        </small>
    </div>
</div>

    <!-- Active Trucks -->
    <div class="col-md-4">
        <div class="dashboard-card">
            <h6>
                <i class="bi bi-truck-front-fill me-2 text-primary"></i>
                Active Trucks
            </h6>
            <h3 class="mt-2">&nbsp;</h3>
        </div>
    </div>

<!-- Incomplete Collections -->
<div class="col-md-4">

<div class="dashboard-card"
     id="incompleteCard"
     style="cursor:pointer;">

    <h6 id="incompleteTitle">
        <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
        All Garbage Types
    </h6>

    <h3 class="mt-2" id="incompleteValue">
        <?= $allIncomplete ?> INCOMPLETE
    </h3>

    <small class="text-muted">
        Click to change garbage type
    </small>

</div>

</div>

</div>

    <!-- Truck Section -->
    <div class="mt-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5><i class="bi bi-truck-front-fill me-2 text-primary"></i>Truck Monitoring</h5>

        <!-- VIEW DROPDOWN -->
        <div class="dropdown">
          <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="viewDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            View
          </button>
          <ul class="dropdown-menu" aria-labelledby="viewDropdown">
            <li><a class="dropdown-item active" href="#" data-view="list">List View</a></li>
            <li><a class="dropdown-item" href="#" data-view="map">Map View</a></li>
          </ul>
        </div>

      </div>

      <!-- List View Table -->
      <div id="truckListView" class="table-responsive mt-2">
        <table class="table table-bordered table-striped align-middle">
          <thead>
<tr>
    <th>Collector Name</th>
    <th>Plate No.</th>
    <th>Location</th>
    <th>Status</th>
    <th>Last Updated</th>
</tr>
</thead>
          <tbody id="truckListBody">
          
          </tbody>
        </table>
      </div>

      <!-- Icon/List Cards -->
      <div id="truckMapContainer" style="height:480px; display:none;"></div>
    </div>

    <!-- Recent Reports Table -->
    <div class="mt-4">
      <h5><i class="bi bi-clipboard-data-fill me-2 text-primary"></i>Recent Reports (from Residents)</h5>
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle mt-2">
          <thead>
            <tr><th>#</th><th>Barangay</th><th>Issue</th><th>Status</th><th>Date</th></tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="offline-router.js"></script>

<script>
// SIDEBAR LOGIC
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
    if(sidebar.classList.contains('hidden')){
      mainContent.style.marginLeft = '0';
      sidebarControls.style.display = 'none';
      hamburger.style.display = 'flex';
    } else {
      mainContent.style.marginLeft = '70px';
      sidebarControls.style.display = 'flex';
      hamburger.style.display = 'none';
    }
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
  updateContentMargin();
});

hamburger.addEventListener('click', ()=>{
  if(!isMobile()) return;
  sidebar.classList.remove('hidden');
  sidebar.classList.remove('expanded');
  toggleBtn.querySelector('i').className='bi bi-chevron-right';
  updateContentMargin();
});

window.addEventListener('resize', updateContentMargin);
updateContentMargin();


// TRUCK VIEW LOGIC
const truckMapContainer = document.getElementById('truckMapContainer');
const truckListView = document.getElementById('truckListView');
const truckListBody = document.getElementById('truckListBody');

let mapInitialized = false;
let truckMap;
let truckMarkers = {}; // keep track of markers by truck_id

// --- Map Initialization ---
function initTruckMap() {
    if (mapInitialized) return;

    truckMap = L.map('truckMapContainer').setView([13.433, 122.567], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(truckMap);

    // Show warning overlay when there are no trucks
    const warningDiv = L.control({position: 'topright'});
    warningDiv.onAdd = function() {
        const div = L.DomUtil.create('div', 'truck-warning');
        div.innerHTML = "<b>No trucks to locate</b>";
        div.style.background = 'rgba(255,255,0,0.8)';
        div.style.padding = '5px 10px';
        div.style.borderRadius = '5px';
        div.style.fontWeight = 'bold';
        div.style.zIndex = '1000';
        return div;
    };
    warningDiv.addTo(truckMap);

    mapInitialized = true;
}

function computeTruckStatus(startTime, endTime, lastUpdated){

    if(!startTime || !endTime){
        return "Pending";
    }

    const now = new Date();
    const last = lastUpdated ? new Date(lastUpdated) : null;

    const today = new Date().toISOString().split("T")[0];

    const start = new Date(today + " " + startTime);
    const end = new Date(today + " " + endTime);


    // Before assigned schedule
    if(now < start){
        return "Pending";
    }


    // If collector stopped updating after schedule started
    if(last){

        const minutes =
            (now - last) / 1000 / 60;

        if(minutes > 20){
            return "Delayed";
        }

    }


    // After schedule time
    if(now > end){

        // If last update is near the end,
        // assume completed/finished route
        return "Cancelled";
    }


    const totalMinutes =
        (end-start)/1000/60;


    const elapsedMinutes =
        (now-start)/1000/60;


    const expected =
        elapsedMinutes / totalMinutes;


    let actual = expected;


    if(last){

        actual =
        ((last-start)/1000/60)
        /
        totalMinutes;

    }


    if(actual > expected + 0.15){
        return "Early";
    }


    if(actual < expected - 0.15){
        return "Delayed";
    }


    return "On Time";
}

// --- SSE for Truck Locations ---
let sseStarted = false;
function startTruckSSE() {
    if (sseStarted) return;
    sseStarted = true;

    const evtSource = new EventSource('truck-locations-sse.php');

    evtSource.onmessage = function(e) {
        const trucks = JSON.parse(e.data);

        // --- Update List View ---
       truckListBody.innerHTML = '';

if(trucks.length === 0){

    truckListBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center text-muted">
                <i class="bi bi-truck me-2"></i>
                No trucks found today
            </td>
        </tr>
    `;

} else {

trucks.forEach(truck => {


    const status = computeTruckStatus(
        truck.start_time,
        truck.end_time,
        truck.last_updated
    );


    let statusBadge = "";

    if(status === "On Time"){
        statusBadge =
        `<span class="badge bg-success">${status}</span>`;
    }

    else if(status === "Early"){
        statusBadge =
        `<span class="badge bg-primary">${status}</span>`;
    }

    else if(status === "Delayed"){
        statusBadge =
        `<span class="badge bg-warning text-dark">${status}</span>`;
    }

    else if(status === "Cancelled"){
        statusBadge =
        `<span class="badge bg-danger">${status}</span>`;
    }

    else{
        statusBadge =
        `<span class="badge bg-secondary">${status}</span>`;
    }



    const row = document.createElement('tr');

    row.innerHTML = `
        <td>${truck.collector_name}</td>

        <td>${truck.plate_no}</td>

        <td>${truck.location ?? "Unknown"}</td>

        <td>${statusBadge}</td>

        <td>${truck.last_updated ?? "-"}</td>
    `;


    truckListBody.appendChild(row);

});

}

        // --- Map Warning Handling ---
        const warningDivs = document.querySelectorAll('.truck-warning');
        warningDivs.forEach(div => div.style.display = trucks.length === 0 ? 'block' : 'none');

        // --- Update Map Markers ---
        if (!mapInitialized) return;

        trucks.forEach(truck => {
            const key = truck.truck_id;
            const latLng = [truck.lat, truck.lng];

            if (truckMarkers[key]) {
                truckMarkers[key].setLatLng(latLng)
                    .setPopupContent(`<b>${truck.plate_no}</b><br>Collector: ${truck.collector_name}<br>Capacity: ${truck.capacity}%<br>Location: ${truck.location}`);
            } else {
                truckMarkers[key] = L.marker(latLng)
                    .addTo(truckMap)
                    .bindPopup(`<b>${truck.plate_no}</b><br>Collector: ${truck.collector_name}<br>Capacity: ${truck.capacity}%<br>Location: ${truck.location}`);
            }
        });

        // Remove markers not in new data
        Object.keys(truckMarkers).forEach(key => {
            if (!trucks.some(t => t.truck_id === key)) {
                truckMap.removeLayer(truckMarkers[key]);
                delete truckMarkers[key];
            }
        });
    };

    evtSource.onerror = function() {
        console.error("SSE connection error. Retrying...");
    };
}

// --- Dropdown View Handling ---
document.querySelectorAll('.dropdown-item').forEach(item => {
    item.addEventListener('click', function(e){
        e.preventDefault();
        document.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('viewDropdown').textContent = this.textContent;

        const view = this.getAttribute('data-view');
        if(view === 'list'){

    truckListView.style.display = 'block';
    truckMapContainer.style.display = 'none';

}
else if(view === 'map'){

    truckListView.style.display = 'none';
    truckMapContainer.style.display = 'block';

    initTruckMap();
    truckMap.invalidateSize();
    startTruckSSE();

}
    });
});

const progressModes = [
    {
        title: "All Schedules Today",
        completed: <?= $allProgress['completed'] ?>,
        total: <?= $allProgress['total'] ?>
    },
    {
        title: "Biodegradable",
        completed: <?= $bioProgress['completed'] ?>,
        total: <?= $bioProgress['total'] ?>
    },
    {
        title: "Non-Biodegradable",
        completed: <?= $nonBioProgress['completed'] ?>,
        total: <?= $nonBioProgress['total'] ?>
    }
];

let currentMode = 0;

document.getElementById("collectionCard").addEventListener("click", function () {

    currentMode++;

    if(currentMode >= progressModes.length){
        currentMode = 0;
    }

    document.getElementById("collectionTitle").innerHTML =
        `<i class="bi bi-check2-square me-2 text-success"></i>${progressModes[currentMode].title}`;

    document.getElementById("collectionValue").textContent =
    progressModes[currentMode].completed +
    "/" +
    progressModes[currentMode].total +
    " COMPLETED";
});

const incompleteModes = [
    {
        title: "All Garbage Types",
        count: <?= $allIncomplete ?>
    },
    {
        title: "Biodegradable",
        count: <?= $bioIncomplete ?>
    },
    {
        title: "Non-Biodegradable",
        count: <?= $nonBioIncomplete ?>
    }
];


let incompleteMode = 0;


document.getElementById("incompleteCard")
.addEventListener("click", function(){

    incompleteMode++;

    if(incompleteMode >= incompleteModes.length){
        incompleteMode = 0;
    }


    document.getElementById("incompleteTitle").innerHTML =
    `<i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
    ${incompleteModes[incompleteMode].title}`;


    document.getElementById("incompleteValue").textContent =
    incompleteModes[incompleteMode].count +  " INCOMPLETE";

});

startTruckSSE();
</script>

</body>
</html>