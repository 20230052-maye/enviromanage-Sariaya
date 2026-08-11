<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collector Message</title>

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
    position:static;
    margin:0;
    padding:0;
    transform:none;
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
    width:25px;
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

/* ===========================
   PROFILE DROPDOWN
=========================== */

.navbar-nav{
    height:100%;
}

.navbar-nav .nav-item{
    position:relative;
    display:flex;
    align-items:center;
}

.navbar-nav .nav-link{
    height:100%;
    display:flex;
    align-items:center;
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
    max-width:1000px;
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
   MESSAGES PAGE
=========================== */

.message-list{
    max-width:700px;
    margin:0 auto;
}

.message-card{
    display:flex;
    justify-content:space-between;
    align-items:center;

    gap:15px;

    background:#fff;

    border-radius:12px;

    padding:15px 18px;

    margin-bottom:15px;

    text-decoration:none;

    color:#333;

    box-shadow:0 4px 12px rgba(0,0,0,.15);

    transition:.25s ease;
}

.message-card:hover{
    transform:translateY(-2px);
    color:#184D27;
}

.message-info{
    flex:1;
    min-width:0;
}

.message-title{
    font-size:14px;
    font-weight:700;
    color:#222;
    margin-bottom:4px;
}

.message-text{
    font-size:14px;
    color:#555;

    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.message-card i{
    font-size:18px;
    color:#444;
    flex-shrink:0;
}

/* ===========================
   TABLET
=========================== */

@media(max-width:991px){

    .message-list{
        max-width:100%;
    }

}

/* ===========================
   MOBILE
=========================== */

@media(max-width:576px){


    .message-card{

        padding:12px 14px;

        border-radius:10px;

        margin-bottom:12px;
    }

    .message-title{

        font-size:13px;

    }

    .message-text{

        font-size:12px;

    }

    .message-card i{

        font-size:16px;

    }


}
/* ===========================
   CHAT BUTTON
=========================== */

.chat-btn{
    position:fixed;
    right:30px;
    bottom:95px;

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
}

.chat-btn i{
    font-size:28px;
    color:#555;
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
   MESSAGES PAGE
=========================== */

.message-list{
    max-width:700px;
    margin:0 auto;
}

.message-card{
    display:flex;
    justify-content:space-between;
    align-items:center;

    gap:15px;

    background:#fff;

    border-radius:12px;

    padding:15px 18px;

    margin-bottom:15px;

    text-decoration:none;

    color:#333;

    box-shadow:0 4px 12px rgba(0,0,0,.15);

    transition:.25s ease;
}

.message-card:hover{
    transform:translateY(-2px);
    color:#184D27;
}

.message-info{
    flex:1;
    min-width:0;
}

.message-title{
    font-size:14px;
    font-weight:700;
    color:#222;
    margin-bottom:4px;
}

.message-text{
    font-size:14px;
    color:#555;

    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.message-card i{
    font-size:18px;
    color:#444;
    flex-shrink:0;
}

.chat-body{

    background:#f5f5f5;
    height:400px;
    overflow-y:auto;
    padding:20px;

}

.message{

    display:flex;
    margin-bottom:15px;

}

.message.left{

    justify-content:flex-start;

}

.message.right{

    justify-content:flex-end;

}

.bubble{

    max-width:70%;
    padding:12px 18px;
    border-radius:18px;
    font-size:14px;

}

.left .bubble{

    background:#fff;
    border:1px solid #ddd;

}

.right .bubble{

    background:#1e5631;
    color:#fff;

}

.chat-footer{

    display:flex;
    gap:10px;

}

.chat-footer .form-control{

    height:45px;

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

  .message-list{
        max-width:100%;
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

    .nav-item{
        font-size:10px;
    }

    .nav-item i{
        font-size:22px;
    }

    .nav-item img{
        width:28px;
    }
 .message-card{

        padding:12px 14px;

        border-radius:10px;

        margin-bottom:12px;
    }

    .message-title{

        font-size:13px;

    }

    .message-text{

        font-size:12px;

    }

    .message-card i{

        font-size:16px;

    }
}
</style>
</head>
<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-dark fixed-top">
    <div class="container-fluid">

        <!-- Logo -->
        <a class="navbar-brand navbar-logo" href="#">
            <img src="logo.png" alt="EnviroManage Logo">
        </a>

        <!-- Profile -->
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">

                <a class="nav-link text-white" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-4"></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-person me-2"></i>
                            Profile
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Logout
                        </a>
                    </li>
                </ul>

            </li>
        </ul>

    </div>
</nav>



<!-- ================= PAGE WRAPPER ================= -->

<div class="page-wrapper">

    <!-- ================= SIDEBAR ================= -->

    <aside class="sidebar">

       
    <div class="sidebar-menu">

        <!-- Home -->
        <a href="collector-home.php" class="sidebar-item">
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

    <!-- ================= MAIN CONTENT ================= -->

    <main class="main-content">

        <div class="container">

            <div class="text-center mb-4">

                <h2 class="fw-bold text-success">
                    MESSAGES
                </h2>

            </div>

            <div class="message-list">

                <a href="#" class="message-card">

                    <div class="message-info">

                        <div class="message-title">
                            MENRO
                        </div>

                        <div class="message-text">
                            Scheduled pickup booking added to the collection queue.
                        </div>

                    </div>

                    <i class="bi bi-chevron-right"></i>

                </a>

                <a href="#" class="message-card">

                    <div class="message-info">

                        <div class="message-title">
                            MENRO
                        </div>

                        <div class="message-text">
                            Collection route is now active. Please prepare your waste before arrival.
                        </div>

                    </div>

                    <i class="bi bi-chevron-right"></i>

                </a>

                <a href="#" class="message-card">

                    <div class="message-info">

                        <div class="message-title">
                            MENRO
                        </div>

                        <div class="message-text">
                            Your pickup request has been completed successfully.
                        </div>

                    </div>

                    <i class="bi bi-chevron-right"></i>

                </a>

                <a href="#" class="message-card">

                    <div class="message-info">

                        <div class="message-title">
                            MENRO
                        </div>

                        <div class="message-text">
                            Reminder: Separate biodegradable and non-biodegradable waste before collection.
                        </div>

                    </div>

                    <i class="bi bi-chevron-right"></i>

                </a>

            </div>

        </div>

    </main>

</div>

<!-- ================= CHAT BUTTON ================= -->

<button class="chat-btn">

    <i class="bi bi-chat-dots"></i>

    <span class="badge" id="messageBadge">0</span>

</button>
<!-- Bottom Navigation -->
<nav class="bottom-nav">

    <a href="collector-home.php" class="nav-item">
        <i class="bi bi-house-fill"></i>
        <span>HOME</span>
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
<!-- ================= CHAT MODAL ================= -->

<div class="modal fade" id="chatModal" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <!-- Header -->

            <div class="modal-header bg-success text-white">

                <h5 class="modal-title">
                    <i class="bi bi-person-circle me-2"></i>
                    MENRO
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <!-- Body -->

            <div class="modal-body chat-body" id="chatBody">

                <div class="message left">
                    <div class="bubble">
                        Good morning Collector. Please proceed to Brgy. Sampaloc 1.
                    </div>
                </div>

                <div class="message right">
                    <div class="bubble">
                        Copy. I'm on my way.
                    </div>
                </div>

                <div class="message left">
                    <div class="bubble">
                        Kindly prioritize pickup requests before 10:00 AM.
                    </div>
                </div>

            </div>

            <!-- Footer -->

            <div class="modal-footer chat-footer">

                <input
                    type="text"
                    class="form-control"
                    id="messageInput"
                    placeholder="Type your message...">

                <button
                    class="btn btn-success"
                    id="sendBtn">

                    <i class="bi bi-send-fill"></i>

                </button>

            </div>

        </div>

    </div>

</div>
<!-- Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>


const sendBtn=document.getElementById("sendBtn");
const input=document.getElementById("messageInput");
const chatBody=document.getElementById("chatBody");

sendBtn.onclick=sendMessage;

input.addEventListener("keypress",function(e){

    if(e.key==="Enter"){

        sendMessage();

    }

});

function sendMessage(){

    const text=input.value.trim();

    if(text==="") return;

    const msg=document.createElement("div");

    msg.className="message right";

    msg.innerHTML=`
        <div class="bubble">${text}</div>
    `;

    chatBody.appendChild(msg);

    input.value="";

    chatBody.scrollTop=chatBody.scrollHeight;

}
// ===============================
// MESSAGE CARD CLICK
// ===============================
const chatModal = new bootstrap.Modal(document.getElementById("chatModal"));

chatModal.show();

// ===============================
// CHAT BUTTON
// ===============================

const chatBtn = document.querySelector(".chat-btn");

if (chatBtn) {

    chatBtn.addEventListener("click", function () {

        console.log("Already on Messages page.");

    });

}

// ===============================
// PROFILE DROPDOWN
// ===============================

const dropdownItems = document.querySelectorAll(".dropdown-item");

dropdownItems.forEach(item => {

    item.addEventListener("click", function () {

        console.log("Menu:", this.innerText.trim());

    });

});


// ===============================
// PAGE ANIMATION
// ===============================

window.addEventListener("load", function () {

    const cards = document.querySelectorAll(".message-card");

    cards.forEach((card, index) => {

        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";

        setTimeout(() => {

            card.style.transition = "all .35s ease";

            card.style.opacity = "1";
            card.style.transform = "translateY(0)";

        }, index * 120);

    });

});
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

// Sample lang ito:
// After 5 seconds, may bagong message
setTimeout(() => {
    receiveNewMessage();
}, 5000);
</script>

</body>
</html>