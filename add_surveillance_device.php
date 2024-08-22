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
    $device_type = $_POST["device_type"];
    $installation_date = $_POST["installation_date"];
    $location = $_POST["location"];
    $city_id = $_POST["city_id"];

    // Validate input
    if (empty($device_type) || empty($installation_date) || empty($location) || empty($city_id)) {
        $error = "Please fill in all fields.";
    } elseif (!is_numeric($city_id)) {
        $error = "City ID must be a number.";
    } else {
        // Check if city ID exists
        $query = "SELECT * FROM Cities WHERE CityID = '$city_id'";
        $result = $conn->query($query);
        if ($result->num_rows == 0) {
            $error = "City ID does not exist.";
        } else {
            // Insert new surveillance device into database
            $query = "INSERT INTO SurveillanceDevices (DeviceType, InstallationDate, Location, CityID) VALUES ('$device_type', '$installation_date', '$location', '$city_id')";
            if ($conn->query($query) === TRUE) {
                $success = "Surveillance device added successfully.";
            } else {
                $error = "Error adding surveillance device: " . $conn->error;
            }
        }
    }
}

// Get all cities
$query = "SELECT * FROM Cities";
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
    <title>Add Surveillance Device</title>
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
        <h1>Add Surveillance Device</h1>
        <a href="manage_surveillance_devices.php" class="btn btn-secondary">Back</a>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } elseif (isset($success)) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="form-group">
                <label for="device_type">Device Type:</label>
                <input type="text" class="form-control" id="device_type" name="device_type">
            </div>
            <div class="form-group">
                <label for="installation_date">Installation Date:</label>
                <input type="date" class="form-control" id="installation_date" name="installation_date">
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" class="form-control" id="location" name="location">
            </div>
            <div class="form-group">
                <label for="city_id">City:</label>
                <select class="form-control" id="city_id" name="city_id">
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <option value="<?php echo $row["CityID"]; ?>"><?php echo $row["CityName"]; ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Surveillance Device</button>
        </form>
    </div>
</body>
</html>