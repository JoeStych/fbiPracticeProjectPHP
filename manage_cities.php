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

// Define the number of rows to display per page
$rows_per_page = 10;

// Define the current page number
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the offset for the query
$offset = ($page - 1) * $rows_per_page;

// Query to select all cities
$query = "SELECT * FROM Cities LIMIT $rows_per_page OFFSET $offset";

// Execute query
$result = $conn->query($query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Display cities
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Cities</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
        .table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Cities</h1>
        <a href="protected.php" class="btn btn-secondary">Back</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>City ID</th>
                    <th>City Name</th>
                    <th>Country ID</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row["CityID"]; ?></td>
                        <td><?php echo $row["CityName"]; ?></td>
                        <td><?php echo $row["CountryID"]; ?></td>
                        <td><a href='edit_city.php?id=<?php echo $row["CityID"]; ?>'>Edit</a></td>
                        <td><input type="checkbox" name="delete[]" value="<?php echo $row["CityID"]; ?>"></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <?php
                // Display the pagination links
                $total_rows = $conn->query("SELECT COUNT(*) FROM Cities")->fetch_row()[0];
                $total_pages = ceil($total_rows / $rows_per_page);

                // Display the "Previous" button
                if ($page > 1) {
                    echo "<li class='page-item'><a class='page-link' href='manage_cities.php?page=" . ($page - 1) . "'>Previous</a></li>";
                }

                // Display the page numbers
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $page) {
                        echo "<li class='page-item active'><a class='page-link' href='manage_cities.php?page=$i'>$i</a></li>";
                    } elseif (abs($i - $page) <= 2) {
                        echo "<li class='page-item'><a class='page-link' href='manage_cities.php?page=$i'>$i</a></li>";
                    }
                }

                // Display the "Next" button
                if ($page < $total_pages) {
                    echo "<li class='page-item'><a class='page-link' href='manage_cities.php?page=" . ($page + 1) . "'>Next</a></li>";
                }
                ?>
            </ul>
        </nav>
        <button class="btn btn-danger" id="delete-selected">Delete Selected</button>
        <a href="add_city.php" class="btn btn-primary">Add New City</a>
    </div>

    <script>
        // Delete selected cities
        document.getElementById('delete-selected').addEventListener('click', function() {
            var checkboxes = document.getElementsByName('delete[]');
            var selected = [];
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    selected.push(checkboxes[i].value);
                }
            }
            if (selected.length > 0) {
                if (confirm('Are you sure you want to delete the selected cities?')) {
                    // Make an AJAX request to delete the selected cities
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'delete_cities.php', true);
                    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    xhr.send('cities=' + selected.join(','));
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            var response = xhr.responseText; // Get the response from the server
                            // Display the response on the screen
                            alert(response); // Show the response in an alert dialog
                            location.reload();
                        }
                    };
                }
            } else {
                alert('Please select at least one city to delete.');
            }
        });
    </script>
</body>
</html>