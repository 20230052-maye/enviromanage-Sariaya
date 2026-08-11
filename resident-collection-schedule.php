<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'resident'
) {
    header("Location: login.php");
    exit;
}

/* ===========================
   DATABASE
=========================== */

$conn = new mysqli(
    "localhost",
    "u820562602_fleurscents",
    "Aa2RmDG?Pe0",
    "u820562602_fleurscents_db"
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ===========================
   CALENDAR
=========================== */

$month = isset($_GET["month"])
    ? max(1, min(12, intval($_GET["month"])))
    : date("n");

$year = isset($_GET["year"])
    ? intval($_GET["year"])
    : date("Y");

$firstDay      = mktime(0,0,0,$month,1,$year);
$totalDays     = date("t",$firstDay);
$startWeekDay  = date("w",$firstDay);

$todayDay   = date("j");
$todayMonth = date("n");
$todayYear  = date("Y");

/* ===========================
   RESIDENT BARANGAY
=========================== */

$barangay = "";

if(isset($_SESSION["selected_address_id"])){

    $stmt = $conn->prepare("
        SELECT barangay
        FROM resident_addresses
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        "i",
        $_SESSION["selected_address_id"]
    );

}else{

    $stmt = $conn->prepare("
        SELECT barangay
        FROM users
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param(
        "i",
        $_SESSION["user_id"]
    );

}

$stmt->execute();
$stmt->bind_result($barangay);
$stmt->fetch();
$stmt->close();

/* ===========================
   CURRENT ADDRESS
=========================== */

$currentAddress = "Unknown Location";

if(isset($_SESSION["selected_address_id"])){

    $stmt = $conn->prepare("
        SELECT house_no, street, barangay
        FROM resident_addresses
        WHERE id=?
    ");

    $stmt->bind_param(
        "i",
        $_SESSION["selected_address_id"]
    );

}else{

    $stmt = $conn->prepare("
        SELECT house_no, street, barangay
        FROM users
        WHERE id=?
    ");

    $stmt->bind_param(
        "i",
        $_SESSION["user_id"]
    );

}

$stmt->execute();

$result = $stmt->get_result();

if($row = $result->fetch_assoc()){

    $parts=[];

    if(!empty($row["house_no"])){
        $parts[]=$row["house_no"];
    }

    if(!empty($row["street"])){
        $parts[]=$row["street"];
    }

    if(!empty($row["barangay"])){
        $parts[]=$row["barangay"];
    }

    $currentAddress = implode(", ",$parts);

}

$stmt->close();

/* ===========================
   NAVBAR ADDRESS DATA
=========================== */

$address = $currentAddress;

$addresses = [];

$stmt = $conn->prepare("
    SELECT
        id,
        house_no,
        street,
        barangay
    FROM resident_addresses
    WHERE resident_id = ?
    ORDER BY is_default DESC, id DESC
");

$stmt->bind_param(
    "i",
    $_SESSION["user_id"]
);

$stmt->execute();

$result = $stmt->get_result();

while($row = $result->fetch_assoc()){

    $addresses[] = $row;

}

$stmt->close();

/* ===========================
   COLLECTION SCHEDULES
=========================== */

$schedules = [];

$stmt = $conn->prepare("
SELECT
    s.id,
    s.barangay,
    s.day_of_week,
    s.start_time,
    s.end_time,
    s.garbage_type,
    t.plate_no AS truck_name,

    cp.status,
    cp.updated_at

FROM schedules s

LEFT JOIN trucks t
ON s.truck_id = t.id

LEFT JOIN collection_progress cp
ON cp.schedule_id = s.id

WHERE s.barangay = ?

ORDER BY
FIELD(
    s.day_of_week,
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday',
    'Saturday'
),
cp.updated_at DESC
");

$stmt->bind_param("s",$barangay);

$stmt->execute();

$result = $stmt->get_result();

while($row = $result->fetch_assoc()){

    $schedules[$row["day_of_week"]][] = $row;

}

$stmt->close();

/* ===========================
   TODAY'S COLLECTION
=========================== */

$todayName = date("l");

$todayCollection = null;

if(isset($schedules[$todayName])){
    $todayCollection = $schedules[$todayName][0];
}

/* ===========================
   MONTH NAVIGATION
=========================== */

$prevMonth = $month - 1;
$prevYear  = $year;

if($prevMonth < 1){

    $prevMonth = 12;
    $prevYear--;

}

$nextMonth = $month + 1;
$nextYear  = $year;

if($nextMonth > 12){

    $nextMonth = 1;
    $nextYear++;

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
EnviroManage | Collection Schedule
</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">

<style>
    :root{
    --green:#1e5631;
    --green2:#2f7d44;
    --light:#f5f7f9;
    --card:#ffffff;
    --border:#e5e7eb;
    --text:#2d3436;
    --muted:#6c757d;
}

*{
    box-sizing:border-box;
}

body{
    font-family:"Segoe UI",Tahoma,Geneva,Verdana,sans-serif;
    background:#f8f9fa;
    margin:0;
    padding-top:70px;
}

.navbar{
    background:#1e5631 !important;
    height:70px;
}

.navbar-brand img{
    height:42px;
}





.profile-btn{
    color:#fff;
    font-size:1.7rem;
}
/* ===========================
   SIDEBAR
=========================== */

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
    justify-content:flex-start;
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

.main-content{
    margin-left:250px;
    padding:25px;
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
    box-shadow:0 2px 6px rgba(0,0,0,.08);
    transition:.2s;
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
    font-size:.95rem;
    display:flex;
    align-items:center;
    gap:8px;
    color:#fff;
    cursor:pointer;
    font-weight:600;
    user-select:none;
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

.location-search{
    position:fixed;
    top:70px;
    left:0;
    width:100%;
    background:#fff;
    padding:15px;
    box-shadow:0 3px 10px rgba(0,0,0,.12);
    display:none;
    z-index:1040;
}

.location-search.show{
    display:block;
}

.location-search input{
    max-width:500px;
    margin:auto;
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

/* ===========================
   MOBILE
=========================== */

.mobile-nav{
    display:none;
}
/*==========================
PAGE TITLE
==========================*/

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

/* ===========================
   TODAY CARD
=========================== */

.today-card{
    background:linear-gradient(135deg,#2f7d44,#1e5631);
    color:#fff;
    border-radius:18px;
    padding:24px;
    margin-bottom:25px;
    box-shadow:0 10px 30px rgba(0,0,0,.12);
}

.today-card h4{
    margin-bottom:18px;
    font-weight:700;
}

.today-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(160px,1fr));
    gap:18px;
}

.today-item{
    background:rgba(255,255,255,.15);
    border-radius:12px;
    padding:15px;
}

.today-item small{
    display:block;
    opacity:.85;
}

.today-item strong{
    display:block;
    margin-top:6px;
    font-size:1rem;
}

/* ===========================
   CALENDAR
=========================== */

.calendar-card{
    background:#fff;
    border-radius:18px;
    padding:22px;
    box-shadow:0 5px 20px rgba(0,0,0,.05);
}

.calendar-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.calendar-header h5{
    margin:0;
    font-weight:700;
}

.calendar-table{
    width:100%;
    table-layout:fixed;   /* Equal width for every day */
    border-collapse:collapse;
    text-align:center;
}

.calendar-table th{
    padding:12px;
    color:#666;
}

.calendar-table th,
.calendar-table td{
    width:14.285%;
}

.calendar-table td{
    height:88px;
    border:1px solid #eee;
    vertical-align:top;
    padding:4px;
    position:relative;
    overflow:hidden;
}

.calendar-day{
    width:32px;
    height:32px;
    line-height:32px;
    margin:auto;
    border-radius:50%;
}

.today{
    background:var(--green);
    color:#fff;
}

.event{
    display:block;
    margin-top:4px;
    padding:3px 6px;
    border-radius:8px;
    font-size:10px;
    font-weight:600;
    white-space:normal;
    word-break:break-word;
    line-height:1.2;
}

/* Biodegradable - Green */
.event.bio{
    background:#d9f2de;
    color:#1e5631;
}

/* Non-Biodegradable - Blue */
.event.nonbio{
    background:#dbeafe;
    color:#1d4ed8;
}

/* ==========================
   ADDRESS LIST
========================== */

.list-group-item{
    display:flex !important;
    align-items:flex-start !important;
    justify-content:flex-start !important;
    text-align:left;
    padding:15px 18px;
}

.list-group-item .form-check-input{
    margin-top:4px;
    margin-right:15px !important;
    flex-shrink:0;
}

.list-group-item > div{
    flex:1;
    text-align:left;
}

.list-group-item small{
    display:block;
    margin-top:3px;
    color:#6c757d;
    word-break:break-word;
}
.mobile-nav{
    display:none;
}

/* ===========================
   WEEKLY LIST
=========================== */

.schedule-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:20px;
    margin-top:25px;
}

.schedule-card{
    background:#fff;
    border-radius:18px;
    padding:20px;
    box-shadow:0 5px 20px rgba(0,0,0,.05);
}

.schedule-card h5{
    color:var(--green);
    font-weight:700;
    margin-bottom:15px;
}

.schedule-info{
    display:flex;
    justify-content:space-between;
    padding:10px 0;
    border-bottom:1px solid #eee;
}

.schedule-info:last-child{
    border-bottom:none;
}

/* ===========================
   MOBILE
=========================== */

.mobile-nav{
    display:none;
}

@media(max-width:991px){

.sidebar{
    display:none;
}

.main-content{
    margin-left:0;
    padding:15px;
    padding-bottom:95px;
}

.today-grid{
    grid-template-columns:repeat(2,1fr);
}

.schedule-grid{
    grid-template-columns:1fr;
}

.calendar-table td{
    height:60px;
}

.navbar-brand span{
    display:none;
}

.location-btn{
    max-width:200px;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.mobile-nav{
    display:flex;
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:#fff;
    border-top:1px solid #ddd;
    height:68px;
    justify-content:space-around;
    align-items:center;
    z-index:2000;
}

.mobile-nav a{
    text-decoration:none;
    color:#777;
    display:flex;
    flex-direction:column;
    align-items:center;
    font-size:12px;
}

.mobile-nav a i{
    font-size:22px;
    margin-bottom:3px;
}

.mobile-nav a.active{
    color:var(--green);
}

}
/* Mobile */

@media(max-width:768px){

.sidebar{
    display:none;
}

.main-content{
    margin-left:0;
    padding:18px 14px 90px;
}


.dropdown-menu{
    position:absolute !important;
    right:0 !important;
    left:auto !important;
    transform:none !important;
}

.dropdown-item{
    font-size:.85rem;
    padding:10px 15px;
}
.page-header{

    margin-bottom:20px;

}
.back-btn{
    width:36px;
    height:36px;
    font-size:18px;
    border-radius:50%;
}
.page-heading{
    margin:0;
    font-size:18px;
    font-weight:700;
    color:#1e5631;
    line-height:1.2;
}

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
    /* MOBILE LOCATION CENTER */

.location-wrapper{
    position:absolute;
    left:51%;
    transform:translateX(-50%);
    width:65%;
    display:flex;
    justify-content:center;
    z-index:1;
}

.location-btn{
    width:100%;
    justify-content:center;
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
@media(max-width:576px){

 .page-top{
    display:flex;
    align-items:center;
    gap:8px;
    margin-bottom:16px;
}

.back-btn{
    width:36px;
    height:36px;
    font-size:18px;
    border-radius:50%;
}

.page-heading{
    margin:0;
    font-size:18px;
    font-weight:700;
    color:#1e5631;
    line-height:1.2;
}
.today-card{
    padding:18px;
}

.today-grid{
    grid-template-columns:1fr;
}

.calendar-table th{
    font-size:12px;
}

.calendar-table td{
    height:52px;
    font-size:12px;
}

.calendar-day{
    width:28px;
    height:28px;
    line-height:28px;
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




.calendar-table td.clickable-day{
    cursor:pointer;
    transition:.2s;
}

.calendar-table td.clickable-day:hover{
    background:#eef8f0;
}

.calendar-table td.clickable-day:hover .calendar-day{
    background:#1e5631;
    color:#fff;
}

/* ===========================
   GOOGLE CALENDAR MONTH PICKER
=========================== */

.month-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:2px;
    margin-top:6px;
    padding:0 8px 12px; /* adds bottom padding */
}

.month-item{
    text-align:center;
    padding:8px 0;
    border-radius:18px;
    cursor:pointer;
    font-weight:500;
    font-size:15px;
    transition:.18s ease;
}

.month-item:hover{
    background:#1e5631;
    color:#fff;
    transform:scale(.96);
}

.month-item.active{
    background:#1e5631;
    color:#fff;
    font-weight:600;
}

/* Header */
.month-picker-header{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:10px;
    margin-bottom:20px;
}


.year-nav{
    position:relative;
    width:100%;
    display:flex;
    justify-content:center;
    align-items:center;
}

#pickerYear{
    min-width:60px;
    text-align:center;
    font-size:1.2rem;
    font-weight:600;
    color:#202124;
}

.year-btn{
    position:static;
    width:32px;
    height:32px;
    border:none;
    border-radius:50%;
    background:transparent;
    color:#5f6368;
    display:flex;
    align-items:center;
    justify-content:center;
    transition:.2s;
}

.year-btn:hover{
    background:#f1f3f4;
}

#prevYear{
    left:0;
}

#nextYear{
    right:0;
}

/* Google Calendar spacing */
#monthPickerModal .modal-content{
    border:none;
    border-radius:20px;
    box-shadow:0 12px 35px rgba(0,0,0,.18);
}

#monthPickerModal .modal-body{
    padding:24px 24px 30px; /* extra bottom padding */
}

#eventCarousel .carousel-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:15px;
}

#eventCarousel .carousel-title{
    flex:1;
    text-align:center;
}

#eventCarousel .carousel-title h5{
    margin:0;
    font-weight:600;
}

#eventCarousel .carousel-control-prev,
#eventCarousel .carousel-control-next{
    position:static;
    width:38px;
    height:38px;
    background:#f8f9fa;
    border-radius:50%;
    opacity:1;
    flex-shrink:0;
}

#eventCarousel .carousel-control-prev-icon,
#eventCarousel .carousel-control-next-icon{
    filter:invert(1);
    width:18px;
    height:18px;
}

#todayCarousel .carousel-control-prev,
#todayCarousel .carousel-control-next,
.schedule-card .carousel-control-prev,
.schedule-card .carousel-control-next{
    width:36px;
    height:36px;
    top:50%;
    transform:translateY(-50%);
    background:#fff;
    border-radius:50%;
    opacity:1;
    box-shadow:0 2px 8px rgba(0,0,0,.15);
}

#todayCarousel .carousel-control-prev-icon,
#todayCarousel .carousel-control-next-icon,
.schedule-card .carousel-control-prev-icon,
.schedule-card .carousel-control-next-icon{
    filter:invert(1);
    width:18px;
    height:18px;
}

.today-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
}

.today-carousel-nav{
    display:flex;
    align-items:center;
    gap:10px;
}

.today-carousel-nav span{
    font-size:.9rem;
    font-weight:600;
    white-space:nowrap;
}

.today-carousel-nav .carousel-control-prev,
.today-carousel-nav .carousel-control-next{
    position:static;
    width:34px;
    height:34px;
    transform:none;
    background:rgba(255,255,255,.18);
    border-radius:50%;
    opacity:1;
    box-shadow:none;
}

.today-carousel-nav .carousel-control-prev-icon,
.today-carousel-nav .carousel-control-next-icon{
    width:16px;
    height:16px;
}

.schedule-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
}

.week-carousel-nav{
    display:flex;
    align-items:center;
    gap:10px;
}

.week-carousel-nav span{
    font-size:.85rem;
    font-weight:600;
    white-space:nowrap;
}

.week-carousel-nav .carousel-control-prev,
.week-carousel-nav .carousel-control-next{
    position:static;
    width:34px;
    height:34px;
    transform:none;
    background:#f3f4f6;
    border-radius:50%;
    opacity:1;
    box-shadow:0 2px 6px rgba(0,0,0,.12);
}

.week-carousel-nav .carousel-control-prev-icon,
.week-carousel-nav .carousel-control-next-icon{
    width:16px;
    height:16px;
    filter:invert(1);
}
</style>

</head>

<body>

<!-- ==========================
     NAVBAR
========================== -->

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

                <i class="bi bi-chevron-down" id="locationArrow"></i>

            </div>

        </div>

        <!-- Right Side -->

      <ul class="navbar-nav flex-row align-items-center ms-auto">
     

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
onclick="confirmLogout()">
    Logout <i class="bi bi-box-arrow-right ms-1"></i>
</button>
        </li>

    </ul>


            </li>

        </ul>

    </div>

</nav>

<!-- ==========================
     LOCATION DROPDOWN
========================== -->

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

                        <div>

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
<!-- ==========================
     ADD ADDRESS MODAL
========================== -->

<div class="modal fade" id="addAddressModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header bg-success text-white">

                <h5 class="modal-title">

                    Add Pickup Address

                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="mb-3">

                    <label class="form-label">

                        House No.

                    </label>

                    <input type="text"
                           class="form-control"
                           placeholder="House Number">

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Barangay

                    </label>

                    <input type="text"
                           class="form-control"
                           placeholder="Barangay">

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Street / Sitio / Purok

                    </label>

                    <input type="text"
                           class="form-control"
                           placeholder="Street">

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Postal Code

                    </label>

                    <input type="text"
                           class="form-control"
                           value="4322">

                </div>

            </div>

            <div class="modal-footer">

              

                <button class="btn btn-success">

                    Save Address

                </button>

            </div>

        </div>

    </div>

</div>


<div class="sidebar">

    <nav class="nav flex-column">

        <a class="nav-link" href="resident-home.php">
            <i class="bi bi-house-door-fill"></i>
            <span>Home</span>
        </a>

        <a class="nav-link" href="resident-schedule-pickup.php">
            <i class="bi bi-calendar-week-fill"></i>
            <span>Schedule Pickup</span>
        </a>

        <a class="nav-link" href="resident-profile.php">
            <i class="bi bi-person-circle"></i>
            <span>Profile</span>
        </a>

    </nav>

</div>

<!-- ===========================
     MAIN CONTENT
=========================== -->

<div class="main-content">

<div class="page-top">

    <a href="resident-home.php" class="back-btn">
        <i class="bi bi-arrow-left"></i>
    </a>

    <h3 class="page-heading">
            COLLECTION SCHEDULE
        </h3>

    </div>

<?php if(isset($schedules[$todayName])): ?>

<div class="today-card">

<div class="today-header">
    <h4 class="mb-0">
        <i class="bi bi-truck"></i>
        Today's Collection
    </h4>

    <?php if(count($schedules[$todayName])>1): ?>
    <div class="today-carousel-nav">
        <button class="carousel-control-prev"
                type="button"
                data-bs-target="#todayCarousel"
                data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>

        <span id="todayCarouselCounter">
            Schedule 1 of <?= count($schedules[$todayName]) ?>
        </span>

        <button class="carousel-control-next"
                type="button"
                data-bs-target="#todayCarousel"
                data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
    <?php endif; ?>
</div>

<div id="todayCarousel"
     class="carousel slide"
     data-bs-touch="true"
     data-bs-ride="false">

    <div class="carousel-inner">

        <?php foreach($schedules[$todayName] as $index=>$todayCollection): ?>

        <div class="carousel-item <?= $index==0 ? 'active' : '' ?>">

            

            <div class="today-grid">

                <div class="today-item">
                    <small>Barangay</small>
                    <strong><?= htmlspecialchars($todayCollection["barangay"]) ?></strong>
                </div>

                <div class="today-item">
                    <small>Garbage Type</small>
                    <strong><?= htmlspecialchars($todayCollection["garbage_type"]) ?></strong>
                </div>

                <div class="today-item">
                    <small>Time</small>
                    <strong>
                        <?= date("g:i A",strtotime($todayCollection["start_time"])) ?>
                        -
                        <?= date("g:i A",strtotime($todayCollection["end_time"])) ?>
                    </strong>
                </div>

                <div class="today-item">
                    <small>Truck</small>
                    <strong><?= htmlspecialchars($todayCollection["truck_name"]) ?></strong>
                </div>

                <div class="today-item">
    <small>Status</small>
    <strong>
        <?= htmlspecialchars($todayCollection["status"] ?? "Pending") ?>
    </strong>
</div>

<div class="today-item">
    <small>Updated</small>
    <strong>
        <?= !empty($todayCollection["updated_at"])
            ? date("M d, Y g:i A", strtotime($todayCollection["updated_at"]))
            : "-" ?>
    </strong>
</div>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

</div>

</div>

<?php else: ?>

<div class="alert alert-warning shadow-sm">
<i class="bi bi-info-circle-fill"></i>
There is no garbage collection scheduled for today.
</div>

<?php endif; ?>


<!-- ===========================
     CALENDAR
=========================== -->

<div class="calendar-card">

<div class="calendar-header">

<a
class="btn btn-outline-success"
href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>">

<i class="bi bi-chevron-left"></i>

</a>

<h5>
    <button
        class="btn btn-link text-decoration-none fw-bold fs-5 text-dark p-0"
        id="changeMonthBtn">

        <?= date("F Y", mktime(0,0,0,$month,1,$year)); ?>

        <i class="bi bi-chevron-down"></i>

    </button>
</h5>

<a
class="btn btn-outline-success"
href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>">

<i class="bi bi-chevron-right"></i>

</a>

</div>

<table class="calendar-table">

<thead>

<tr>

<th>Sun</th>
<th>Mon</th>
<th>Tue</th>
<th>Wed</th>
<th>Thu</th>
<th>Fri</th>
<th>Sat</th>

</tr>

</thead>

<tbody>

<tr>

<?php

for($blank=0;$blank<$startWeekDay;$blank++){

    echo "<td></td>";

}

$currentColumn = $startWeekDay;

for($day=1;$day<=$totalDays;$day++){

    $dateName = date(
        "l",
        mktime(
            0,
            0,
            0,
            $month,
            $day,
            $year
        )
    );

    $isToday = (
        $day==$todayDay &&
        $month==$todayMonth &&
        $year==$todayYear
    );

 $hasSchedule = isset($schedules[$dateName]);

echo "<td class='clickable-day'
        data-date='".date("F j, Y", mktime(0,0,0,$month,$day,$year))."'
        data-day='".$dateName."'";

if($hasSchedule){

echo "
    data-has='1'
    data-events='".htmlspecialchars(json_encode($schedules[$dateName]), ENT_QUOTES)."'
";

}else{

    echo " data-has='0' ";

}

echo ">";

echo "<div class='calendar-day";

if($isToday){
    echo " today";
}

echo "'>$day</div>";

if($hasSchedule){

   foreach($schedules[$dateName] as $event){

    $class = (stripos($event["garbage_type"], "non") !== false)
        ? "nonbio"
        : "bio";

    echo "<div class='event {$class}'>";
    echo htmlspecialchars($event["garbage_type"]);
    echo "</div>";

}

}

echo "</td>";

    $currentColumn++;

    if($currentColumn==7){

        echo "</tr>";

        if($day!=$totalDays){

            echo "<tr>";

        }

        $currentColumn=0;

    }

}

while($currentColumn>0 && $currentColumn<7){

    echo "<td></td>";

    $currentColumn++;

}

?>

</tr>

</tbody>

</table>

</div>

<!-- ===========================
     WEEKLY COLLECTION SCHEDULE
=========================== -->

<?php

$days = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
    "Sunday"
];

?>

<div class="schedule-grid">

<?php foreach($days as $day): ?>

<div class="schedule-card">

    <div class="schedule-header">

    <h5 class="mb-0">
        <i class="bi bi-calendar-check"></i>
        <?= $day ?>
    </h5>

    <?php if(isset($schedules[$day]) && count($schedules[$day]) > 1): ?>

    <div class="week-carousel-nav">

        <button
            class="carousel-control-prev"
            type="button"
            data-bs-target="#weekCarousel<?= md5($day) ?>"
            data-bs-slide="prev">

            <span class="carousel-control-prev-icon"></span>

        </button>

        <span id="weekCounter<?= md5($day) ?>">
            Schedule 1 of <?= count($schedules[$day]) ?>
        </span>

        <button
            class="carousel-control-next"
            type="button"
            data-bs-target="#weekCarousel<?= md5($day) ?>"
            data-bs-slide="next">

            <span class="carousel-control-next-icon"></span>

        </button>

    </div>

    <?php endif; ?>

</div>

   <?php if(isset($schedules[$day])): ?>

<div id="weekCarousel<?= md5($day) ?>"
     class="carousel slide"
     data-bs-touch="true"
     data-bs-ride="false">

<div class="carousel-inner">

<?php foreach($schedules[$day] as $index=>$schedule): ?>

<div class="carousel-item <?= $index==0?'active':'' ?>">

        <div class="schedule-info">

            <span>Garbage Type</span>

            <strong>

                <?= htmlspecialchars($schedule["garbage_type"]) ?>

            </strong>

        </div>

        <div class="schedule-info">

            <span>Collection Time</span>

            <strong>

                <?= date(
                    "g:i A",
                    strtotime($schedule["start_time"])
                ); ?>

                -

                <?= date(
                    "g:i A",
                    strtotime($schedule["end_time"])
                ); ?>

            </strong>

        </div>

        <div class="schedule-info">

            <span>Truck</span>

            <strong>

                <?= htmlspecialchars($schedule["truck_name"]) ?>

            </strong>

        </div>


        <div class="schedule-info">

            <span>Barangay</span>

            <strong>

                <?= htmlspecialchars($schedule["barangay"]) ?>

            </strong>

        </div>

               <div class="schedule-info">
    <span>Collection Status</span>
    <strong>
        <?= htmlspecialchars($schedule["status"] ?? "Pending") ?>
    </strong>
</div>

<div class="schedule-info">
    <span>Last Updated</span>
    <strong>
        <?= !empty($schedule["updated_at"])
            ? date("M d, Y g:i A", strtotime($schedule["updated_at"]))
            : "-" ?>
    </strong>
</div>

        </div>

<?php endforeach; ?>

</div>


</div>

    <?php else: ?>

        <div class="text-center text-muted py-4">

            <i class="bi bi-calendar-x fs-1"></i>

            <p class="mt-3 mb-0">

                No collection schedule.

            </p>

        </div>

    <?php endif; ?>

</div>

<?php endforeach; ?>

</div>

</div>
    </div>
<!-- ===========================
     MOBILE BOTTOM NAVIGATION
=========================== -->
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

<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-week"></i>
                    Collection Schedule
                </h5>

                <button
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body" id="scheduleModalBody">

            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="monthPickerModal" tabindex="-1">

<div class="modal-dialog modal-sm modal-dialog-centered">

<div class="modal-content">

<div class="modal-body p-4">

<div class="month-picker-header">

    <button class="year-btn" id="prevYear">
        <i class="bi bi-chevron-left"></i>
    </button>

    <div id="pickerYear"><?= $year ?></div>

    <button class="year-btn" id="nextYear">
        <i class="bi bi-chevron-right"></i>
    </button>

</div>

</div>

<div class="month-grid">

<?php

$months=[
"January","February","March",
"April","May","June",
"July","August","September",
"October","November","December"
];

foreach($months as $i=>$m):

?>

<div
class="month-item <?= ($month==$i+1)?'active':'' ?>"
data-month="<?= $i+1 ?>">

<?= substr($m,0,3) ?>

</div>

<?php endforeach; ?>

</div>

</div>

</div>

</div>

</div>

<!-- ===========================
     BOOTSTRAP
=========================== -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

    const todayCarousel = document.getElementById("todayCarousel");

if (todayCarousel) {

    const bsCarousel = bootstrap.Carousel.getOrCreateInstance(todayCarousel);

    const items = todayCarousel.querySelectorAll(".carousel-item");
    const counter = document.getElementById("todayCarouselCounter");

    todayCarousel.addEventListener("slid.bs.carousel", function () {

        const active = todayCarousel.querySelector(".carousel-item.active");
        const index = [...items].indexOf(active);

        counter.textContent = `Schedule ${index + 1} of ${items.length}`;

    });

}

document.querySelectorAll("[id^='weekCarousel']").forEach(function(carousel){

    const items = carousel.querySelectorAll(".carousel-item");
    const counter = document.getElementById(
        "weekCounter" + carousel.id.replace("weekCarousel","")
    );

    if(!counter) return;

    carousel.addEventListener("slid.bs.carousel", function(){

        const active = carousel.querySelector(".carousel-item.active");
        const index = [...items].indexOf(active);

        counter.textContent =
            `Schedule ${index + 1} of ${items.length}`;

    });

});

// Highlight today's schedule card
document.addEventListener("DOMContentLoaded", function(){

    const today = new Date().toLocaleDateString(
        "en-US",
        { weekday: "long" }
    );

    document.querySelectorAll(".schedule-card h5").forEach(function(title){

        if(title.textContent.trim() === today){

            title.parentElement.style.border = "none";
        }

    });

});

const toggle=document.getElementById("locationToggle");
const search=document.getElementById("locationSearch");
const arrow=document.getElementById("locationArrow");

toggle.onclick=function(){

    if(search.classList.contains("show")){

        checkAddressChange();

    }else{

        search.classList.add("show");

        arrow.classList.remove("bi-chevron-down");
        arrow.classList.add("bi-chevron-up");

    }

};

document.addEventListener("click", function(e){

    // Dropdown is closed
    if(!search.classList.contains("show")){
        return;
    }

    // Ignore clicks on the location button
    if(toggle.contains(e.target)){
        return;
    }

    // Ignore clicks inside the dropdown
    if(search.contains(e.target)){
        return;
    }

    // Ignore clicks inside the Add Address modal
    const modal = document.getElementById("addAddressModal");

    if(modal.classList.contains("show")){
        return;
    }

    checkAddressChange();

});

const locationText = document.getElementById("currentLocation");

const savedAddress = localStorage.getItem("pickup_address");
const savedAddressId = localStorage.getItem("pickup_address_id");

if(savedAddress){

    locationText.textContent = savedAddress;

    const radio = document.querySelector(
        `input[name="pickup_address"][value="${savedAddressId}"]`
    );

    if(radio){
        radio.checked = true;
    }

}

let activeRadio =
    document.querySelector(
        'input[name="pickup_address"]:checked'
    ) ||
    document.querySelector(
        'input[name="pickup_address"]'
    );

let pendingRadio = activeRadio;

if(!localStorage.getItem("pickup_address")){

    localStorage.setItem(
        "pickup_address",
        locationText.textContent.trim()
    );

    localStorage.setItem(
        "pickup_address_id",
        activeRadio.value
    );

}

document.querySelectorAll(
    'input[name="pickup_address"]'
).forEach(radio=>{

    radio.addEventListener("change",function(){

        pendingRadio = this;

    });

});

function closeDropdown(){

    search.classList.remove("show");

    arrow.classList.remove("bi-chevron-up");
    arrow.classList.add("bi-chevron-down");

}
async function checkAddressChange(){

    if(activeRadio === pendingRadio){

        closeDropdown();
        return;

    }

    const result = await Swal.fire({

        title: "Change Pickup Address?",

        html: `
            <div class="text-start">

                <small class="text-muted">
                    Your pickup address will be changed to:
                </small>

                <div class="fw-semibold mt-2">
                    <i class="bi bi-geo-alt-fill text-success"></i>
                    ${pendingRadio.dataset.address}
                </div>

            </div>
        `,

        icon: "question",

        showCancelButton: true,

        confirmButtonText: "Change",

        cancelButtonText: "Cancel",

        confirmButtonColor: "#1e5631",

        reverseButtons: true,

        allowOutsideClick: false,

        focusCancel: true

    });

    if(result.isConfirmed){

        locationText.textContent =
    pendingRadio.dataset.address;

activeRadio = pendingRadio;

localStorage.setItem(
    "pickup_address",
    pendingRadio.dataset.address
);

localStorage.setItem(
    "pickup_address_id",
    pendingRadio.value
);
        const form = new FormData();

        form.append(
            "address_id",
            pendingRadio.value || 0
        );

        const response = await fetch(
            "resident-set-active-address.php",
            {
                method:"POST",
                body:form
            }
        );

        const data = await response.json();

        if(data.success){

            Swal.fire({

                toast:true,

                position:"top-end",

                icon:"success",

                title:"Pickup address updated",

                showConfirmButton:false,

                timer:1800,

                timerProgressBar:true

            });

        }

    }else{

        activeRadio.checked = true;
        pendingRadio = activeRadio;

    }

    closeDropdown();

}

fetch("barangays.json")
.then(res => res.json())
.then(data=>{

    const barangayInput = document.getElementById("barangay");
    const streetInput = document.getElementById("street");

    const barangaySuggestions =
        document.getElementById("barangaySuggestions");

    const streetSuggestions =
        document.getElementById("streetSuggestions");

    const barangays = Object.keys(data);

    // -------------------------
    // BARANGAY AUTOCOMPLETE
    // -------------------------

    barangayInput.addEventListener("input",function(){

        const value=this.value.toLowerCase();

        barangaySuggestions.innerHTML="";

        if(value===""){

            barangaySuggestions.style.display="none";
            return;

        }

        barangays
        .filter(b=>b.toLowerCase().includes(value))
        .forEach(barangay=>{

            const item=document.createElement("button");

            item.type="button";
            item.className="list-group-item list-group-item-action";
            item.textContent=barangay;

            item.onclick=function(){

                barangayInput.value=barangay;

                barangaySuggestions.style.display="none";

                streetInput.value="";

            };

            barangaySuggestions.appendChild(item);

        });

        barangaySuggestions.style.display =
            barangaySuggestions.children.length
            ? "block"
            : "none";

    });

    // -------------------------
    // STREET AUTOCOMPLETE
    // -------------------------

    streetInput.addEventListener("input",function(){

        const barangay = barangayInput.value;

        if(!data[barangay]){

            streetSuggestions.style.display="none";
            return;

        }

        const value=this.value.toLowerCase();

        streetSuggestions.innerHTML="";

        data[barangay]
        .filter(s=>s.toLowerCase().includes(value))
        .forEach(street=>{

            const item=document.createElement("button");

            item.type="button";
            item.className="list-group-item list-group-item-action";
            item.textContent=street;

            item.onclick=function(){

                streetInput.value=street;

                streetSuggestions.style.display="none";

            };

            streetSuggestions.appendChild(item);

        });

        streetSuggestions.style.display =
            streetSuggestions.children.length
            ? "block"
            : "none";

    });

    document.addEventListener("click",function(e){

        if(!barangayInput.contains(e.target))
            barangaySuggestions.style.display="none";

        if(!streetInput.contains(e.target))
            streetSuggestions.style.display="none";

    });

});



</script>

<script>

document
.getElementById("addAddressForm")
.addEventListener("submit",async function(e){

    e.preventDefault();

    const formData = new FormData(this);

    const response = await fetch(
        "resident-add-address.php",
        {
            method:"POST",
            body:formData
        }
    );

    const result = await response.json();

    if(result.success){

        location.reload();

    }else{

        alert("Unable to save address.");

    }

});

const scheduleModal = new bootstrap.Modal(
    document.getElementById("scheduleModal")
);

document.querySelectorAll(".clickable-day").forEach(cell=>{

    cell.addEventListener("click",function(){

        const body=document.getElementById("scheduleModalBody");

        const date=this.dataset.date;

        if(this.dataset.has==="0"){

            body.innerHTML=`
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x fs-1"></i>
                    <h5>${date}</h5>
                    <p>No collection schedule.</p>
                </div>
            `;

            scheduleModal.show();
            return;
        }

        const events=JSON.parse(this.dataset.events);

           events.forEach(event => {
            console.log(event.barangay);
            console.log(event.garbage_type);
            console.log(event.start_time);
            console.log(event.end_time);
            console.log(event.truck_name);
        });

        let html=`
        <div id="eventCarousel"
             class="carousel slide"
             data-bs-touch="true"
             data-bs-ride="false">

            <div class="carousel-inner">
        `;

        events.forEach((event,index)=>{

            html+=`

            <div class="carousel-item ${index===0 ? 'active' : ''}">

    <div class="carousel-top">

        <button class="carousel-control-prev"
                type="button"
                data-bs-target="#eventCarousel"
                data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>

        <div class="carousel-title">
            <h5 class="text-success mb-1">${date}</h5>
            <small>Event ${index + 1} of ${events.length}</small>
        </div>

        <button class="carousel-control-next"
                type="button"
                data-bs-target="#eventCarousel"
                data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>

    </div>

    <div class="list-group mt-3">

        <div class="list-group-item d-flex justify-content-between">
            <strong>Barangay</strong>
            <span>${event.barangay}</span>
        </div>

        <div class="list-group-item d-flex justify-content-between">
            <strong>Garbage Type</strong>
            <span>${event.garbage_type}</span>
        </div>

        <div class="list-group-item d-flex justify-content-between">
            <strong>Collection Time</strong>
            <span>${event.start_time} - ${event.end_time}</span>
        </div>

        <div class="list-group-item d-flex justify-content-between">
            <strong>Truck</strong>
            <span>${event.truck_name}</span>
        </div>

        <div class="list-group-item d-flex justify-content-between">
    <strong>Collection Status</strong>
    <span>${event.status ?? 'Pending'}</span>
</div>

<div class="list-group-item d-flex justify-content-between">
    <strong>Last Updated</strong>
    <span>${
        event.updated_at
        ? new Date(event.updated_at).toLocaleString()
        : '-'
    }</span>
</div>

    </div>

</div>
            `;

        });

      html += `
    </div>
</div>
`;

        body.innerHTML=html;

        scheduleModal.show();

    });

});

const monthModal = new bootstrap.Modal(
    document.getElementById("monthPickerModal")
);

let pickerYear = <?= $year ?>;

const yearText =
    document.getElementById("pickerYear");

document
.getElementById("changeMonthBtn")
.onclick=function(){

    monthModal.show();

};

document
.getElementById("prevYear")
.onclick=function(){

    pickerYear--;

    yearText.textContent=pickerYear;

};

document
.getElementById("nextYear")
.onclick=function(){

    pickerYear++;

    yearText.textContent=pickerYear;

};

document.querySelectorAll(".month-item")
.forEach(month=>{

    month.onclick=function(){

        window.location=
        `?month=${this.dataset.month}&year=${pickerYear}`;

    };

});

</script>




</body>
</html>