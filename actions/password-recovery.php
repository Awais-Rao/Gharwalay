<?php
require '../connection/connection.php';

// Get the email from POST request
$email = trim($_POST['email']);

// Prepare the response
$response = ['success' => false, 'message' => ''];

// Query to check if the email exists
$query = "SELECT user_id FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_id = $user['user_id'];

    // Generate a new 6-digit password
    $new_password = rand(100000, 999999);
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update the user's password in the database
    $update_query = "UPDATE users SET password = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('si', $hashed_password, $user_id);
    $update_stmt->execute();

    // Send the new password to the user's email
    $subject = "Your New Password";
    $message = "Your new password is: $new_password";
    $headers = "From: notifications@gharwalay.com";

    if (mail($email, $subject, $message, $headers)) {
        $response['success'] = true;
    } else {
        $response['message'] = 'Failed to send email. Please try again.';
    }
} else {
    $response['message'] = 'Email not found.';
}

// Return the response as JSON
echo json_encode($response);
?>
