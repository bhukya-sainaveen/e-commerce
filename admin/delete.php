<?php
session_start();

// Check if admin is not logged in, redirect to login page
if (!isset($_SESSION["name"]) or $_SESSION["name"] != "admin") {
    header("Location: login");
    exit();
}

// Include your database configuration file (db_config.php)
require '../services/db_config.php';

if (isset($_GET["id"])) {
    // Retrieve the image_name before deletion
    $getImageNameQuery = "SELECT image_name FROM product WHERE product_id = ?";
    $getImageNameStmt = mysqli_prepare($conn, $getImageNameQuery);

    if ($getImageNameStmt) {
        mysqli_stmt_bind_param($getImageNameStmt, "i", $_GET["id"]);
        mysqli_stmt_execute($getImageNameStmt);
        mysqli_stmt_bind_result($getImageNameStmt, $imageName);
        mysqli_stmt_fetch($getImageNameStmt);
        mysqli_stmt_close($getImageNameStmt);

        // Check the number of products before allowing deletion
        $countProductsQuery = "SELECT COUNT(*) FROM product";
        $countResult = mysqli_query($conn, $countProductsQuery);

        if ($countResult) {
            $rowCount = mysqli_fetch_array($countResult)[0];

            if ($rowCount <= 3) {
                $_SESSION["deleteError"] = "Cannot delete all the products. Minimum 3 products should be present for illustration. <br>Please add some products before deleting.";
                header("Location: ../admin");
                exit();
            }

            // Continue with the deletion if the number of products is greater than 3
            $deleteQuery = "DELETE FROM product WHERE product_id = ?";
            $stmt = mysqli_prepare($conn, $deleteQuery);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $_GET["id"]);

                mysqli_stmt_execute($stmt);

                // Check if the delete was successful
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $imagePath = "../assets/".$imageName;
                    if ($imageName != "no_image.png" && file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    $_SESSION["success"] = "Product deleted successfully";
                    header("Location: ../admin");
                    exit();
                } else {
                    $_SESSION["deleteError"] = "Error deleting product. Please try again later.";
                    header("Location: ../admin");
                    exit();
                }

                // Close the statement
                mysqli_stmt_close($stmt);
            } else {
                $_SESSION["deleteError"] = "Error deleting product. Please try again later.";
                header("Location: ../admin");
                exit();
            }
        } else {
            $_SESSION["deleteError"] = "Error retrieving the product info. Please try again later.";
            header("Location: ../admin");
            exit();
        }
    } else {
        $_SESSION["deleteError"] = "Error deleting image. Please try again later.";
        header("Location: ../admin");
        exit();
    }
}

// Close the database connection
mysqli_close($conn);
?>
