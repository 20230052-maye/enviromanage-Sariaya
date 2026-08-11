<?php
session_start();
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify Code</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

<style>
body, html { margin:0; padding:0; font-family:'Roboto',sans-serif; height:100%;overflow-x: hidden; }
.container-desktop { display:none; height:100vh; }
.left-panel { background-color:#285e33; display:flex; justify-content:center; align-items:center; }
.left-panel img { width:300px; height:300px; }
.right-panel { display:flex; justify-content:center; align-items:center; background-color:#ffffff; }
.card-box { width:100%; max-width:400px; padding:30px; border-radius:15px; box-shadow:0 4px 12px rgba(0,0,0,0.15); text-align:center; }
.card-box h2 { color:#1D4525; margin-bottom:20px; }

.code-inputs { display:flex; justify-content:space-between; gap:10px; margin-bottom:20px; }
.code-inputs input {
  width:50px;
  height:55px;
  text-align:center;
  font-size:22px;
  border-radius:8px;
  border:1px solid #ced4da;
}

.btn-verify { width:100%; background-color:#1D4525; color:white; border-radius:8px; }
.btn-verify:hover { background-color:#163519; }

.resend-btn { color:#1D4525; text-decoration:none; }
.resend-btn:disabled { color:#999; pointer-events:none; }

.container-mobile { display:flex; flex-direction:column; align-items:center; justify-content:flex-start; min-height:100vh; background-color:#ffffff; }
.mobile-logo-bg { background-color:#1D4525; width:120%; height:280px; border-bottom-left-radius:80% 100%; border-bottom-right-radius:80% 100%; display:flex; justify-content:center; align-items:center; }
.mobile-logo-bg img { width:200px; height:200px; }
.mobile-card { background-color:#ffffff; width:90%; max-width:400px; margin-top:100px; padding:30px 20px; border-radius:15px; box-shadow:0 4px 12px rgba(0,0,0,0.15); text-align:center; }

@media (min-width:992px){
  .container-desktop{display:flex;}
  .container-mobile{display:none;}
  .container-desktop .left-panel{width:50%;}
  .container-desktop .right-panel{width:50%;}
}
@media (max-width: 576px) {
  .mobile-card .code-inputs {
    gap: 5px; /* reduce spacing between inputs */
  }

  .mobile-card .code-inputs input {
    width: 40px;   /* smaller width for mobile */
    height: 45px;  /* adjust height proportionally */
    font-size: 18px; /* slightly smaller font */
  }
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
  <div class="left-panel"><img src="assets/enviromanage-logo-512.png"></div>
  <div class="right-panel">
    <div class="card-box">
      <h2>Enter Verification Code</h2>
      <form id="verifyDesktopForm">
        <input type="hidden" name="email" value="<?php echo $email; ?>">
        <div class="code-inputs">
          <input type="text" maxlength="1" class="code-box">
          <input type="text" maxlength="1" class="code-box">
          <input type="text" maxlength="1" class="code-box">
          <input type="text" maxlength="1" class="code-box">
          <input type="text" maxlength="1" class="code-box">
          <input type="text" maxlength="1" class="code-box">
        </div>
        <input type="hidden" name="code" class="full-code">
        <button type="submit" class="btn btn-verify mb-3">VERIFY</button>
        <div>
          If you didn’t receive a code,
          <button type="button" class="btn btn-link p-0 resend-btn">Resend</button>
          <span class="countdown text-muted"></span>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Mobile -->
<div class="container-mobile">
  <div class="mobile-logo-bg"><img src="assets/enviromanage-logo-512.png"></div>
  <div class="mobile-card">
    <h2>Enter Verification Code</h2>
    <form id="verifyMobileForm">
      <input type="hidden" name="email" value="<?php echo $email; ?>">
      <div class="code-inputs">
        <input type="text" maxlength="1" class="code-box">
        <input type="text" maxlength="1" class="code-box">
        <input type="text" maxlength="1" class="code-box">
        <input type="text" maxlength="1" class="code-box">
        <input type="text" maxlength="1" class="code-box">
        <input type="text" maxlength="1" class="code-box">
      </div>
      <input type="hidden" name="code" class="full-code">
      <button type="submit" class="btn btn-verify mb-3">VERIFY</button>
      <div>
        If you didn’t receive a code,
        <button type="button" class="btn btn-link p-0 resend-btn">Resend</button>
        <span class="countdown text-muted"></span>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelectorAll('.code-box').forEach((input, index, arr) => {
  input.addEventListener('input', () => {
    input.value = input.value.replace(/[^0-9]/g,'');
    if (input.value && index < arr.length - 1) arr[index + 1].focus();
    updateFullCode(input.closest('form'));
  });
  input.addEventListener('keydown', (e) => {
    if (e.key === "Backspace" && !input.value && index > 0) arr[index - 1].focus();
  });
});

function updateFullCode(form){
  const code = Array.from(form.querySelectorAll('.code-box')).map(i => i.value).join('');
  form.querySelector('.full-code').value = code;
}

function showModal(message){
  document.getElementById('modalMessage').innerText = message;
  new bootstrap.Modal(document.getElementById('messageModal')).show();
}

function ajaxVerify(formId){
  const form = document.getElementById(formId);
  const resendBtn = form.querySelector('.resend-btn');

  form.addEventListener('submit', e=>{
    e.preventDefault();
    const code = form.querySelector('.full-code').value;
    if(code.length !== 6){
      showModal("Please enter the 6-digit code.");
      return;
    }

    const data = new FormData(form);

    fetch('verify-code-handler.php', { method:'POST', body:data })
      .then(res=>res.json())
      .then(json=>{
        showModal(json.error || json.success);

        if(json.success){
          // Disable Resend button and inputs after successful verification
          resendBtn.disabled = true;
          form.querySelectorAll('.code-box').forEach(i => i.disabled = true);

          document.getElementById('messageModal')
            .addEventListener('hidden.bs.modal', ()=>{
              window.location.href = 'reset-password.php?email=' +
                encodeURIComponent(form.querySelector('input[name="email"]').value);
            }, {once:true});
        }
      })
      .catch(err=>{
        showModal("An error occurred. Please try again.");
        console.error(err);
      });
  });
}

ajaxVerify('verifyDesktopForm');
ajaxVerify('verifyMobileForm');

let resendCooldown = 60;
function startCountdown(form){
  const btn = form.querySelector('.resend-btn');
  const countdown = form.querySelector('.countdown');
  btn.disabled = true;
  countdown.textContent = ` (${resendCooldown}s)`;
  const timer = setInterval(()=>{
    resendCooldown--;
    countdown.textContent = ` (${resendCooldown}s)`;
    if(resendCooldown <= 0){
      clearInterval(timer);
      btn.disabled = false;
      countdown.textContent = '';
      resendCooldown = 60;
    }
  },1000);
}

document.querySelectorAll('form').forEach(form=>{
  const resendBtn = form.querySelector('.resend-btn');
  resendBtn.addEventListener('click', ()=>{
    const email = form.querySelector('input[name="email"]').value;

    fetch('resend-otp-handler.php', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:'email=' + encodeURIComponent(email)
    })
    .then(res=>res.json())
    .then(json=>{
      showModal(json.error || json.success);
      if(json.success) startCountdown(form);
    })
    .catch(err=>{
      showModal("An error occurred while resending the code.");
      console.error(err);
    });
  });
});
</script>
</body>
</html>