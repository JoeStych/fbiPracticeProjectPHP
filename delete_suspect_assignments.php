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

// Get the agent ID and suspect IDs from the form
$agent_id = $_POST["agent_id"];
$suspect_ids = $_POST["suspect_ids"];

// Delete the assignments from the database
foreach ($suspect_ids as $suspect_id) {
    $query = "DELETE FROM AgentAssignments WHERE AgentID = '$agent_id' AND SuspectID = '$suspect_id'";
    if ($conn->query($query) === TRUE) {
        echo "Assignment deleted successfully";
    } else {
        echo "Error deleting assignment: " . $conn->error;
    }
}

$conn->close();
?>