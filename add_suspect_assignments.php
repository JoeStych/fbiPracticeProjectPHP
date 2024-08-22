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

// Get the agent ID and suspect ID from the form
$agent_id = $_POST["agent_id"];
$suspect_id = $_POST["suspect_id"];

// Add the assignment to the database
$query = "INSERT INTO AgentAssignments (AgentID, SuspectID) VALUES ('$agent_id', '$suspect_id')";
if ($conn->query($query) === TRUE) {
    echo "Assignment added successfully";
} else {
    echo "Error adding assignment: " . $conn->error;
}

$conn->close();
?>