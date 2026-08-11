<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'collector'
) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Logged-in collector ID
$collector_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collector Homepage</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CSS -->
  <style>/* ===========================
   RESET
=========================== */

/* ===========================
   GENERAL
=========================== */


*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}




body{
  background:#f4f7f9;
    margin:0;
    padding-top:70px;
}

/* ===========================
   NAVBAR
=========================== */

.navbar{
    background:#1e5631 !important;
    height:70px;
    box-shadow:0 2px 8px rgba(0,0,0,.12);
}

.navbar .container-fluid{
    height:70px;
}

.navbar-brand img{
    height:42px;
}
.navbar .container-fluid{
    position:relative;
}

.navbar-logo{
    position:static;
    transform:none;
    margin:0;
    padding:0;
}

.navbar-logo img{
    height:42px;
}
/* ===========================
   PAGE LAYOUT
=========================== */

.page-wrapper{

    display:flex;

    min-height:calc(100vh - 70px);

    width:100%;

}
/* ===========================
   SIDEBAR
=========================== */

.sidebar{
    width:270px;
    background:#fff;
    border-right:1px solid #dee2e6;
    display:flex;
    flex-direction:column;
    flex-shrink:0;
    position:sticky;
    top:70px;
    height:calc(100vh - 70px);
    overflow-y:auto;
}

.sidebar-menu{
    display:flex;
    flex-direction:column;
    padding:15px 0;
}

.sidebar-item{
    display:flex;
    align-items:center;
    gap:15px;

    padding:15px 22px;

    text-decoration:none;

    color:#495057;

    font-size:18px;

    line-height:1.2;

    transition:.25s ease;
}

.sidebar-item i{
    width:26px;
     font-size:20px;
  
    text-align:center;
    color:inherit;
}

.sidebar-item img{
    width:22px;
    height:22px;
    object-fit:contain;
}

.sidebar-item span{
    white-space:nowrap;
}

.sidebar-item:hover{
    background:#1e5631;
    color:#fff;
}

.sidebar-item.active{
    background:#1e5631;
    color:#fff;
}

.navbar-actions{
    display:flex;
    align-items:center;
    gap:12px;
}

.nav-icon-btn{
    width:42px;
    height:42px;
    border:none;
    border-radius:50%;
    background:transparent;
    color:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    cursor:pointer;
    transition:.25s;
     transform:translateY(-3px);
}

.nav-icon-btn i{
    font-size:24px;
}

.nav-icon-btn:hover{
    background:rgba(255,255,255,.15);
}


/* ===========================
   MAIN CONTENT
=========================== */

.main-content{

    flex:1;

    padding:35px;

    transition:.3s;

    overflow-x:hidden;

}
.container{
    max-width:1300px;
    margin:0 auto;
}
h2{
    color:#6b7d34;
    font-weight:800;
}

/* ===========================
   CARD
=========================== */

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.card-body{
    padding:25px;
}

.form-control{
    height:48px;
    border-radius:10px;
}

/* ===========================
   CHAT BUTTON
=========================== */

.chat-btn{
    position:fixed;

    right:30px;
    bottom:95px;

    left:auto;
    top:auto;

    width:58px;
    height:58px;

    border:none;
    border-radius:50%;

    background:#ececec;

    display:flex;
    justify-content:center;
    align-items:center;

    box-shadow:0 4px 12px rgba(0,0,0,.18);
    cursor:pointer;
    z-index:1100;
    transition:.25s ease;
}
.chat-btn.active i{
    color:gray;
}
.chat-btn i{
    font-size:28px;
    color:darkgray;
    transition:.25s ease;
}

.badge{
    position:absolute;
    top:6px;
    right:6px;

    width:18px;
    height:18px;

    border-radius:50%;

    background:#fff;
    color:#333;

    border:1px solid #666;

    display:flex;
    justify-content:center;
    align-items:center;

    font-size:10px;
}


/* ===========================
   BOTTOM NAVIGATION
=========================== */

.bottom-nav{
    position:fixed;
    left:0;
    bottom:0;

    width:100%;
    height:75px;

    background:#184D27;

    display:flex;
    justify-content:space-around;
    align-items:center;

    border-top-left-radius:20px;
    border-top-right-radius:20px;

    box-shadow:0 -2px 10px rgba(0,0,0,.15);

    z-index:1000;
}

.nav-item{
    color:#fff;
    text-decoration:none;

    display:flex;
    flex-direction:column;
    align-items:center;

    font-size:11px;
    font-weight:600;
}

.nav-item i{
    font-size:26px;
    margin-bottom:4px;
}

.nav-item img{
    width:32px;
    margin-bottom:4px;
}

.nav-item.active{
    color:#b8c77c;
}


/* ===========================
   COLLECTION DETAILS
=========================== */

#collectionForm{
    display:none;
    margin-top:20px;
}

#collectionForm .card{
    border:none;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.10);
    margin-bottom:20px;
}

#collectionForm .card-body{
    padding:22px;
}

#collectionForm .form-label{
    font-weight:600;
    margin-bottom:6px;
}

#collectionForm .form-control,
#collectionForm .form-select{
    height:45px;
    border-radius:8px;
}

#collectionForm textarea.form-control{
    height:auto;
    min-height:120px;
    resize:none;
}

#collectionForm .btn-success{
    background:#a8be68;
    border:none;
    border-radius:25px;
    font-weight:600;
}

#collectionForm .btn-success:hover{
    background:#93ab57;
}

@media(min-width:992px){

    .sidebar{
        display:flex;
    }

    .bottom-nav{
        display:none;
    }

}
/* ===========================
   TABLET
=========================== */

@media(max-width:991px){

.chat-btn{
    right:20px;
    bottom:95px;
}

    .navbar .container-fluid{
        display:flex;
        justify-content:space-between;
        align-items:center;
    }

    .navbar-logo img{
        height:38px;
         transform:translateY(-3px);
    }

    .navbar-brand img{
        height:38px;

    }

    .main-content{
        padding:25px 15px 120px;
    }

    .chat-btn{
        right:20px;
    }
.sidebar{
    display:none;
}

.bottom-nav{
    display:flex;
}

.page-wrapper{

    display:block;

}

.main-content{

    padding:25px 15px 120px;

}
}

/* ===========================
   MOBILE
=========================== */

@media(max-width:576px){
.chat-btn{
    width:52px;
    height:52px;

    right:15px;
    bottom:90px;
}


   .navbar-logo img{
        height:38px;
    }

    h2{
        font-size:26px;
    }

    .card-body{
        padding:18px;
    }

    .chat-btn{
        width:52px;
        height:52px;
        right:15px;
        bottom:90px;
    }

    .chat-btn i{
        font-size:24px;
    }

     .bottom-nav{
        height:70px;
    }

    .bottom-nav .nav-item{
        font-size:10px;
    }

    .bottom-nav .nav-item i{
        font-size:22px;
        margin-bottom:4px;
        line-height:1;
    }

    .bottom-nav .nav-item img{
        width:28px;
        height:28px;
        margin-bottom:4px;
        object-fit:contain;
    }
    .nav-item{
        font-size:10px;
    }

    .nav-item i{
        font-size:22px;
    }

    .nav-item img{
        width:28px;
    }
 #collectionForm .card-body{
        padding:18px;
    }
  /* Collection Progress Header */
    #collectionForm .card-body .d-flex{
        flex-wrap: nowrap;
        align-items: center;
    }

    #collectionForm .card-body h5{
        font-size:16px;
        white-space: nowrap;
        margin-bottom:0;
    }

    #collectionForm .card-body small{
        font-size:11px;
        white-space: nowrap;
        margin-left:10px;
    }

    #collectionForm .btn{
        margin-bottom:10px;
    }
}


.chat-window{

    position:fixed;
    right:30px;
    bottom:170px;
cursor:default;
    width:360px;
    height:500px;

    background:#fff;
    border-radius:18px;

    box-shadow:0 8px 25px rgba(0,0,0,.2);

    overflow:hidden;
    display:none;

    z-index:1200;
}

.chat-window.show{
    display:flex;
    flex-direction:column;
}


.chat-header{

    background:#1e5631;
    color:#fff;
cursor:move;
    padding:12px 15px;
touch-action: none;
    display:flex;
    align-items:center;
     justify-content:space-between;
    gap:10px;

}
.chat-close-btn{
    border:none;
    background:transparent;
    color:#fff;
    font-size:18px;
    cursor:pointer;
    padding:4px;
    transition:.2s;
      pointer-events:auto;
}
.chat-close-btn:hover{
    transform:scale(1.15);
    opacity:.85;
}
.chat-header button{

    border:none;
    background:none;
    color:white;
  pointer-events:auto;
    position:relative;
    z-index:9999;
    font-size:22px;

}


.chat-user{

    display:flex;
    align-items:center;

    gap:15px;

    padding:15px;

    cursor:pointer;

    border-bottom:1px solid #eee;

}


.chat-user div{
    display:flex;
    flex-direction:column;
}


.chat-user strong{
    font-size:16px;
    color:#333;
}


.chat-user small{

    display:block;

    color:#777;

    margin-top:3px;

    font-size:13px;

}


.chat-user:hover{

    background:#f1f5f2;

}


.chat-user i{

    font-size:38px;
    color:#1e5631;

}


.chat-body{

    flex:1;

    overflow:auto;

    padding:15px;

    background:#f5f5f5;

}


.chat-footer{

    display:flex;

    gap:10px;

    padding:15px;

}


#conversation{

    height:100%;

    display:flex;

    flex-direction:column;

}


.conversation-header{

    display:flex;

    align-items:center;

    gap:10px;

}
.conversation-header button{
    pointer-events:auto;
}

.message{

    display:flex;

    align-items:center;

    gap:8px;

    margin-bottom:15px;

}


.message.left{

    justify-content:flex-start;

}


.message.right{

    justify-content:flex-end;

}


.sender-icon{

    font-size:32px;
    color:#1e5631;

}


.bubble{

    background:#e9ecef;

    padding:10px 15px;

    border-radius:15px;

    max-width:70%;

}


.collector-bubble{

    background:#1e5631;

    color:white;

}

.message.right .sender-icon{
    color:#1e5631;
}

#barangayScheduleList .list-group-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    font-size:15px;
}

#barangayScheduleList .text-muted{
    font-weight:600;
    white-space:nowrap;
}

#barangayScheduleList .list-group-item{
    border-radius:10px;
    margin-bottom:10px;
    padding:12px 16px;
}

#barangayScheduleList .text-muted{
    font-weight:600;
    color:#6c757d !important;
}

@media (max-width:576px){

    #barangayScheduleList .d-flex{
        flex-direction:column;
        align-items:flex-start !important;
        gap:4px;
    }

}

/* Read-only display */
.readonly-field{
    display:flex;
    align-items:flex-start;
    gap:6px;
    padding:10px 0;
    font-size:15px;
    color:#212529;
}

.readonly-label{
    font-weight:700;
    white-space:nowrap;
}

.readonly-value{
    font-weight:400;
    word-break:break-word;
}

@media(max-width:576px){
    .readonly-field{
        font-size:14px;
    }
}

/* Highlight Report Issue card */
.report-focus{
    animation: reportFlash 1.2s ease-in-out 2;
    border:3px solid #dc3545 !important;
    box-shadow:0 0 20px rgba(220,53,69,.35);
}

@keyframes reportFlash{
    0%{
        transform:scale(1);
        box-shadow:0 0 0 rgba(220,53,69,0);
    }
    50%{
        transform:scale(1.02);
        box-shadow:0 0 20px rgba(220,53,69,.45);
    }
    100%{
        transform:scale(1);
        box-shadow:0 0 0 rgba(220,53,69,0);
    }
}
</style>
</head>
<body>

    <!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-dark fixed-top">
    <div class="container-fluid">

        <!-- Center Logo -->
        <a class="navbar-brand navbar-logo" href="#">
            <img src="assets/enviromanage-logo.png" alt="EnviroManage Logo">
        </a>

        <!-- Profile -->
        <ul class="navbar-nav ms-auto">
            <a href="collector-notification.php"
       class="text-decoration-none">

        <button class="nav-icon-btn position-relative">

            <i class="bi bi-bell-fill"></i>

        </button>

    </a>

        </ul>

    </div>
</nav>
  

    <!-- ================= MAIN CONTENT ================= -->
<div class="page-wrapper">
<!-- ================= SIDEBAR ================= -->
<aside class="sidebar" id="sidebar">

    <div class="sidebar-menu">

        <!-- Home -->
        <a href="collector-home.php" class="sidebar-item active">
            <i class="bi bi-house-fill"></i>
            <span>Home</span>
        </a>

        <!-- Route Map -->
        <a href="collector-route-map.php" class="sidebar-item ">
            <img src="assets/location.png" alt="Route Map">
            <span>Route Map</span>
        </a>

        <!-- Profile -->
        <a href="collector-profile.php" class="sidebar-item">
            <i class="bi bi-person-fill"></i>
            <span>Profile</span>
        </a>

    </div>

</aside>
<main class="main-content">
        <div class="container">

            <!-- Page Title -->

            <div class="text-center mb-4">

                <h2 class="fw-bold text-success">
                    COLLECTION MANAGER
                </h2>

        
            </div>

            <!-- Date Card -->

   <div class="card">

    <div class="card-body">

        <label class="form-label fw-semibold">
            Collection Date
        </label>

        <input
            type="date"
            id="collectionDate"
            class="form-control mb-4"
        >

       
     <div id="scheduleDetails" style="display:none;">

    <div class="row">

        <div class="col-md-6">


            <p>
                <strong>Collector:</strong>
                <span id="scheduleCollector"></span>
            </p>


            <p class="mb-0">
                <strong>Garbage Type:</strong>
                <span id="scheduleGarbageType"></span>
            </p>

        </div>

    </div>

</div>
</div>
</div>

<div id="collectionForm" style="display:none;">

    <!-- ================= COLLECTION PROGRESS ================= -->
    <div class="card mb-3">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Collection Progress</h5>

                <small class="text-muted">
                    Last Updated:
                    <span id="lastUpdated">--</span>
                </small>
            </div>

            <!-- Covered Barangay -->
            <div class="mb-3">
                <label id="barangayLabel" class="form-label fw-semibold">
    Covered Barangays
</label>


                <select id="barangaySelect" class="form-select"></select>

                <div id="barangayText" class="readonly-field d-none">
    <span class="readonly-label">Barangay:</span>
    <span class="readonly-value"></span>
</div>
            </div>

            <!-- Street -->
            <div class="mb-3">
                <label id="streetLabel" class="form-label fw-semibold">
    Street
</label>

                <select id="streetSelect" class="form-select">
                    <option selected disabled>Select Street</option>
                </select>

                <div id="streetText" class="readonly-field d-none">
                    <span class="readonly-label">Street:</span>
                    <span class="readonly-value"></span>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-4">
                
<label id="statusLabel" class="form-label fw-semibold">
    Status
</label>

                <select id="statusSelect" class="form-select">
                    <option value="" selected disabled>Select Status</option>
                    <option>In Progress</option>
                    <option>Completed</option>
                    <option>Incomplete</option>
                </select>

                <div id="statusText" class="readonly-field d-none">
                    <span class="readonly-label">Status:</span>
                    <span class="readonly-value"></span>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button
                    id="saveBtn"
                    class="btn btn-success btn-sm rounded-pill px-4"
                    onclick="validateSave()">
                    Save Changes
                </button>

                <button
                    id="editBtn"
                    class="btn btn-success btn-sm rounded-pill px-4 d-none"
                    onclick="enableEditing()">
                    Update
                </button>
            </div>

        </div>
    </div>

    <!-- ================= REPORT ISSUE ================= -->
    <div class="card" id="reportIssueCard">
        <div class="card-body">

            <h5 class="fw-bold mb-3">Report Issue</h5>

            <div class="mb-3">
                <label class="form-label fw-semibold">Issue Type</label>

                <select class="form-select" id="issueType">
                    <option selected disabled>Select Issue Type</option>
                    <option>Road Blocked</option>
                    <option>Truck Breakdown</option>
                    <option>Overflowing Waste</option>
                    <option>Resident Complaint</option>
                    <option>Weather Delay</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>

                <textarea
                    id="issueDescription"
                    class="form-control"
                    rows="5"
                    placeholder="Describe the issue..."></textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button
                    class="btn btn-success btn-sm rounded-pill px-4"
                    onclick="validateReport()">
                    Submit Report
                </button>
            </div>

        </div>
    </div>

</div>

    </main>
</div>
<div class="modal fade" id="updateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Update Progress</h5>
                   
            </div>

            <div class="modal-body text-center">
                Are you sure you want to update the collection progress?
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-success"
                        onclick="confirmUpdate()">
                    Yes, Update
                </button>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="saveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Save Changes</h5>
                  
            </div>

            <div class="modal-body text-center">
                Do you want to save all changes made to this collection?
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-success"
                        onclick="confirmSave()">
                    Save
                </button>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Submit Report</h5>
                     
            </div>

            <div class="modal-body text-center">
                Are you sure you want to submit this issue report?
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-success"
                        onclick="confirmReport()">
                    Submit
                </button>
            </div>

        </div>
    </div>
</div>
    <!-- ================= CHAT BUTTON ================= -->

 <div class="chat-window" id="chatWindow">

<!-- CHAT LIST -->
<div id="chatList">

  <div class="chat-header">

    <strong>
        <i class="bi bi-chat-left-text-fill"></i>
        Messages
    </strong>

    <button class="chat-close-btn" onclick="closeChatWindow()">
        <i class="bi bi-x-lg"></i>
    </button>

</div>


<div class="chat-user" onclick="openChat('MENRO')">

    <i class="bi bi-person-circle"></i>

    <div>

        <strong>MENRO</strong>

        <small>
            Good morning Collector.
        </small>

    </div>

</div>



</div>




    <!-- CONVERSATION -->
    <div id="conversation" style="display:none;">


 <div class="chat-header conversation-header">

    <div class="d-flex align-items-center gap-2">

      <button type="button" onclick="backToList(event)">
    <i class="bi bi-arrow-left"></i>
</button>
        <i class="bi bi-person-circle sender-header-icon"></i>

        <strong id="chatName">MENRO</strong>

    </div>

  <button type="button" class="chat-close-btn" onclick="closeChatWindow(event)">
    <i class="bi bi-x-lg"></i>
</button>
</div>



  <div class="chat-body">

    <div class="message left">

        <i class="bi bi-person-circle sender-icon"></i>

        <div class="bubble">
            Good morning Collector.
        </div>

    </div>

</div>


      <div class="chat-footer">

    <input 
    type="text"
    id="messageInput"
    class="form-control"
    placeholder="Type message...">

    <button class="btn btn-success" onclick="sendMessage()">
        <i class="bi bi-send-fill"></i>
    </button>

</div>

        </div>


    </div>


<!-- Floating Message Button -->
<button class="chat-btn" id="chatBtn">

    <i class="bi bi-chat-dots-fill"></i>

   <span class="badge" id="messageBadge" style="display:none;"></span>
</button>
    <!-- ================= BOTTOM NAVIGATION ================= -->

  <nav class="bottom-nav">

    <a href="collector-home.php" class="nav-item active">
        <i class="bi bi-house-fill"></i>
        <span>Home</span>
    </a>

    <a href="collector-route-map.php" class="nav-item">
        <img src="assets/location.png" alt="Route Map">
        <span>Route Map</span>
    </a>

    <a href="collector-profile.php" class="nav-item">
        <i class="bi bi-person-fill"></i>
        <span>Profile</span>
    </a>

</nav>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>

    let isViewOnly = false;
    
 // ===============================
// COLLECTION DATE
// ===============================

const dateInput = document.getElementById("collectionDate");
const collectionForm = document.getElementById("collectionForm");
const scheduleDetails = document.getElementById("scheduleDetails");


// Philippine Time
const today = new Date(
    new Date().toLocaleString("en-US", {
        timeZone: "Asia/Manila"
    })
);

const currentDate =
    today.getFullYear() +
    "-" +
    String(today.getMonth() + 1).padStart(2, "0") +
    "-" +
    String(today.getDate()).padStart(2, "0");


// Set current date automatically
dateInput.value = currentDate;


// Allow selecting other dates
dateInput.max = ""; 
dateInput.min = "";


// Date changed
dateInput.addEventListener("change", function(){

    loadSchedule(this.value);

});


// Load automatically when page opens
window.addEventListener("load", function(){

    loadSchedule(currentDate);

});



function loadSchedule(selectedDate){

    if (!selectedDate) {

        scheduleDetails.style.display = "none";
        collectionForm.style.display = "none";

        return;
    }

       // Check if selected date is today
    isViewOnly = selectedDate !== currentDate;


    fetch(
        "collector-fetch-schedule.php?date=" 
        + encodeURIComponent(selectedDate)
    )

    .then(response => response.json())

    .then(data => {


        if (!data.success || data.schedules.length === 0) {


            scheduleDetails.style.display = "none";
            collectionForm.style.display = "none";


            Swal.fire({
                icon:"info",
                title:"No Schedule",
                text:"No collection schedule found for today."
            });


            return;

        }


        const schedule = data.schedules[0];


        document.getElementById("scheduleCollector").textContent =
            schedule.collector_name;


        document.getElementById("scheduleGarbageType").textContent =
            schedule.garbage_type;



        data.schedules.sort((a,b)=>{

            return a.start_time.localeCompare(b.start_time);

        });



        const barangaySelect =
            document.getElementById("barangaySelect");


        barangaySelect.innerHTML =
            '<option value="" selected disabled>Select Barangay</option>';



        data.schedules.forEach(schedule=>{


            const option =
                document.createElement("option");


            option.value = schedule.barangay;


            option.dataset.time =
                schedule.time;


            option.dataset.scheduleId =
                schedule.id;


            option.textContent =
                `${schedule.barangay} | ${schedule.time}`;


            barangaySelect.appendChild(option);


        });



        document.getElementById("lastUpdated").textContent =
            new Date().toLocaleTimeString([],{
                hour:"numeric",
                minute:"2-digit"
            });


        scheduleDetails.style.display="block";

        collectionForm.style.display="block";

        if(isViewOnly){

    disableCollectorActions();

}else{

    enableCollectorActions();

}


    })

    .catch(error=>{


        console.error(error);


        scheduleDetails.style.display="none";

        collectionForm.style.display="none";


        Swal.fire({
            icon:"error",
            title:"Error",
            text:"Unable to load collection schedule."
        });


    });

}

function updateLastUpdated() {
    document.getElementById("lastUpdated").textContent =
        new Date().toLocaleTimeString([], {
            hour: "numeric",
            minute: "2-digit"
        });
}


// ===============================
// CHAT BUTTON
// ===============================
const chatBtn=document.getElementById("chatBtn");
const chatWindow=document.getElementById("chatWindow");

let isDragging = false;
let startX = 0;
let startY = 0;
let active = false;
let x = 0;
let y = 0;

chatBtn.addEventListener("pointerdown", function(e){

    isDragging = false;

   

    startX = e.clientX;
    startY = e.clientY;

    active = true;

    x = e.clientX - chatBtn.offsetLeft;
    y = e.clientY - chatBtn.offsetTop;

    chatBtn.setPointerCapture(e.pointerId);

});
   

chatBtn.addEventListener("pointermove", function(e){

    if(!active) return;


    let moveX = Math.abs(e.clientX - startX);
    let moveY = Math.abs(e.clientY - startY);


 if(moveX > 5 || moveY > 5){

    isDragging = true;

    // Isara ang chat window habang dini-drag ang button
    if(chatWindow.classList.contains("show")){
        chatWindow.classList.remove("show");
        chatBtn.classList.remove("active");

        // Bumalik sa chat list kung nasa conversation
        backToList();
    }

}

let top = e.clientY - y;

const maxY = window.innerHeight - chatBtn.offsetHeight;
top = Math.max(0, Math.min(top, maxY));
// Left o Right lang pero may gap gaya ng default position
let left;

let sideGap = 30; // desktop default gap

if(window.innerWidth <= 576){
    sideGap = 15;
}
else if(window.innerWidth <= 991){
    sideGap = 20;
}


if (e.clientX < window.innerWidth / 2) {

    // LEFT SIDE
    left = sideGap;

} else {

    // RIGHT SIDE
    left = window.innerWidth - chatBtn.offsetWidth - sideGap;

}


chatBtn.style.left = left + "px";
chatBtn.style.top = top + "px";

chatBtn.style.right = "auto";
chatBtn.style.bottom = "auto";
 
});


chatBtn.addEventListener("pointerup",function(){

    active=false;


    if(window.innerWidth > 991){

        localStorage.setItem("chatLeft",chatBtn.style.left);
        localStorage.setItem("chatTop",chatBtn.style.top);

    }


});
chatBtn.addEventListener("click", function () {

    if (isDragging) {
        isDragging = false;
        return;
    }


    if (!chatWindow.classList.contains("show")) {


const btnRect = chatBtn.getBoundingClientRect();

const gap = 12;

const chatWidth = window.innerWidth <= 576 ? 320 : 360;
const chatHeight = window.innerWidth <= 576 ? 420 : 500;

chatWindow.style.width = window.innerWidth <= 576 ? "88%" : "360px";
chatWindow.style.maxWidth = chatWidth + "px";
chatWindow.style.height = chatHeight + "px";

let left;
let top = btnRect.top;
          // Kapag nasa left side ang button,
// ilagay ang chat window sa kanan ng button

if(btnRect.left < window.innerWidth / 2){

      left = btnRect.right + gap + 25;

}

// Kapag nasa right side ang button,
// ilagay ang chat window sa kaliwa ng button

else{

    left = btnRect.left - chatWidth - gap;

}


        left = Math.max(
    15,
    Math.min(
        left,
        window.innerWidth - chatWidth - 15
    )
);

            top = Math.max(
                10,
                Math.min(top, window.innerHeight - chatHeight - 10)
            );


          chatWindow.style.left = left + "px";
chatWindow.style.right = "auto";
chatWindow.style.transform = "none";

if(window.innerWidth <= 576){

    chatWindow.style.top = "auto";
    chatWindow.style.bottom = "145px";

}else{

    chatWindow.style.top = top + "px";
    chatWindow.style.bottom = "auto";

}

}

if(chatWindow.classList.contains("show")){
    chatWindow.classList.remove("show");
    chatBtn.classList.remove("active");

    backToList();

}else{

    chatWindow.classList.add("show");
    chatBtn.classList.add("active");

}

});
function setChatPosition(){

    if(window.innerWidth <= 991){

        chatBtn.style.left = "auto";
        chatBtn.style.top = "auto";
        if(window.innerWidth <= 576){

            chatBtn.style.right = "15px";
            chatBtn.style.bottom = "90px";

        }else{

            chatBtn.style.right = "20px";
            chatBtn.style.bottom = "95px";

        }

        return;
    }


    // DESKTOP
    const savedLeft = localStorage.getItem("chatLeft");
    const savedTop = localStorage.getItem("chatTop");


    if(savedLeft && savedTop){
chatBtn.style.left = savedLeft;
chatBtn.style.top = savedTop;

chatBtn.style.right = "auto";
chatBtn.style.bottom = "auto";

    }
    else{

    chatBtn.style.left = "auto";
    chatBtn.style.top = "auto";

    chatBtn.style.right = "30px";
    chatBtn.style.bottom = "95px";

}

}


window.onload = function(){

    setChatPosition();

};


window.addEventListener("resize",function(){

    setChatPosition();

});
// ===============================
// MAP CARD ANIMATION
// ===============================

const mapCard=document.querySelector(".map-card");

if(mapCard){

    mapCard.style.opacity="0";
    mapCard.style.transform="translateY(20px)";

    window.addEventListener("load",function(){

        setTimeout(function(){

            mapCard.style.transition="all .45s ease";

            mapCard.style.opacity="1";

            mapCard.style.transform="translateY(0)";

        },200);

    });

}


// Sample unread messages
let unreadMessages = 0;

// halimbawa may bagong message
function receiveNewMessage() {
    unreadMessages++;
    updateBadge();
}

function updateBadge() {
    const badge = document.getElementById("messageBadge");

    if (unreadMessages > 0) {
        badge.textContent = unreadMessages;
        badge.style.display = "flex";
    } else {
        badge.style.display = "none";
    }
}

// Initial load
updateBadge();

function openChat(name){

    document.getElementById("chatList").style.display="none";

    document.getElementById("conversation").style.display="flex";

    document.getElementById("chatName").innerHTML=name;

}



function backToList(e){

    if(e){
        e.stopPropagation();
    }

    document.getElementById("conversation").style.display = "none";
    document.getElementById("chatList").style.display = "block";

}

function closeChatWindow(e){

    if(e){
        e.stopPropagation();
    }

    chatWindow.classList.remove("show");
    chatBtn.classList.remove("active");

    backToList();

}
function sendMessage(){

    const input = document.getElementById("messageInput");
    const message = input.value.trim();

    if(message === ""){
        return;
    }


    const chatBody = document.querySelector(".chat-body");


    const newMessage = document.createElement("div");

   newMessage.classList.add("message", "right");


newMessage.innerHTML = `

    <div class="bubble collector-bubble">
        ${message}
    </div>

    <i class="bi bi-person-circle sender-icon"></i>

`;


    chatBody.appendChild(newMessage);


    input.value = "";


    chatBody.scrollTop = chatBody.scrollHeight;

}
document.getElementById("messageInput")
.addEventListener("keypress", function(e){

    if(e.key === "Enter"){
        sendMessage();
    }

});

const chatHeaders = document.querySelectorAll(".chat-header");

let chatDragging = false;
let chatOffsetX = 0;
let chatOffsetY = 0;

chatHeaders.forEach(header=>{

    header.addEventListener("pointerdown",function(e){

        if(e.target.closest("button")) return;

        chatDragging = true;

        const rect = chatWindow.getBoundingClientRect();

        chatOffsetX = e.clientX - rect.left;
        chatOffsetY = e.clientY - rect.top;

        header.setPointerCapture(e.pointerId);

    });

    header.addEventListener("pointermove",function(e){

        if(!chatDragging) return;

        let left = e.clientX - chatOffsetX;
        let top = e.clientY - chatOffsetY;

        left = Math.max(
            10,
            Math.min(left, window.innerWidth - chatWindow.offsetWidth - 10)
        );

        top = Math.max(
            10,
            Math.min(top, window.innerHeight - chatWindow.offsetHeight - 10)
        );

        chatWindow.style.left = left + "px";
        chatWindow.style.top = top + "px";

        chatWindow.style.right = "auto";
        chatWindow.style.bottom = "auto";

    });

    header.addEventListener("pointerup",function(){

        chatDragging = false;

    });

    header.addEventListener("pointercancel",function(){

        chatDragging = false;

    });

});

// ===============================
// STREET DROPDOWN FROM BARANGAYS.JSON
// ===============================

const streetSelect = document.getElementById("streetSelect");

let barangayData = {};

fetch("barangays.json")
    .then(res => res.json())
    .then(data => {
        barangayData = data;
    })
    .catch(err => console.error(err));

const barangaySelect = document.getElementById("barangaySelect");
const statusSelect = document.getElementById("statusSelect");
const saveBtn = document.getElementById("saveBtn");
const editBtn = document.getElementById("editBtn");


// Barangay selected
barangaySelect.addEventListener("change", function(){

    streetSelect.innerHTML =
        '<option value="" selected disabled>Select Street</option>';

    const streets = barangayData[this.value] || [];

   streets.forEach(street => {

    const cleanStreet = street.split(",")[0].trim();

    const option = document.createElement("option");

    option.value = cleanStreet;
    option.textContent = cleanStreet;

    streetSelect.appendChild(option);

});

});


// Street selected -> search database
streetSelect.addEventListener("change", function(){

    loadSavedCollection();

});



function loadSavedCollection() {

    const barangayOption =
        barangaySelect.options[barangaySelect.selectedIndex];

        console.log(
    "Searching:",
    barangaySelect.value,
    streetSelect.value
);

    if (
        barangaySelect.selectedIndex <= 0 ||
        streetSelect.selectedIndex <= 0
    ) {
        return;
    }

    const schedule_id = barangayOption.dataset.scheduleId;

    fetch(
        "collector-fetch-collection.php?" +
        new URLSearchParams({
            schedule_id: schedule_id,
            barangay: barangayOption.value,
            street: streetSelect.value
        })
    )
    .then(res => res.json())
    .then(data => {

        if(!data.success) return;

        if(!data.exists){

    document.getElementById("barangayText").classList.add("d-none");
    document.getElementById("streetText").classList.add("d-none");
    document.getElementById("statusText").classList.add("d-none");

    document.getElementById("barangaySelect").classList.remove("d-none");
    document.getElementById("streetSelect").classList.remove("d-none");
    document.getElementById("statusSelect").classList.remove("d-none");

    saveBtn.classList.remove("d-none");
    editBtn.classList.add("d-none");

    statusSelect.value="";

    isSaved=false;

    return;
}

        const record = data.record;

        window.progressId = data.progress_id;

console.log("Progress ID:", window.progressId);

        // Fill status
        statusSelect.value = record.status;

        // Readonly values
        document.querySelector("#barangayText .readonly-value").textContent =
            barangayOption.value +
            " | " +
            barangayOption.dataset.time;

        document.querySelector("#streetText .readonly-value").textContent =
            record.street;

        document.querySelector("#statusText .readonly-value").textContent =
            record.status;

        // Hide dropdowns
        barangaySelect.classList.add("d-none");
        streetSelect.classList.add("d-none");
        statusSelect.classList.add("d-none");

        document.getElementById("barangayLabel").classList.add("d-none");
        document.getElementById("streetLabel").classList.add("d-none");
        document.getElementById("statusLabel").classList.add("d-none");

        document.getElementById("barangayText").classList.remove("d-none");
        document.getElementById("streetText").classList.remove("d-none");
        document.getElementById("statusText").classList.remove("d-none");

        saveBtn.classList.add("d-none");
        editBtn.classList.remove("d-none");

        isSaved = true;

    });

}


let isUpdated = false;
let isSaved = false;
let isReported = false;

function confirmUpdate(){

    bootstrap.Modal.getInstance(
        document.getElementById("updateModal")
    ).hide();

    isUpdated = true;

    updateLastUpdated();

    Swal.fire({
        icon:"success",
        title:"Collection Progress Updated Successfully!",
        confirmButtonText:"OK",
        timer:3000,
        timerProgressBar:true
    });

}

function enableEditing(){

    document.getElementById("barangaySelect").classList.remove("d-none");
    document.getElementById("streetSelect").classList.remove("d-none");
    document.getElementById("statusSelect").classList.remove("d-none");

    //show labels again
    document.getElementById("barangayLabel").classList.remove("d-none");
document.getElementById("streetLabel").classList.remove("d-none");
document.getElementById("statusLabel").classList.remove("d-none");

    document.getElementById("barangayText").classList.add("d-none");
    document.getElementById("streetText").classList.add("d-none");
    document.getElementById("statusText").classList.add("d-none");

    document.getElementById("saveBtn").classList.remove("d-none");
    document.getElementById("editBtn").classList.add("d-none");

    isSaved = false;
}

function disableCollectorActions(){

    // Disable dropdowns
    document.getElementById("barangaySelect").disabled = true;
    document.getElementById("streetSelect").disabled = true;
    document.getElementById("statusSelect").disabled = true;


    // Hide save/update buttons
    document.getElementById("saveBtn")
        .classList.add("d-none");

    document.getElementById("editBtn")
        .classList.add("d-none");


    // Disable report issue
    document.getElementById("issueType").disabled = true;
    document.getElementById("issueDescription").disabled = true;


    document.querySelector("#reportIssueCard button")
        .disabled = true;


}


function enableCollectorActions(){

    document.getElementById("barangaySelect").disabled = false;
    document.getElementById("streetSelect").disabled = false;
    document.getElementById("statusSelect").disabled = false;


    document.getElementById("issueType").disabled = false;
    document.getElementById("issueDescription").disabled = false;


    document.querySelector("#reportIssueCard button")
        .disabled = false;

}

function confirmSave() {


    if(isViewOnly){

        Swal.fire({
            icon:"info",
            title:"View Only Mode",
            text:"You cannot modify collection records from another date."
        });

        return;
    }

    bootstrap.Modal.getInstance(
        document.getElementById("saveModal")
    ).hide();

    const selectedOption =
        barangaySelect.options[barangaySelect.selectedIndex];

    const barangay = selectedOption.value;

    const street =
        streetSelect.options[streetSelect.selectedIndex].text;

    const status =
        document.getElementById("statusSelect").value;

    // Schedule ID
    const schedule_id = selectedOption.dataset.scheduleId;

const collection_date = document.getElementById("collectionDate").value;

   console.log({
    schedule_id,
    barangay,
    street,
    status,
    collection_date
});

    fetch("collector-update-collection.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body:
            "schedule_id=" + encodeURIComponent(schedule_id) +
            "&barangay=" + encodeURIComponent(barangay) +
            "&street=" + encodeURIComponent(street) +
            "&status=" + encodeURIComponent(status)  +
    "&collection_date=" + encodeURIComponent(collection_date)
    })
    .then(response => response.json())
    .then(data => {

        console.log(data);

        if (!data.success) {

            Swal.fire({
                icon: "error",
                title: "Save Failed",
                text: data.message
            });

            return;
        }

        window.progressId = data.progress_id;

        isSaved = true;

        updateLastUpdated();

        // Display readonly values
        document.querySelector("#barangayText .readonly-value").textContent =
            `${barangay} | ${selectedOption.dataset.time}`;

        document.querySelector("#streetText .readonly-value").textContent =
            street;

        document.querySelector("#statusText .readonly-value").textContent =
            status;

        // Hide dropdowns
        document.getElementById("barangaySelect").classList.add("d-none");
        document.getElementById("streetSelect").classList.add("d-none");
        document.getElementById("statusSelect").classList.add("d-none");

        // Hide labels
        document.getElementById("barangayLabel").classList.add("d-none");
        document.getElementById("streetLabel").classList.add("d-none");
        document.getElementById("statusLabel").classList.add("d-none");

        // Show readonly text
        document.getElementById("barangayText").classList.remove("d-none");
        document.getElementById("streetText").classList.remove("d-none");
        document.getElementById("statusText").classList.remove("d-none");

        // Toggle buttons
        document.getElementById("saveBtn").classList.add("d-none");
        document.getElementById("editBtn").classList.remove("d-none");

        Swal.fire({
            icon: "success",
            title: "Changes Saved Successfully!",
            text: data.message,
            timer: 3000,
            timerProgressBar: true,
            confirmButtonText: "OK"
        });

        // If collection is incomplete, focus on Report Issue
        if (status === "Incomplete") {

            const reportCard = document.getElementById("reportIssueCard");

            reportCard.scrollIntoView({
                behavior: "smooth",
                block: "center"
            });

            reportCard.classList.add("report-focus");

            setTimeout(() => {
                reportCard.classList.remove("report-focus");
            }, 2500);

            Swal.fire({
                icon: "warning",
                title: "Collection Incomplete",
                text: "Please complete the Issue Report below before leaving this page.",
                confirmButtonText: "OK"
            });
        }

    })
    .catch(error => {

        console.error(error);

        Swal.fire({
            icon: "error",
            title: "Server Error",
            text: "Unable to save collection progress."
        });

    });

}

function validateReport(){

    const issueType = document.getElementById("issueType");
    const description = document.getElementById("issueDescription").value.trim();

    // Already submitted
    if(isReported){

        Swal.fire({
            icon:"info",
            title:"Already Submitted",
            text:"This issue report has already been submitted.",
            confirmButtonText:"OK",
            timer:3000,
            timerProgressBar:true
        });

        return;
    }

    // Incomplete
    if(issueType.selectedIndex <= 0 || description === ""){

        Swal.fire({
            icon:"warning",
            title:"Incomplete Report",
            text:"Please select an issue type and enter a description.",
            confirmButtonText:"OK",
            timer:3000,
            timerProgressBar:true
        });

        return;
    }

    // Show confirmation modal
    new bootstrap.Modal(
        document.getElementById("reportModal")
    ).show();

}

function confirmReport() {

if(isViewOnly){

        Swal.fire({
            icon:"info",
            title:"View Only Mode",
            text:"Issue reports can only be submitted for today's collection."
        });

        return;
    }
    
    bootstrap.Modal.getInstance(
        document.getElementById("reportModal")
    ).hide();

    const selectedOption =
        barangaySelect.options[barangaySelect.selectedIndex];

    const schedule_id = selectedOption.dataset.scheduleId;
    const barangay = selectedOption.value;
    const street = streetSelect.value;
    const issue_type = document.getElementById("issueType").value;
    const description = document.getElementById("issueDescription").value.trim();

    // Make sure progressId was returned after saving collection progress
    if (!window.progressId || window.progressId <= 0) {

        Swal.fire({
            icon: "warning",
            title: "Save Collection First",
            text: "Please save the collection progress before submitting an issue report."
        });

        return;
    }

    fetch("collector-submit-report.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            progress_id: window.progressId,
            schedule_id: schedule_id,
            barangay: barangay,
            street: street,
            issue_type: issue_type,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {

        if (!data.success) {

            Swal.fire({
                icon: "error",
                title: "Submission Failed",
                text: data.message || "Unable to submit report."
            });

            return;
        }

        isReported = true;

        Swal.fire({
            icon: "success",
            title: "Issue Report Submitted Successfully!",
            timer: 3000,
            timerProgressBar: true,
            confirmButtonText: "OK"
        });

    })
    .catch(error => {

        console.error(error);

        Swal.fire({
            icon: "error",
            title: "Server Error",
            text: "Unable to submit report."
        });

    });

}

function validateUpdate(){

    const barangay = barangaySelect.selectedIndex;
    const street = streetSelect.selectedIndex;
    const status = document.getElementById("statusSelect").value;

    // Already updated
    if(isUpdated){
        Swal.fire({
            icon:"info",
            title:"Already Updated",
            text:"The collection progress has already been updated.",
            confirmButtonText:"OK",
            timer:3000,
            timerProgressBar:true
        });
        return;
    }

    // Walang ginawa
    if(barangay <= 0 && street <= 0){

        Swal.fire({
            icon:"warning",
            title:"No Changes Detected",
            text:"Please update the collection details first.",
            confirmButtonText:"OK",
            timer:3000,
            timerProgressBar:true
        });

        return;
    }

    // Incomplete
    if(barangay <= 0 || street <= 0 || status === ""){

        Swal.fire({
            icon:"warning",
            title:"Incomplete Details",
            text:"Please complete all required fields.",
            confirmButtonText:"OK",
            timer:3000,
            timerProgressBar:true
        });

        return;
    }

    new bootstrap.Modal(
        document.getElementById("updateModal")
    ).show();

}
function validateSave(){

    const barangay = barangaySelect.selectedIndex;
    const street = streetSelect.selectedIndex;
    const status = document.getElementById("statusSelect").value;

    if(isSaved){

        Swal.fire({
            icon:"info",
            title:"Already Saved",
            text:"The collection details have already been saved.",
            confirmButtonText:"OK",
            timer:3000,
            timerProgressBar:true
        });

        return;

    }

    if(barangay <= 0 && street <= 0){

        Swal.fire({
            icon:"warning",
            title:"Nothing to Save",
            text:"Please update the collection details first.",
            confirmButtonText:"OK",
            timer:3000,
            timerProgressBar:true
        });

        return;

    }

    if(barangay <= 0 || street <= 0 || status === ""){

        Swal.fire({
            icon:"warning",
            title:"Incomplete Details",
            text:"Please complete all required fields.",
            confirmButtonText:"OK",
            timer:3000,
            timerProgressBar:true
        });

        return;

    }

    new bootstrap.Modal(
        document.getElementById("saveModal")
    ).show();

}


</script>

</body>
</html>