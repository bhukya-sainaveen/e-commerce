<?php
session_start();

// Check if admin is not logged in, redirect to login page
if (!isset($_SESSION["name"]) or $_SESSION["name"] != "admin") {
    header("Location: login");
    exit();
}

// Check for error in the session
if (isset($_SESSION['addError'])) {
    $addError = $_SESSION['addError'];
    // Display the error message as needed
    echo '<div class="alert alert-danger" role="alert" style="text-align: center;">';
    echo $addError; 
    echo '</div>';

    // Clear the error message from the session
    unset($_SESSION['addError']);
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
        // Process the image upload
        $uploadDir = "../assets/";
        $imagePath = $uploadDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
        $imageName = basename($_FILES["image"]["name"]);
        if (empty($imageName)) { $imageName = "no_image.png"; }

        // Insert data into the 'product' table using prepared statement
        $insertQuery = "INSERT INTO product (name, description, category, price, image_name) VALUES (?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $insertQuery);

        if ($stmt) {
            // Bind parameters to the prepared statement
            mysqli_stmt_bind_param($stmt, "sssds", $name, $description, $category, $price, $imageName);

            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                // Success, you can redirect or display a success message here
                // header("Location: success.php");
                // exit();
                $_SESSION["success"]  = "Product added successfully";
                header("Location: edit");
                exit();
            } else {
                // Display an error message if the execution fails
                $_SESSION["addError"] =  "Error adding product. Please try again later.";
                header("Location: edit");
                exit();
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            // Display an error message if the statement preparation fails
            $_SESSION["addError"] =  "Error adding product. Please try again later.";
            header("Location: edit");
            exit();
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
