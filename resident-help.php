<?php
session_start();

/* ==========================
   AUTHENTICATION
========================== */

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'resident'
) {
    header("Location: login.php");
    exit;
}

/* ==========================
   DATABASE CONNECTION
========================== */

$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ==========================
   FETCH CURRENT ADDRESS ONLY
========================== */

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT
        house_no,
        street,
        barangay
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$resident = $stmt->get_result()->fetch_assoc();

$stmt->close();

/* ==========================
   BUILD ADDRESS
========================== */

$addressParts = [];

if (!empty($resident['house_no'])) {
    $addressParts[] = $resident['house_no'];
}

if (!empty($resident['street'])) {
    $addressParts[] = $resident['street'];
}

if (!empty($resident['barangay'])) {
    $addressParts[] = $resident['barangay'];
}

$address = !empty($addressParts)
    ? implode(", ", $addressParts)
    : "Unknown Location";
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Resident Help</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
      rel="stylesheet">

<!-- Font Awesome (for chatbot & FAQ icons) -->
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

/* ==================================================
   GLOBAL
================================================== */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:"Segoe UI",Tahoma,Geneva,Verdana,sans-serif;
    background:#f8f9fa;
    padding-top:70px;
    color:#333;
}

/* ==================================================
   NAVBAR
================================================== */

.navbar{
    background:#1e5631 !important;
    height:70px;
}

.navbar .container-fluid{
    position:relative;
    height:70px;
}

/* Logo */
.navbar-brand{
    position:absolute;
    left:15px;
    top:50%;
    transform:translateY(-50%);
    margin:0;
    z-index:1055;
}

/* Profile */
.navbar-nav{
    position:absolute;
    right:15px;
    top:50%;
    transform:translateY(-50%);
    margin:0;
    z-index:1055;
}


.dropdown-toggle::after{
    display:none;
}

.dropdown-menu{
    position:absolute !important;
    top:52px !important;
    right:0 !important;
    left:auto !important;
    margin-top:0 !important;
    min-width:140px;
}
.navbar-brand img{
    height:42px;
}

/* ==================================================
   LOCATION BUTTON
================================================== */

.location-wrapper{
    position:absolute;
    left:50%;
    transform:translateX(-50%);
}

.location-btn{

    background:#1e5631;

    border:none;

    border-radius:30px;

    padding:8px 18px;

    display:flex;

    align-items:center;

    gap:8px;

    color:#fff;

    cursor:pointer;

    user-select:none;

    font-weight:600;

    transition:.2s;
}

.location-btn:hover{
    opacity:.9;
}

.location-btn i{
    color:#fff;
}

.location-btn span{

    max-width:320px;

    overflow:hidden;

    white-space:nowrap;

    text-overflow:ellipsis;
}

/* ==================================================
   LOCATION DROPDOWN
================================================== */

.location-search{

    position:fixed;

    top:70px;

    left:0;

    width:100%;

    background:#fff;

    padding:15px;

    display:none;

    z-index:1040;

    box-shadow:0 3px 10px rgba(0,0,0,.12);
}

.location-search.show{
    display:block;
}

.location-search input{
    max-width:500px;
    margin:auto;
}

/* ==================================================
   PROFILE DROPDOWN
================================================== */



.profile-btn{
    color:#fff;
    font-size:1.7rem;
}

/* ==================================================
   SIDEBAR
================================================== */

.sidebar{

    position:fixed;

    top:70px;

    left:0;

    width:220px;

    height:100%;

    background:#fff;

    border-right:1px solid #dee2e6;

    padding-top:15px;

    overflow-y:auto;

    z-index:1050;
}

.sidebar .nav-link{

    color:#495057;

    padding:10px 20px;

    display:flex;

    align-items:center;

    gap:10px;
}

.sidebar .nav-link span{
    display:inline;
}

.sidebar .nav-link:hover,
.sidebar .nav-link.active{

    background:#1e5631;

    color:#fff;

    border-radius:5px;
}

/* ==================================================
   MAIN CONTENT
================================================== */

.main-content{

    margin-left:220px;

    width:calc(100% - 220px);

    padding:25px;
}

/* ==================================================
   PAGE HEADER
================================================== */

.page-header{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:25px;
}

.page-title{
    margin:0;
    font-size:30px;
    font-weight:700;
    color:#1e5631;
}
.page-top{

    display:flex;

    align-items:center;

    gap:12px;

    margin-bottom:25px;
}

.back-btn{

    width:42px;

    height:42px;

    background:#fff;

    border:1px solid #ddd;

    border-radius:50%;

    display:flex;

    align-items:center;

    justify-content:center;

    text-decoration:none;

    color:#1e5631;

    font-size:22px;

    transition:.2s;

    box-shadow:0 2px 6px rgba(0,0,0,.08);
}

.back-btn:hover{

    background:#1e5631;

    color:#fff;
}

.page-heading{
    margin:0;
    font-size:28px;
    font-weight:700;
    color:#1e5631;
}
/* ==================================================
   HELP CONTENT
================================================== */

.help-container{
    width:100%;
}

.card{
    border:none;
    border-radius:16px;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
    margin-bottom:25px;
}

.card-body{
    padding:25px;
}

.card h2{
    color:#1e5631;
    font-size:24px;
    font-weight:700;
    margin-bottom:20px;
}

/* ==================================================
   FAQ
================================================== */

.faq-item{
    border:1px solid #dee2e6;
    border-radius:12px;
    overflow:hidden;
    margin-bottom:12px;
}

.faq-question{

    width:100%;

    background:#fff;

    border:none;

    padding:18px 20px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    font-size:15px;

    font-weight:600;

    cursor:pointer;

    transition:.2s;
}

.faq-question:hover{
    background:#f8f9fa;
}

.faq-question i{
    color:#1e5631;
    transition:.3s;
}

.faq-answer{

    display:none;

    padding:0 20px 20px;

    color:#666;

    line-height:1.7;

    font-size:14px;
}

.faq-item.active .faq-answer{
    display:block;
}

.faq-item.active .faq-question i{
    transform:rotate(180deg);
}

/* ==================================================
   CONTACT
================================================== */

.contact-grid{

    display:grid;

    grid-template-columns:repeat(3,1fr);

    gap:20px;
}

.contact-box{

    background:#f8f9fa;

    border-radius:15px;

    padding:25px;

    text-align:center;

    transition:.2s;
}

.contact-box:hover{
    transform:translateY(-3px);
}

.contact-box i{

    font-size:32px;

    color:#1e5631;

    margin-bottom:12px;
}

.contact-box h3{

    font-size:18px;

    margin-bottom:8px;

    font-weight:700;
}

.contact-box p{

    margin:0;

    color:#666;

    font-size:14px;

    line-height:1.6;
}
.contact-info{
    margin-top:15px;
}

.contact-info p{
    display:flex;
    align-items:flex-start;
    gap:10px;
    margin-bottom:18px;
    font-size:15px;
    color:#444;
    line-height:1.6;
}

.contact-info i{
    font-size:18px;
    color:#1e5631;
    margin-top:2px;
    min-width:20px;
}

.contact-info strong{
    color:#1e5631;
}
/* ==================================================
   CHATBOT
================================================== */

.chatbot-btn{

    position:fixed;

    right:25px;

    bottom:25px;

    width:60px;

    height:60px;

    border-radius:50%;

    background:#1e5631;

    color:#fff;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:24px;

    cursor:pointer;

    box-shadow:0 5px 15px rgba(0,0,0,.25);

    z-index:1100;
}

.chat-window{

    position:fixed;

    right:25px;

    bottom:95px;

    width:340px;

    background:#fff;

    border-radius:16px;

    overflow:hidden;

    display:none;

    box-shadow:0 8px 25px rgba(0,0,0,.18);

    z-index:1100;
}

.chat-window.active{
    display:block;
}

.chat-header{

    background:#1e5631;

    color:#fff;

    padding:15px;

    font-weight:600;
}

.chat-body{

    height:200px;

    padding:20px;

    overflow-y:auto;

    color:#555;

    font-size:14px;
}

.chat-input{

    border-top:1px solid #eee;

    display:flex;

    gap:10px;

    padding:12px;
}

.chat-input input{

    flex:1;

    border:1px solid #ddd;

    border-radius:25px;

    padding:10px 14px;

    outline:none;
}

.chat-input button{

    width:42px;

    height:42px;

    border:none;

    border-radius:50%;

    background:#1e5631;

    color:#fff;
}

/* ==================================================
   MOBILE NAVIGATION
================================================== */

.mobile-nav{
    display:none;
}

/* ==================================================
   RESPONSIVE
================================================== */

@media(max-width:991px){

    .sidebar{
        display:none;
    }

    .main-content{
        margin-left:0;
        width:100%;
        padding:20px;
        padding-bottom:90px;
    }

    .page-heading{
        font-size:20px;
    }

    .back-btn{
        width:38px;
        height:38px;
        font-size:18px;
    }

    .contact-grid{
        grid-template-columns:1fr;
    }

    .location-wrapper{
        position:static;
        transform:none;
        margin:auto;
        max-width:65%;
    }

    .location-btn{
        width:100%;
        overflow:hidden;
    }

    .location-btn span{
        max-width:120px;
        font-size:12px;
    }

 /* ===========================
   MOBILE BOTTOM NAVBAR
=========================== */

.mobile-nav{
    position:fixed;
    left:0;
    bottom:0;
    width:100%;
    height:70px;
    background:#14532d;
    display:flex;
    justify-content:space-around;
    align-items:center;
    box-shadow:0 -3px 15px rgba(0,0,0,.15);
    z-index:1050;
    border-radius:20px 20px 0 0;
    overflow:hidden;
}

.mobile-nav a{
    flex:1;
    height:100%;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    color:#ffffff;
    font-size:.75rem;
    font-weight:600;
    transition:.2s;
}

.mobile-nav a i{
    font-size:1.45rem;
    margin-bottom:4px;
    color:#ffffff;
}

/* ACTIVE */
.mobile-nav a.active{
    color:#c8d77b;
}

.mobile-nav a.active i{
    color:#c8d77b;
}


/* Hover */
.mobile-nav a:hover{
    color:#c8d77b;
}

.mobile-nav a:hover i{
    color:#c8d77b;
}

    .chatbot-btn{
        bottom:90px;
        right:18px;
    }

    .chat-window{

        right:15px;

        bottom:160px;

        width:calc(100% - 30px);
    }
}

@media(max-width:576px){

    .card-body{
        padding:20px;
    }

    .card h2{
        font-size:20px;
    }

    .faq-question{
        font-size:14px;
        padding:16px;
    }

    .faq-answer{
        font-size:13px;
        padding:0 16px 16px;
    }

    .page-heading{
        font-size:18px;
    }

   
/* MOBILE LOCATION ELLIPSIS */

.location-wrapper{
    position:static;
    transform:none;
    margin:auto;
    max-width:65%;
}

.location-btn{
    width:100%;
    max-width:100%;
    overflow:hidden;
}

.location-btn span{
    display:block;
    max-width:120px;
    overflow:hidden;
    white-space:nowrap;
    text-overflow:ellipsis;
    font-size:.75rem;
}
.location-btn i{
    flex-shrink:0;
}
}

/* LOGOUT SWEETALERT */

.logout-popup{
    width:350px !important;
    border-radius:15px !important;
    padding:20px !important;
}

.logout-title{
    font-size:1rem;
    font-weight:600;
    color:#555;
    white-space:nowrap;
    margin:0 0 20px 0;
}


.logout-yes,
.logout-cancel{
    font-size:.85rem !important;
    padding:8px 22px !important;
    border-radius:8px !important;
}


/* MOBILE */

@media(max-width:768px){

    .logout-popup{
        width:85% !important;
        padding:15px !important;
    }


    .logout-title{
        font-size:.75rem !important;
        white-space:nowrap !important;
        margin-bottom:15px !important;
    }


    .logout-yes,
    .logout-cancel{
        font-size:.75rem !important;
        padding:7px 18px !important;
    }

}

</style>

</head>
<!-- ==================================================
     NAVBAR
================================================== -->

<nav class="navbar navbar-dark fixed-top">

    <div class="container-fluid position-relative">

        <!-- Logo -->
        <a class="navbar-brand" href="resident-home.php">
            <img src="assets/enviromanage-logo.png">
        </a>

        <!-- Current Location -->
        <div class="location-wrapper">

            <div class="location-btn" id="locationToggle">

                <i class="bi bi-geo-alt-fill"></i>

                <span id="currentLocation">
                    <?= htmlspecialchars($address) ?>
                </span>

                <i class="bi bi-chevron-down"></i>

            </div>

        </div>

        <!-- Profile -->
        <ul class="navbar-nav flex-row align-items-center ms-auto">

            <li class="nav-item dropdown">

                <a class="nav-link text-white p-0"
                   href="#"
                   data-bs-toggle="dropdown"
                   data-bs-display="static">

                    <i class="bi bi-person-circle fs-4"></i>

                </a>

              <ul class="dropdown-menu dropdown-menu-end"
        aria-labelledby="profileDropdown">

        <li>
    <button class="dropdown-item text-center"
onclick="confirmLogout()">
    Logout <i class="bi bi-box-arrow-right ms-1"></i>
</button>
        </li>

    </ul>


            </li>

        </ul>

    </div>

</nav>

<!-- ==================================================
     LOCATION DROPDOWN
================================================== -->

<div class="location-search" id="locationSearch">

    <div class="container" style="max-width:600px;">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <h6 class="fw-semibold mb-3">

                    <i class="bi bi-geo-alt-fill text-success"></i>

                    Select Pickup Address

                </h6>

                <div class="list-group mb-3">

                    <label class="list-group-item d-flex align-items-start">

                        <input
                            class="form-check-input me-3 mt-1"
                            type="radio"
                            checked>

                        <div class="flex-grow-1">

                            <div class="fw-semibold text-success">
                                Current Address
                            </div>

                            <small>
                                <?= htmlspecialchars($address) ?>
                            </small>

                        </div>

                    </label>

                </div>

                <button
                    class="btn btn-success w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#addAddressModal">

                    <i class="bi bi-plus-circle me-1"></i>

                    Add New Address

                </button>

            </div>

        </div>

    </div>

</div>

<!-- ==================================================
     ADD ADDRESS MODAL
================================================== -->

<div class="modal fade"
     id="addAddressModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header bg-success text-white">

                <h5 class="modal-title">
                    Add Pickup Address
                </h5>

                <button
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="mb-3">

                    <label class="form-label">
                        House No.
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        placeholder="House Number">

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Barangay
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        placeholder="Barangay">

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Street / Sitio / Purok
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        placeholder="Street">

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Postal Code
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="4322">
                </div>

            </div>

            <div class="modal-footer">

                <button
                    class="btn btn-success">

                    Save Address

                </button>

            </div>

        </div>

    </div>

</div>

<!-- ==================================================
     SIDEBAR
================================================== -->

<div class="sidebar">

    <nav class="nav flex-column">

        <a class="nav-link"
           href="resident-home.php">

            <i class="bi bi-house-door-fill"></i>

            <span>Home</span>

        </a>

        <a class="nav-link"
           href="resident-schedule-pickup.php">

            <i class="bi bi-calendar-week-fill"></i>

            <span>Schedule Pickup</span>

        </a>

        <a class="nav-link"
           href="resident-profile.php">

            <i class="bi bi-person-circle"></i>

            <span>Profile</span>

        </a>


    </nav>

</div>
<!-- ==================================================
     MAIN CONTENT
================================================== -->

<div class="main-content">

    <!-- PAGE HEADER -->

    <div class="page-top">

        <a href="resident-profile.php" class="back-btn">

            <i class="bi bi-arrow-left"></i>

        </a>

        <h3 class="page-heading">
            HELP CENTER / FAQs
        </h3>

    </div>

    <div class="help-container">

        <!-- ==========================
             FAQ
        ========================== -->

        <div class="card">

            <div class="card-body">

                <h2>
                    Frequently Asked Questions
                </h2>

                <div class="faq-item">

                    <button class="faq-question">

                        How can I report a waste collection problem?

                        <i class="fa-solid fa-chevron-down"></i>

                    </button>

                    <div class="faq-answer">

                        You can submit a concern through the complaint section or contact the MENRO administration for assistance.

                    </div>

                </div>

                <div class="faq-item">

                    <button class="faq-question">

                        How do I schedule a pickup?

                        <i class="fa-solid fa-chevron-down"></i>

                    </button>

                    <div class="faq-answer">

                        Open the Schedule Pickup page, choose your preferred pickup date, and submit your request.

                    </div>

                </div>

                <div class="faq-item">

                    <button class="faq-question">

                        How do I update my profile?

                        <i class="fa-solid fa-chevron-down"></i>

                    </button>

                    <div class="faq-answer">

                        Go to your Profile page and select Edit Profile to update your personal information.

                    </div>

                </div>

                <div class="faq-item">

                    <button class="faq-question">

                        Where can I view my activities?

                        <i class="fa-solid fa-chevron-down"></i>

                    </button>

                    <div class="faq-answer">

                        You can see all of your requests and completed activities from the My Activities page.

                    </div>

                </div>

                <div class="faq-item">

                    <button class="faq-question">

                        Can I change my pickup address?

                        <i class="fa-solid fa-chevron-down"></i>

                    </button>

                    <div class="faq-answer">

                        Yes. Tap the location button on the navbar, then choose Add New Address.

                    </div>

                </div>

            </div>

        </div>
<!-- ==========================
     CONTACT US
========================== -->

<div class="card">

    <div class="card-body">

        <h2>Contact Us</h2>

        <div class="contact-info">

            <p>
                <i class="fa-solid fa-phone text-success me-2"></i>
               0917-315-6213
            </p>

            <p>
                <i class="fa-solid fa-envelope text-success me-2"></i>
                menro.sariaya@gmail.com
            </p>

            <p>
                <i class="fa-solid fa-building text-success me-2"></i>
               Barangay Sampaloc 2, Sariaya, Quezon
            </p>

        </div>

    </div>

</div>
<!-- ==================================================
     CHATBOT BUTTON
================================================== -->

<div class="chatbot-btn" onclick="toggleChat()">

    <i class="fa-solid fa-message"></i>

</div>

<!-- ==================================================
     CHATBOT WINDOW
================================================== -->

<div class="chat-window" id="chatWindow">

    <div class="chat-header">

        EnviroManage Assistant

    </div>

    <div class="chat-body">

        Hello! 👋

        <br><br>

        Welcome to EnviroManage.

        <br><br>

        How can I help you today?

    </div>

    <div class="chat-input">

        <input
            type="text"
            placeholder="Type your message...">

        <button>

            <i class="fa-solid fa-paper-plane"></i>

        </button>

    </div>

</div>

<!-- ==================================================
     MOBILE NAVIGATION
================================================== -->

<nav class="mobile-nav">

    <a href="resident-home.php">

        <i class="bi bi-house-door-fill"></i>

        <span>Home</span>

    </a>

    <a href="resident-schedule-pickup.php">

        <i class="bi bi-calendar-week-fill"></i>

        <span>Schedule Pickup</span>

    </a>

    <a href="resident-profile.php">

        <i class="bi bi-person-fill"></i>

        <span>Profile</span>

    </a>

</nav>
</div>
<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

/* ==================================================
   LOCATION DROPDOWN
================================================== */

const locationToggle = document.getElementById("locationToggle");
const locationSearch = document.getElementById("locationSearch");

locationToggle.addEventListener("click", function(e){

    e.stopPropagation();

    locationSearch.classList.toggle("show");

});

document.addEventListener("click", function(e){

    if(
        !locationToggle.contains(e.target) &&
        !locationSearch.contains(e.target)
    ){

        locationSearch.classList.remove("show");

    }

});


/* ==================================================
   FAQ ACCORDION
================================================== */

const faqItems = document.querySelectorAll(".faq-item");

faqItems.forEach(function(item){

    const question = item.querySelector(".faq-question");

    question.addEventListener("click", function(){

        faqItems.forEach(function(other){

            if(other !== item){

                other.classList.remove("active");

            }

        });

        item.classList.toggle("active");

    });

});


/* ==================================================
   CHATBOT
================================================== */

function toggleChat(){

    document
        .getElementById("chatWindow")
        .classList
        .toggle("active");

}


/* ==================================================
   CLOSE CHAT WHEN CLICKING OUTSIDE
================================================== */

document.addEventListener("click", function(e){

    const chat = document.getElementById("chatWindow");
    const btn = document.querySelector(".chatbot-btn");

    if(
        chat &&
        btn &&
        !chat.contains(e.target) &&
        !btn.contains(e.target)
    ){

        chat.classList.remove("active");

    }

});

function confirmLogout(){

Swal.fire({

    html: `
        <h5 class="logout-title">
            Are you sure you want to log out?
        </h5>
    `,

    showCancelButton:true,

    confirmButtonText:"Yes",

    cancelButtonText:"Cancel",

    confirmButtonColor:"#e3344f",

    cancelButtonColor:"#6c757d",

    reverseButtons:true,

    customClass:{
        popup:'logout-popup',
        confirmButton:'logout-yes',
        cancelButton:'logout-cancel'
    },

    allowOutsideClick:false

}).then((result)=>{

    if(result.isConfirmed){
        window.location.href="logout.php";
    }

});

}
</script>

</body>
</html>