<?php
require '../connection/connection.php';

ini_set('display_errors', 0);  // Hide errors from displaying to users
ini_set('log_errors', 1);      // Enable error logging

$response = ['success' => false];

if (isset($_SESSION['registerd_email'])) {
    $email = $_SESSION['registerd_email'];

    // Fetch the user ID associated with the email
    $query = "SELECT user_id FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_id = $result->fetch_assoc()['user_id'];

        // Generate a new OTP and current timestamp
        $new_otp = rand(100000, 999999);
        $sent_at = date('Y-m-d H:i:s');

        // Update the OTP in the user_verification table
        $query = "UPDATE user_verification SET verification_code = ?, sent_at = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssi', $new_otp, $sent_at, $user_id);
        $stmt->execute();

        // Send the new OTP via email
        $subject = "Your New OTP Code";
        $message = "Your new OTP code is $new_otp.";
        $headers = "From: notifications@gharwalay.com";
        if (mail($email, $subject, $message, $headers)) {
            $response['success'] = true;
        } else {
            error_log("Failed to send email to $email");
        }
    }
}

// Return the response as JSON
echo json_encode($response);
?>
