<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'resident'
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

$sql = "
SELECT
    id,
    title,
    category,
    content,
    created_at,
    updated_at
FROM news
WHERE status='Published'
ORDER BY created_at DESC
";

$result = $conn->query($sql);
$address = "Unknown Location";

if (isset($_SESSION['user_id'])) {

    $stmt = $conn->prepare("
        SELECT house_no, street, barangay
        FROM users
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();

    $addressResult = $stmt->get_result();

    if ($row = $addressResult->fetch_assoc()) {

        $parts = [];

        if (!empty($row['house_no'])) {
            $parts[] = $row['house_no'];
        }

        if (!empty($row['street'])) {
            $parts[] = $row['street'];
        }

        if (!empty($row['barangay'])) {
            $parts[] = $row['barangay'];
        }

        $address = implode(", ", $parts);
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>EnviroManage | News & Articles</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

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

.location-wrapper{
    position:absolute;
    left:50%;
    transform:translateX(-50%);
}

.location-btn{
    color:#fff;
    display:flex;
    align-items:center;
    gap:6px;
    cursor:pointer;
    font-weight:600;
    user-select:none;
}

.location-btn:hover{
    opacity:.9;
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

.location-btn span{
    max-width:320px;
    overflow:hidden;
    white-space:nowrap;
    text-overflow:ellipsis;
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

/* News Container */

.news-item{

    background:#fff;
    border-radius:14px;
    padding:18px 20px;
    margin-bottom:18px;

    border:1px solid #ececec;

    box-shadow:0 3px 10px rgba(0,0,0,.08);

    cursor:pointer;

    transition:.25s;

}

.news-item:hover{

    transform:translateY(-3px);

    box-shadow:0 8px 18px rgba(0,0,0,.12);

}

.news-header{

    display:flex;

    justify-content:space-between;

    align-items:flex-start;

    gap:20px;

}

.news-header h5{

    margin:0;

    font-weight:700;

    color:#222;

}

.news-time{
    white-space:nowrap;
    color:#777;
    font-size:.85rem;
    text-align:right;
    flex-shrink:0;
}

.news-category{

    display:inline-block;

    margin:12px 0;

    padding:5px 12px;

    background:#e8f5eb;

    color:#1e5631;

    border-radius:30px;

    font-size:.8rem;

    font-weight:600;

}

.news-content{

    color:#555;

    line-height:1.6;

    margin-bottom:18px;

    display:-webkit-box;
    -webkit-line-clamp:3;
    -webkit-box-orient:vertical;

    overflow:hidden;

    text-overflow:ellipsis;

}

.news-footer{

    display:flex;

    justify-content:space-between;

    align-items:center;

}

.news-updated{

    color:#888;

    font-size:.82rem;

}

.mobile-nav{
    display:none;
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

.page-title{
    font-size:1.35rem;
}

.news-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:10px;
}

.news-content{

    -webkit-line-clamp:2;

}

.news-footer{

    flex-wrap:wrap;

    gap:10px;

}
.page-header{

    margin-bottom:20px;

}


.page-title{

    font-size:1.35rem;

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

.news-category{
    display:inline-block;
    margin:6px 0;
    padding:2px 8px;
    font-size:.65rem;
    font-weight:600;
    border-radius:16px;
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

/* =========================
   Gallery
========================= */

.gallery-wrapper{

    position:relative;

    margin-bottom:18px;

    border-radius:16px;

    overflow:hidden;

    background:#f5f5f5;

}

.gallery-track{

    display:flex;

    overflow-x:auto;

    scroll-snap-type:x mandatory;

    scroll-behavior:smooth;

    scrollbar-width:none;

    -ms-overflow-style:none;

}

.gallery-track::-webkit-scrollbar{

    display:none;

}

.gallery-slide{

    flex:0 0 100%;

    scroll-snap-align:center;

    display:flex;

    justify-content:center;

    align-items:center;

    height:420px;

    background:#f5f5f5;

}

.gallery-slide img{

    display:block;

    margin:auto;

    max-width:100%;

    max-height:100%;

    width:auto;

    height:auto;

    object-fit:contain;

    user-select:none;

    pointer-events:none;

}

/* arrows */

.gallery-btn{

    position:absolute;

    top:50%;

    transform:translateY(-50%);

    width:46px;

    height:46px;

    border:none;

    border-radius:50%;

    background:rgba(0,0,0,.45);

    color:#fff;

    font-size:24px;

    display:flex;

    justify-content:center;

    align-items:center;

    transition:.25s;

}

.gallery-btn:hover{

    background:#1e5631;

}

.gallery-prev{

    left:15px;

}

.gallery-next{

    right:15px;

}

/* dots */

.gallery-dots{

    position:absolute;

    bottom:12px;

    width:100%;

    display:flex;

    justify-content:center;

    gap:8px;

}

.gallery-dot{

    width:10px;

    height:10px;

    border-radius:50%;

    background:rgba(255,255,255,.5);

}

.gallery-dot.active{

    background:#fff;

}
#profileDropdown{
    transform:translateX(-2px);
}

@media(max-width:768px){

    .gallery-slide{

        height:240px;

    }

    .gallery-btn{

        width:38px;

        height:38px;

        font-size:18px;

    }

    .page-header{
    gap:10px;
    margin-bottom:18px;
}

.back-btn{
    display:flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    color:#1e5631;
    font-size:1.75rem;
    font-weight:900;
    line-height:1;
    transition:.2s;
}

.back-btn:hover{
    color:#145224;
    transform:translateX(-2px);
}
}

/* Modal */

.modal-dialog{
    max-width:850px;
}

.modal-content{
    border:none;
    border-radius:18px;
    overflow:hidden;
}

@media (max-width:768px){

    .modal-dialog{
        max-width:95%;
        margin:1rem auto;
    }

    .modal-content{
        border-radius:14px;
    }

    .modal-header{
        padding:15px 18px;
    }

    .modal-body{
        padding:18px;
        font-size:14px;
    }

    .modal-header h4{
        font-size:1.15rem;
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

/* Reduce the gap below images/carousel */
.modal-body img,
.carousel{
    margin-bottom:15px !important;
}

/* ===========================
   News Details
=========================== */

.news-details{
    margin-top:8px;
}

.news-dates{

    display:flex;
    flex-direction:column;

    gap:8px;

    padding:12px 14px;

    margin-bottom:15px;

    background:#f8f9fa;

    border:1px solid #e9ecef;

    border-radius:12px;

    font-size:.82rem;

    color:#6c757d;

}

.news-dates div{

    display:flex;
    align-items:center;

    gap:8px;

}

.news-dates i{

    width:18px;

    text-align:center;

    color:#1e5631;

    font-size:15px;

}

.news-message{

    background:#f8f9fa;

    border:1px solid #e9ecef;

    border-radius:12px;

    padding:2px 15px 15px;

    color:#444;

    font-size:14px;

    line-height:1.8;

    white-space:pre-wrap;

    word-break:break-word;

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


@media(max-width:576px){

    .page-top{
        margin-bottom:18px;
    }

    .back-btn{
        width:36px;
        height:36px;
        font-size:18px;
    }

    .page-heading{
        font-size:18px;
    }

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
    <!-- MAIN CONTENT -->

    <div class="main-content">

      <div class="page-top">

    <a href="resident-home.php" class="back-btn">
        <i class="bi bi-arrow-left"></i>
    </a>

    <h3 class="page-heading">
        NEWS & ARTICLES
    </h3>
</div>


<?php if($result->num_rows > 0): ?>

<?php while($news = $result->fetch_assoc()): ?>


<?php
$images = [];
$imgQuery = $conn->query("
    SELECT image_path
    FROM news_images
    WHERE news_id = {$news['id']}
");

while($img = $imgQuery->fetch_assoc()){
    $images[] = $img['image_path'];
}
?>

<div class="news-item"
     data-bs-toggle="modal"
     data-bs-target="#newsModal<?= $news['id'] ?>">

<div class="news-header">

<h5><?= htmlspecialchars($news['title']) ?></h5>

<div class="news-time">
<?= date("M d, Y • g:i A", strtotime($news['created_at'])) ?>
</div>

</div>

<div class="news-category">
<?= htmlspecialchars($news['category']) ?>
</div>

<div class="news-content">
<?= nl2br(htmlspecialchars($news['content'])) ?>
</div>

<div class="news-footer">

<div class="news-updated">
Updated:
<?= date("M d, Y g:i A",strtotime($news['updated_at'])) ?>
</div>

<button
    class="btn btn-success btn-sm"
    data-bs-toggle="modal"
    data-bs-target="#newsModal<?= $news['id'] ?>"
    onclick="event.stopPropagation();">

    Read More
    <i class="bi bi-arrow-right ms-1"></i>

</button>

</div>

</div>

<!-- News Modal -->
<div class="modal fade"
     id="newsModal<?= $news['id'] ?>"
     tabindex="-1"
     aria-labelledby="newsModalLabel<?= $news['id'] ?>"
     aria-hidden="true">

<div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <h4 id="newsModalLabel<?= $news['id'] ?>" class="mb-2 fw-bold">
                        <?= htmlspecialchars($news['title']) ?>
                    </h4>

                    <span class="badge bg-success">
                        <?= htmlspecialchars($news['category']) ?>
                    </span>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <!-- Images -->
                <?php if(count($images) > 0): ?>

                    <?php if(count($images) == 1): ?>

                        <div class="gallery-wrapper">

    <div class="gallery-slide">

        <img
            src="<?= htmlspecialchars($images[0]) ?>"
            loading="lazy">

    </div>

</div>

                    <?php else: ?>

                        <div class="gallery-wrapper">

    <div class="gallery-track" id="gallery<?= $news['id'] ?>">

        <?php foreach($images as $img): ?>

            <div class="gallery-slide">

                <img
                    src="<?= htmlspecialchars($img) ?>"
                    loading="lazy">

            </div>

        <?php endforeach; ?>

    </div>

    <?php if(count($images) > 1): ?>

    <button
        class="gallery-btn gallery-prev"
        onclick="slideGallery('gallery<?= $news['id'] ?>',-1)">
        <i class="bi bi-chevron-left"></i>
    </button>

    <button
        class="gallery-btn gallery-next"
        onclick="slideGallery('gallery<?= $news['id'] ?>',1)">
        <i class="bi bi-chevron-right"></i>
    </button>

    <div class="gallery-dots">

        <?php foreach($images as $i=>$img): ?>

            <span
                class="gallery-dot <?= $i==0?'active':'' ?>"
                data-index="<?= $i ?>">
            </span>

        <?php endforeach; ?>

    </div>

    <?php endif; ?>

</div>

                    <?php endif; ?>

                <?php endif; ?>

               <div class="news-details">

    <div class="news-dates">

        <div>
            <i class="bi bi-calendar-event me-1"></i>
            <strong>Published:</strong>
            <?= date("F d, Y g:i A", strtotime($news['created_at'])) ?>
        </div>

        <div>
            <i class="bi bi-clock-history me-1"></i>
            <strong>Updated:</strong>
            <?= date("F d, Y g:i A", strtotime($news['updated_at'])) ?>
        </div>

    </div>

    <div class="news-message">
        <?= nl2br(htmlspecialchars($news['content'])) ?>
    </div>

</div>

            </div>

        </div>

    </div>

</div>

<?php endwhile; ?>

<?php else: ?>

    <?php endif; ?>

</div>
<!-- End Main Content -->

</div>
<!-- End App Layout -->

<!-- Mobile Bottom Navigation -->

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

<!-- Empty State -->

<?php if($result->num_rows == 0): ?>

<div class="container mt-5">

    <div class="text-center py-5">

        <i class="bi bi-newspaper"
           style="font-size:90px;color:#d0d0d0;"></i>

        <h4 class="mt-3 text-secondary">
            No News Available
        </h4>

        <p class="text-muted">
            There are currently no published news or announcements.
            Please check back later.
        </p>

    </div>

</div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

// Make the entire news container clickable
document.querySelectorAll(".news-item").forEach(item=>{

    item.addEventListener("mouseenter",function(){
        this.style.borderColor="#1e5631";
    });

    item.addEventListener("mouseleave",function(){
        this.style.borderColor="#ececec";
    });

});



// Prevent button click from triggering twice
document.querySelectorAll(".news-item button").forEach(btn=>{

    btn.addEventListener("click",function(e){

        e.stopPropagation();

    });

});

function slideGallery(id,direction){

    const gallery=document.getElementById(id);

    gallery.scrollBy({

        left:gallery.clientWidth*direction,

        behavior:"smooth"

    });

}

document.querySelectorAll(".gallery-track").forEach(track=>{

    const dots=track.parentElement.querySelectorAll(".gallery-dot");

    if(!dots.length) return;

    track.addEventListener("scroll",()=>{

        const index=Math.round(track.scrollLeft/track.clientWidth);

        dots.forEach(dot=>dot.classList.remove("active"));

        if(dots[index]) dots[index].classList.add("active");

    });

});

// Toggle Location Dropdown

const locationToggle = document.getElementById("locationToggle");
const locationSearch = document.getElementById("locationSearch");
const locationArrow = document.getElementById("locationArrow");

if(locationToggle){

    locationToggle.addEventListener("click",function(){

        locationSearch.classList.toggle("show");

   if(locationArrow){
    locationArrow.classList.toggle("bi-chevron-up");
    locationArrow.classList.toggle("bi-chevron-down");
}
    });

}

// Close Location Dropdown

document.addEventListener("click",function(e){

if(
    locationToggle &&
    locationSearch &&
    !locationToggle.contains(e.target) &&
    !locationSearch.contains(e.target)
){

        locationSearch.classList.remove("show");

        locationArrow.classList.remove("bi-chevron-up");
        locationArrow.classList.add("bi-chevron-down");

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