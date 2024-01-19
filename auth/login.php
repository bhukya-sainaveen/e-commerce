<?php
session_start();

if (isset($_GET['redirect'])) {
    $_SESSION['redirect'] = $_GET['redirect'];
}

// Check if the user is already logged in
if (isset($_SESSION["name"])) {
    // Redirect the user to the home page or some other authenticated area
    $redirect = isset($_SESSION['redirect']) ? $_SESSION['redirect'] : '../';
    unset($_SESSION['redirect']);

    // Redirect the user back to the original page or to the specified redirect parameter
    header("Location: ".$redirect);
    exit();
}

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
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];

    // Perform login logic
    $sql = "SELECT customer_id, name, password FROM customer WHERE email = ?";

    // Using prepared statements to prevent SQL injection
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Bind the result variables
            mysqli_stmt_bind_result($stmt, $customerId, $name, $hashedPassword);

            // Fetch the result
            if (mysqli_stmt_fetch($stmt)) {

                // Verify the password
                if (password_verify($password, $hashedPassword)) {
                    // Login successful, set the session variable and redirect
                    $_SESSION["name"] = $name;
                    $_SESSION["customer_id"] = $customerId;
                    // Check if a redirect parameter is set
                    $redirect = isset($_SESSION['redirect']) ? $_SESSION['redirect'] : '../';
                    unset($_SESSION['redirect']);
                    // Regenerate the session ID
                    session_regenerate_id(true);
                    // Redirect the user back to the original page or to the specified redirect parameter
                    header("Location: ".$redirect);
                    exit();
                } else {
                    $loginError = "Invalid password. Please try again.";
                }
            } else {
                $loginError = "Email does not exist. Please register.";
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
    <h1 class="text-secondary">E-commerce Demo</h1>
    <div class="container px-lg-5" style="border-style:groove;border-color:#1F97FF;">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validateForm()">
            <div class="form-group">
                <br>
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter email" autocomplete="email">
                <span id="emailError" class="text-danger"></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" autocomplete="current-password">
                <span id="passwordError" class="text-danger"></span>
            </div>
            <button type="submit" class="btn btn-info form-control">Log In</button><br><br>
            <div class="text-dark"> New User? <a href="register"> Register</a></div>
            <br>
        </form>
    </div>

    <script>
        function validateForm() {
            // Reset error messages
            document.getElementById("emailError").innerHTML = "";
            document.getElementById("passwordError").innerHTML = "";
            email_check = true;
            password_check = true;

            // Get values from the form
            var email = document.getElementById("email").value;
            var password = document.getElementById("password").value;

            // Email validation
            if (email.trim() === "") {
                document.getElementById("emailError").innerHTML = "Email is required";
                email_check = false;
            } else {
                // Regular expression for a valid email format
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    document.getElementById("emailError").innerHTML = "Enter a valid email address";
                    email_check = false;
                }
            }

            // Password validation
            if (password.trim() === "") {
                document.getElementById("passwordError").innerHTML = "Password is required";
                password_check = false;
            } else {
                // Regular expression for a password with at least 8 characters, including a number
                var passwordRegex = /^(?=.*\d).{8,}$/;
                if (!passwordRegex.test(password)) {
                    document.getElementById("passwordError").innerHTML = "Password must be at least 8 characters long and include a number";
                    password_check = false;
                }
            }

            return email_check && password_check; // Form is valid, continue with submission
        }
    </script>

</div>


<?php require '../templates/base_tail.php';?>