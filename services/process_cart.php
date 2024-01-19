<?php 
session_start();

// Include your database configuration file (services/db_config.php)
require 'db_config.php';

$response = array('success' => false, 'message' => 'initial one');

if (isset($_SESSION['customer_id']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $customerId = $_SESSION['customer_id'];
    $productId = $_POST['id'];

    if (!isset($_POST['quantity'])) {
        // Insert a new record if the product is not in the cart
        $insertCartQuery = "INSERT INTO cart (customer_id, product_id) VALUES (?, ?)";
        $insertCartStmt = mysqli_prepare($conn, $insertCartQuery);

        if ($insertCartStmt) {
            mysqli_stmt_bind_param($insertCartStmt, "ii", $customerId, $productId);
            mysqli_stmt_execute($insertCartStmt);
            $response['success'] = true;
        }
        // Set success message in the response
        $response['message'] = $response['success'] ? 'Product added to cart successfully' : 'Error adding product to cart';
        mysqli_stmt_close($insertCartStmt);
    } else {
        // Validate and sanitize input data
        $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
        // Check if validation failed
        if ($quantity === false) {
            $response['message'] = 'Invalid input data';
            // Optionally log the validation error for debugging purposes
            $response['message'] = 'Invalid input data: Quantity=' . $_POST['quantity'];
            // Return the response as JSON
            header('Content-Type: application/json');
            echo json_encode($response);
            exit; // Stop further execution
        }

        // If quantity is zero, delete the product from the cart
        if ($quantity == 0) {
            $deleteCartQuery = "DELETE FROM cart WHERE customer_id = ? AND product_id = ?";
            $deleteCartStmt = mysqli_prepare($conn, $deleteCartQuery);

            if ($deleteCartStmt) {
                mysqli_stmt_bind_param($deleteCartStmt, "ii", $customerId, $productId);
                mysqli_stmt_execute($deleteCartStmt);
                $response['success'] = true;
            }
            // Set success message in the response
            $response['message'] = $response['success'] ? 'Product deleted from cart successfully' : 'Error deleting product from cart';
            mysqli_stmt_close($deleteCartStmt);
        } else {
            // If quantity is not zero, update the quantity in the cart
            $updateCartQuery = "UPDATE cart SET quantity = ? WHERE customer_id = ? AND product_id = ?";
            $updateCartStmt = mysqli_prepare($conn, $updateCartQuery);

            if ($updateCartStmt) {
                mysqli_stmt_bind_param($updateCartStmt, "iii", $quantity, $customerId, $productId);
                mysqli_stmt_execute($updateCartStmt);
                $response['success'] = true;
            }
            // Set success message in the response
            $response['message'] = $response['success'] ? 'Cart updated successfully' : 'Error updating cart';
            mysqli_stmt_close($updateCartStmt);
        }
    }
} else {
    $response['message'] = "Invalid request";
}

// Close the database connection
mysqli_close($conn);

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
