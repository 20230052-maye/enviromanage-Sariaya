<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Collector Route Map</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

/* ===========================
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
.bottom-nav .nav-item img{
    width:32px;
    height:32px;
    margin-bottom:4px;
    object-fit:contain;
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
}

.container{
    max-width:1200px;
}

.page-title{
    color:#6b7d34;
    font-weight:800;
    margin-bottom:25px;
}

/* ===========================
   ROUTE MAP CARD
=========================== */

.map-card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.map-container{
    width:100%;
    height:500px;
    background:#e9ecef;
    border:2px dashed #c9c9c9;
    display:flex;
    justify-content:center;
    align-items:center;
    color:#6c757d;
    font-size:22px;
    font-weight:600;
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
    display:none;

    position:fixed;
    left:0;
    bottom:0;
    width:100%;
    height:75px;

    background:#184D27;

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
   RESPONSIVE
=========================== */

@media(max-width:991px){

.navbar .container-fluid{

    position:relative;
}



    .navbar-brand img{
        height:38px;
    }
.chat-btn{
    right:20px;
    bottom:95px;
}
    .sidebar{
        display:none;
    }

    .page-wrapper{
        display:block;
    }

    .main-content{
        padding:25px 15px 120px;
    }

    .bottom-nav{
        display:flex;
    }
.bottom-nav .nav-item img{
    width:32px;
    height:32px;
    margin-bottom:4px;
    object-fit:contain;
}


    .map-container{
        height:70vh;
    }

}
/* Phones only */
@media (max-width: 767px){

    .sidebar{
        display:none;
    }

    .page-wrapper{
        display:block;
    }

    .main-content{
        padding:25px 15px 120px;
    }

    .bottom-nav{
        display:flex;
    }

}
/* Tablets */
@media (min-width:768px) and (max-width:991px){

    .bottom-nav{
        display:none !important;
    }

}
@media(max-width:576px){

.chat-btn{
    width:52px;
    height:52px;

    right:15px;
    bottom:90px;
}
    .page-title{
        font-size:28px;
    }

    .map-container{
        height:65vh;
        font-size:18px;
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

    display:flex;
    align-items:center;
     justify-content:space-between;
    gap:10px;
    touch-action: none;

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
</style>
</head>

<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-dark fixed-top">
    <div class="container-fluid">

        <!-- Logo -->
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


<div class="page-wrapper">

<aside class="sidebar">

    <div class="sidebar-menu">

        <a href="collector-home.php" class="sidebar-item">
            <i class="bi bi-house-fill"></i>
            <span>Home</span>
        </a>

        <!-- Route Map -->
        <a href="collector-route-map.php" class="sidebar-item active ">
            <img src="assets/location.png" alt="Route Map">
            <span>Route Map</span>
        </a>

        <a href="collector-profile.php" class="sidebar-item">
            <i class="bi bi-person-fill"></i>
            <span>Profile</span>
        </a>

    </div>

</aside>

<main class="main-content">
    <div class="container">

    <!-- ================= PAGE TITLE ================= -->

  <div class="text-center mb-4">

                <h2 class="fw-bold text-success">
                    ROUTE MAP
                </h2>
      
    </div>

    <!-- ================= MAP CARD ================= -->

    <div class="card map-card">

        <div class="card-body p-2">

            <!-- Placeholder muna ito.
                 Papalitan natin ng Leaflet map sa susunod. -->

            <div class="map-container">

                <div class="text-center">

                    <i class="bi bi-map-fill display-1 text-secondary mb-3"></i>

                    <h4 class="fw-bold text-secondary">
                        Route Map
                    </h4>

                    <p class="text-muted mb-0">
                        Leaflet Map Placeholder
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

</main>

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
<!-- Bottom Navigation -->
<nav class="bottom-nav">

    <a href="collector-home.php" class="nav-item">
        <i class="bi bi-house-fill"></i>
        <span>Home</span>
    </a>

    <a href="collector-route-map.php" class="nav-item active">
        <img src="assets/location.png" alt="">
        <span>Route Map</span>
    </a>

    <a href="collector-profile.php" class="nav-item">
        <i class="bi bi-person-fill"></i>
        <span>Profile</span>
    </a>

</nav>
</main>

<!-- ================= BOOTSTRAP JS ================= -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

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


    // MOBILE POSITION
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

}
else{

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

chatHeaders.forEach(header => {

    header.addEventListener("pointerdown", function(e){

        // Huwag mag-drag kapag button ang pinindot
        if(e.target.closest("button")){
            return;
        }

        chatDragging = true;

        const rect = chatWindow.getBoundingClientRect();

chatOffsetX = e.clientX - rect.left;
chatOffsetY = e.clientY - rect.top;
        header.setPointerCapture(e.pointerId);

    });

    header.addEventListener("pointerup", function(){

    chatDragging = false;

});
header.addEventListener("pointermove", function(e){

    if(!chatDragging) return;

    let left = e.clientX - chatOffsetX;
    let top = e.clientY - chatOffsetY;

    // Limit sa loob ng screen
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
header.addEventListener("pointercancel", function(){

    chatDragging = false;

});

});

// ===============================
// LEAFLET PLACEHOLDER
// ===============================

// Kapag ready ka na sa Leaflet,
// palitan lang natin ang .map-container
// ng:
// <div id="map"></div>
//
// Tapos dito ilalagay ang Leaflet initialization.

</script>

</body>
</html>

