<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collector Notification</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CSS -->
  <style>
 
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
    display:flex;
    align-items:center;
    height:70px;
    margin:0;
    padding:0;
    transform:translateY(-3px);
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
main{
    flex:1;
    padding:35px;
}

.notification-list{
    width:100%;
    max-width:100%;
}

.notification-card{
    width:100%;
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

/* ==========================================================
   NOTIFICATION PAGE
========================================================== */

.page-title{
    font-size:1.8rem;
    font-weight:700;
    color:#198754;
}
.back-arrow{
    display:inline-flex;
    align-items:center;
}
.category-tabs{
    display:flex;
    flex-wrap:wrap;
    gap:12px;
    margin-bottom:25px;
    align-items:center;
}

.category-btn{
    display:flex;
    align-items:center;
    gap:8px;
    background:#fff;
    border:2px solid #e8e8e8;
    color:#666;
    border-radius:30px;
    padding:8px 15px;
    font-size:14px;
    font-weight:600;
    transition:.3s;
}

.category-btn:hover,
.category-btn.active{
    background:#198754;
    color:#fff;
    border-color:#198754;
}

.notification-list{
    width:100%;
    display:flex;
    flex-direction:column;
    gap:18px;
}

.notification-card{
    width:100%;
    background:#fff;
    border:none;
    border-radius:18px;
    padding:18px 22px;
    display:flex;
    align-items:center;
    gap:18px;
    box-shadow:0 8px 20px rgba(0,0,0,.06);
    transition:.25s;
    cursor:pointer;
}

.notification-card:hover{
    transform:translateY(-2px);
}

.notification-left{
    display:flex;
    align-items:center;
    gap:16px;
    flex:1;
}

.notification-left div{
    flex:1;
}

.notification-card .bi-chevron-right{
    font-size:18px;
    color:#999;
    margin-left:auto;
}

.notification-left h6{
    margin:0;
    font-size:17px;
    font-weight:600;
}

.notification-left small{
    margin-top:6px;
    display:block;
    color:#777;
    font-size:14px;
}
.status-dot{
    width:13px;
    height:13px;
    border-radius:50%;
    background:#198754;
    flex-shrink:0;
}

.status-dot.read{
    background:#bdbdbd;
}

.notification-card.unread{
    border-left:6px solid #198754;
    background:#f8fff9;
}

.notification-card.unread h6{
    color:#198754;
}

.notification-info{
    background:#fff;
    border:1px solid #e8e8e8;
    border-radius:15px;
    padding:25px;
}

.notification-info label{
    display:block;
    color:#198754;
    font-size:13px;
    font-weight:600;
    margin-bottom:5px;
}

.notification-info p{
    font-size:15px;
    color:#444;
    margin-bottom:20px;
}
.modal-content{
    border:none;
    border-radius:18px;
    overflow:hidden;
    max-height:85vh;
    display:flex;
    flex-direction:column;
}

.modal-dialog{
    max-width:850px;
    margin:2rem auto;
}

.modal.show{
    display:flex !important;
    align-items:center;
    justify-content:center;
}

.modal-header{
    background:#198754;
    color:#fff;
    border:none;
    position:sticky;
    top:0;
    z-index:10;
}

.modal-title{
    font-weight:600;
}

.btn-close{
    filter:invert(1);
}

.modal-body{
    padding:20px;
    overflow-y:auto;
    flex:1;
}

.modal-body label{
    color:#198754;
    display:block;
    margin-bottom:4px;
    font-weight:600;
}

.modal-body p{
    margin-bottom:10px;
    font-size:14px;
    color:#555;
}

.modal-footer{
    border:none;
    padding:18px 25px;
    background:#fff;
}
/* Desktop default */

.bottom-nav{
    display:none;
}
/* ===========================
   RESPONSIVE
=========================== */

@media (max-width:992px){
.navbar .container-fluid{

    position:relative;
}



    .navbar-brand img{
        height:38px;
    }
.sidebar{
    display:none;
}

.bottom-nav{
    display:flex;
}

.back-arrow{
    display:none;
}

.main-content{
    padding-bottom:95px;
}

.notification-card{
    padding:16px 20px;
}

.page-title{
    font-size:1.7rem;
}

/* MODAL */

.modal.show{
    display:flex !important;
    align-items:center;
    justify-content:center;
}

.modal-dialog{
    width:92%;
    max-width:720px;
    margin:auto;
}

.modal-content{
    border-radius:16px;
    max-height:82vh;
}

.modal-body{
    padding:18px;
}

}
@media (max-width:768px){

.main-content{
    padding:20px 15px 110px;
}

.page-title{
    font-size:1.45rem;
}

.category-tabs{
    overflow-x:auto;
    flex-wrap:nowrap;
    padding-bottom:6px;
    scrollbar-width:none;
}

.category-tabs::-webkit-scrollbar{
    display:none;
}

.category-btn{
    white-space:nowrap;
    flex-shrink:0;
    padding:7px 13px;
    font-size:13px;
    gap:5px;
}

.notification-card{
    padding:15px;
}

.notification-left{
    gap:12px;
}

.notification-left h6{
    font-size:15px;
}

.notification-left small{
    font-size:12px;
}

/* MODAL */

.modal.show{
    display:flex !important;
    align-items:center;
    justify-content:center;
}

.modal-dialog{
    width:94%;
    max-width:600px;
    margin:auto;
}

.modal-content{
    border-radius:15px;
    max-height:84vh;
}

.modal-body{
    padding:16px;
}

.notification-info{
    padding:16px;
}

}
@media (max-width:576px){

.page-title{
    font-size:1.45rem;
}

.category-tabs{
    overflow-x:auto;
    flex-wrap:nowrap;
    gap:8px;
    padding-bottom:6px;
    scrollbar-width:none;
}

.category-tabs::-webkit-scrollbar{
    display:none;
}

.category-btn{
    white-space:nowrap;
    flex-shrink:0;
    padding:6px 12px;
    font-size:12px;
    gap:4px;
}

.notification-card{
    padding:14px;
}

.notification-left{
    gap:12px;
}

.notification-left h6{
    font-size:14px;
}

.notification-left small{
    font-size:11px;
}

/* MODAL */

.modal.show{
    display:flex !important;
    align-items:center;
    justify-content:center;
}

.modal-dialog{
    width:95%;
    max-width:95%;
    margin:auto;
}

.modal-content{
    border-radius:14px;
    max-height:85vh;
}

.modal-header{
    padding:12px 15px;
}

.modal-title{
    font-size:16px;
}

.modal-body{
    padding:15px;
}

.notification-info{
    padding:14px;
}

.notification-info label{
    font-size:12px;
}

.notification-info p{
    font-size:13px;
    margin-bottom:12px;
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

.notification-info{
    padding:12px;
}

.notification-info label{
    font-size:11px;
}

.notification-info p{
    font-size:12px;
}

#modalHeading{
    font-size:18px;
}

#modalStatus{
    font-size:12px !important;
    padding:5px 10px !important;
}

.modal-body{
    padding:12px;
}

.modal-header{
    padding:12px 14px;
}

}
.notification-info .row > div{
    margin-bottom:15px;
}


.modal-body{
    overflow-y:auto;
}
/* Keep two-column layout on all screen sizes */

.notification-info .row{
    --bs-gutter-x: 1rem;
    --bs-gutter-y: .8rem;
}

.notification-info label{
    font-size:13px;
    margin-bottom:3px;
}

.notification-info p{
    font-size:14px;
    margin-bottom:0;
    line-height:1.4;
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
    
        <!-- ===========================
             Notification Page
        ============================ -->

  <main class="main-content px-4 py-4">
    <!-- Page Header -->
<div class="d-flex align-items-center mb-4">

   <a href="collector-home.php"
   class="back-arrow text-success me-3 fs-3 text-decoration-none">
    <i class="bi bi-arrow-left"></i>
</a>

    <div>
        <h3 class="page-title mb-1">Notifications</h3>
        <p class="text-muted mb-0">
            View all recent notifications.
        </p>
    </div>

</div>

            <!-- Categories -->

            <div class="category-tabs mb-4">

                <button class="category-btn active"
                        data-category="all">

                    All

                </button>

                <button class="category-btn"
                        data-category="collection">
<i class="bi bi-person-check-fill">  </i>
                 Collection Updates

                </button>

                <button class="category-btn"
                        data-category="route">
    <i class="bi bi-chat-left-text-fill">  </i>
                    Route Updates    
                </button>

                <button class="category-btn"
                        data-category="announcements">
<i class="bi bi-megaphone-fill"> </i>
                 Announcements

                </button>

                <button class="category-btn"
                        data-category="system">
<i class="bi bi-gear-fill"> </i>
                  System

                </button>

            </div>

            <!-- Notification List -->

            <div class="notification-list">

                <!-- Notification 1 -->

      <div class="notification-card unread"
     data-category="collection"
     data-heading="Collection Assignment"
     data-reference="COL-001"
     data-date="July 13, 2026 | 7:00 AM"
     data-status="Assigned"

     data-value1="Route 3"
     data-value2="Barangay Sampaloc"
     data-value3="Non-Biodegradable"
     data-value4="7:00 AM - 11:00 AM"

     data-description="You have been assigned to collect non-biodegradable waste in Barangay Sampaloc.">

    <div class="notification-left">

        <span class="status-dot"></span>

        <div>

            <h6>New Collection Assignment</h6>

            <small>10 minutes ago</small>

        </div>

    </div>

    <i class="bi bi-chevron-right"></i>

</div>
             <div class="notification-card unread"
     data-category="route"
     data-heading="Route Update"
     data-reference="RTE-014"
     data-date="July 13, 2026 | 8:10 AM"
     data-status="Updated"

     data-value1="Route 3"
     data-value2="Barangay Sampaloc"
     data-value3="Road Closure"
     data-value4="High Priority"

     data-description="The assigned route has been updated due to road maintenance. Please use the alternate route provided in the Route Map.">

    <div class="notification-left">

        <span class="status-dot"></span>

        <div>

            <h6>Route Updated</h6>

            <small>35 minutes ago</small>

        </div>

    </div>

    <i class="bi bi-chevron-right"></i>

</div>
                <!-- Notification 3 -->

             <div class="notification-card"
     data-category="announcements"
     data-heading="MENRO Announcement"
     data-reference="ANN-011"
     data-date="July 12, 2026"
     data-status="Published"

     data-value1="MENRO"
     data-value2="Collectors"
     data-value3="Collection Schedule"
     data-value4="Information"

     data-description="Collection schedules for tomorrow will start one hour earlier due to expected heavy rainfall.">

    <div class="notification-left">

        <span class="status-dot read"></span>

        <div>

            <h6>New Announcement</h6>

            <small>Yesterday</small>

        </div>

    </div>

    <i class="bi bi-chevron-right"></i>

</div>
                <!-- Notification 4 -->

            <div class="notification-card"
     data-category="system"
     data-heading="System Notification"
     data-reference="SYS-009"
     data-date="July 11, 2026"
     data-status="Scheduled"

     data-value1="System Maintenance"
     data-value2="Collector Portal"
     data-value3="10:00 PM - 11:30 PM"
     data-value4="Scheduled"

     data-description="EnviroManage Collector Portal will undergo scheduled maintenance. Some services may be temporarily unavailable.">

    <div class="notification-left">

        <span class="status-dot read"></span>

        <div>

            <h6>System Maintenance Reminder</h6>

            <small>2 days ago</small>

        </div>

    </div>

    <i class="bi bi-chevron-right"></i>

</div>
</div>
        </main>

    </div>




<!-- ==================================
     Notification Details Modal
=================================== -->

<div class="modal fade"

     id="notificationModal"

     tabindex="-1">

<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">

                    Notification Details

                </h5>

                <button class="btn-close"

                        data-bs-dismiss="modal">

                </button>

            </div>

       <div class="modal-body">

    <div class="notification-info">

        <div class="d-flex justify-content-between align-items-start mb-3">

            <div>
                <h5 id="modalHeading" class="fw-bold text-success mb-1">
                    Collection Request Details
                </h5>

                <small class="text-muted" id="modalSubtitle">
                    View complete notification information.
                </small>
            </div>

            <span class="badge bg-success px-3 py-2" id="modalStatus">
                Pending
            </span>

        </div>

        <hr>

    <div class="row g-2">

    <div class="col-6">
        <label>Reference No.</label>
        <p id="modalReference"></p>
    </div>

  <div class="col-6">
        <label>Date</label>
        <p id="modalDate"></p>
    </div>

    <div class="col-6">
        <label id="fieldOneLabel">Route</label>
        <p id="fieldOneValue"></p>
    </div>

   <div class="col-6">
        <label id="fieldTwoLabel">Barangay</label>
        <p id="fieldTwoValue"></p>
    </div>

    <div class="col-6">
        <label id="fieldThreeLabel">Garbage Type</label>
        <p id="fieldThreeValue"></p>
    </div>

    <div class="col-6">
        <label id="fieldFourLabel">Collection Time</label>
        <p id="fieldFourValue"></p>
    </div>

</div>
        <hr>

        <label>Description</label>

        <p id="modalDescription" class="mb-0"></p>

    </div>

</div>

        <div class="modal-footer d-none"></div>

        </div>

    </div>

</div>
<!-- Bottom Navigation -->
<nav class="bottom-nav">

    <a href="collector-home.php" class="nav-item">
        <i class="bi bi-house-fill"></i>
        <span>Home</span>
    </a>

    <a href="collector-route-map.php" class="nav-item">
        <img src="assets/location.png" alt="">
        <span>Route Map</span>
    </a>

    <a href="collector-profile.php" class="nav-item">
        <i class="bi bi-person-fill"></i>
        <span>Profile</span>
    </a>

</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {

    const notificationModal = new bootstrap.Modal(
        document.getElementById("notificationModal")
    );

    const cards = document.querySelectorAll(".notification-card");
    const categoryButtons = document.querySelectorAll(".category-btn");

    const modalHeading = document.getElementById("modalHeading");
    const modalReference = document.getElementById("modalReference");
    const modalDate = document.getElementById("modalDate");
    const modalStatus = document.getElementById("modalStatus");
    const modalDescription = document.getElementById("modalDescription");

    const fieldOneLabel = document.getElementById("fieldOneLabel");
    const fieldTwoLabel = document.getElementById("fieldTwoLabel");
    const fieldThreeLabel = document.getElementById("fieldThreeLabel");
    const fieldFourLabel = document.getElementById("fieldFourLabel");

    const fieldOneValue = document.getElementById("fieldOneValue");
    const fieldTwoValue = document.getElementById("fieldTwoValue");
    const fieldThreeValue = document.getElementById("fieldThreeValue");
    const fieldFourValue = document.getElementById("fieldFourValue");

    function updateNotificationCount(){

        const unread =
            document.querySelectorAll(".notification-card.unread").length;

        // Ready na kung maglalagay ka ng badge sa bell
    }

    updateNotificationCount();

    cards.forEach(card => {

        card.addEventListener("click", function(){

            modalHeading.textContent = this.dataset.heading;

            modalReference.textContent = this.dataset.reference;

            modalDate.textContent = this.dataset.date;

            modalDescription.textContent = this.dataset.description;

            fieldOneValue.textContent = this.dataset.value1;
            fieldTwoValue.textContent = this.dataset.value2;
            fieldThreeValue.textContent = this.dataset.value3;
            fieldFourValue.textContent = this.dataset.value4;

            modalStatus.textContent = this.dataset.status;

            modalStatus.className =
                "badge rounded-pill px-3 py-2 fs-6";

            switch(this.dataset.status){

                case "Assigned":
                    modalStatus.classList.add("bg-warning","text-dark");
                    break;

                case "Updated":
                    modalStatus.classList.add("bg-info","text-dark");
                    break;

                case "Published":
                    modalStatus.classList.add("bg-primary");
                    break;

                case "Scheduled":
                    modalStatus.classList.add("bg-secondary");
                    break;

                default:
                    modalStatus.classList.add("bg-success");

            }

            switch(this.dataset.category){

                case "collection":

                    fieldOneLabel.textContent="Route";
                    fieldTwoLabel.textContent="Barangay";
                    fieldThreeLabel.textContent="Garbage Type";
                    fieldFourLabel.textContent="Collection Time";

                    break;

                case "route":

                    fieldOneLabel.textContent="Route";
                    fieldTwoLabel.textContent="Affected Barangay";
                    fieldThreeLabel.textContent="Reason";
                    fieldFourLabel.textContent="Priority";

                    break;

                case "announcements":

                    fieldOneLabel.textContent="Sender";
                    fieldTwoLabel.textContent="Audience";
                    fieldThreeLabel.textContent="Subject";
                    fieldFourLabel.textContent="Type";

                    break;

                case "system":

                    fieldOneLabel.textContent="Activity";
                    fieldTwoLabel.textContent="Module";
                    fieldThreeLabel.textContent="Schedule";
                    fieldFourLabel.textContent="Status";

                    break;

            }

            this.classList.remove("unread");

            const dot = this.querySelector(".status-dot");

            if(dot){

                dot.classList.add("read");

            }

            updateNotificationCount();

            notificationModal.show();

        });

    });

    categoryButtons.forEach(button => {

        button.addEventListener("click", function(){

            categoryButtons.forEach(btn =>
                btn.classList.remove("active")
            );

            this.classList.add("active");

            const category = this.dataset.category;

            cards.forEach(card => {

                if(category === "all" ||
                   card.dataset.category === category){

                    card.style.display = "flex";

                }else{

                    card.style.display = "none";

                }

            });

        });

    });

});
</script>

</body>
</html>