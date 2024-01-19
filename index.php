<?php
session_start();

// Include your database configuration file (services/db_config.php)
require 'services/db_config.php';

$products = array();

// Get the current user's ID from the session
if (isset($_SESSION['customer_id'])) {
    $customerId = $_SESSION['customer_id'];

    // Fetch all products from the database along with product IDs in the cart
    $productsQuery = "SELECT product.*, cart.product_id AS in_cart
                    FROM product
                    LEFT JOIN cart ON product.product_id = cart.product_id AND cart.customer_id = ?";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $productsQuery);

    // Bind the parameter
    mysqli_stmt_bind_param($stmt, "i", $customerId);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Get the result set
    $result = mysqli_stmt_get_result($stmt);
} else {
    // Fetch all products from the database
    $productsQuery = "SELECT * FROM product";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $productsQuery);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Get the result set
    $result = mysqli_stmt_get_result($stmt);
}

// Check if there are products
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    mysqli_free_result($result);
}

// Close the statement
mysqli_stmt_close($stmt);

// Close the database connection
mysqli_close($conn);
?>

<?php 
require 'templates/base_head.php';
require 'templates/header.php';
?>
<br>
<div class="container text-center">
    <!-- Display Products in Grid -->
    <div class="row">
        <?php foreach ($products as $product) : ?>
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <img class="bd-placeholder-img card-img-top" width="100%" height="225" src="assets/<?php echo $product['image_name']; ?>" alt="<?php echo $product['name']; ?> Image">
                    <div class="card-body">
                        <h3><?php echo $product['name']; ?></h3>
                        <p class="card-text">
                            <?php
                            $description = $product['description'];
                            $shortDescription = strlen($description) > 50 ? substr($description, 0, 50) . '...' : $description;
                            echo $shortDescription;
                            ?>
                            <a href="#" data-toggle="modal" data-target="#readMoreModal<?php echo $product['product_id']; ?>">Read More</a>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <?php if (isset($_SESSION['name'])) : ?>
                                    <!-- Buy Now Form -->
                                    <form action="services/order/buy" method="post">
                                        <input type="hidden" name="id" value="<?php echo $product['product_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-success">Buy Now</button>
                                    </form>
                                <?php else : ?>
                                    <!-- Redirect to Login -->
                                    <a href="auth/login" class="btn btn-sm btn-success">Buy Now</a>
                                <?php endif ?>&nbsp;&nbsp;
                                <?php
                                // Display "Go to Cart" if the product is in the cart, otherwise, "Add to Cart"
                                if ($_SESSION['customer_id'] && $product['in_cart'] !== null) {
                                    echo '<a href="services/cart" class="btn btn-sm btn-warning go-to-cart">Go to Cart</a>';
                                } else if ($_SESSION['customer_id']) {
                                    echo '<a href="#" class="btn btn-sm btn-secondary add-to-cart" data-product-id="' . $product['product_id'] . '">Add to Cart</a>';
                                } else {
                                    echo '<a href="auth/login" class="btn btn-sm btn-secondary" >Add to Cart</a>';
                                }
                                ?>
                            </div>
                            <small class="text-muted"><?php echo $product['category']; ?> | ₹<?php echo $product['price']; ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Read More Modal -->
            <div class="modal fade" id="readMoreModal<?php echo $product['product_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="readMoreModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="readMoreModalLabel"><?php echo $product['name']; ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php echo $product['description']; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="services/add_to_cart.js"></script>

<?php 
include 'templates/footer.php';
require 'templates/base_tail.php';
?>