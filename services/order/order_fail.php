<?php
session_start();

if (!isset($_SESSION['errorOrder'])) {
    $_SESSION['errorOrder'] = "Invalid Request";
}

require "../../templates/order_message.php";
?>