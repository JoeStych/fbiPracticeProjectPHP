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

// Check if search term is set
if (isset($_GET['search_term']) && isset($_GET['search_by'])) {
    $search_term = $_GET['search_term'];
    $search_by = $_GET['search_by'];

    if ($search_by == 'name') {
        $query = "SELECT * FROM Suspects WHERE SuspectName LIKE ? LIMIT ? OFFSET ?";
    } elseif ($search_by == 'alias') {
        $query = "SELECT * FROM Suspects WHERE Alias LIKE ? LIMIT ? OFFSET ?";
    } elseif ($search_by == 'date_of_birth') {
        $query = "SELECT * FROM Suspects WHERE DateOfBirth = ? LIMIT ? OFFSET ?";
    } elseif ($search_by == 'nationality') {
        $query = "SELECT * FROM Suspects WHERE Nationality LIKE ? LIMIT ? OFFSET ?";
    } elseif ($search_by == 'last_known_location') {
        $query = "SELECT * FROM Suspects WHERE LastKnownLocation LIKE ? LIMIT ? OFFSET ?";
    } elseif ($search_by == 'vehicle') {
        $query = "SELECT s.* FROM Suspects s JOIN SuspectVehicles v ON s.SuspectID = v.SuspectID WHERE v.VehicleMake LIKE ? OR v.VehicleModel LIKE ? OR v.LicensePlate LIKE ? LIMIT ? OFFSET ?";
    } else {
        $query = "SELECT * FROM Suspects LIMIT ? OFFSET ?";
    }

    $stmt = $conn->prepare($query);
    if ($search_by == 'vehicle') {
        $search_term = "%" . $search_term . "%";
        $stmt->bind_param("sssss", $search_term, $search_term, $search_term, $rows_per_page, $offset);
    } else {
        $search_term = "%" . $search_term . "%";
        $stmt->bind_param("sss", $search_term, $rows_per_page, $offset);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT * FROM Suspects LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $rows_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Display suspects
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Suspects</title>
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
        <h1>Manage Suspects</h1>
        <a href="protected.php" class="btn btn-secondary">Back</a>
        <form action="" method="get">
            <div class="form-group">
                <select name="search_by" id="search_by">
                    <option value="name">Name</option>
                    <option value="alias">Alias</option>
                    <option value="date_of_birth">Date of Birth</option>
                    <option value="nationality">Nationality</option>
                    <option value="last_known_location">Last Known Location</option>
                    <option value="vehicle">Vehicle</option>
                </select>
                <input type="text" name="search_term" id="search_term" placeholder="Search term">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <script>
            document.getElementById('search_by').addEventListener('change', function() {
                var searchBy = this.value;
                var searchTermInput = document.getElementById('search_term');
                
                if (searchBy == 'date_of_birth') {
                    searchTermInput.type = 'date';
                } else {
                    searchTermInput.type = 'text';
                }
            });
        </script>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Suspect Name</th>
                    <th>Alias</th>
                    <th>Nationality</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>View More</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row["SuspectName"]; ?></td>
                        <td><?php echo $row["Alias"]; ?></td>
                        <td><?php echo $row["Nationality"]; ?></td>
                        <td><?php echo $row["DateOfBirth"]; ?></td>
                        <td><?php echo $row["Gender"]; ?></td>
                        <td><a href='view_suspect.php?id=<?php echo $row["SuspectID"]; ?>' class="btn btn-info">View More</a></td>
                        <td><input type="checkbox" name="delete[]" value="<?php echo $row["SuspectID"]; ?>"></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <?php
                // Display the pagination links
                if (isset($_GET['search_term'])) {
                    $total_rows = $conn->query("SELECT COUNT(*) FROM Suspects WHERE SuspectName LIKE '%$search_term%'")->fetch_row()[0];
                } else {
                    $total_rows = $conn->query("SELECT COUNT(*) FROM Suspects")->fetch_row()[0];
                }
                $total_pages = ceil($total_rows / $rows_per_page);

                // Display the "Previous" button
                if ($page > 1) {
                    echo "<li class='page-item'><a class='page-link' href='manage_suspects.php?page=" . ($page - 1) . "&search_term=$search_term&search_by=$search_by'>Previous</a></li>";
                }

                // Display the page numbers
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $page) {
                        echo "<li class='page-item active'><a class='page-link' href='manage_suspects.php?page=$i&search_term=$search_term&search_by=$search_by'>$i</a></li>";
                    } elseif (abs($i - $page) <= 2) {
                        echo "<li class='page-item'><a class='page-link' href='manage_suspects.php?page=$i&search_term=$search_term&search_by=$search_by'>$i</a></li>";
                    }
                }

                // Display the "Next" button
                if ($page < $total_pages) {
                    echo "<li class='page-item'><a class='page-link' href='manage_suspects.php?page=" . ($page + 1) . "&search_term=$search_term&search_by=$search_by'>Next</a></li>";
                }
                ?>
            </ul>
        </nav>
        <button class="btn btn-danger" id="delete-selected">Delete Selected</button>
        <a href="add_suspect.php" class="btn btn-primary">Add New Suspect</a>
    </div>

    <script>
        // Delete selected suspects
        document.getElementById('delete-selected').addEventListener('click', function() {
            var checkboxes = document.getElementsByName('delete[]');
            var selected = [];
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    selected.push(checkboxes[i].value);
                }
            }
            if (selected.length > 0) {
                if (confirm('Are you sure you want to delete the selected suspects?')) {
                    // Make an AJAX request to delete the selected suspects
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'delete_suspects.php', true);
                    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    xhr.send('suspects=' + selected.join(','));
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
                alert('Please select at least one suspect to delete.');
            }
        });
    </script>
</body>
</html>