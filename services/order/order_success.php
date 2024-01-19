<?php
session_start();

if (!isset($_SESSION['success'])) {
    $_SESSION['success'] = "Order Placed Successfully!!!";
}
require "../../templates/order_message.php";
?>