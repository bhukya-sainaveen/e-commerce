

<?php
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
session_start();

// Check if the user is already logged in
if (isset($_SESSION["name"])) {
    // Redirect the user to the home page or some other authenticated area
    header("Location: ../");
    exit();
}

// Check for error in the session
if (isset($_SESSION['registrationError'])) {
    $registrationError = $_SESSION['registrationError'];
    // Display the error message as needed
    echo '<div class="alert alert-danger" role="alert" style="text-align: center;">';
    echo $registrationError; 
    echo '</div>';

    // Clear the error message from the session
    unset($_SESSION['registrationError']);
}

// Include your database configuration file (db_config.php)
require '../services/db_config.php';

// Process the registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize user inputs
    $name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Check if the email already exists
    $checkEmailQuery = "SELECT * FROM customer WHERE email = ?";
    if ($checkEmailStmt = mysqli_prepare($conn, $checkEmailQuery)) {
        mysqli_stmt_bind_param($checkEmailStmt, "s", $email);
        mysqli_stmt_execute($checkEmailStmt);
        mysqli_stmt_store_result($checkEmailStmt);

        if (mysqli_stmt_num_rows($checkEmailStmt) > 0) {
            // Email already exists, set an error message and exit
            $registrationError = "Email already exists. Please login.";
            mysqli_stmt_close($checkEmailStmt);
            mysqli_close($conn);
            $_SESSION['registrationError'] = $registrationError;
            header("Location: register");
            exit();
        }

        // Close the statement
        mysqli_stmt_close($checkEmailStmt);
    }

    // Perform registration logic
    $sql = "INSERT INTO customer (name, email, password) VALUES (?, ?, ?)";
    // Using prepared statements to prevent SQL injection
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $password);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Registration successful, set the session variable and redirect
            $_SESSION["name"] = $name;
            $_SESSION["email"] = $email;
            $_SESSION["customer_id"] = mysqli_insert_id($conn);
            // Regenerate the session ID
            session_regenerate_id(true);
            header("Location: ../");
            exit();
        } else {
            $registrationError = "Registration failed. Please try again.";
            $_SESSION['registrationError'] = $registrationError;
            header("Location: register");
            exit();
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    }
}

// Close the database connection
mysqli_close($conn);
?>


<?php require '../templates/base_head.php';?>

<div class="text-center" style="margin:10px auto;width:300px">
    <h1 class="text-secondary">E-commerce Demo</h1>
    <div class="container px-lg-5" style="border-style:groove;border-color:#1F97FF;">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validateForm()">
            <div class="form-group">
                <br>
                <label for="name">Name</label>
                <input type="name" class="form-control" id="name" name="name" aria-describedby="name" placeholder="Name" autocomplete="name">
                <span id="nameError" class="text-danger"></span>
            </div>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter email" autocomplete="email">
                <span id="emailError" class="text-danger"></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" autocomplete="current-password">
                <span id="passwordError" class="text-danger"></span>
            </div>
            <button type="submit" class="btn btn-info form-control">Register</button><br><br>
            <div class="text-dark">Already have an account <a href="login"> Log In</a></div>
            <br>
        </form>
    </div>

    <script>
        function validateForm() {
            // Reset error messages
            document.getElementById("nameError").innerHTML = "";
            document.getElementById("emailError").innerHTML = "";
            document.getElementById("passwordError").innerHTML = "";

            // Get values from the form
            var name = document.getElementById("name").value;
            var email = document.getElementById("email").value;
            var password = document.getElementById("password").value;
            check_name = true;
            check_email = true;
            check_password = true;

            // Name validation
            if (name.trim() === "" || name.length < 3) {
                document.getElementById("nameError").innerHTML = "Name must be at least 3 characters long";
                check_name = false;
            }

            // Email validation
            if (email.trim() === "") {
                document.getElementById("emailError").innerHTML = "Email is required";
                check_email =  false;
            } else {
                // Regular expression for a valid email format
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    document.getElementById("emailError").innerHTML = "Enter a valid email address";
                    check_email = false;
                }
            }

            // Password validation
            if (password.trim() === "") {
                document.getElementById("passwordError").innerHTML = "Password is required";
                check_password = false;
            } else {
                // Regular expression for a password with at least 8 characters, including a number
                var passwordRegex = /^(?=.*\d).{8,}$/;
                if (!passwordRegex.test(password)) {
                    document.getElementById("passwordError").innerHTML = "Password must be at least 8 characters long and include a number";
                    check_password = false;
                }
            }

            return check_name && check_email && check_password; // Form is valid, continue with submission
        }
    </script>
</div>

<?php require '../templates/base_tail.php';?>
