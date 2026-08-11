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
<title>EnviroManage | Collection Records</title>

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

/* SweetAlert mobile sizing */
@media (max-width: 768px) {

  .swal2-popup.issue-modal {
    width: 90% !important;
    max-width: 320px !important;
    padding: 1rem !important;
    font-size: 0.85rem;
  }

  .swal2-popup.issue-modal .swal2-title {
    font-size: 1rem;
  }

  .swal2-popup.issue-modal .swal2-html-container {
    font-size: 0.85rem;
    margin-top: .5rem;
  }

  .swal2-popup.issue-modal .swal2-confirm {
    font-size: .85rem;
    padding: .45rem 1rem;
  }
}

/* Smaller Issue button */
.issue-btn{
    font-size:0.68rem !important;
    padding:0.15rem 0.4rem !important;
    line-height:1.1;
    white-space:nowrap;
}

.issue-btn i{
    font-size:0.72rem;
    margin-right:2px;
}

@media (max-width:768px){
    .issue-btn{
        font-size:0.62rem !important;
        padding:0.12rem 0.32rem !important;
    }

    .issue-btn i{
        font-size:0.65rem;
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
    <a class="nav-link" href="admin-trucks-collectors.php">
    <i class="bi bi-truck-front-fill"></i>
    <span>Trucks & Collectors</span>
</a>

<a class="nav-link active" href="admin-collection-record.php">
    <i class="bi bi-trash-fill"></i>
    <span>Collection Records</span>
</a>
    <a class="nav-link" href="admin-pickup-requests.php"><i class="bi bi-exclamation-circle-fill"></i> <span>Pickup Requests</span></a>
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

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

    <h4 class="fw-semibold mb-0">
        <i class="bi bi-trash-fill me-2 text-success"></i>
        Collection Records
    </h4>


    <button class="btn btn-success btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#filterModal">

        <i class="bi bi-funnel-fill me-1"></i>
        Filter

    </button>

</div>

        <div class="table-responsive">

            <table class="table table-hover align-middle text-center" id="collectionTable">

                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Barangay</th>
                        <th>Street</th>
                        <th>Truck</th>
                        <th>Collector</th>
                        <th>Status</th>
                        <th>Issue</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>

                <tbody id="collectionTableBody"></tbody>

            </table>

        </div>

        <!-- Pagination -->
        <div id="collectionPagination"
             class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 gap-2">

            <div class="text-center text-md-start">

                <small id="collectionPaginationInfo" class="text-muted">
                    Showing 0 to 0 of 0 records
                </small>

            </div>

            <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">

                <button id="collectionPrevBtn"
                        class="btn btn-sm btn-outline-success">
                    Previous
                </button>

                <span id="collectionPageNumber"
                      class="fw-semibold px-2">
                    Page 1 of 1
                </span>

                <button id="collectionNextBtn"
                        class="btn btn-sm btn-outline-success">
                    Next
                </button>

            </div>

        </div>

    </div>

</div>

<!-- FILTER MODAL -->
<div class="modal fade" id="filterModal" tabindex="-1">

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content">


<div class="modal-header">

<h5 class="modal-title text-success">
    <i class="bi bi-funnel-fill me-2"></i>
    Filter Collection Records
</h5>

<button type="button"
        class="btn-close"
        data-bs-dismiss="modal"></button>

</div>


<div class="modal-body">


<!-- DATE -->
<div class="mb-3">

<label class="form-label fw-semibold">
Date
</label>

<select id="filterDate"
        class="form-select">

<option value="all">
All Dates
</option>

<option value="today">
Today
</option>

<option value="week">
This Week
</option>

<option value="month">
This Month
</option>

</select>

</div>



<!-- BARANGAY -->
<div class="mb-3">

<label class="form-label fw-semibold">
Barangay
</label>

<div class="position-relative">

    <input type="text"
           id="filterBarangay"
           class="form-control"
           placeholder="Search barangay..."
           autocomplete="off">

    <div id="barangaySuggestions"
         class="list-group position-absolute w-100"
         style="z-index:1055;">
    </div>

</div>

</div>




<!-- TRUCK -->
<div class="mb-3">

<label class="form-label fw-semibold">
Truck
</label>

<select id="filterTruck"
        class="form-select">

    <option value="all">
        All Trucks
    </option>

</select>

</div>




<!-- STATUS -->
<div class="mb-3">

<label class="form-label fw-semibold">
Status
</label>


<select id="filterStatus"
        class="form-select">


<option value="all">
All Status
</option>

<option value="completed">
Completed
</option>

<option value="in progress">
In Progress
</option>

<option value="incomplete">
Incomplete
</option>

<option value="pending">
Pending
</option>


</select>

</div>





<!-- ISSUE -->
<div class="mb-3">

<label class="form-label fw-semibold">
Issue
</label>


<select id="filterIssue" class="form-select">

<option value="all">
All Issues
</option>

<option value="Road Blocked">
Road Blocked
</option>

<option value="Truck Breakdown">
Truck Breakdown
</option>

<option value="Overflowing Waste">
Overflowing Waste
</option>

<option value="Resident Complaint">
Resident Complaint
</option>

<option value="Weather Delay">
Weather Delay
</option>

</select>

</div>


</div>



<div class="modal-footer">


<button class="btn btn-secondary" id="resetFilterBtn">
    Reset
</button>


<button class="btn btn-success" id="applyFilterBtn">
    Apply Filter
</button>


</div>


</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Wait for the DOM to load
document.addEventListener('DOMContentLoaded', () => {

let collectionRecords = [];
const ROWS_PER_PAGE = 10;

let currentPage = 1;
let filteredRecords = [];

  // ===== SIDEBAR LOGIC =====
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('toggleBtn');
  const closeBtn = document.getElementById('closeBtn');
  const sidebarControls = document.getElementById('sidebarControls');
  const hamburger = document.getElementById('hamburger');
  const mainContent = document.querySelector('.main-content');

  document.getElementById("applyFilterBtn")
.addEventListener("click", applyFilters);


document.getElementById("resetFilterBtn")
.addEventListener("click", resetFilters);

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

  


function applyFilters(){


let date = document.getElementById("filterDate").value;
let barangay = document.getElementById("filterBarangay").value;
let truck = document.getElementById("filterTruck").value;
let status = document.getElementById("filterStatus").value;
let issue = document.getElementById("filterIssue").value;



filteredRecords = collectionRecords.filter(record => {



let matchBarangay =
barangay === "" ||
record.barangay
.toLowerCase()
.includes(
    barangay.toLowerCase()
);



let matchTruck =
truck === "all" ||
record.truck === truck;



let matchStatus =
status === "all" ||
(record.status || "").toLowerCase() === status;



let matchIssue =
issue === "all" ||
record.issue_type === issue;



let matchDate = true;


if(date !== "all"){


let recordDate = new Date(record.date);

let today = new Date();



if(date === "today"){

matchDate =
recordDate.toDateString()
===
today.toDateString();

}


if(date === "week"){

let weekAgo = new Date();

weekAgo.setDate(today.getDate()-7);

matchDate =
recordDate >= weekAgo;

}


if(date === "month"){

matchDate =
recordDate.getMonth()
===
today.getMonth();

}


}




return matchBarangay &&
matchTruck &&
matchStatus &&
matchIssue &&
matchDate;


});

currentPage = 1;
renderFilteredRecords(filteredRecords);



bootstrap.Modal
.getInstance(
document.getElementById("filterModal")
)
.hide();


}



function resetFilters(){

document.querySelectorAll("#filterModal select")
.forEach(select=>{
    select.value="all";
});


document.getElementById("filterBarangay").value="";


currentPage = 1;
renderFilteredRecords(collectionRecords);

}



function renderFilteredRecords(records){

    filteredRecords = records;

    const tbody = document.getElementById("collectionTableBody");
    tbody.innerHTML = "";

    if(records.length===0){

        tbody.innerHTML=`
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    No matching records found.
                </td>
            </tr>
        `;

        updatePagination();

        return;

    }

    const start=(currentPage-1)*ROWS_PER_PAGE;
    const end=start+ROWS_PER_PAGE;

    records.slice(start,end).forEach(record=>{

        tbody.insertAdjacentHTML("beforeend",`

            <tr>

                <td>${record.id}</td>
                <td>${record.date}</td>
                <td>${record.barangay}</td>
                <td>${record.street}</td>
                <td>${record.truck}</td>
                <td>${record.collector}</td>
                <td>${getStatusBadge(record.status)}</td>

                <td>

                    ${
                        record.issue_type
                        ?
                        `
                        <button
                            class="btn btn-outline-danger issue-btn"
                            onclick="viewIssue(${JSON.stringify(record).replace(/"/g,'&quot;')})">

                            <i class="bi bi-exclamation-circle"></i>

                            ${record.issue_type}

                        </button>
                        `
                        :
                        "-"
                    }

                </td>

                <td>${record.last_updated}</td>

            </tr>

        `);

    });

    updatePagination();

}

function updatePagination(){

    const totalRecords = filteredRecords.length;

    const totalPages =
        Math.max(1,Math.ceil(totalRecords/ROWS_PER_PAGE));

    if(currentPage>totalPages){
        currentPage=totalPages;
    }

    const start =
        totalRecords===0
        ? 0
        : ((currentPage-1)*ROWS_PER_PAGE)+1;

    const end =
        Math.min(currentPage*ROWS_PER_PAGE,totalRecords);

    document.getElementById("collectionPaginationInfo").textContent =
        `Showing ${start} to ${end} of ${totalRecords} records`;

    document.getElementById("collectionPageNumber").textContent =
        `Page ${currentPage} of ${totalPages}`;

    document.getElementById("collectionPrevBtn").disabled =
        currentPage===1;

    document.getElementById("collectionNextBtn").disabled =
        currentPage===totalPages;

}

document.getElementById("collectionPrevBtn").addEventListener("click",()=>{

    if(currentPage>1){

        currentPage--;

        renderFilteredRecords(filteredRecords);

    }

});

document.getElementById("collectionNextBtn").addEventListener("click",()=>{

    const totalPages =
        Math.ceil(filteredRecords.length/ROWS_PER_PAGE);

    if(currentPage<totalPages){

        currentPage++;

        renderFilteredRecords(filteredRecords);

    }

});




async function loadTruckFilters(){

    try{

        const response =
        await fetch("admin-fetch-trucks.php");


        const result =
        await response.json();


        const select =
        document.getElementById("filterTruck");


        if(result.success){


            result.data.forEach(truck=>{


                select.innerHTML += `

                <option value="${truck.plate_no}">
                    ${truck.plate_no}
                </option>

                `;


            });

        }


    }catch(error){

        console.error(
            "Truck loading failed:",
            error
        );

    }

}

  // ===== FETCH COLLECTION RECORDS =====
async function loadCollectionRecords() {

    try {

        const response = await fetch("admin-fetch-collection-records.php");
        const result = await response.json();

        const tbody = document.getElementById("collectionTableBody");
        tbody.innerHTML = "";

        if (!result.success || result.records.length === 0) {

            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        No collection records found.
                    </td>
                </tr>
            `;

            document.getElementById("collectionPaginationInfo").textContent =
                "Showing 0 to 0 of 0 records";

            document.getElementById("collectionPageNumber").textContent =
                "Page 1 of 1";

            return;
        }

        collectionRecords = result.records;

currentPage = 1;
renderFilteredRecords(collectionRecords);

        document.getElementById("collectionPaginationInfo").textContent =
            `Showing 1 to ${result.records.length} of ${result.records.length} records`;

        document.getElementById("collectionPageNumber").textContent =
            "Page 1 of 1";

    } catch (error) {

        console.error(error);

        document.getElementById("collectionTableBody").innerHTML = `
            <tr>
                <td colspan="9" class="text-danger text-center py-4">
                    Failed to load collection records.
                </td>
            </tr>
        `;

    }

}

function getStatusBadge(status) {

    switch ((status || "").toLowerCase()) {

        case "completed":
            return `<span class="badge bg-success">Completed</span>`;

        case "incomplete":
            return `<span class="badge bg-danger">Incomplete</span>`;

        case "in progress":
            return `<span class="badge bg-warning text-dark">In Progress</span>`;

        default:
            return `<span class="badge bg-secondary">Pending</span>`;
    }

}


let barangayList = [];


async function loadBarangays(){

    try{

        const response = await fetch("barangays.json");

        const data = await response.json();


        // Convert JSON object keys into barangay array
        barangayList = Object.keys(data);


        const input =
        document.getElementById("filterBarangay");


        const suggestionBox =
        document.getElementById("barangaySuggestions");



        function renderSuggestions(list){

    suggestionBox.innerHTML = "";

    if(list.length === 0){

        suggestionBox.innerHTML = `
            <div class="list-group-item text-muted">
                No matches found. Search for barangays within Sariaya only.
            </div>
        `;

        return;
    }

    list.slice(0,10).forEach(barangay=>{

        let button = document.createElement("button");

        button.type = "button";
        button.className = "list-group-item list-group-item-action";
        button.textContent = barangay;

        button.onclick = ()=>{

            input.value = barangay;
            suggestionBox.innerHTML = "";

        };

        suggestionBox.appendChild(button);

    });

}

input.addEventListener("focus",()=>{

    renderSuggestions(barangayList);

});

input.addEventListener("input",function(){

    let value = this.value.trim().toLowerCase();

    let matches = barangayList.filter(barangay=>

        barangay.toLowerCase().includes(value)

    );

    renderSuggestions(matches);

});

input.addEventListener("keydown",function(e){

    if(e.key !== "Enter") return;

    e.preventDefault();

    let keyword = this.value.trim().toLowerCase();

    let exactMatch = barangayList.find(barangay=>

        barangay.toLowerCase() === keyword

    );

    if(exactMatch){

        input.value = exactMatch;
        suggestionBox.innerHTML = "";

    }else{

        suggestionBox.innerHTML = `
            <div class="list-group-item text-muted">
                No matches found. Search for barangays within Sariaya only.
            </div>
        `;

    }

});




        // hide suggestions when clicking outside
        document.addEventListener("click", function(e){

            if(
                !input.contains(e.target) &&
                !suggestionBox.contains(e.target)
            ){

                suggestionBox.innerHTML="";

            }

        });



    }catch(error){


        console.error(
            "Barangay JSON loading failed:",
            error
        );


    }

}


   window.viewIssue = function(record){

    Swal.fire({

        title: record.issue_type,

        html: `

        <div class="text-start">

            <p>
                <strong>Barangay:</strong><br>
                ${record.barangay}
            </p>

            <p>
                <strong>Street:</strong><br>
                ${record.street}
            </p>

            <hr>

            <p>
                <strong>Description:</strong><br>
                ${
                    record.issue_description
                    ? record.issue_description
                    : "No description provided."
                }
            </p>

            ${
                record.issue_date
                ? `
                <p>
                    <strong>Reported At:</strong><br>
                    ${record.issue_date}
                </p>
                `
                : ""
            }

        </div>
        `,

        icon: "warning",
        width: window.innerWidth <= 768 ? "320px" : "500px",
        customClass: {
            popup: "issue-modal"
        },
        confirmButtonText: "Close",
        confirmButtonColor: "#1e5631"

    });

};

loadBarangays();

loadTruckFilters();

loadCollectionRecords();

  document.getElementById("logoutBtn").addEventListener("click", function () {
    window.location.href = "logout.php";
});

   });
  
</script>
</body>
</html>