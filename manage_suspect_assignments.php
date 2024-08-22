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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
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
                // Get agent name
                $query = "SELECT AgentName FROM FieldAgents WHERE AgentID = '$agent_id'";
                $result = $conn->query($query);
                $agent_name = $result->fetch_assoc()["AgentName"];

                // Get assigned suspects for selected agent
                $query = "SELECT s.SuspectID, s.SuspectName FROM AgentAssignments aa JOIN Suspects s ON aa.SuspectID = s.SuspectID WHERE aa.AgentID = '$agent_id'";
                $result = $conn->query($query);
                $assigned_suspects = array();
                while ($row = $result->fetch_assoc()) {
                    $assigned_suspects[] = $row;
                }

                // Display assigned suspects
                echo "<h2>Assigned Suspects for Agent: $agent_name</h2>";
                if (empty($assigned_suspects)) {
                    echo "<p>No suspects assigned to this agent.</p>";
                } else {
                    echo "<form action='delete_suspect_assignments.php' method='post'>";
                    echo "<input type='hidden' name='agent_id' value='$agent_id'>";
                    echo "<ul>";
                    foreach ($assigned_suspects as $suspect) {
                        echo "<li><input type='checkbox' name='suspect_ids[]' value='" . $suspect["SuspectID"] . "'> " . $suspect["SuspectName"] . "</li>";
                    }
                    echo "</ul>";
                    echo "<button type='submit' class='btn btn-danger'>Delete Selected Assignments</button>";
                    echo "</form>";
                }

                // Display form to add new assignments
                echo "<form action='add_suspect_assignments.php' method='post'>";
                echo "<input type='hidden' name='agent_id' value='$agent_id'>";
                echo "<div class='form-group'>";
                echo "<label for='suspect_id'>Add New Suspect:</label>";
                echo "<select class='form-control' id='suspect_id' name='suspect_id'>";
                echo "<option value=''>Select Suspect</option>";
                foreach ($suspects as $suspect) {
                    echo "<option value='" . $suspect["SuspectID"] . "'>" . $suspect["SuspectName"] . "</option>";
                }
                echo "</select>";
                echo "</div>";
                echo "<button type='submit' class='btn btn-primary'>Add New Assignment</button>";
                echo "</form>";
            } elseif (!empty($suspect_id)) {
                // Get suspect name
                $query = "SELECT SuspectName FROM Suspects WHERE SuspectID = '$suspect_id'";
                $result = $conn->query($query);
                $suspect_name = $result->fetch_assoc()["SuspectName"];

                // Get assigned agents for selected suspect
                $query = "SELECT a.AgentID, a.AgentName FROM AgentAssignments aa JOIN FieldAgents a ON aa.AgentID = a.AgentID WHERE aa.SuspectID = '$suspect_id'";
                $result = $conn->query($query);
                $assigned_agents = array();
                while ($row = $result->fetch_assoc()) {
                    $assigned_agents[] = $row;
                }

                // Display assigned agents
                echo "<h2>Assigned Agents for Suspect: $suspect_name</h2>";
                if (empty($assigned_agents)) {
                    echo "<p>No agents assigned to this suspect.</p>";
                } else {
                    echo "<form action='delete_agent_assignments.php' method='post'>";
                    echo "<input type='hidden' name='suspect_id' value='$suspect_id'>";
                    echo "<ul>";
                    foreach ($assigned_agents as $agent) {
                        echo "<li><input type='checkbox' name='agent_ids[]' value='" . $agent["AgentID"] . "'> " . $agent["AgentName"] . "</li>";
                    }
                    echo "</ul>";
                    echo "<button type='submit' class='btn btn-danger'>Delete Selected Assignments</button>";
                    echo "</form>";
                }

                // Display form to add new assignments
                echo "<form action='add_agent_assignments.php' method='post'>";
                echo "<input type='hidden' name='suspect_id' value='$suspect_id'>";
                echo "<div class='form-group'>";
                echo "<label for='agent_id'>Add New Agent:</label>";
                echo "<select class='form-control' id='agent_id' name='agent_id'>";
                echo "<option value=''>Select Agent</option>";
                foreach ($agents as $agent) {
                    echo "<option value='" . $agent["AgentID"] . "'>" . $agent["AgentName"] . "</option>";
                }
                echo "</select>";
                echo "</div>";
                echo "<button type='submit' class='btn btn-primary'>Add New Assignment</button>";
                echo "</form>";
            }
        }
        ?>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#agent_id').select2();
            $('#suspect_id').select2();
        });
    </script>
</body>
</html>