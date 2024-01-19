<?php
// Replace these values with your actual database credentials
$servername = "YOUR_SERVER_NAME";
$username = "YOUR_USER_NAME";
$password = "YOUR_PASSWORD";
$dbname = "YOUR_DATABASE_NAME";

// Step 1: Establish a database connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>