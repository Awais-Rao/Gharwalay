<?php
require 'connection/connection.php';
include 'components/header.php';
include 'components/navbar-2.php';
?>

<main id="content">
    <section class="py-10">
        <div class="container">
            <div class="row justify-content-center login-register">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-xxs-2 mb-6">
                        <div class="card-body px-8 py-6">
                            <h2 class="card-title fs-30 font-weight-600 text-dark lh-16 mb-2">
                                Log In
                            </h2>
                            <form id="login-form" class="form">
                                <div class="form-group mb-4">
                                    <label for="username-1">Email</label>
                                    <input type="text" class="form-control form-control-lg border-0" id="username-1"
                                        name="username" placeholder="Your email" />
                                    <p class="form-text text-danger" id="login-email-message"></p>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="password-2">Password</label>
                                    <div class="input-group input-group-lg">
                                        <input type="password" class="form-control border-0 shadow-none fs-13"
                                            id="login-page-password" name="password" placeholder="Password" />
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-gray-01 border-0 text-body fs-18"
                                                id="login-page-password-toggle">
                                                <i class="far fa-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <p class="form-text text-danger" id="login-page-password-message"></p>
                                </div>
                                <div class="d-flex mb-4">
                                    <a href="password-recovery.php" class="d-inline-block ml-auto fs-13 lh-2 text-body">
                                        <u>Forgot your password?</u>
                                    </a>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block rounded">
                                    Log in
                                </button>
                            </form>
                            <p class="mt-4 text-center">
                                Don’t have an account?
                                <a href="register.php" class="text-heading hover-primary"><u>Register here for
                                        free</u></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'components/footer.php';
?>


<script>
    $(document).ready(function () {

        // Login Submit Handler
        $('#login-form').on('submit', function (e) {
            e.preventDefault();
            var email = $('#username-1').val();
            var password = $('#login-page-password').val();

            $.ajax({
                url: 'actions/login.php',
                type: 'POST',
                data: { email: email, password: password },
                success: function (response) {
                    try {
                        var data = JSON.parse(response); // Parse JSON response
                        console.log('Login response:', data); // Debugging

                        if (data.success) {
                            if (data.verified) {
                                // Successful login
                                Swal.fire({
                                    title: 'Login Successful',
                                    // text: 'Redirecting to your dashboard...',
                                    text: '.',
                                    icon: 'success',
                                    timer: 3000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = 'index.php'; // Redirect to index page
                                });
                            } else {
                                // Account not verified
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Account Not Verified',
                                    text: 'Would you like to send an OTP to your email?',
                                    showCancelButton: false,
                                    confirmButtonColor: '#0ec6d5',
                                    confirmButtonText: 'Send OTP',
                                    cancelButtonText: 'Cancel'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Send OTP request
                                        $.ajax({
                                            url: 'actions/request-verification.php',
                                            type: 'POST',
                                            success: function (response) {
                                                try {
                                                    var data = JSON.parse(response); // Parse JSON response
                                                    console.log('Verification response:', data); // Debugging

                                                    if (data.success) {
                                                        Swal.fire({
                                                            title: 'OTP Sent',
                                                            text: 'An OTP has been sent to your email. Redirecting to OTP verification page...',
                                                            icon: 'success',
                                                            timer: 3000,
                                                            timerProgressBar: true,
                                                            showConfirmButton: false
                                                        }).then(() => {
                                                            window.location.href = 'otp-verification.php'; // Redirect to OTP verification page
                                                        });
                                                    } else {
                                                        Swal.fire({
                                                            title: 'Ooopps!',
                                                            text: 'Failed to send OTP. Please try again.',
                                                            icon: 'error',
                                                            timer: 3000,
                                                            timerProgressBar: true,
                                                            showConfirmButton: false
                                                        });
                                                    }
                                                } catch (e) {
                                                    console.error('Error parsing JSON:', e);
                                                    Swal.fire({
                                                        title: 'Ooopps!',
                                                        text: 'An unexpected error occurred.',
                                                        icon: 'error',
                                                        timer: 3000,
                                                        timerProgressBar: true,
                                                        showConfirmButton: false
                                                    });
                                                }
                                            },
                                            error: function (jqXHR, textStatus, errorThrown) {
                                                console.error('AJAX error:', textStatus, errorThrown);
                                                Swal.fire({
                                                    title: 'Ooopps!',
                                                    text: 'An unexpected error occurred.',
                                                    icon: 'error',
                                                    timer: 3000,
                                                    timerProgressBar: true,
                                                    showConfirmButton: false
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                        } else {
                            // Invalid email or password
                            Swal.fire({
                                icon: 'error',
                                title: 'Ooopps!',
                                text: data.message,
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    Swal.fire({
                        title: 'Error',
                        text: 'An unexpected error occurred.',
                        icon: 'error'
                    });
                }
            });
        });
    });
</script>