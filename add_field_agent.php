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
    $agent_name = $_POST["agent_name"];
    $agency = $_POST["agency"];
    $date_of_birth = $_POST["date_of_birth"];
    $city_id = $_POST["city_id"];

    // Validate input
    if (empty($agent_name) || empty($agency) || empty($date_of_birth) || empty($city_id)) {
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
            // Insert new field agent into database
            $query = "INSERT INTO FieldAgents (AgentName, Agency, DateOfBirth, CityID) VALUES ('$agent_name', '$agency', '$date_of_birth', '$city_id')";
            if ($conn->query($query) === TRUE) {
                $success = "Field agent added successfully.";
            } else {
                $error = "Error adding field agent: " . $conn->error;
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
    <title>Add Field Agent</title>
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
        <h1>Add Field Agent</h1>
        <a href="manage_field_agents.php" class="btn btn-secondary">Back</a>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } elseif (isset($success)) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="form-group">
                <label for="agent_name">Agent Name:</label>
                <input type="text" class="form-control" id="agent_name" name="agent_name">
            </div>
            <div class="form-group">
                <label for="agency">Agency:</label>
                <input type="text" class="form-control" id="agency" name="agency">
            </div>
            <div class="form-group">
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
            </div>
            <div class="form-group">
                <label for="city_id">City:</label>
                <select class="form-control" id="city_id" name="city_id">
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <option value="<?php echo $row["CityID"]; ?>"><?php echo $row["CityName"]; ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Field Agent</button>
        </form>
    </div>
</body>
</html>