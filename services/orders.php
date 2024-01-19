<?php
session_start();

if (!isset($_SESSION["name"])) {
    header("Location: ../auth/login");
    exit();
}

// Include your database configuration file
require 'db_config.php';

// Fetch all orders for the current customer from the database
$customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;

if ($customer_id) {
    $ordersQuery = "SELECT o.*, p.image_name, p.name as product_name, o.price 
                    FROM `order` o
                    INNER JOIN product p ON o.product_id = p.product_id
                    WHERE o.customer_id = ?
                    ORDER BY o.time_stamp DESC";

    // Using prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, $ordersQuery);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $customer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Check if there are orders
        $orders = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }

        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
    } else {
        // Handle the case when the prepared statement fails
        echo "<script>console.log('Error preparing statement')</script>";
    }

    // Close the database connection
    mysqli_close($conn);
} else {
    // Handle the case when customer_id is not set in the session
    echo "<script>console.log('Customer ID not set in the session')</script>";
}
?>

<?php require '../templates/base_head.php'; ?>
<?php require '../templates/header.php'; ?>
<br>
<!-- Display Orders in Table -->
<div class="container text-center">
    <a href="../" class="btn btn-sm btn-primary">Go Home</a><br><br>
    <?php if (empty($orders)) : ?>
        <h2 class="text-secondary">Your Order History is Empty</h2><br>
    <?php else : ?>
        <h2 class="text-secondary">Your Order History</h2><br>
        <div class="table-responsive">
            <table class="table table-hover" ondblclick="return false;">
                <thead>
                    <tr class="text-danger">
                        <th scope="col">Product</th>
                        <th scope="col" class="d-none d-md-table-cell">Name</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Total Price</th>
                        <th scope="col">Ordered on</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order) : ?>
                        <tr>
                            <!-- Product Image -->
                            <td>
                                <img align='center' width='50px'  height='34px' src='../assets/<?php echo $order['image_name']; ?>' class='align-self-center mr-3' /><br class="d-md-none">
                                <span class="d-md-none"><?php echo $order['product_name']; ?></span>
                            </td>

                            <!-- Order Details -->
                            <td class="d-none d-md-table-cell"><?php echo $order['product_name']; ?></td>
                            <td><?php echo $order['quantity']; ?></td>
                            <td>₹<?php echo $order['price']; ?></td>
                            <td><?php echo $order['time_stamp']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
    <br><br><br>
</div>


<?php 
    include '../templates/footer_fixed.php';
    require '../templates/base_tail.php'; 
?>
