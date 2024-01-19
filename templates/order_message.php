<?php
session_start();
if (!isset($_SESSION['name'])) {
    header("Location: ../auth/login");
    exit();
}
require 'base_head.php';
require 'header.php';
?>

<br><br><br><br><br><br><br><br><br>
<?php 
    if (isset($_SESSION['success'])) {
        echo "<h1 style='color:green;text-align:center;'>".$_SESSION['success']."</h1>";
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['errorOrder'])) {
        echo "<h1 style='color:red;text-align:center;'>".$_SESSION['errorOrder']."</h1>";
        unset($_SESSION['errorOrder']);
    }
    
?>

<br>

<div class="container text-center">


    <a href="../orders" class="btn btn-dark">Go to Orders</a><br><br>
    <a href="../cart" class="btn btn-dark">Go to Cart</a><br><br>
    <a href="../../" class="btn btn-dark">Go Home</a><br><br>

</div>

<br><br><br><br><br><br><br><br><br>

<?php 
include 'footer_fixed.php';
require 'base_tail.php';
?>