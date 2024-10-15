<?php
require 'connection/connection.php';
include 'components/header.php';
include 'components/navbar-2.php';
?>

<main id="content">
    <section class="py-10">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-xxs-2 login-register">
                        <div class="card-body p-6">
                            <h2 class="card-title fs-30 font-weight-600 text-dark lh-16 mb-2">
                                Forgot your password?
                            </h2>

                            <form id="password-recovery-form" class="form">
                                <div class="form-group">
                                    <label for="email" class="text-heading">Enter your email address</label>
                                    <input type="email" name="mail" class="form-control form-control-lg border-0"
                                        id="email" placeholder="Enter your email address" />
                                </div>
                                <p class="form-text text-danger" id="password-recovery-email-message"></p>
                                <button type="submit" class="btn btn-primary btn-lg rounded">
                                    Get new password
                                </button>
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
    $(document).ready(function () {
        $('#password-recovery-form').on('submit', function (e) {
            e.preventDefault();
            var email = $('#email').val();

            $.ajax({
                url: 'actions/password-recovery.php',
                type: 'POST',
                data: { email: email },
                success: function (response) {
                    try {
                        var data = JSON.parse(response); // Parse JSON response

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'A new password has been sent to your email.',
                                timer: 5000, // Auto-dismiss after 5 seconds
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                    window.location.href = 'login.php'; // Redirect to login page
                                });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Ooopps!',
                                text: data.message,
                                timer: 3000, // Auto-dismiss after 5 seconds
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        Swal.fire({
                            icon: 'error',
                            title: 'Ooopps!',
                            text: 'An unexpected error occurred.',
                            timer: 3000, // Auto-dismiss after 5 seconds
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    Swal.fire({
                        icon: 'error',
                        title: 'Ooopps!',
                        text: 'An unexpected error occurred.',
                        timer: 3000, // Auto-dismiss after 5 seconds
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                }
            });
        });
    });
</script>
