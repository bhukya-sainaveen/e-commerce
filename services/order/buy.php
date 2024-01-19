<?php
session_start();

if (!isset($_SESSION["customer_id"])) {
    header("Location: ../../auth/login");
    exit();
}

// Include your database configuration file (services/db_config.php)
require '../db_config.php';

$customerId = $_SESSION['customer_id'];


function buyProduct($customerId, $productId, $quantity, $price, $conn) {
    $price = $price * $quantity;
    // Insert order into the order table
    $insertOrderQuery = "INSERT INTO `order` (customer_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmtInsertOrder = mysqli_prepare($conn, $insertOrderQuery);

    if ($stmtInsertOrder) {

        mysqli_stmt_bind_param($stmtInsertOrder, "iiii", $customerId, $productId, $quantity, $price);
        mysqli_stmt_execute($stmtInsertOrder);

        if (mysqli_stmt_error($stmtInsertOrder) === '') {
            $_SESSION['success'] = 'Order placed successfully';
        } else {
            $_SESSION['errorOrder'] = 'Error placing order. Please try again later.';
        }
    } else {
        $_SESSION['errorOrder'] = 'Error placing order. Please try again later';
    }
    mysqli_stmt_close($stmtInsertOrder);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $productId = $_POST['id'];
    // Query to fetch product information
    $productQuery = "SELECT product_id, price FROM product WHERE product_id = ?";
    $productStmt = mysqli_prepare($conn, $productQuery);
    $product_id = null;

    if ($productStmt) {
        mysqli_stmt_bind_param($productStmt, "i", $productId);
        mysqli_stmt_execute($productStmt);
        mysqli_stmt_bind_result($productStmt, $product_id, $price);
        mysqli_stmt_fetch($productStmt);
        mysqli_stmt_close($productStmt);
        // Check if any rows exist
        if ($product_id) {
            buyProduct($customerId, $product_id, 1, $price, $conn);
        } else {
            $_SESSION['errorOrder'] = 'Product not found';
        }
    } else {
        $_SESSION['errorOrder'] = 'Error purchasing order. Please try again later';
    }
    
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cart'])) {
    // Query to fetch product_id and qty from cart
    $cartQuery = "SELECT c.product_id, c.quantity, p.price
                  FROM cart c
                  JOIN product p ON c.product_id = p.product_id
                  WHERE c.customer_id = ?";
    $cartStmt = mysqli_prepare($conn, $cartQuery);

    if ($cartStmt) {
        mysqli_stmt_bind_param($cartStmt, "i", $customerId);
        
        // Execute the statement
        if (mysqli_stmt_execute($cartStmt)) {
            mysqli_stmt_store_result($cartStmt);  // Store the result set in memory

            mysqli_stmt_bind_result($cartStmt, $product_id, $qty, $price);

            // Loop through the results
            while (mysqli_stmt_fetch($cartStmt)) {
                // Call buyProduct function for each product in the cart
                buyProduct($customerId, $product_id, $qty, $price, $conn);
            }

            mysqli_stmt_free_result($cartStmt);  // Free the stored result set
        } else {
            // Handle the case where executing the statement fails
            $_SESSION['errorOrder'] = "Error placing order";
        }
        // Close the SELECT statement before executing the DELETE statement
        mysqli_stmt_close($cartStmt);
        // After processing, delete cart items
        $deleteCartQuery = "DELETE FROM cart WHERE customer_id = ?";
        $deleteCartStmt = mysqli_prepare($conn, $deleteCartQuery);

        if ($deleteCartStmt) {
            mysqli_stmt_bind_param($deleteCartStmt, "i", $customerId);
            mysqli_stmt_execute($deleteCartStmt);
            mysqli_stmt_close($deleteCartStmt);
        } else {
            // Handle the case where preparing the delete statement fails
            $_SESSION['errorOrder'] = "Error preparing delete statement";
        }
    } else {
        // Handle the case where preparing the statement fails
        $_SESSION['errorOrder'] = "Error placing order";
    }
} else {
    $_SESSION['errorOrder'] = "Invalid Request";
}

mysqli_close($conn);

if (isset($_SESSION['success'])) {
    header("Location: order_success");
} else if (isset($_SESSION['errorOrder']))  {
    header("Location: order_fail");
} else {
    $_SESSION['errorOrder'] = "There is an unexpected error. Please try again later";
    header("Location: order_fail");
}
exit();

?>