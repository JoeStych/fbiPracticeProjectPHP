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

// Get the suspect ID from the URL
$suspect_id = $_GET['id'];

// Query to select suspect information
$query = "SELECT * FROM Suspects WHERE SuspectID = '$suspect_id'";

// Execute query
$result = $conn->query($query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Get the suspect information
$suspect = $result->fetch_assoc();

// Query to select suspect sightings
$query = "SELECT * FROM SuspectSightings WHERE SuspectID = '$suspect_id' ORDER BY SightingTime DESC";

// Execute query
$result = $conn->query($query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Get the suspect sightings
$sightings = $result->fetch_all(MYSQLI_ASSOC);

// Query to select suspect vehicles
$query = "SELECT * FROM SuspectVehicles WHERE SuspectID = '$suspect_id' ORDER BY LastSeenTime DESC";

// Execute query
$result = $conn->query($query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Get the suspect vehicles
$vehicles = $result->fetch_all(MYSQLI_ASSOC);

// Query to select agent assignments
$query = "SELECT * FROM AgentAssignments WHERE SuspectID = '$suspect_id'";

// Execute query
$result = $conn->query($query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Get the agent assignments
$assignments = $result->fetch_all(MYSQLI_ASSOC);

// Display suspect information
?>
<!DOCTYPE html>
<html>
<head>
    <title>Suspect Information</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .card {
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #007bff;
            color: #ffffff;
        }
        .card-body {
            padding: 20px;
        }
        .table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Suspect Information</h1>
        <a href="manage_suspects.php?search_term=&search_by=name" class="btn btn-secondary">Back</a>
        <a href="edit_suspect_info.php?id=<?php echo $suspect_id; ?>" class="btn btn-primary">Edit Information</a>
        <div class="card">
            <div class="card-header">Suspect Details</div>
            <div class="card-body">
                <h2><?php echo $suspect["SuspectName"]; ?></h2>
                <p>Alias: <?php echo $suspect["Alias"]; ?></p>
                <p>Nationality: <?php echo $suspect["Nationality"]; ?></p>
                <p>Date of Birth: <?php echo $suspect["DateOfBirth"]; ?></p>
                <p>Gender: <?php echo $suspect["Gender"]; ?></p>
                <p>Description: <?php echo $suspect["Description"]; ?></p>
                <p>Last Known Location: <?php echo $suspect["LastKnownLocation"]; ?></p>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Suspect Sightings</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Sighting Time</th>
                            <th>Sighting Location</th>
                            <th>Device Type</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Sort sightings by latest date at the top
                        usort($sightings, function($a, $b) {
                            return strtotime($b["SightingTime"]) - strtotime($a["SightingTime"]);
                        });
                        foreach ($sightings as $sighting) { ?>
                            <tr>
                                <td><?php echo $sighting["SightingTime"]; ?></td>
                                <td><?php echo $sighting["SightingLocation"]; ?></td>
                                <td><?php
                                // Get the device type from the SurveillanceDevices table
                                $device_query = "SELECT DeviceType FROM SurveillanceDevices WHERE DeviceID = '$sighting[DeviceID]'";
                                $device_result = $conn->query($device_query);
                                $device_type = $device_result->fetch_assoc()["DeviceType"];
                                echo $device_type;
                                ?></td>
                                <td><?php echo $sighting["Description"]; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Suspect Vehicles</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Vehicle Make</th>
                            <th>Vehicle Model</th>
                            <th>License Plate</th>
                            <th>Last Seen Location</th>
                            <th>Last Seen Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Sort vehicles by latest date at the top
                        usort($vehicles, function($a, $b) {
                            return strtotime($b["LastSeenTime"]) - strtotime($a["LastSeenTime"]);
                        });
                        foreach ($vehicles as $vehicle) { ?>
                            <tr>
                                <td><?php echo $vehicle["VehicleMake"]; ?></td>
                                <td><?php echo $vehicle["VehicleModel"]; ?></td>
                                <td><?php echo $vehicle["LicensePlate"]; ?></td>
                                <td><?php echo $vehicle["LastSeenLocation"]; ?></td>
                                <td><?php echo $vehicle["LastSeenTime"]; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Agent Assignments</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Agent Name</th>
                            <th>Agency</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignments as $assignment) { ?>
                            <tr>
                                <td><?php echo $assignment["AgentName"]; ?></td>
                                <td><?php
                                // Get the agency from the FieldAgents table
                                $agent_query = "SELECT Agency FROM FieldAgents WHERE AgentID = '$assignment[AgentID]'";
                                $agent_result = $conn->query($agent_query);
                                $agency = $agent_result->fetch_assoc()["Agency"];
                                echo $agency;
                                ?></td>
                                <td><?php echo $assignment["StartTime"]; ?></td>
                                <td><?php echo $assignment["EndTime"]; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>