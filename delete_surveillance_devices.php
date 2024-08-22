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

try {
    // Begin transaction
    $conn->begin_transaction();

    // Get the list of surveillance devices to delete
    $devices = $_POST['devices'];

    // Delete the surveillance devices
    $query = "DELETE FROM SurveillanceDevices WHERE DeviceID IN ($devices)";
    $conn->query($query);

    // Commit transaction
    $conn->commit();

    echo "Surveillance devices deleted successfully";
} catch (mysqli_sql_exception $e) {
    // Rollback transaction
    $conn->rollback();

    echo "Error deleting surveillance devices: " . $e->getMessage();
}

// Close connection
$conn->close();
?>