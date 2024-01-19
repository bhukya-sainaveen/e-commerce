<?php

session_start();



// Check for error in the session
if (isset($_SESSION['loginError'])) {
    $loginError = $_SESSION['loginError'];
    // Display the error message as needed
    echo '<div class="alert alert-danger" role="alert" style="text-align: center;">';
    echo $loginError; 
    echo '</div>';

    // Clear the error message from the session
    unset($_SESSION['loginError']);
}


// Process the login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include your database configuration file (db_config.php)
    require '../services/db_config.php';
    // Validate and sanitize user inputs
    $name = $_POST["name"];
    $password = $_POST["password"];

    // Perform login logic
    $sql = "SELECT admin_name, password FROM admin WHERE admin_name = ?";

    // Using prepared statements to prevent SQL injection
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $name);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Bind the result variables
            mysqli_stmt_bind_result($stmt, $name, $hashedPassword);

            // Fetch the result
            if (mysqli_stmt_fetch($stmt)) {

                // Verify the password
                if ($password == $hashedPassword) {
                    // Login successful, set the session variable and redirect
                    $_SESSION["name"] = $name;
                    header("Location: ../admin");
                    exit();
                } else {
                    $loginError = "Invalid password. Please try again.";
                }
            } else {
                $loginError = "Admin name does not exist. Please contact website owner.";
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($conn);

    // Set an error message and redirect back to the login page
    $_SESSION['loginError'] = $loginError;
    header("Location: login");
    exit();
}



?>

<?php require '../templates/base_head.php';?>

<div class="text-center" style="margin:10px auto;width:300px">
    <h1 class="text-secondary">E-commerce Admin Login</h1>
    <div class="container px-lg-5" style="border-style:groove;border-color:#1F97FF;">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validateForm()">
            <div class="form-group">
                <br>
                <label for="name">Admin Name</label>
                <input type="name" class="form-control" id="name" name="name" aria-describedby="nameHelp" placeholder="Enter name" autocomplete="name">
                <span id="nameError" class="text-danger"></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" autocomplete="current-password">
                <span id="passwordError" class="text-danger"></span>
            </div>
            <button type="submit" class="btn btn-info form-control">Log In</button><br><br>
            <div class="text-dark"> Not Admin? <a href="../auth/login"> User Login</a></div>
            <br>
        </form>
    </div>

    <script>
        function validateForm() {
            // Reset error messages
            document.getElementById("nameError").innerHTML = "";
            document.getElementById("passwordError").innerHTML = "";
            name_check = true;
            password_check = true;

            // Get values from the form
            var name = document.getElementById("name").value;
            var password = document.getElementById("password").value;

            // name validation
            if (name.trim() === "") {
                document.getElementById("nameError").innerHTML = "name is required";
                name_check = false;
            }

            // Password validation
            if (password.trim() === "") {
                document.getElementById("passwordError").innerHTML = "Password is required";
                password_check = false;
            }

            return name_check && password_check; // Form is valid, continue with submission
        }
    </script>

</div>


<?php require '../templates/base_tail.php';?>