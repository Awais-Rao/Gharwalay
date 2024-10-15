<?php
require 'connection/connection.php';
include 'components/header.php';
include 'components/navbar-2.php';
?>

<main id="content">
    <section class="py-9">
        <div class="container">
            <div class="row justify-content-center login-register">
                <div class="col-lg-6">

                    <div class="card border-0">
                        <div class="card-body shadow-xxs-2 px-6 py-6">
                            <h2 class="card-title fs-30 font-weight-600 text-dark lh-16 mb-2">Verify OTP</h2>

                            <form id="otp-form">

                                <div class="form-row mx-n2">
                                    <div class="col-12 px-2">
                                        <div class="form-group">
                                            <label for="otp" class="text-heading">Enter OTP</label>
                                            <input type="text" name="otp" class="form-control form-control-lg border-0"
                                                id="otp" />
                                        </div>
                                        <p class="form-text text-danger" id="otp-message"></p>
                                    </div>
                                </div>
                                <button type="submit" id="submit-otp"
                                    class="btn btn-primary btn-lg btn-block rounded mt-2">Submit OTP</button>
                                <button type="button" id="resend-otp" style="display: none;"
                                    class="btn btn-primary btn-lg btn-block rounded mt-2">Resend OTP</button>

                                <div class="form-row mx-n2">
                                    <div class="col-12 px-2">
                                        <p class="form-text text-center" id="timer">Request new OTP in : <span id="countdown">60</span>
                                            seconds</p>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'components/footer.php'; ?>


<script>
    // Countdown Timer Logic
    var timer = 60;
    var interval;

    // Function to start or reset the countdown
    function startTimer() {
        clearInterval(interval); // Clear any existing interval to prevent multiple timers
        timer = 60; // Reset the timer to 60 seconds
        document.getElementById('countdown').textContent = timer;
        document.getElementById('resend-otp').style.display = 'none'; // Hide the resend button during countdown
        document.getElementById('timer').style.display = 'block'; // Show the timer paragraph

        interval = setInterval(updateTimer, 1000); // Start the countdown
    }

    // Function to update the countdown each second
    function updateTimer() {
        timer--;
        document.getElementById('countdown').textContent = timer;

        if (timer <= 0) {
            clearInterval(interval); // Stop the countdown when it reaches 0
            document.getElementById('resend-otp').style.display = 'block'; // Show the resend button
            document.getElementById('timer').style.display = 'none'; // Hide the timer paragraph
        }
    }

    // Start the timer when the page loads
    startTimer();

    // OTP Submit Handler
    $('#otp-form').on('submit', function (e) {
        e.preventDefault();
        var otp = $('#otp').val();

        $.ajax({
            url: 'actions/verify-otp.php',
            type: 'POST',
            data: { otp: otp },
            success: function (data) {
                try {
                    if (typeof data === 'string') {
                        data = JSON.parse(data);
                    }

                    if (data.success) {
                        Swal.fire({
                            title: 'OTP Verified!',
                            text: 'Kindly login to proceed.',
                            icon: 'success',
                            timer: 5000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.href = 'login.php'; // Redirect to login page
                        });
                    } else if (data.expired) {
                        Swal.fire({
                            title: 'OTP Expired',
                            text: 'Please request a new one.',
                            icon: 'error',
                            timer: 5000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });
                    } else {
                        Swal.fire({
                            title: 'Invalid OTP',
                            text: 'Please try again.',
                            icon: 'error',
                            timer: 5000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });
                    }
                } catch (e) {
                    console.error("Invalid JSON response:", e);
                }
            },
            error: function (xhr, status, error) {
                console.log('Error:', error); // Log any errors for debugging
            }
        });
    });

    // Resend OTP Button
    $('#resend-otp').on('click', function () {
        $.ajax({
            url: 'actions/resend-otp.php',
            type: 'POST',
            success: function (data) {
                try {
                    if (typeof data === 'string') {
                        data = JSON.parse(data);
                    }

                    if (data.success) {
                        Swal.fire({
                            title: 'OTP Sent!',
                            text: 'A new OTP has been sent to your email.',
                            icon: 'success',
                            timer: 5000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });

                        // Start or reset the timer after sending a new OTP
                        startTimer();
                    } else {
                        Swal.fire({
                            title: 'Failed to Resend OTP',
                            text: 'Please try again.',
                            icon: 'error',
                            timer: 5000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });
                    }
                } catch (e) {
                    console.error("Invalid JSON response:", e);
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while resending OTP.',
                        icon: 'error',
                        timer: 5000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log('Error:', error); // Log any errors for debugging
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                    icon: 'error',
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                });
            }
        });
    });
</script>
