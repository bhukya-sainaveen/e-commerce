<?php
session_start();

// Check if admin is not logged in, redirect to login page
if (!isset($_SESSION["name"]) or $_SESSION["name"] != "admin") {
    header("Location: login");
    exit();
}


// Check for error in the session
if (isset($_SESSION['deleteError'])) {
    $deleteError = $_SESSION['deleteError'];
    // Display the error message as needed
    echo '<div class="alert alert-danger" role="alert" style="text-align: center;">';
    echo $deleteError; 
    echo '</div>';

    // Clear the error message from the session
    unset($_SESSION['deleteError']);
} 
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    // Display the success message as needed
    echo '<div class="alert alert-success" role="alert" style="text-align: center;">';
    echo $success; 
    echo '</div>';

    // Clear the success message from the session
    unset($_SESSION['success']);
}

// Include your database configuration file (db_config.php)
require '../services/db_config.php';

// Fetch all products from the database
$productsQuery = "SELECT * FROM product";
$result = mysqli_query($conn, $productsQuery);

// Check if there are products
$products = array();
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    mysqli_free_result($result);
}

// Close the database connection
mysqli_close($conn);
?>

<?php require '../templates/base_head.php'; ?>

<br><br>
<div class="container text-center">

    <a href="../auth/logout" class="btn btn-primary">Log Out</a><br><br>

    <a href="add" class="btn btn-primary">Add Products</a><br><br>

    <!-- Display Products in Grid -->
    <div class="row">
        <?php foreach ($products as $product) : ?>
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <img class="bd-placeholder-img card-img-top" width="100%" height="225" src="../assets/<?php echo $product['image_name']; ?>" alt="<?php echo $product['name']; ?> Image">
                    <div class="card-body">
                        <h3><?php echo $product['name']; ?></h3>
                        <p class="card-text"><?php echo $product['description']; ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <a href="edit?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>&nbsp;&nbsp;
                                <a href="delete?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirmDelete()">Delete</a>
                            </div>
                            <small class="text-muted"><?php echo $product['category']; ?> | ₹<?php echo $product['price']; ?></small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    

</div>

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete the product?");
    }
</script>

<?php require '../templates/base_tail.php'; ?>
