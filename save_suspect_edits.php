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

// Get the suspect ID from the form
$suspect_id = $_POST['suspect_id'];

// Update suspect information
$query = "UPDATE Suspects SET SuspectName = ?, Alias = ?, DateOfBirth = ?, Gender = ?, Nationality = ?, Description = ?, LastKnownLocation = ? WHERE SuspectID = ?";

// Prepare statement
$stmt = $conn->prepare($query);

// Bind parameters
$stmt->bind_param("sssssssi", $_POST['suspect_name'], $_POST['alias'], $_POST['date_of_birth'], $_POST['gender'], $_POST['nationality'], $_POST['description'], $_POST['last_known_location'], $suspect_id);

// Execute statement
$stmt->execute();

// Check if query was successful
if ($stmt->affected_rows === 1) {
    echo "Suspect information updated successfully.";
} else {
    echo "Failed to update suspect information.";
}

// Close statement
$stmt->close();

// Close connection
$conn->close();

// Redirect back to view suspect page
header("Location: view_suspect.php?id=$suspect_id");
?>