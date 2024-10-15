<?php

// Start the session
session_start();


// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$database = "gharwalay";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);


// echo"database connected successfuly";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
