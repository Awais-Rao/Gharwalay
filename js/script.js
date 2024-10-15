// Handle login from navbar modal

$(document).ready(function () {

  // Login Submit Handler for Modal
  $("#modal-login-form").on("submit", function (e) {
    e.preventDefault();
    var email = $("#login-modal-email").val().trim();
    var password = $("#login-modal-password").val().trim();

    // Clear previous error messages
    $("#login-modal-email-message").text("");
    $("#login-modal-password-message").text("");

    $.ajax({
      url: "actions/login.php",
      type: "POST",
      data: { email: email, password: password },
      success: function (response) {
        if (!response) {
          Swal.fire({
            title: "Error",
            text: "No response from server.",
            icon: "error",
          });
          return;
        }
        try {
          var data = JSON.parse(response); // Parse JSON response

          if (data.success) {
            if (data.verified) {
              // Successful login
              $("#login-register-modal").modal("hide"); // Hide the modal

              Swal.fire({
                title: "Login Successful",
                text: "Welcome back!",
                icon: "success",
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
              });
            } else {
              // Account not verified
              Swal.fire({
                icon: "info",
                title: "Account Not Verified",
                text: "Would you like to send an OTP to your email?",
                confirmButtonText: "Send OTP",
                showCancelButton: true,
                cancelButtonText: "Cancel",
              }).then((result) => {
                if (result.isConfirmed) {
                  // Handle OTP request
                  sendOTP();
                }
              });
            }
          } else {
            // Invalid email or password
            if (data.message.toLowerCase().includes("email")) {
              $("#login-modal-email-message").text(data.message);
            } else if (data.message.toLowerCase().includes("password")) {
              $("#login-modal-password-message").text(data.message);
            } else {
              Swal.fire({
                icon: "error",
                title: "Oops!",
                text: data.message,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
              });
            }
          }
        } catch (e) {
          Swal.fire({
            title: "Error",
            text: "Failed to parse the server response.",
            icon: "error",
          });
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        Swal.fire({
          title: "Error",
          text: "An unexpected error occurred.",
          icon: "error",
        });
      },
    });
  });

  // Function to send OTP (assuming an AJAX function is set up for sending OTP)
  function sendOTP() {
    $.ajax({
      url: "actions/request-verification.php",
      type: "POST",
      success: function (response) {
        var data = JSON.parse(response);
        if (data.success) {
          Swal.fire({
            title: "OTP Sent",
            text: "An OTP has been sent to your email.",
            icon: "success",
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
          });
        } else {
          Swal.fire({
            title: "Oops!",
            text: "Failed to send OTP. Please try again.",
            icon: "error",
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
          });
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        Swal.fire({
          title: "Error",
          text: "An unexpected error occurred while sending OTP.",
          icon: "error",
          timer: 3000,
          timerProgressBar: true,
          showConfirmButton: false,
        });
      },
    });
  }
});



$(document).ready(function () {
  // Reusable function to toggle password visibility
  function togglePasswordVisibility(passwordInputSelector, toggleIconSelector) {
    $(toggleIconSelector).css("cursor", "pointer"); // Set cursor to pointer

    $(toggleIconSelector).on("click", function () {
      const passwordInput = $(passwordInputSelector);
      const icon = $(this).find("i");

      if (passwordInput.attr("type") === "password") {
        passwordInput.attr("type", "text");
        icon.removeClass("fa-eye-slash").addClass("fa-eye");
      } else {
        passwordInput.attr("type", "password");
        icon.removeClass("fa-eye").addClass("fa-eye-slash");
      }
    });
  }

  // Call the function for different password fields
  togglePasswordVisibility("#register-password", "#register-password-toggle");
  togglePasswordVisibility(
    "#register-confirm-password",
    "#register-confirm-password-toggle"
  );
  togglePasswordVisibility(
    "#login-page-password",
    "#login-page-password-toggle"
  );
  togglePasswordVisibility(
    "#login-modal-password",
    "#login-modal-password-toggle"
  );
});

//Gloabal function to show toast

function showToast(message, background, position) {
  Toastify({
    text: message,
    duration: 3000,
    close: true,
    gravity: "bottom", // `top` or `bottom`
    position: position, // `left`, `center`, or `right`
    stopOnFocus: true, // Prevents dismissing of toast on hover
    style: {
      background: background,
    },
    onClick: function () {}, // Callback after click
  }).showToast();
}
