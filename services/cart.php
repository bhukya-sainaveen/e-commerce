<?php 
session_start();

if (!isset($_SESSION["customer_id"])) {
    header("Location: ../auth/login");
    exit();
}

// Include your database configuration file (services/db_config.php)
require 'db_config.php';

// Fetch products from the cart for the current customer
$customerId = $_SESSION["customer_id"];
$productsQuery = "SELECT product.*, cart.quantity as quantity 
                  FROM product 
                  JOIN cart ON product.product_id = cart.product_id 
                  WHERE cart.customer_id = ?";
$stmt = mysqli_prepare($conn, $productsQuery);

// Check if the statement was prepared successfully
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $customerId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    // Check if there are products
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        mysqli_free_result($result);
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    echo '<div class="alert alert-danger" role="alert" style="text-align: center;">';
    echo "Error fetching products"; 
    echo '</div>';
}

// Close the database connection
mysqli_close($conn);
?>


<?php require '../templates/base_head.php'; ?>
<?php require '../templates/header.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<div class="alert" id="error" role="alert" style="text-align: center;"></div>

<br>
<!-- Display Orders in Table -->
<div class="container text-center">
    <?php if (empty($products)) : ?>
        <h2 class="text-secondary">Your cart is empty</h2>
    <?php else : ?>
        <h2 class="text-secondary">Your Cart</h2><br>
        <h3 class="text-dark">Order Total: ₹<span id="orderTotal">0.00</span></h3><br>

        <table class="table table-hover" ondblclick="return false;">
            <thead>
                <tr class="text-danger">
                    <th>Product</th>
                    <th>Name</th>
                    <th>Total Price</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) : ?>
                    <tr id="product<?php echo $product['product_id']; ?>">
                        <td><img src="../assets/<?php echo $product['image_name']; ?>" alt="<?php echo $product['name']; ?>" width="50"  height="34"></td>
                        <td><?php echo $product['name']; ?></td>
                        <td>₹<span id="totalPrice<?php echo $product['product_id']; ?>"><?php echo $product['price']; ?></span></td>
                        <td class="d-flex justify-content-center">
                            <button class="btn btn-sm btn-danger" onclick="updateQuantity(<?php echo $product['product_id']; ?>, -1)">-</button>
                            <span id="quantity<?php echo $product['product_id']; ?>" class="mx-2"><?php echo $product['quantity']; ?></span>
                            <button class="btn btn-sm btn-primary" onclick="updateQuantity(<?php echo $product['product_id']; ?>, 1)">+</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <form id="buyNowForm" action="order/buy" method="post">
            <!-- Add any additional input fields if needed -->
            <input type="hidden" name="cart" value="buy">
            <!-- Button to submit the form -->
            <button type="submit" class="btn btn-sm btn-primary">Buy Now</button>
        </form>
    <?php endif; ?><br>
    <a href="../" class="btn btn-sm btn-primary">Go Home</a>
</div>
<br><br><br>


<?php 
require 'update_cart.php';
include '../templates/footer_fixed.php';
require '../templates/base_tail.php'; 
?>
