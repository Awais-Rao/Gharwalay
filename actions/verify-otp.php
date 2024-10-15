<?php
require '../connection/connection.php';

$email = $_SESSION['registerd_email'];
$input_otp = trim($_POST['otp']); // Trim any whitespace

$response = ['success' => false, 'expired' => false];

// Fetch the OTP details from the database
$query = "SELECT verification_code, sent_at FROM user_verification WHERE user_id = (SELECT user_id FROM users WHERE email = ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $db_otp = trim($row['verification_code']); // Trim any whitespace
    $sent_at = $row['sent_at'];

    // Check if OTP is expired (5 minutes limit)
    $now = new DateTime();
    $otp_sent_time = new DateTime($sent_at);
    $interval = $now->diff($otp_sent_time);

    if ($interval->i >= 5) {
        // OTP expired
        $response['expired'] = true;
    } elseif ($input_otp === $db_otp) {
        // OTP is valid, update the user's verification status
        $query = "UPDATE users SET verification_status = 1 WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();

        // Successfully verified
        $response['success'] = true;
    } else {
        // Invalid OTP
        $response['success'] = false;
    }
} else {
    // No OTP found
    $response['success'] = false;
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
