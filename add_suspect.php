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

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $suspect_name = $_POST["suspect_name"];
    $alias = $_POST["alias"];
    $date_of_birth = $_POST["date_of_birth"];
    $gender = $_POST["gender"];
    $nationality = $_POST["nationality"];
    $description = $_POST["description"];
    $last_known_location = $_POST["last_known_location"];

    // Insert the new suspect into the database
    $query = "INSERT INTO Suspects (SuspectName, Alias, DateOfBirth, Gender, Nationality, Description, LastKnownLocation) VALUES ('$suspect_name', '$alias', '$date_of_birth', '$gender', '$nationality', '$description', '$last_known_location')";
    if ($conn->query($query) === TRUE) {
        echo "New suspect added successfully";
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Suspect</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Suspect</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="suspect_name">Suspect Name:</label>
                <input type="text" class="form-control" id="suspect_name" name="suspect_name" required>
            </div>
            <div class="form-group">
                <label for="alias">Alias:</label>
                <input type="text" class="form-control" id="alias" name="alias">
            </div>
            <div class="form-group">
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select class="form-control" id="gender" name="gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="nationality">Nationality:</label>
                <input type="text" class="form-control" id="nationality" name="nationality">
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="last_known_location">Last Known Location:</label>
                <input type="text" class="form-control" id="last_known_location" name="last_known_location">
            </div>
            <a href="manage_suspects.php?search_term=&search_by=name" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Add Suspect</button>
        </form>
    </div>
</body>
</html>