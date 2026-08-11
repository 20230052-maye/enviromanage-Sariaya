<?php
session_start();

if (!isset($_SESSION['verified_reset'])) {
    header("Location: forgot-password.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Password</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<!-- FontAwesome for eye icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body, html { margin:0; padding:0; font-family:'Roboto',sans-serif; height:100%; }

/* Desktop Layout */
.container-desktop { display:none; height:100vh; }
.left-panel { background-color:#285e33; display:flex; justify-content:center; align-items:center; }
.left-panel img { width:300px; height:300px; }
.right-panel { display:flex; justify-content:center; align-items:center; background-color:#ffffff; }
.card-box { width:100%; max-width:400px; padding:30px; border-radius:15px; box-shadow:0 4px 12px rgba(0,0,0,0.15); text-align:center; }
.card-box h2 { color:#1D4525; margin-bottom:20px; }

input.form-control { margin-bottom:15px; border-radius:8px; padding-right:40px; }
.password-wrapper { position:relative; width:100%; margin-bottom:15px; }
.password-wrapper .toggle-password { position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer; color:#6c757d; z-index:2; }

.password-strength { height: 6px; width: 100%; background: #ddd; border-radius: 4px; margin-bottom:10px; }
.password-strength span { display:block; height:100%; width:0%; border-radius:4px; transition:0.3s; }
.requirements { font-size:12px; margin-bottom:15px; text-align:left; }
.requirements span { display:block; }
.valid { color:green; }
.invalid { color:red; }

.btn-submit { width:100%; background-color:#1D4525; color:white; border-radius:8px; }
.btn-submit:hover { background-color:#163519; }

.container-mobile { display:flex; flex-direction:column; align-items:center; justify-content:flex-start; min-height:100vh; background-color:#ffffff; }
.mobile-logo-bg { background-color:#1D4525; width:120%; height:280px; border-bottom-left-radius:80% 100%; border-bottom-right-radius:80% 100%; display:flex; justify-content:center; align-items:center; }
.mobile-logo-bg img { width:200px; height:200px; }
.mobile-card { background-color:#ffffff; width:90%; max-width:400px; margin-top:100px; padding:30px 20px; border-radius:15px; box-shadow:0 4px 12px rgba(0,0,0,0.15); text-align:center; }

@media (min-width: 992px){
  .container-desktop{display:flex;}
  .container-mobile{display:none;}
  .container-desktop .left-panel{width:50%;}
  .container-desktop .right-panel{width:50%;}
}
</style>
</head>
<body>

<!-- Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header">
        <h5 class="modal-title">Message</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body"><p id="modalMessage"></p></div>
    </div>
  </div>
</div>

<!-- Desktop -->
<div class="container-desktop">
  <div class="left-panel"><img src="assets/enviromanage-logo-512.png" alt="EnviroManage Logo"></div>
  <div class="right-panel">
    <div class="card-box">
      <h2>New Password</h2>
      <form id="resetFormDesktop">
        <div class="password-wrapper">
          <input type="password" name="password" id="passwordDesktop" class="form-control" placeholder="Enter New Password" required>
          <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePassword('passwordDesktop', this)"></i>
        </div>
        <div class="password-strength"><span id="strengthBarDesktop"></span></div>
        <div class="requirements">
          <span id="lengthDesktop" class="invalid">• At least 8 characters</span>
          <span id="uppercaseDesktop" class="invalid">• At least 1 uppercase letter</span>
          <span id="lowercaseDesktop" class="invalid">• At least 1 lowercase letter</span>
          <span id="numberDesktop" class="invalid">• At least 1 number</span>
        </div>

        <div class="password-wrapper">
          <input type="password" name="confirm_password" id="confirmPasswordDesktop" class="form-control" placeholder="Confirm Password" required>
          <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePassword('confirmPasswordDesktop', this)"></i>
        </div>
        <span id="matchMessageDesktop" class="invalid"></span>

        <button type="submit" class="btn btn-submit mt-3">Submit</button>
      </form>
    </div>
  </div>
</div>

<!-- Mobile -->
<div class="container-mobile">
  <div class="mobile-logo-bg"><img src="assets/enviromanage-logo-512.png" alt="EnviroManage Logo"></div>
  <div class="mobile-card">
    <h2>New Password</h2>
    <form id="resetFormMobile">
      <div class="password-wrapper">
        <input type="password" name="password" id="passwordMobile" class="form-control" placeholder="Enter New Password" required>
        <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePassword('passwordMobile', this)"></i>
      </div>
      <div class="password-strength"><span id="strengthBarMobile"></span></div>
      <div class="requirements">
        <span id="lengthMobile" class="invalid">• At least 8 characters</span>
        <span id="uppercaseMobile" class="invalid">• At least 1 uppercase letter</span>
        <span id="lowercaseMobile" class="invalid">• At least 1 lowercase letter</span>
        <span id="numberMobile" class="invalid">• At least 1 number</span>
      </div>

      <div class="password-wrapper">
        <input type="password" name="confirm_password" id="confirmPasswordMobile" class="form-control" placeholder="Confirm Password" required>
        <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePassword('confirmPasswordMobile', this)"></i>
      </div>
      <span id="matchMessageMobile" class="invalid"></span>

      <button type="submit" class="btn btn-submit mt-3">Submit</button>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toggle password visibility
function togglePassword(inputId, icon) {
    const passInput = document.getElementById(inputId);
    if(passInput.type === "password") {
        passInput.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    } else {
        passInput.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
}

// Password validation
function setupPasswordValidation(passInput, confirmInput, strengthBar, lengthEl, upperEl, lowerEl, numberEl, matchMsg){
    passInput.addEventListener("input", ()=> {
        let value = passInput.value;
        let strength = 0;
        if(value.length >= 8){ lengthEl.classList.replace("invalid","valid"); strength++; } else lengthEl.classList.replace("valid","invalid");
        if(/[A-Z]/.test(value)){ upperEl.classList.replace("invalid","valid"); strength++; } else upperEl.classList.replace("valid","invalid");
        if(/[a-z]/.test(value)){ lowerEl.classList.replace("invalid","valid"); strength++; } else lowerEl.classList.replace("valid","invalid");
        if(/[0-9]/.test(value)){ numberEl.classList.replace("invalid","valid"); strength++; } else numberEl.classList.replace("valid","invalid");

        strengthBar.style.width = (strength*25)+"%";
        strengthBar.style.background = strength===1?"red":strength===2?"orange":strength===3?"yellowgreen":"green";
    });

    confirmInput.addEventListener("input", ()=> {
        if(confirmInput.value === passInput.value){
            matchMsg.textContent = "Passwords match";
            matchMsg.classList.replace("invalid","valid");
        } else {
            matchMsg.textContent = "Passwords do not match";
            matchMsg.classList.replace("valid","invalid");
        }
    });
}

// Modal helper
function showModal(message){
    document.getElementById('modalMessage').innerText = message;
    new bootstrap.Modal(document.getElementById('messageModal')).show();
}

// Form submit handler
function setupForm(formId){
    const form = document.getElementById(formId);
    form.addEventListener("submit", e=>{
        e.preventDefault();
        const password = form.querySelector('input[name="password"]').value;
        const confirm = form.querySelector('input[name="confirm_password"]').value;

        if(password !== confirm){ showModal("Passwords do not match."); return; }
        if(password.length < 8){ showModal("Password must be at least 8 characters."); return; }

        const data = new FormData(form);
        fetch('reset-password-handler.php',{method:'POST',body:data})
        .then(res=>res.json())
        .then(json=>{
            // Show new password vs old password message if error
            if(json.error){
                showModal(json.error);
            } else if(json.success){
                showModal(json.success);
                document.getElementById('messageModal')
                .addEventListener('hidden.bs.modal', ()=>{ window.location.href='login.php'; }, {once:true});
            }
        })
        .catch(err=>{ showModal("An error occurred. Please try again."); console.error(err); });
    });
}

// Desktop
setupPasswordValidation(
    document.getElementById("passwordDesktop"),
    document.getElementById("confirmPasswordDesktop"),
    document.getElementById("strengthBarDesktop"),
    document.getElementById("lengthDesktop"),
    document.getElementById("uppercaseDesktop"),
    document.getElementById("lowercaseDesktop"),
    document.getElementById("numberDesktop"),
    document.getElementById("matchMessageDesktop")
);
setupForm("resetFormDesktop");

// Mobile
setupPasswordValidation(
    document.getElementById("passwordMobile"),
    document.getElementById("confirmPasswordMobile"),
    document.getElementById("strengthBarMobile"),
    document.getElementById("lengthMobile"),
    document.getElementById("uppercaseMobile"),
    document.getElementById("lowercaseMobile"),
    document.getElementById("numberMobile"),
    document.getElementById("matchMessageMobile")
);
setupForm("resetFormMobile");
</script>
</body>
</html>