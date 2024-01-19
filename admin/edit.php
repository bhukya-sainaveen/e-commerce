<?php
session_start();

// Check if admin is not logged in, redirect to login page
if (!isset($_SESSION["name"]) or $_SESSION["name"] != "admin") {
    header("Location: login");
    exit();
}

// Check for error in the session
if (isset($_SESSION['editError'])) {
    $editError = $_SESSION['editError'];
    // Display the error message as needed
    echo '<div class="alert alert-danger" role="alert" style="text-align: center;">';
    echo $editError; 
    echo '</div>';

    // Clear the error message from the session
    unset($_SESSION['editError']);
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

// Function to fetch categories from the database
function getCategories($conn) {
    $categories = array();
    $sql = "SELECT name FROM category";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row['name'];
        }
        mysqli_free_result($result);
    }

    return $categories;
}

// Admin is logged in, display admin content

// Define variables to store form data
$name = $description = $category = $price = "";
$nameErr = $priceErr = $descErr = "";

if (isset($_GET["id"])) {
    $productId = $_GET["id"];

    // Fetch the product details based on the product ID
    $fetchQuery = "SELECT * FROM product WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $fetchQuery);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $productId);
        mysqli_stmt_execute($stmt);

        // Bind result variables
        mysqli_stmt_bind_result($stmt, $productId, $name, $description, $category, $price, $imageName);

        // Fetch the product details
        mysqli_stmt_fetch($stmt);

        // Close the statement
        mysqli_stmt_close($stmt);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form inputs
    $name = htmlspecialchars($_POST["name"]);
    $description = htmlspecialchars($_POST["description"]);
    $category = htmlspecialchars($_POST["category"]);
    $price = htmlspecialchars($_POST["price"]);

    // Validate name
    if (empty($name)) {
        $nameErr = "Name is required";
    }

    // Validate price
    if (empty($price) || !is_numeric($price) || $price < 0) {
        $priceErr = "Valid price is required";
    }

    if (str_word_count($description) < 10) {
        $descErr = "Describe product in at least 10 words";
    }

    // If there are no errors, proceed with form processing
    if (empty($nameErr) && empty($priceErr) && empty($descErr)) {
        
        $imageNameNew = basename($_FILES["image"]["name"]);
        // Process the image upload
        move_uploaded_file($_FILES["image"]["tmp_name"], "../assets/" . $imageNameNew);


        if (isset($_GET["id"])) {

            // Delete the previous image
            $imagePath = "../assets/".$imageName;
            if ($imageNameNew && $imageName != "no_image.png" && file_exists($imagePath)) {
                unlink($imagePath);
            }
            if (empty($imageNameNew)) { $imageNameNew = $imageName; }
        
            $updateQuery = "UPDATE product SET name = ?, description = ?, category = ?, price = ?, image_name = ? WHERE product_id = ?";
            $stmt = mysqli_prepare($conn, $updateQuery);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssdsi", $name, $description, $category, $price, $imageNameNew, $_GET["id"]);
                mysqli_stmt_execute($stmt);

                // Check if the update was successful
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $_SESSION["success"] = "Product updated successfully";
                    header("Location: ../admin");
                    exit();
                } else {
                    $_SESSION["editError"] = "Error updating product. Please try again later. Also make sure you edited the product.";
                    header("Location: edit?id=".$_GET["id"]);
                    exit();
                }

                // Close the statement
                mysqli_stmt_close($stmt);
            } else {
                $_SESSION["editError"] = "Error updating product. Please try again later.";
                header("Location: edit?id=".$_GET["id"]);
                exit();
            }
        }
    }
}

?>


<?php require '../templates/base_head.php';

require '../templates/admin_form.php';


// Close the database connection
mysqli_close($conn);

require '../templates/base_tail.php';
?>
