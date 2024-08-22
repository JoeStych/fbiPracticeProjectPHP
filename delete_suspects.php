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

    // Get the list of suspects to delete
    $suspects = $_POST['suspects'];

    // Delete the suspects
    $query = "DELETE FROM suspects WHERE SuspectID IN ($suspects)";
    $conn->query($query);

    // Commit transaction
    $conn->commit();

    echo "Suspects deleted successfully";
} catch (mysqli_sql_exception $e) {
    // Rollback transaction
    $conn->rollback();

    echo "Error deleting suspects: " . $e->getMessage();
}

// Close connection
$conn->close();
?>