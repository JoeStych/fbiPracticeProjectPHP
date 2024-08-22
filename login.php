<?php
session_start();
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
$error = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if username and password are not empty
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        try {
            // Check if username and password are correct
            $query = "SELECT * FROM users WHERE username = '$username'";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row["password"])) {
                    // Login successful, redirect to protected page
		    
    		    $_SESSION["username"] = $username;
                    header("Location: protected.php");
                    exit;
                } else {
                    $error = "Invalid username or password";
                }
            } else {
                $error = "Invalid username or password";
            }
        } catch (mysqli_sql_exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" value="<?php echo $username; ?>"><br><br>
            <label for="password">Password:</label>
            <input type="password" name="password"><br><br>
            <input type="submit" value="Login">
        </form>
        <p><?php echo $error; ?></p>
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>
</body>
</html>