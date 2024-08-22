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

    // Get the list of countries to delete
    $countries = $_POST['countries'];

    // Delete the countries
    $query = "DELETE FROM countries WHERE CountryID IN ($countries)";
    $conn->query($query);

    // Commit transaction
    $conn->commit();

    echo "Countries deleted successfully";
} catch (mysqli_sql_exception $e) {
    // Rollback transaction
    $conn->rollback();

    echo "Error deleting countries: " . $e->getMessage();
}

// Close connection
$conn->close();
?>