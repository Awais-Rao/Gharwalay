<?php
require '../connection/connection.php';

// Get the data from POST request
$email = trim($_POST['email']);
$password = trim($_POST['password']);

// Prepare the response
$response = ['success' => false, 'verified' => false, 'message' => ''];

// Query to get the user details
$query = "SELECT user_id, password, verification_status FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Check if the password is correct
    if (password_verify($password, $user['password'])) {
        if ($user['verification_status'] == 1) {
            // Login successful and user is verified
            $response['success'] = true;
            $response['verified'] = true; // User is verified
            $_SESSION['logged_in_user_id'] = $user['user_id'];
        } else {
            // User is not verified
            $response['success'] = true;
            $response['verified'] = false;
            $response['message'] = 'Your account is not verified. Please verify your email.';
        }
    } else {
        // Incorrect password
        $response['message'] = 'Invalid email or password.';
    }
} else {
    // User does not exist
    $response['message'] = 'User with provided email does not exist.';
}

// Return the response as JSON
echo json_encode($response);
?>
