<?php

// Function to check if the user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['logged_in_user_id']);
}

// Function to get logged-in user ID
function getLoggedInUserId() {
    if (isUserLoggedIn()) {
        return $_SESSION['logged_in_user_id'];
    }
    return null;
}

// Additional common functions can be added here
?>
