<?php
// Start the session
session_start();

$home = "../";

// Unset all of the session variables
if (isset($_SESSION["name"]) and $_SESSION["name"] == "admin") {
    $home = "../admin";
}
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page or any other page as needed
header("Location: ".$home);
exit();
?>