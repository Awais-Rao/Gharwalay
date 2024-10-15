<?php
require 'connection/connection.php';
include 'components/header.php';
include 'components/navbar-2.php';
?>

<style>
    .swal2-timer-progress-bar {
        background-color: #28a745 !important; /* Green color matching the success icon */
    }
</style>

<main id="content">
    <section class="py-9">
        <div class="container">
            <div class="row justify-content-center login-register">
                <div class="col-lg-6">
                    <div class="card border-0">
                        <div class="card-body shadow-xxs-2 px-6 py-6">
                            <h2 class="card-title fs-30 font-weight-600 text-dark lh-16 mb-2">Register</h2>
                            <form id="register-form" class="form">
                                <div class="form-row mx-n2">
                                    <div class="col-sm-6 px-2">
                                        <div class="form-group">
                                            <label for="firstName" class="text-heading">First Name</label>
                                            <input type="text" name="first-name"
                                                class="form-control form-control-lg border-0" id="firstName"
                                                placeholder="First Name" />
                                        </div>
                                        <p class="form-text text-danger" id="register-firstname-message"></p>
                                    </div>
                                    <div class="col-sm-6 px-2">
                                        <div class="form-group">
                                            <label for="lastName" class="text-heading">Last Name</label>
                                            <input type="text" name="last-name"
                                                class="form-control form-control-lg border-0" id="lastName"
                                                placeholder="Last Name" />
                                        </div>
                                        <p class="form-text text-danger" id="register-lastname-message"></p>
                                    </div>
                                </div>
                                <div class="form-row mx-n2">
                                    <div class="col-12 px-2">
                                        <div class="form-group">
                                            <label for="email" class="text-heading">Email</label>
                                            <input type="email" name="email"
                                                class="form-control form-control-lg border-0" id="email"
                                                placeholder="Your Email" />
                                        </div>
                                        <p class="form-text text-danger" id="register-email-message"></p>
                                    </div>
                                </div>
                                <div class="form-row mx-n2">
                                    <div class="col-sm-6 px-2">
                                        <div class="form-group">
                                            <label for="password" class="text-heading">Password</label>
                                            <div class="input-group input-group-lg">
                                                <input type="password" name="password"
                                                    class="form-control border-0 shadow-none" id="register-password"
                                                    placeholder="Password" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-gray-01 border-0 text-body fs-18"
                                                        id="register-password-toggle">
                                                        <i class="far fa-eye-slash"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="form-text text-danger" id="register-password-message"></p>
                                    </div>
                                    <div class="col-sm-6 px-2">
                                        <div class="form-group">
                                            <label for="confirm-password">Re-Enter Password</label>
                                            <div class="input-group input-group-lg">
                                                <input type="password" name="confirm-password"
                                                    class="form-control border-0 shadow-none"
                                                    id="register-confirm-password" placeholder="Re-Enter Password" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-gray-01 border-0 text-body fs-18"
                                                        id="register-confirm-password-toggle">
                                                        <i class="far fa-eye-slash"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="form-text text-danger" id="register-confirm-password-message"></p>
                                    </div>
                                </div>
                                <button type="submit"
                                    class="btn btn-primary btn-lg btn-block rounded mt-2">Register</button>
                            </form>
                            <p class="mt-4 text-center">Already have an account? <a href="login.php"
                                    class="text-heading hover-primary"><u>Login Now</u></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'components/footer.php'; ?>

<script>

    $(document).ready(function () {
        $("#register-form").on("submit", function (e) {
            e.preventDefault();

            const formData = $(this).serialize();

            // Clear previous error messages
            $(".form-text.text-danger").text("");

            $.ajax({
                url: "actions/register-user.php",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function (data) {
                    if (data.success) {

                        // SweetAlert for success message with 5-second auto-dismiss
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
                            text: 'An OTP has been sent to your email.',
                            timer: 5000,
                            showConfirmButton: false,
                            timerProgressBar: true,
                        }).then(() => {
                            window.location.href = 'otp-verification.php'; // Redirect to OTP entry page
                        });


                    } else {
                        // Display error messages under each field
                        if (data.errors.firstName) {
                            $("#register-firstname-message").text(data.errors.firstName);
                        }
                        if (data.errors.lastName) {
                            $("#register-lastname-message").text(data.errors.lastName);
                        }
                        if (data.errors.email) {
                            $("#register-email-message").text(data.errors.email);
                        }
                        if (data.errors.password) {
                            $("#register-password-message").text(data.errors.password);
                        }
                        if (data.errors.confirmPassword) {
                            $("#register-confirm-password-message").text(data.errors.confirmPassword);
                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.log("An error occurred: " + error);
                },
            });
        });
    });


</script>