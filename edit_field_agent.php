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

// Get agent ID from URL
$agent_id = $_GET["id"];

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
        // Update agent information in database
        $query = "UPDATE FieldAgents SET AgentName = '$agent_name', Agency = '$agency', DateOfBirth = '$date_of_birth', CityID = '$city_id' WHERE AgentID = '$agent_id'";
        if ($conn->query($query) === TRUE) {
            $success = "Agent information updated successfully.";
        } else {
            $error = "Error updating agent information: " . $conn->error;
        }
    }
}

// Get agent information from database
$query = "SELECT * FROM FieldAgents WHERE AgentID = '$agent_id'";
$result = $conn->query($query);
$agent = $result->fetch_assoc();

// Get all cities from database
$query = "SELECT * FROM Cities";
$result = $conn->query($query);
$cities = array();
while ($row = $result->fetch_assoc()) {
    $cities[] = $row;
}

// Get assigned suspects from database
$query = "SELECT s.SuspectName FROM AgentAssignments aa JOIN Suspects s ON aa.SuspectID = s.SuspectID WHERE aa.AgentID = '$agent_id'";
$result = $conn->query($query);
$assigned_suspects = array();
while ($row = $result->fetch_assoc()) {
    $assigned_suspects[] = $row["SuspectName"];
}

// Display form
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Field Agent</title>
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
        <h1>Edit Field Agent</h1>
        <a href="manage_field_agents.php" class="btn btn-secondary">Back</a>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } elseif (isset($success)) { ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $agent_id; ?>" method="post">
            <div class="form-group">
                <label for="agent_name">Agent Name:</label>
                <input type="text" class="form-control" id="agent_name" name="agent_name" value="<?php echo $agent["AgentName"]; ?>">
            </div>
            <div class="form-group">
                <label for="agency">Agency:</label>
                <input type="text" class="form-control" id="agency" name="agency" value="<?php echo $agent["Agency"]; ?>">
            </div>
            <div class="form-group">
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo $agent["DateOfBirth"]; ?>">
            </div>
            <div class="form-group">
                <label for="city_id">City:</label>
                <select class="form-control" id="city_id" name="city_id">
                    <?php foreach ($cities as $city) { ?>
                        <option value="<?php echo $city["CityID"]; ?>" <?php if ($city["CityID"] == $agent["CityID"]) { echo "selected"; } ?>><?php echo $city["CityName"]; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Assigned Suspects:</label>
                <ul>
                    <?php foreach ($assigned_suspects as $suspect) { ?>
                        <li><?php echo $suspect; ?></li>
                    <?php } ?>
                </ul>
                <a href="#" class="btn btn-primary">Edit Suspect Assignments</a>
            </div>
            <button type="submit" class="btn btn-primary">Update Agent Information</button>
        </form>
    </div>
</body>
</html>