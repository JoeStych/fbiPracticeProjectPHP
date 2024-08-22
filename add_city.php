<?php
// Connect to database
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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $city_name = $_POST["city_name"];
    $country_id = $_POST["country_id"];

    // Validate input
    if (empty($city_name) || empty($country_id)) {
        $error = "Please fill in all fields.";
    } elseif (!is_numeric($country_id)) {
        $error = "Country ID must be a number.";
    } else {
        // Check if country ID exists
        $query = "SELECT * FROM Countries WHERE CountryID = '$country_id'";
        $result = $conn->query($query);
        if ($result->num_rows == 0) {
            $error = "Country ID does not exist.";
        } else {
            // Insert new city into database
            $query = "INSERT INTO Cities (CityName, CountryID) VALUES ('$city_name', '$country_id')";
            if ($conn->query($query) === TRUE) {
                $success = "City added successfully.";
            } else {
                $error = "Error adding city: " . $conn->error;
            }
        }
    }
}

// Get all countries
$query = "SELECT * FROM Countries";
$result = $conn->query($query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Display form
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add City</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
        .form-group {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add City</h1>
        <a href="manage_cities.php" class="btn btn-secondary">Back</a>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } elseif (isset($success)) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="form-group">
                <label for="city_name">City Name:</label>
                <input type="text" class="form-control" id="city_name" name="city_name">
            </div>
            <div class="form-group">
                <label for="country_id">Country:</label>
                <select class="form-control" id="country_id" name="country_id">
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <option value="<?php echo $row["CountryID"]; ?>"><?php echo $row["CountryName"]; ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add City</button>
        </form>
    </div>
</body>
</html>