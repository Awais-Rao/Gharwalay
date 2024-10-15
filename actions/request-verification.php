<?php
require '../connection/connection.php';

// Check if email is set in session
if (!isset($_SESSION['registerd_email'])) {
    echo json_encode(['success' => false, 'message' => 'Email not found in session.']);
    exit;
}

$email = $_SESSION['registerd_email'];

// Fetch user_id from the database
$query = "SELECT user_id FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $user_id = $user['user_id'];

    // Generate OTP
    $otp = rand(100000, 999999);
    $sent_at = date('Y-m-d H:i:s');

    // Update OTP and sent_at in the database if record exists
    $query = "UPDATE user_verification 
              SET verification_code = ?, sent_at = ?
              WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssi', $otp, $sent_at, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Send OTP to user's email
        $subject = "Your OTP Code";
        $message = "Your OTP code is: $otp\nIt was sent at: $sent_at";
        $headers = "From: notifications@gharwalay.com";

        if (mail($email, $subject, $message, $headers)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send OTP email.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User verification record not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
}
?>
