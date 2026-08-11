<?php
session_start();
include "db.php";

// Handle AJAX form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    header('Content-Type: application/json; charset=utf-8');

    // Collect inputs
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

   $first_name = ucwords(strtolower(trim($_POST["first_name"])));
$middle_initial = strtoupper(trim($_POST["middle_initial"]));
$last_name = ucwords(strtolower(trim($_POST["last_name"])));
    $gender = $_POST["gender"];
    $birthdate = $_POST["birthdate"];
    $phone = trim($_POST["phone"]);
    $barangay = trim($_POST["barangay"]);
$street = trim($_POST["street"]);
$house_no = trim($_POST["house_no"]);
$postal_code = trim($_POST["postal_code"]);

    $error = "";
$success = "";


/* ==========================
   AGE VALIDATION (18 ABOVE)
========================== */

$birthDateObj = new DateTime($birthdate);
$today = new DateTime();

$age = $today->diff($birthDateObj)->y;

if($age < 18){
    $error = "You must be 18 years old and above to register.";
}


/* ==========================
   PHONE VALIDATION
========================== */

if(!preg_match('/^9[0-9]{9}$/', $phone)){
    $error = "Phone number must be exactly 10 digits and must start with 9.";
}


/* ==========================
   HOUSE NUMBER VALIDATION
========================== */

if(!preg_match('/^[0-9]{5}$/', $house_no)){
    $error = "House number must be exactly 5 digits and numbers only.";
}
    // Password validation
    $passwordPattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/";

    if (!preg_match($passwordPattern, $password)) {
        $error = "Password must be at least 8 characters and include uppercase, lowercase, and number.";
    } 
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } 
    elseif (!isset($_POST['terms'])) {
        $error = "You must accept Terms & Conditions.";
    } 
    else {
        // Check duplicate email OR username
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();

       if ($result->num_rows > 0) {
    $error = "Email or username already exists.";
} else {

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if (!isset($_FILES['valid_id']) || $_FILES['valid_id']['error'] !== UPLOAD_ERR_OK) {
    $error = "Please upload a valid ID.";
}
    // Upload Valid ID
    $uploadDir = "uploads/valid_ids/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $extension = strtolower(pathinfo($_FILES["valid_id"]["name"], PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png'];

if (!in_array($extension, $allowed)) {
    $error = "Only JPG, JPEG and PNG files are allowed.";
}

    $filename = uniqid("id_") . "." . $extension;

    $destination = $uploadDir . $filename;

    if (!move_uploaded_file($_FILES["valid_id"]["tmp_name"], $destination)) {
    $error = "Failed to upload Valid ID.";
}

    // Insert user
    $stmt = $conn->prepare("
        INSERT INTO users
        (email, username, password, first_name, middle_initial, last_name,
        gender, birthdate, phone, street, house_no, barangay,
        postal_code, valid_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

           $stmt->bind_param(
    "ssssssssssssss",
    $email,
    $username,
    $hashedPassword,
    $first_name,
    $middle_initial,
    $last_name,
    $gender,
    $birthdate,
    $phone,
    $street,
    $house_no,
    $barangay,
    $postal_code,
    $destination
);

            if ($stmt->execute()) {
                $success = "Account created successfully! You can now login.";
            } else {
                $error = "Database error: " . $stmt->error;
            }
        }
    }

    echo json_encode([
        'error' => $error,
        'success' => $success
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EnviroManage Sign Up</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body, html { margin:0; padding:0; font-family:'Roboto',sans-serif; height:100%;overflow-x: hidden; }
.container-desktop { display:none; height:100vh; }
.left-panel { background-color:#285e33; color:white; display:flex; justify-content:center; align-items:center; }
.left-panel img { width:300px; height:300px; }
.right-panel { display:flex; justify-content:center; align-items:center; background-color:#ffffff; }
.card-box { width:100%; max-width:600px; padding:30px; border-radius:15px; box-shadow:0 4px 12px rgba(0,0,0,0.15); text-align:center; }
.card-box h2 { color:#1D4525; margin-bottom:20px; }
.card-box label.gender-label { display:block; text-align:left; margin-bottom:5px; font-weight:500; }
.password-wrapper { position:relative; width:100%; margin-bottom:15px; }
.password-wrapper input { width:100%; border-radius:8px; padding-right:40px; }
.password-wrapper .toggle-password { position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer; color:#6c757d; z-index:2; }
.card-box .form-control { margin-bottom:15px; border-radius:8px; }
.card-box .btn-signup { width:100%; background-color:#1D4525; color:white; border-radius:8px; }
.card-box .btn-signup:hover { background-color:#163519; }
.strength-bar-container { width:100%; height:8px; background:#e0e0e0; border-radius:4px; margin-top:-10px; margin-bottom:15px; overflow:hidden; }
.strength-bar-fill { height:100%; width:0; background:red; transition: width 0.3s, background 0.3s; border-radius:4px; }
.requirements-list { text-align:left; font-size:13px; margin-bottom:15px; color:#6c757d; }
.requirements-list li { margin-bottom:3px; }

/* Suggestion Dropdown */
.address-wrapper{
    position:relative;
}

.suggestion-box{
    position:absolute;
    top:100%;
    left:0;
    width:100%;
    background:#fff;
    border:1px solid #ced4da;
    border-top:none;
    border-radius:0 0 8px 8px;
    max-height:180px;
    overflow-y:auto;
    z-index:9999;
    display:none;
}

.suggestion-item{
    padding:10px;
    cursor:pointer;
    text-align:left;
}

.suggestion-item:hover{
    background:#f1f1f1;
}

.container-mobile { display:flex; flex-direction:column; align-items:center; justify-content:flex-start; min-height:100vh; background-color:#ffffff; }
@keyframes dropSemiCircle { 0%{transform:translateY(-200px);opacity:0;}100%{transform:translateY(0);opacity:1;} }
@keyframes fadeInLogo {0%{opacity:0;transform:scale(0.8);}100%{opacity:1;transform:scale(1);} }
.mobile-logo-bg { background-color:#1D4525; width:120%; height:280px; border-bottom-left-radius:80% 100%; border-bottom-right-radius:80% 100%; display:flex; justify-content:center; align-items:center; opacity:0; transform:translateY(-200px); animation:dropSemiCircle 0.7s forwards; }
.mobile-logo-bg img { width:200px; height:200px; opacity:0; transform:scale(0.8); animation:fadeInLogo 0.7s forwards; animation-delay:0.5s; }
.mobile-card { background-color:#ffffff; width:90%; max-width:500px; margin-top:100px; padding:30px 20px; border-radius:15px; box-shadow:0 4px 12px rgba(0,0,0,0.15); text-align:center; display:flex; flex-direction:column; opacity:0; animation:fadeInLogo 0.7s forwards; animation-delay:0.8s; }
.mobile-card label.gender-label { display:block; text-align:left; margin-bottom:5px; font-weight:500; }
.mobile-card .form-control { width:100%; border-radius:8px; margin-bottom:20px; }
.mobile-card .password-wrapper { margin-bottom:20px; }
.mobile-card .btn-signup { width:100%; background-color:#1D4525; color:white; border-radius:8px; margin-top:10px; }
.mobile-card .btn-signup:hover { background-color:#163519; }

.input-group-text, .input-group .form-control { border-radius:8px; }
.input-group .form-control { flex:1 1 auto; }

/* MOBILE DATE PLACEHOLDER */
.mobile-card input[type="date"]:invalid::-webkit-datetime-edit {
  color: #6c757d;
}

.mobile-card input[type="date"]:valid::-webkit-datetime-edit {
  color: #212529;
}

@media (min-width:992px){ .container-desktop{display:flex;} .container-mobile{display:none;} .container-desktop .left-panel{width:50%;} .container-desktop .right-panel{width:50%;} }
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
  <div class="left-panel"><img src="assets/enviromanage-logo-512.png" alt="Logo"></div>
  <div class="right-panel">
    <div class="card-box">
      <h2>Sign Up</h2>

      <form id="signupDesktopForm" enctype="multipart/form-data">
        <!-- Name Fields -->
        <div class="d-flex gap-2 mb-2">
          <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
          <input type="text" name="middle_initial" class="form-control" placeholder="M.I." maxlength="1">
          <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
        </div>

        <!-- Gender -->
        <div class="row mb-2 align-items-center">
          <label class="col-auto gender-label mb-0">Gender:</label>
          <div class="col d-flex gap-3">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="gender" value="Female" required>
              <label class="form-check-label">Female</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="gender" value="Male" required>
              <label class="form-check-label">Male</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="gender" value="Others" required>
              <label class="form-check-label">Others</label>
            </div>
          </div>
        </div>

      <!-- Phone & Birthdate Row -->
<div class="row mb-2 g-2 align-items-center">
  <div class="col-auto p-0" style="max-width:70px;">
    <input type="text" class="form-control" value="+63" readonly>
  </div>
  <div class="col">
<input 
type="text"
name="phone"
class="form-control phone-input"
placeholder="Phone Number"
maxlength="10"
inputmode="numeric"
pattern="9[0-9]{9}"
required>
  </div>
  <div class="col">
    <input 
type="date" 
name="birthdate" 
class="form-control birthdate-input"
max="<?= date('Y-m-d', strtotime('-18 years')) ?>"
required>
  </div>
</div>

<div class="row mb-2 g-2">

  <!-- Barangay -->
  <div class="col-md-3">
    <div class="address-wrapper">
      <input 
        type="text"
        name="barangay"
        class="form-control barangay-input"
        placeholder="Barangay"
        autocomplete="off"
        required
      >
      <div class="suggestion-box"></div>
    </div>
  </div>

  <!-- Street -->
  <div class="col-md-4">
    <div class="address-wrapper">
     <input 
type="text"
name="street"
class="form-control street-input"
placeholder="Select Barangay first"
autocomplete="off"
required
disabled
>
      <div class="suggestion-box"></div>
    </div>
  </div>

  <!-- House No -->
  <div class="col-md-2">
    <input 
type="text"
name="house_no"
class="form-control house-input"
placeholder="House No."
maxlength="5"
inputmode="numeric"
pattern="[0-9]{5}"
required
    >
  </div>

  <!-- Postal Code -->
  <div class="col-md-3">
    <input 
      type="text"
      name="postal_code"
      class="form-control"
      value="4322"
      readonly
    >
  </div>

</div>

        <!-- Email & Username -->
        <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
        <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>

        <!-- Password Fields -->
        <div class="password-wrapper">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
          <i class="fa-solid fa-eye-slash toggle-password"></i>
        </div>
        <div class="strength-bar-container"><div class="strength-bar-fill"></div></div>
        <ul class="requirements-list">
          <li>At least 8 characters</li>
          <li>Uppercase letter</li>
          <li>Lowercase letter</li>
          <li>Number</li>
        </ul>
        <div class="password-wrapper mb-2">
          <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
          <i class="fa-solid fa-eye-slash toggle-password"></i>
        </div>

        <div class="mb-3 text-start">

    <label class="form-label fw-semibold">Valid ID</label>

    <input
        type="file"
        name="valid_id"
        class="form-control valid-id-input"
        accept="image/*"
        required
    >

    <small class="text-muted">
        Upload a photo of your valid ID.
    </small>

    <div class="mt-3 text-center">
        <img
            class="valid-id-preview d-none img-thumbnail"
            style="max-width:220px;max-height:180px;object-fit:contain;"
        >
    </div>

</div>

        <!-- Terms & Conditions -->
<div class="form-check mb-2 text-start">
  <input class="form-check-input" type="checkbox" name="terms" id="termsCheckbox" required>
  <label class="form-check-label" for="termsCheckbox">
    I agree to the
    <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">
        Terms & Conditions
    </a>
</label>
</div>

        <button type="submit" class="btn btn-signup mb-3">Sign Up</button>
        <div>Already have an account? <a href="login.php">Login</a></div>
      </form>
    </div>
  </div>
</div>

<!-- Mobile -->
<div class="container-mobile">
  <div class="mobile-logo-bg"><img src="assets/enviromanage-logo-512.png" alt="Logo"></div>
  <div class="mobile-card">
    <h2>Sign Up</h2>

    <form id="signupMobileForm" enctype="multipart/form-data">
      <!-- Name Fields -->
      <div class="d-flex gap-2 mb-2">
        <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
        <input type="text" name="middle_initial" class="form-control" placeholder="M.I." maxlength="1">
        <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
      </div>

      <!-- Gender -->
      <div class="row mb-2 align-items-center">
        <label class="col-auto gender-label mb-0">Gender:</label>
        <div class="col d-flex gap-3">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="gender" value="Female" required>
            <label class="form-check-label">Female</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="gender" value="Male" required>
            <label class="form-check-label">Male</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="gender" value="Others" required>
            <label class="form-check-label">Others</label>
          </div>
        </div>
      </div>

      <!-- Phone & Birthdate Row -->
<div class="row mb-2 g-2 align-items-center">
  <div class="col-auto p-0" style="max-width:70px;">
    <input type="text" class="form-control" value="+63" readonly>
  </div>
  <div class="col">
    <input 
type="text"
name="phone"
class="form-control phone-input"
placeholder="Phone Number"
maxlength="10"
inputmode="numeric"
pattern="9[0-9]{9}"
required>
  </div>
 <div class="col">
 <input
type="text"
name="birthdate"
class="form-control birthdate-input"
placeholder="Birthdate"
required
onclick="if(this.type!=='date'){ 
this.type='date'; 
this.max='<?= date('Y-m-d', strtotime('-18 years')) ?>';
this.showPicker(); 
}"
onblur="if(!this.value)this.type='text'"
>
</div>
</div>

   <div class="row mb-2 g-2">

  <!-- Barangay -->
  <div class="col-md-3">
    <div class="address-wrapper">
      <input 
        type="text"
        name="barangay"
        class="form-control barangay-input"
        placeholder="Barangay"
        autocomplete="off"
        required
      >
      <div class="suggestion-box"></div>
    </div>
  </div>

  <!-- Street -->
  <div class="col-md-4">
    <div class="address-wrapper">
    <input 
type="text"
name="street"
class="form-control street-input"
placeholder="Street"
autocomplete="off"
required
disabled
>
      <div class="suggestion-box"></div>
    </div>
  </div>

  <!-- House No -->
  <div class="col-md-2">
    <input 
type="text"
name="house_no"
class="form-control house-input"
placeholder="House No."
maxlength="5"
inputmode="numeric"
pattern="[0-9]{5}"
required>
  </div>

  <!-- Postal Code -->
  <div class="col-md-3">
    <input 
      type="text"
      name="postal_code"
      class="form-control"
      value="4322"
      readonly
    >
  </div>

</div>

      <!-- Email & Username -->
      <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
      <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>

      <!-- Password Fields -->
      <div class="password-wrapper">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <i class="fa-solid fa-eye-slash toggle-password"></i>
      </div>
      <div class="strength-bar-container"><div class="strength-bar-fill"></div></div>
      <ul class="requirements-list">
        <li>At least 8 characters</li>
        <li>Uppercase letter</li>
        <li>Lowercase letter</li>
        <li>Number</li>
      </ul>
      <div class="password-wrapper mb-2">
        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
        <i class="fa-solid fa-eye-slash toggle-password"></i>
      </div>

      <div class="mb-3 text-start">

    <label class="form-label fw-semibold">Valid ID</label>

    <!-- Hidden File Inputs -->
    <input
        type="file"
        id="validIdGallery"
        name="valid_id"
        accept="image/*"
        hidden
    >

    <input
        type="file"
        id="validIdCamera"
        accept="image/*"
        capture="environment"
        hidden
    >

    <!-- Buttons -->
    <div class="d-flex gap-2 mb-2">

        <button
            type="button"
            class="btn btn-outline-primary flex-fill"
            onclick="document.getElementById('validIdCamera').click()">
            <i class="fa-solid fa-camera"></i>
            Take Photo
        </button>

        <button
            type="button"
            class="btn btn-outline-success flex-fill"
            onclick="document.getElementById('validIdGallery').click()">
            <i class="fa-solid fa-folder-open"></i>
            Upload
        </button>

    </div>

    <div id="selectedValidId" class="small text-muted mb-2">
    No file selected.
</div>

<div class="text-center">
    <img
        id="validIdPreviewMobile"
        class="img-thumbnail d-none"
        style="max-width:220px;max-height:180px;object-fit:contain;"
    >
</div>

</div>

     <!-- Terms & Conditions -->
<div class="form-check mb-2 text-start">
  <input class="form-check-input" type="checkbox" name="terms" id="termsCheckbox" required>
  <label class="form-check-label" for="termsCheckbox">
    I agree to the
    <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">
        Terms & Conditions
    </a>
</label>
</div>

      <button type="submit" class="btn btn-signup mb-3">Sign Up</button>
      <div>Already have an account? <a href="login.php">Login</a></div>
    </form>
  </div>
</div>

<!-- Terms & Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fa-solid fa-file-contract me-2"></i>
                    EnviroManage Terms & Conditions
                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body" style="text-align:justify; line-height:1.7;">

                <h6>1. Introduction</h6>

                <p>
                    EnviroManage is a web-based Garbage Collection Management
                    and Tracking System developed for the Municipal Environment
                    and Natural Resources Office (MENRO) of Sariaya, Quezon.
                    The system provides residents with access to garbage
                    collection schedules, announcements, complaint reporting,
                    and other waste management services.
                </p>

                <hr>

                <h6>2. Acceptance of Terms</h6>

                <p>
                    By creating an EnviroManage account, you confirm that you
                    have read, understood, and agreed to these Terms and
                    Conditions. If you do not agree, you should not register or
                    use the system.
                </p>

                <hr>

                <h6>3. Eligibility</h6>

                <p>
                    Registration is intended only for residents of the
                    Municipality of Sariaya, Quezon. Users must provide
                    complete and truthful personal information during
                    registration.
                </p>

                <hr>

                <h6>4. Valid ID Verification</h6>

                <p>
                    A valid government-issued ID is required to verify your
                    identity and residency. Your uploaded ID will only be used
                    for account verification and fraud prevention. It will be
                    accessible only to authorized MENRO personnel and will not
                    be disclosed to unauthorized individuals.
                </p>

                <hr>

                <h6>5. Data Privacy</h6>

                <p>
                    EnviroManage complies with the Data Privacy Act of 2012
                    (Republic Act No. 10173). Personal information collected
                    through the system will only be used for waste management
                    services, account verification, complaint processing,
                    notifications, and other legitimate government operations.
                </p>

                <hr>

                <h6>6. User Responsibilities</h6>

                <ul>
                    <li>Provide accurate and truthful information.</li>
                    <li>Keep your username and password confidential.</li>
                    <li>Submit only legitimate complaints and reports.</li>
                    <li>Do not impersonate another person.</li>
                    <li>Do not misuse or attempt to compromise the system.</li>
                </ul>

                <hr>

                <h6>7. Notifications</h6>

                <p>
                    EnviroManage may send notifications regarding collection
                    schedules, announcements, complaint updates, and service
                    advisories through the application or SMS. Delivery of
                    notifications may be affected by internet connectivity or
                    telecommunications services.
                </p>

                <hr>

                <h6>8. Suspension of Accounts</h6>

                <p>
                    MENRO reserves the right to suspend or permanently remove
                    accounts that provide false information, upload fraudulent
                    documents, violate these Terms and Conditions, or misuse
                    the system.
                </p>

                <hr>

                <h6>9. Limitation of Liability</h6>

                <p>
                    MENRO and the developers of EnviroManage shall not be held
                    liable for delays, interruptions, or service disruptions
                    caused by internet connectivity, technical failures,
                    maintenance, or circumstances beyond their control.
                </p>

                <hr>

                <h6>10. Amendments</h6>

                <p>
                    MENRO may revise these Terms and Conditions whenever
                    necessary to improve the system, comply with applicable
                    laws, or implement new features. Continued use of the
                    system after revisions constitutes acceptance of the
                    updated Terms.
                </p>

                <hr>

                <h6>11. Governing Law</h6>

                <p>
                    These Terms and Conditions shall be governed by the laws of
                    the Republic of the Philippines, including the Data Privacy
                    Act of 2012 and other applicable government regulations.
                </p>

                <hr>

                <h6>12. Acknowledgment</h6>

                <p>
                    By checking the
                    <strong>"I agree to the Terms & Conditions"</strong>
                    checkbox, you acknowledge that you have read, understood,
                    and agreed to comply with these Terms and Conditions and
                    consent to the collection and processing of your personal
                    information for legitimate waste management services.
                </p>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-success"
                    onclick="document.querySelectorAll('input[name=terms]').forEach(cb=>cb.checked=true)"
                    data-bs-dismiss="modal">
                    I Agree
                </button>

            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>

  let barangayData = {};

// Load JSON
fetch('barangays.json')
    .then(response => response.json())
    .then(data => {
        barangayData = data;
    })
    .catch(error => {
        console.error('Error loading barangays:', error);
    });


// ===========================
// BARANGAY SUGGESTIONS
// ===========================
document.querySelectorAll('form').forEach(form => {

    const barangayInput = form.querySelector('.barangay-input');
  const streetInput = form.querySelector('.street-input');
streetInput.disabled = true;
    const barangayBox = barangayInput.parentElement.querySelector('.suggestion-box');
    const streetBox = streetInput.parentElement.querySelector('.suggestion-box');

  // ===========================
// BARANGAY SUGGESTIONS
// ===========================

barangayInput.addEventListener('focus', function(){

    showBarangaySuggestions("");

});

barangayInput.addEventListener('input', function(){

    this.value = this.value
        .toLowerCase()
        .replace(/\b\w/g, char => char.toUpperCase());

    const keyword = this.value.trim().toLowerCase();

    const matchedBarangay = Object.keys(barangayData).find(barangay =>
        barangay.toLowerCase() === keyword
    );

    showBarangaySuggestions(this.value);

    if(matchedBarangay){

        this.value = matchedBarangay;

        streetInput.disabled = false;
        streetInput.placeholder = "Street";

    }else{

        streetInput.value = "";
        streetInput.disabled = true;
        streetInput.placeholder = "Select Barangay first";

    }

});

function showBarangaySuggestions(value){

    barangayBox.innerHTML = "";

    const search = value.toLowerCase().trim();

    const matches = Object.keys(barangayData).filter(barangay =>
        barangay.toLowerCase().includes(search)
    );

    if(matches.length === 0){

        barangayBox.innerHTML = `
            <div class="suggestion-item text-muted">
                No matches found.<br>
                Search for barangays within Sariaya only.
            </div>
        `;

        barangayBox.style.display = "block";
        return;

    }

    matches.forEach(barangay=>{

        const item = document.createElement("div");

        item.classList.add("suggestion-item");

        item.textContent = barangay;

        item.addEventListener("click",()=>{

            barangayInput.value = barangay;

            streetInput.disabled = false;
            streetInput.placeholder = "Street";

            barangayBox.style.display="none";

        });

        barangayBox.appendChild(item);

    });

    barangayBox.style.display="block";

}

barangayInput.addEventListener("keydown", function(e){

    if(e.key !== "Enter") return;

    e.preventDefault();

    const keyword = this.value.trim().toLowerCase();

    const matchedBarangay = Object.keys(barangayData).find(barangay =>
        barangay.toLowerCase() === keyword
    );

    if(matchedBarangay){

        this.value = matchedBarangay;

        streetInput.disabled = false;
        streetInput.placeholder = "Street";

        barangayBox.style.display = "none";

    }else{

        barangayBox.innerHTML = `
            <div class="suggestion-item text-muted">
                No matches found.<br>
                Search for barangays within Sariaya only.
            </div>
        `;

        barangayBox.style.display = "block";

    }

});
   // ===========================
// STREET SUGGESTIONS
// ===========================

streetInput.addEventListener("focus", function(){

    if(!barangayData[barangayInput.value]){

        Swal.fire({
            icon:"warning",
            title:"Select Barangay First",
            text:"Please select a barangay before choosing a street.",
            confirmButtonColor:"#1D4525"
        });

        streetInput.blur();
        return;

    }

    showStreetSuggestions("");

});


streetInput.addEventListener("input",function(){

    if(!barangayData[barangayInput.value]){

        return;

    }


    showStreetSuggestions(this.value);


});


function showStreetSuggestions(value){

    streetBox.innerHTML="";


    const streets = barangayData[barangayInput.value];


    const search=value.toLowerCase().trim();


    const matches=streets.filter(street=>
        street.toLowerCase().includes(search)
    );


    if(matches.length===0){

        streetBox.style.display="none";
        return;

    }


    matches.forEach(street=>{


        const item=document.createElement("div");

        item.classList.add("suggestion-item");

        item.textContent=street;


        item.onclick=function(){

            streetInput.value=street;

            streetBox.style.display="none";

        };


        streetBox.appendChild(item);


    });


    streetBox.style.display="block";


}


    // Hide dropdowns when clicking outside
    document.addEventListener('click', function(e) {

        if (!barangayInput.parentElement.contains(e.target)) {
            barangayBox.style.display = 'none';
        }

        if (!streetInput.parentElement.contains(e.target)) {
            streetBox.style.display = 'none';
        }
    });

});

// Toggle password visibility
document.querySelectorAll('.toggle-password').forEach(icon=>{
    icon.addEventListener('click', ()=>{
        const input = icon.previousElementSibling;
        input.type = input.type === "password" ? "text" : "password";
        icon.classList.toggle("fa-eye");
        icon.classList.toggle("fa-eye-slash");
    });
});

// Username suggestion
function suggestUsername(form){
    const email = form.querySelector('input[name="email"]');
    const username = form.querySelector('input[name="username"]');
    email.addEventListener('input', ()=>{ 
        if(email.value && !username.value) username.value = email.value.split('@')[0];
    });
}
suggestUsername(document.getElementById('signupDesktopForm'));
suggestUsername(document.getElementById('signupMobileForm'));

// Password strength bar
function setupPasswordStrength(form){
    const passInput = form.querySelector('input[name="password"]');
    const strengthBar = form.querySelector('.strength-bar-fill');
    const reqs = form.querySelectorAll('.requirements-list li');

    passInput.addEventListener('input', ()=>{
        const val = passInput.value;
        let score = 0;
        if(val.length>=8){ score++; reqs[0].style.color='green'; } else reqs[0].style.color='#6c757d';
        if(/[A-Z]/.test(val)){ score++; reqs[1].style.color='green'; } else reqs[1].style.color='#6c757d';
        if(/[a-z]/.test(val)){ score++; reqs[2].style.color='green'; } else reqs[2].style.color='#6c757d';
        if(/\d/.test(val)){ score++; reqs[3].style.color='green'; } else reqs[3].style.color='#6c757d';

        const colors = ['red','orange','yellowgreen','green'];
        strengthBar.style.width = (score*25)+'%';
        strengthBar.style.background = colors[score-1] || 'red';
    });
}
setupPasswordStrength(document.getElementById('signupDesktopForm'));
setupPasswordStrength(document.getElementById('signupMobileForm'));

// AJAX form submission
function ajaxForm(form){
    form.addEventListener('submit', e=>{
        e.preventDefault();
        const formData = new FormData(form);
        fetch('signup.php',{method:'POST',body:formData})
        .then(res=>res.json())
        .then(data=>{
            const modal = new bootstrap.Modal(document.getElementById('messageModal'));
            document.getElementById('modalMessage').textContent = data.error || data.success;
            modal.show();
            if(data.success){
    form.reset();

    // Redirect to login page after 2 seconds
    setTimeout(() => {
        window.location.href = "login.php";
    }, 2000);
}
        });
    });
}
ajaxForm(document.getElementById('signupDesktopForm'));
ajaxForm(document.getElementById('signupMobileForm'));

document.querySelectorAll('input[name="valid_id"]').forEach(input => {

    input.addEventListener("change", function () {

        if (!this.files.length) return;

        const file = this.files[0];

        const allowedTypes = [
            "image/jpeg",
            "image/jpg",
            "image/png"
        ];

        const maxSize = 5 * 1024 * 1024; // 5MB

        if (!allowedTypes.includes(file.type)) {

            Swal.fire({
                icon: "warning",
                title: "Unsupported File",
                text: "Only JPG, JPEG and PNG images are allowed."
            });

            this.value = "";
            return;
        }

        if (file.size > maxSize) {

            Swal.fire({
                icon: "warning",
                title: "File Too Large",
                text: "Maximum file size is 5 MB."
            });

            this.value = "";
        }

    });

});

const galleryInput = document.getElementById("validIdGallery");
const cameraInput = document.getElementById("validIdCamera");
const fileLabel = document.getElementById("selectedValidId");

// When camera is used, copy the file to the real input
cameraInput.addEventListener("change", function () {

    if (!this.files.length) return;

    galleryInput.files = this.files;

    updateFileName();

});

// When gallery is used
galleryInput.addEventListener("change", updateFileName);

function updateFileName(){

    if(!galleryInput.files.length){

        fileLabel.textContent = "No file selected.";
        return;
    }

    const file = galleryInput.files[0];

    const allowed = [
        "image/jpeg",
        "image/png"
    ];

    if(!allowed.includes(file.type)){

        Swal.fire({
            icon:"warning",
            title:"Unsupported File",
            text:"Only JPG, JPEG and PNG images are allowed."
        });

        galleryInput.value = "";
        fileLabel.textContent = "No file selected.";
        document.getElementById("validIdPreviewMobile").classList.add("d-none");
document.getElementById("validIdPreviewMobile").src = "";
        return;
    }

    if(file.size > 5 * 1024 * 1024){

        Swal.fire({
            icon:"warning",
            title:"File Too Large",
            text:"Maximum file size is 5 MB."
        });

        galleryInput.value = "";
        fileLabel.textContent = "No file selected.";
        document.getElementById("validIdPreviewMobile").classList.add("d-none");
document.getElementById("validIdPreviewMobile").src = "";
        return;
    }

    fileLabel.textContent = file.name;

const preview = document.getElementById("validIdPreviewMobile");

preview.src = URL.createObjectURL(file);
preview.classList.remove("d-none");

}

document.querySelectorAll(".valid-id-input").forEach(input=>{

    input.addEventListener("change",function(){

        const preview=this.closest(".mb-3")
            .querySelector(".valid-id-preview");

        if(!this.files.length){

            preview.src="";
            preview.classList.add("d-none");
            return;

        }

        preview.src=URL.createObjectURL(this.files[0]);
        preview.classList.remove("d-none");

    });

});

// PHONE ONLY NUMBERS
document.querySelectorAll(".phone-input").forEach(input=>{

    input.addEventListener("input",function(){

        this.value=this.value.replace(/\D/g,'');

        if(this.value.length > 10){
    this.value=this.value.slice(0,10);
}
if(this.value.length === 1 && this.value !== "9"){
    this.value = "";
}
    });

});


// HOUSE NUMBER ONLY NUMBERS
document.querySelectorAll(".house-input").forEach(input=>{

    input.addEventListener("input",function(){

        this.value=this.value.replace(/\D/g,'');

        if(this.value.length > 5){
            this.value=this.value.slice(0,5);
        }

    });

});

// CHECK AGE BEFORE SUBMIT
document.querySelectorAll("form").forEach(form=>{

form.addEventListener("submit",function(e){

    const birth = this.querySelector(".birthdate-input");

    if(birth && birth.value){

        const birthday = new Date(birth.value);
        const today = new Date();

        let age = today.getFullYear() - birthday.getFullYear();

        const month =
        today.getMonth() - birthday.getMonth();

        if(
            month < 0 ||
            (month === 0 && today.getDate() < birthday.getDate())
        ){
            age--;
        }


        if(age < 18){

            e.preventDefault();

            Swal.fire({
                icon:"warning",
                title:"Age Requirement",
                text:"You must be 18 years old and above to register.",
                confirmButtonColor:"#1D4525"
            });

            return false;
        }

    }

});

});

// AUTO CAPITALIZE NAME FIELDS + PREVENT NUMBERS
document.querySelectorAll('input[name="first_name"], input[name="middle_initial"], input[name="last_name"]').forEach(input => {

    // Prevent numbers from being typed
    input.addEventListener("keydown", function(e){

        // Allow Backspace, Delete, Tab, Enter, Arrow keys, Home, End
        const allowedKeys = [
            "Backspace", "Delete", "Tab", "Enter",
            "ArrowLeft", "ArrowRight", "ArrowUp", "ArrowDown",
            "Home", "End"
        ];

        if (allowedKeys.includes(e.key) || e.ctrlKey || e.metaKey) {
            return;
        }

        // Block numbers
        if (/^[0-9]$/.test(e.key)) {
            e.preventDefault();
        }

    });

    // Clean pasted/typed text and capitalize
    input.addEventListener("input", function(){

        this.value = this.value
            .replace(/[^A-Za-z\s'-]/g, "")
            .toLowerCase()
            .replace(/\b[a-z]/g, char => char.toUpperCase());

    });

});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>