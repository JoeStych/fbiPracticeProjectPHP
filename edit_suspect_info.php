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
    <title>Edit Suspect Information</title>
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
        <h1>Edit Suspect Information</h1>
        <form action="save_suspect_edits.php" method="post">
            <input type="hidden" name="suspect_id" value="<?php echo $suspect_id; ?>">
            <div class="card">
                <div class="card-header">Suspect Details</div>
                <div class="card-body">
                    <label for="suspect_name">Suspect Name:</label>
                    <input type="text" id="suspect_name" name="suspect_name" value="<?php echo $suspect["SuspectName"]; ?>"><br><br>
                    <label for="alias">Alias:</label>
                    <input type="text" id="alias" name="alias" value="<?php echo $suspect["Alias"]; ?>"><br><br>
                    <label for="date_of_birth">Date of Birth:</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo $suspect["DateOfBirth"]; ?>"><br><br>
                    <label for="gender">Gender:</label>
                    <input type="text" id="gender" name="gender" value="<?php echo $suspect["Gender"]; ?>"><br><br>
                    <label for="nationality">Nationality:</label>
                    <input type="text" id="nationality" name="nationality" value="<?php echo $suspect["Nationality"]; ?>"><br><br>
                    <label for="description">Description:</label>
                    <textarea id="description" name="description"><?php echo $suspect["Description"]; ?></textarea><br><br>
                    <label for="last_known_location">Last Known Location:</label>
                    <input type="text" id="last_known_location" name="last_known_location" value="<?php echo $suspect["LastKnownLocation"]; ?>"><br><br>
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sightings_table">
                            <?php foreach ($sightings as $sighting) { ?>
                                <tr>
                                    <td><input type="datetime-local" name="sighting_time[]" value="<?php echo date('Y-m-d\TH:i', strtotime($sighting["SightingTime"])); ?>"></td>
                                    <td><input type="text" name="sighting_location[]" value="<?php echo $sighting["SightingLocation"]; ?>"></td>
                                    <td>
                                        <select name="device_id[]">
                                            <?php
                                            // Query to select all devices
                                            $device_query = "SELECT * FROM SurveillanceDevices";
                                            $device_result = $conn->query($device_query);
                                            while ($device = $device_result->fetch_assoc()) {
                                                if ($device["DeviceID"] == $sighting["DeviceID"]) {
                                                    echo "<option value='" . $device["DeviceID"] . "' selected>" . $device["DeviceType"] . "</option>";
                                                } else {
                                                    echo "<option value='" . $device["DeviceID"] . "'>" . $device["DeviceType"] . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td><textarea name="sighting_description[]"><?php echo $sighting["Description"]; ?></textarea></td>
                                    <td><button type="button" class="btn btn-danger" onclick="deleteRow(this)">Delete</button></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary" onclick="addRow()">Add New Sighting</button>
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="vehicles_table">
                            <?php foreach ($vehicles as $vehicle) { ?>
                                <tr>
                                    <td><input type="text" name="vehicle_make[]" value="<?php echo $vehicle["VehicleMake"]; ?>"></td>
                                    <td><input type="text" name="vehicle_model[]" value="<?php echo $vehicle["VehicleModel"]; ?>"></td>
                                    <td><input type="text" name="license_plate[]" value="<?php echo $vehicle["LicensePlate"]; ?>"></td>
                                    <td><input type="text" name="last_seen_location[]" value="<?php echo $vehicle["LastSeenLocation"]; ?>"></td>
                                    <td><input type="datetime-local" name="last_seen_time[]" value="<?php echo date('Y-m-d\TH:i', strtotime($vehicle["LastSeenTime"])); ?>"></td>
                                    <td><button type="button" class="btn btn-danger" onclick="deleteRow(this)">Delete</button></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary" onclick="addVehicleRow()">Add New Vehicle</button>
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="assignments_table">
                            <?php foreach ($assignments as $assignment) { ?>
                                <tr>
                                    <td>
                                        <select name="agent_id[]">
                                            <?php
                                            // Query to select all agents
                                            $agent_query = "SELECT * FROM FieldAgents";
                                            $agent_result = $conn->query($agent_query);
                                            while ($agent = $agent_result->fetch_assoc()) {
                                                if ($agent["AgentID"] == $assignment["AgentID"]) {
                                                    echo "<option value='" . $agent["AgentID"] . "' selected>" . $agent["AgentName"] . "</option>";
                                                } else {
                                                    echo "<option value='" . $agent["AgentID"] . "'>" . $agent["AgentName"] . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <?php
                                        // Get the agency from the FieldAgents table
                                        $agent_query = "SELECT Agency FROM FieldAgents WHERE AgentID = '$assignment[AgentID]'";
                                        $agent_result = $conn->query($agent_query);
                                        $agency = $agent_result->fetch_assoc()["Agency"];
                                        echo $agency;
                                        ?>
                                    </td>
                                    <td><input type="datetime-local" name="start_time[]" value="<?php echo date('Y-m-d\TH:i', strtotime($assignment["StartTime"])); ?>"></td>
                                    <td><input type="datetime-local" name="end_time[]" value="<?php echo date('Y-m-d\TH:i', strtotime($assignment["EndTime"])); ?>"></td>
                                    <td><button type="button" class="btn btn-danger" onclick="deleteRow(this)">Delete</button></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary" onclick="addAssignmentRow()">Add New Assignment</button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="view_suspect.php?id=<?php echo $suspect_id; ?>" class="btn btn-secondary">Back</a>
        </form>
    </div>
    <script>
        function addRow() {
            var table = document.getElementById("sightings_table");
            var row = table.insertRow(-1);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            cell1.innerHTML = "<input type='datetime-local' name='sighting_time[]'>";
            cell2.innerHTML = "<input type='text' name='sighting_location[]'>";
            cell3.innerHTML = "<select name='device_id[]'><?php
            // Query to select all devices
            $device_query = "SELECT * FROM SurveillanceDevices";
            $device_result = $conn->query($device_query);
            while ($device = $device_result->fetch_assoc()) {
                echo "<option value='" . $device["DeviceID"] . "'>" . $device["DeviceType"] . "</option>";
            }
            ?></select>";
            cell4.innerHTML = "<textarea name='sighting_description[]'></textarea>";
            cell5.innerHTML = "<button type='button' class='btn btn-danger' onclick='deleteRow(this)'>Delete</button>";
        }

        function addVehicleRow() {
            var table = document.getElementById("vehicles_table");
            var row = table.insertRow(-1);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            cell1.innerHTML = "<input type='text' name='vehicle_make[]'>";
            cell2.innerHTML = "<input type='text' name='vehicle_model[]'>";
            cell3.innerHTML = "<input type='text' name='license_plate[]'>";
            cell4.innerHTML = "<input type='text' name='last_seen_location[]'>";
            cell5.innerHTML = "<input type='datetime-local' name='last_seen_time[]'>";
            cell6.innerHTML = "<button type='button' class='btn btn-danger' onclick='deleteRow(this)'>Delete</button>";
        }

        function addAssignmentRow() {
            var table = document.getElementById("assignments_table");
            var row = table.insertRow(-1);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            cell1.innerHTML = "<select name='agent_id[]'><?php
            // Query to select all agents
            $agent_query = "SELECT * FROM FieldAgents";
            $agent_result = $conn->query($agent_query);
            while ($agent = $agent_result->fetch_assoc()) {
                echo "<option value='" . $agent["AgentID"] . "'>" . $agent["AgentName"] . "</option>";
            }
            ?></select>";
            cell2.innerHTML = "";
            cell3.innerHTML = "<input type='datetime-local' name='start_time[]'>";
            cell4.innerHTML = "<input type='datetime-local' name='end_time[]'>";
            cell5.innerHTML = "<button type='button' class='btn btn-danger' onclick='deleteRow(this)'>Delete</button>";
        }

        function deleteRow(button) {
            var row = button.parentNode.parentNode;
            row.parentNode.removeChild(row);
        }
    </script>
</body>
</html>