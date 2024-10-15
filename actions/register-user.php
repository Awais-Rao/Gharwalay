<?php
require '../connection/connection.php';
header('Content-Type: application/json'); // Ensure that JSON is returned

$errors = [];
$success = false;


ini_set('display_errors', 0);  // Hide errors from displaying to users
ini_set('log_errors', 1);      // Enable logging
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/gharwalay/php-error.log');  //

try {
    // Sanitize and validate inputs
    $firstName = trim($_POST['first-name']);
    $lastName = trim($_POST['last-name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    // Validate First Name
    if (empty($firstName)) {
        $errors['firstName'] = 'First name is required.';
    } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $firstName)) {
        $errors['firstName'] = 'Only letters allowed.';
    }

    // Validate Last Name
    if (empty($lastName)) {
        $errors['lastName'] = 'Last name is required.';
    } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $lastName)) {
        $errors['lastName'] = 'Only letters allowed.';
    }

    // Validate Email
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    } else {
        // Check if email already exists
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors['email'] = 'Email already exists.';
        }
    }

    // Validate Password
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters long.';
    }

    // Validate Confirm Password
    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = 'Passwords do not match.';
    }

    // If there are no errors, proceed with registration
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssss', $firstName, $lastName, $email, $hashed_password);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            //save email
            $_SESSION['registerd_email'] = $email;

            // Generate OTP and send email
            $otp = rand(100000, 999999);
            $sent_at = date('Y-m-d H:i:s');
            $query = "INSERT INTO user_verification (user_id, verification_code, sent_at) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('iis', $user_id, $otp, $sent_at);
            $stmt->execute();

            // Send OTP via email
            mail($email, "Your OTP Code", "Your OTP code is $otp.", "From: notifications@gharwalay.com");

            $success = true;
        }
    }

    // Return response as JSON
    echo json_encode(['success' => $success, 'errors' => $errors]);
}  catch (Exception $e) {
    // Temporarily display the error message for debugging
    echo json_encode(['success' => false, 'errors' => ['general' => $e->getMessage()]]);
}
?>
