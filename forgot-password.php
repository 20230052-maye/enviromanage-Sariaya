<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>EnviroManage Forgot Password</title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >


    <style>

        body,
        html {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            height: 100%;
            overflow-x: hidden;
        }

        .container-desktop {
            display: none;
            height: 100vh;
        }

        .left-panel {
            background-color: #285e33;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .left-panel img {
            width: 300px;
            height: 300px;
        }

        .right-panel {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
        }

        .card-box {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        .card-box h2 {
            color: #1D4525;
            margin-bottom: 20px;
        }

        .card-box .form-control {
            margin-bottom: 15px;
            border-radius: 8px;
        }

        .card-box .btn-send {
            width: 100%;
            background-color: #1D4525;
            color: white;
            border-radius: 8px;
        }

        .card-box .btn-send:hover {
            background-color: #163519;
        }

        .card-box a {
            color: #1D4525;
            text-decoration: none;
        }

        .card-box a:hover {
            text-decoration: underline;
        }


        .container-mobile {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            background-color: #ffffff;
        }

        .mobile-logo-bg {
            background-color: #1D4525;
            width: 120%;
            height: 280px;
            border-bottom-left-radius: 80% 100%;
            border-bottom-right-radius: 80% 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .mobile-logo-bg img {
            width: 200px;
            height: 200px;
        }

        .mobile-card {
            background-color: #ffffff;
            width: 90%;
            max-width: 400px;
            margin-top: 100px;
            padding: 30px 20px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            text-align: center;
            display: flex;
            flex-direction: column;
        }

        .mobile-card .form-control {
            width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .mobile-card .btn-send {
            width: 100%;
            background-color: #1D4525;
            color: white;
            border-radius: 8px;
            margin-top: 10px;
        }

        .mobile-card .btn-send:hover {
            background-color: #163519;
        }


        @media (min-width: 992px) {

            .container-desktop {
                display: flex;
            }

            .container-mobile {
                display: none;
            }

            .container-desktop .left-panel {
                width: 50%;
            }

            .container-desktop .right-panel {
                width: 50%;
            }

        }

    </style>

</head>


<body>


<!-- MESSAGE MODAL -->

<div class="modal fade"
     id="messageModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content text-center">

            <div class="modal-header">

                <h5 class="modal-title">
                    Message
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <p
                    id="modalMessage"
                    style="white-space: pre-line;">
                </p>

            </div>

        </div>

    </div>

</div>


<!-- DESKTOP -->

<div class="container-desktop">

    <div class="left-panel">

        <img
            src="assets/enviromanage-logo-512.png"
            alt="EnviroManage Logo"
        >

    </div>


    <div class="right-panel">

        <div class="card-box">

            <h2>
                Forgot Password
            </h2>


            <form id="forgotDesktopForm">

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Enter Email Address"
                    required
                >

                <button
                    type="submit"
                    class="btn btn-send mb-3">
                    Send
                </button>


                <div>
                    <a href="login.php">
                        Back to Log In
                    </a>
                </div>


                <div style="margin-top:10px;">

                    Don't have an account?

                    <a href="signup.php">
                        Sign Up
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- MOBILE -->

<div class="container-mobile">

    <div class="mobile-logo-bg">

        <img
            src="assets/enviromanage-logo-512.png"
            alt="EnviroManage Logo"
        >

    </div>


    <div class="mobile-card">

        <h2>
            Forgot Password
        </h2>


        <form id="forgotMobileForm">

            <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Enter Email Address"
                required
            >

            <button
                type="submit"
                class="btn btn-send mb-3">
                Send
            </button>


            <div>
                <a href="login.php">
                    Back to Log In
                </a>
            </div>


            <div style="margin-top:10px;">

                Don't have an account?

                <a href="signup.php">
                    Sign Up
                </a>

            </div>

        </form>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
</script>


<script>
  
function ajaxForgotFormSubmit(formId) {

    const form = document.getElementById(formId);

    const button = form.querySelector('.btn-send');

    const originalButtonText = button.innerHTML;


    form.addEventListener('submit', async (e) => {

        e.preventDefault();


        // --------------------------------------------------
        // PREVENT MULTIPLE SUBMISSIONS
        // --------------------------------------------------

        if (button.disabled) {
            return;
        }


        const data = new FormData(form);


        // --------------------------------------------------
        // SHOW LOADING STATE
        // --------------------------------------------------

        button.disabled = true;

        button.innerHTML = `
            <span
                class="spinner-border spinner-border-sm me-2"
                role="status"
                aria-hidden="true">
            </span>
            Sending...
        `;


        try {

            const response = await fetch(
                'forgot-password-handler.php',
                {
                    method: 'POST',
                    body: data
                }
            );


            const json = await response.json();


            const modalEl =
                document.getElementById('messageModal');

            const modalMessage =
                document.getElementById('modalMessage');


            // --------------------------------------------------
            // DISPLAY ERROR
            // --------------------------------------------------

            if (json.error) {

                let message = json.error;

                if (json.details) {

                    message +=
                        "\n\nDetails:\n" +
                        json.details;
                }

                modalMessage.innerText = message;

            } else {

                modalMessage.innerText =
                    json.success ||
                    "Something went wrong.";

            }


            const modal =
                new bootstrap.Modal(modalEl);

            modal.show();


            // --------------------------------------------------
            // REDIRECT AFTER SUCCESS
            // --------------------------------------------------

            if (json.redirect) {

                modalEl.addEventListener(
                    'hidden.bs.modal',
                    () => {

                        const emailValue =
                            encodeURIComponent(
                                form.querySelector(
                                    'input[name="email"]'
                                ).value
                            );


                        window.location.href =
                            `${json.redirect}?email=${emailValue}`;

                    },
                    {
                        once: true
                    }
                );

            }


            // --------------------------------------------------
            // RESTORE BUTTON IF THERE IS AN ERROR
            // --------------------------------------------------

            if (!json.redirect) {

                button.disabled = false;

                button.innerHTML =
                    originalButtonText;

            }


        } catch (err) {

            console.error(err);


            const modalEl =
                document.getElementById('messageModal');

            const modalMessage =
                document.getElementById('modalMessage');


            modalMessage.innerText =
                "Something went wrong.\n\n" +
                err.message;


            const modal =
                new bootstrap.Modal(modalEl);

            modal.show();


            // --------------------------------------------------
            // RESTORE BUTTON
            // --------------------------------------------------

            button.disabled = false;

            button.innerHTML =
                originalButtonText;

        }

    });

}



// Desktop form
ajaxForgotFormSubmit('forgotDesktopForm');

// Mobile form
ajaxForgotFormSubmit('forgotMobileForm');

</script>


</body>

</html>
```
