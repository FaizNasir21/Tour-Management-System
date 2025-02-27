<?php
// destination_data.php

class Destination {
    private $conn;

    public function __construct($servername, $username, $password, $dbname) {
        // Create connection
        $this->conn = new mysqli($servername, $username, $password, $dbname);
        
        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function fetchDestinations() {
        $sql = "SELECT * FROM destination";
        return $this->conn->query($sql);
    }

    public function deleteDestination($destination_id) {
        // Begin transaction
        $this->conn->begin_transaction();

        try {
            // Delete records from spot table that reference the destination
            $stmt = $this->conn->prepare("DELETE FROM spot WHERE DESTINATION_ID = ?");
            $stmt->bind_param("i", $destination_id);
            $stmt->execute();
            $stmt->close();

            // Delete records from restaurant table that reference the destination
            $stmt = $this->conn->prepare("DELETE FROM restaurant WHERE DESTINATION_ID = ?");
            $stmt->bind_param("i", $destination_id);
            $stmt->execute();
            $stmt->close();

            // Delete the destination
            $stmt = $this->conn->prepare("DELETE FROM destination WHERE DESTINATION_ID = ?");
            $stmt->bind_param("i", $destination_id);
            $stmt->execute();
            $stmt->close();

            // Commit transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            return false;
        }
    }

    public function __destruct() {
        $this->conn->close();
    }
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tourmanagementsystem";

// Instantiate the Destination class
$destination = new Destination($servername, $username, $password, $dbname);

// Handle deletion if ID is provided
if (isset($_GET['delete']) && !empty($_GET['DESTINATION_ID'])) {
    $destination_id = $_GET['DESTINATION_ID'];

    // Validate destination ID
    if (empty($destination_id)) {
        die("Destination ID is missing.");
    }

    if ($destination->deleteDestination($destination_id)) {
        echo "<p>Destination and related records deleted successfully.</p>";
        // Redirect to the same page to refresh the data
        header("Location: destination_data.php");
        exit();
    } else {
        echo "<p>Error deleting destination.</p>";
    }
}

// Fetch data from the destination table
$result = $destination->fetchDestinations();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Destination Data</title>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(to right, #434343, #000000);
            color: white;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: left;
            margin-top: 50px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid orange;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: black;
            color: orange;
        }
        tr:nth-child(even) {
            background-color: #333;
        }
        tr:hover {
            background-color: #555;
        }
        .navbar {
            background-color: black;
            color: orange;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }
        .navbar a {
            color: orange;
            text-decoration: none;
            padding: 10px 20px;
        }
        .navbar a:hover {
            background-color: #444;
        }
        .create-button, .btn {
            display: block;
            max-width: 200px;
            margin: 10px auto;
            padding: 10px;
            background-color: orange;
            border: none;
            color: black;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .create-button:hover, .btn:hover {
            background-color: #ffb84d;
            transform: scale(1.05);
        }
        .btn {
            display: inline-block;
            margin: 5px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.html">Home</a>
    <a href="AdminInterface.html">Interface Page</a>
    <a href="AdminLogin.html">Logout</a>
</div>

<h1>Destination Data</h1>

<a href="create_destination.php" class="create-button">Create New Destination</a>

<table>
    <thead>
        <tr>
            <th>DESTINATION_ID</th>
            <th>DESTINATION_NAME</th>
            <th>DAYS</th>
            <th>BUDGET</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Check if there are results and display them
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . htmlspecialchars($row["DESTINATION_ID"]) . "</td>
                    <td>" . htmlspecialchars($row["DESTINATION_NAME"]) . "</td>
                    <td>" . htmlspecialchars($row["DAYS"]) . "</td>
                    <td>" . htmlspecialchars($row["BUDGET"]) . "</td>
                    <td>
                        <a href='edit_destination.php?DESTINATION_ID=" . htmlspecialchars($row["DESTINATION_ID"]) . "' class='btn'>Edit</a>
                        <a href='?delete=true&DESTINATION_ID=" . htmlspecialchars($row["DESTINATION_ID"]) . "' class='btn' onclick='return confirm(\"Are you sure you want to delete this destination?\")'>Delete</a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No records found</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
