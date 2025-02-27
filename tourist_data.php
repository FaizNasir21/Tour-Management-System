<?php
session_start();

class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "tourmanagementsystem";
    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
}

class Tourist {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllTourists() {
        $sql = "SELECT * FROM tourist";
        $result = $this->db->query($sql);

        // Error handling for failed query execution
        if ($result === false) {
            die("Error: " . $this->db->error); // Show error if query fails
        }

        return $result;
    }

    public function deleteTourist($touristId) {
        $sql = "DELETE FROM tourist WHERE TOURIST_ID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $touristId);

        // Execute the deletion query
        if ($stmt->execute()) {
            return "Tourist deleted successfully.";
        } else {
            return "Error deleting tourist: " . $this->db->error;
        }
    }
}

// Instantiate classes
$database = new Database();
$tourist = new Tourist($database->conn);
$result = $tourist->getAllTourists();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tourist Data</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery -->
    <style>
        /* Your CSS code */
        body { font-family: 'Nunito', sans-serif; background: linear-gradient(to right, #434343, #000000); color: white; margin: 0; padding: 20px; }
        h1 { text-align: left; margin-top: 50px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid orange; }
        th, td { padding: 15px; text-align: left; }
        th { background-color: black; color: orange; }
        tr:nth-child(even) { background-color: #333; }
        tr:hover { background-color: #555; }
        .navbar { background-color: black; color: orange; padding: 15px; text-align: center; margin-top: -20px; width: 100%; }
        .navbar a { color: orange; text-decoration: none; padding: 10px 20px; }
        .navbar a:hover { background-color: #444; }
        .button { background-color: orange; color: black; border: none; padding: 10px; cursor: pointer; margin: 5px; }
        .button:hover { background-color: #ffb84d; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.html">Home</a>
    <a href="AdminInterface.html">Interface Page</a>
    <a href="AdminLogin.html">Logout</a>
</div>

<h1>Tourist Data</h1>

<table>
    <thead>
        <tr>
            <th>TOURIST_ID</th>
            <th>TOURIST_NAME</th>
            <th>TOURIST_LOCATION</th>
            <th>TOURIST_UserName</th>
            <th>TOURIST_Password</th>
            <th>TOURIST_Phone</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Check if there are results
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr id='tourist_" . $row["TOURIST_ID"] . "'>
                    <td>" . htmlspecialchars($row["TOURIST_ID"]) . "</td>
                    <td>" . htmlspecialchars($row["TOURIST_NAME"]) . "</td>
                    <td>" . htmlspecialchars($row["TOURIST_LOCATION"]) . "</td>
                    <td>" . htmlspecialchars($row["TOURIST_UserName"]) . "</td>
                    <td>" . htmlspecialchars($row["TOURIST_Password"]) . "</td>
                    <td>" . htmlspecialchars($row["TOURIST_Phone"]) . "</td>
                    <td>
                    <form action='edit_tourist.php' method='post' style='display:inline;'>
                            <input type='hidden' name='TOURIST_ID' value='" . $row["TOURIST_ID"] . "'>
                            <button type='submit' class='button'>Edit</button>
                        </form>
                        <button type='button' class='button' onclick='deleteTourist(" . $row["TOURIST_ID"] . ")'>Delete</button>

                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No records found</td></tr>";
        }
        ?>
    </tbody>
</table>

<script>
    function deleteTourist(touristId) {
        if (confirm('Are you sure you want to delete this tourist?')) {
            $.ajax({
                url: 'delete_tourist.php',
                type: 'POST',
                data: {
                    'TOURIST_ID': touristId
                },
                success: function(response) {
                    alert(response);  // Display the response from PHP
                    if (response === "Tourist deleted successfully.") {
                        // Remove the row from the table after successful deletion
                        $('#tourist_' + touristId).remove();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error: ' + error);
                }
            });
        }
    }
</script>

</body>
</html>
