<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fbi";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$username = "";
$password = "";
$confirm_password = "";
$agency_code = "";
$error = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $agency_code = $_POST["agency_code"];

    // Check if all fields are not empty
    if (empty($username) || empty($password) || empty($confirm_password) || empty($agency_code)) {
        $error = "Please fill out all fields";
    } elseif ($password != $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if username already exists
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $error = "Username already exists";
        } else {
            // Check if agency code is valid
            $query = "SELECT * FROM agency_codes WHERE code = '$agency_code'";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                // Encrypt password and insert into database
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $query = "INSERT INTO users (username, password, agency_code) VALUES ('$username', '$hashed_password', '$agency_code')";
                $conn->query($query);

                // Redirect to login page
                header("Location: login.php");
                exit;
            } else {
                $error = "Invalid agency code";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 300px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            width: 120px;
            float: left;
            margin-top: 10px; /* Add this line to add space between labels */
        }
        input[type="text"], input[type="password"] {
            width: 150px;
            float: left;
            margin-top: 10px; /* Add this line to add space between input fields */
        }
        br {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" value="<?php echo $username; ?>"><br>
            <label for="password">Password:</label>
            <input type="password" name="password"><br>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password"><br>
            <label for="agency_code">Agency Code:</label>
            <input type="text" name="agency_code" value="<?php echo $agency_code; ?>"><br>
            <input type="submit" value="Register" style="margin-top: 20px;">
        </form>
        <p><?php echo $error; ?></p>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>