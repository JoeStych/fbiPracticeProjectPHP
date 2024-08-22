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

// Get all agents from database
$query = "SELECT * FROM FieldAgents";
$result = $conn->query($query);
$agents = array();
while ($row = $result->fetch_assoc()) {
    $agents[] = $row;
}

// Get all suspects from database
$query = "SELECT * FROM Suspects";
$result = $conn->query($query);
$suspects = array();
while ($row = $result->fetch_assoc()) {
    $suspects[] = $row;
}

// Display form
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Suspect Assignments</title>
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
        <h1>Manage Suspect Assignments</h1>
        <a href="protected.php" class="btn btn-secondary">Back</a>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="form-group">
                <label for="agent_id">Select Agent:</label>
                <select class="form-control" id="agent_id" name="agent_id">
                    <option value="">Select Agent</option>
                    <?php foreach ($agents as $agent) { ?>
                        <option value="<?php echo $agent["AgentID"]; ?>"><?php echo $agent["AgentName"]; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="suspect_id">Select Suspect:</label>
                <select class="form-control" id="suspect_id" name="suspect_id">
                    <option value="">Select Suspect</option>
                    <?php foreach ($suspects as $suspect) { ?>
                        <option value="<?php echo $suspect["SuspectID"]; ?>"><?php echo $suspect["SuspectName"]; ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">View Assignments</button>
        </form>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $agent_id = $_POST["agent_id"];
            $suspect_id = $_POST["suspect_id"];

            if (!empty($agent_id)) {
                // Get assigned suspects for selected agent
                $query = "SELECT s.SuspectName FROM AgentAssignments aa JOIN Suspects s ON aa.SuspectID = s.SuspectID WHERE aa.AgentID = '$agent_id'";
                $result = $conn->query($query);
                $assigned_suspects = array();
                while ($row = $result->fetch_assoc()) {
                    $assigned_suspects[] = $row["SuspectName"];
                }

                // Display assigned suspects
                echo "<h2>Assigned Suspects for Agent:</h2>";
                echo "<ul>";
                foreach ($assigned_suspects as $suspect) {
                    echo "<li>$suspect</li>";
                }
                echo "</ul>";

                // Display form to add or remove assignments
                echo "<form action='update_suspect_assignments.php' method='post'>";
                echo "<input type='hidden' name='agent_id' value='$agent_id'>";
                echo "<div class='form-group'>";
                echo "<label for='suspect_id'>Add or Remove Suspect:</label>";
                echo "<select class='form-control' id='suspect_id' name='suspect_id'>";
                echo "<option value=''>Select Suspect</option>";
                foreach ($suspects as $suspect) {
                    echo "<option value='" . $suspect["SuspectID"] . "'>" . $suspect["SuspectName"] . "</option>";
                }
                echo "</select>";
                echo "</div>";
                echo "<button type='submit' class='btn btn-primary'>Add or Remove Assignment</button>";
                echo "</form>";
            } elseif (!empty($suspect_id)) {
                // Get assigned agents for selected suspect
                $query = "SELECT a.AgentName FROM AgentAssignments aa JOIN FieldAgents a ON aa.AgentID = a.AgentID WHERE aa.SuspectID = '$suspect_id'";
                $result = $conn->query($query);
                $assigned_agents = array();
                while ($row = $result->fetch_assoc()) {
                    $assigned_agents[] = $row["AgentName"];
                }

                // Display assigned agents
                echo "<h2>Assigned Agents for Suspect:</h2>";
                echo "<ul>";
                foreach ($assigned_agents as $agent) {
                    echo "<li>$agent</li>";
                }
                echo "</ul>";

                // Display form to add or remove assignments
                echo "<form action='update_suspect_assignments.php' method='post'>";
                echo "<input type='hidden' name='suspect_id' value='$suspect_id'>";
                echo "<div class='form-group'>";
                echo "<label for='agent_id'>Add or Remove Agent:</label>";
                echo "<select class='form-control' id='agent_id' name='agent_id'>";
                echo "<option value=''>Select Agent</option>";
                foreach ($agents as $agent) {
                    echo "<option value='" . $agent["AgentID"] . "'>" . $agent["AgentName"] . "</option>";
                }
                echo "</select>";
                echo "</div>";
                echo "<button type='submit' class='btn btn-primary'>Add or Remove Assignment</button>";
                echo "</form>";
            }
        }
        ?>
    </div>
</body>
</html>