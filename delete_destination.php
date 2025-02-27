<?php
// DestinationDelete.php

class DestinationDelete {
    private $conn;

    public function __construct($servername, $username, $password, $dbname) {
        // Create database connection
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Method to delete destination by ID
    public function deleteDestination($destination_id) {
        // Prepare the delete query
        $sql = "DELETE FROM destination WHERE DESTINATION_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $destination_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Close the database connection
    public function closeConnection() {
        $this->conn->close();
    }
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tourmanagementsystem";

// Instantiate the DestinationDelete class
$destinationDelete = new DestinationDelete($servername, $username, $password, $dbname);

// Get the destination ID from the URL
$destination_id = $_GET['DESTINATION_ID'] ?? '';

// Delete the destination
if ($destinationDelete->deleteDestination($destination_id)) {
    echo "Destination deleted successfully. <a href='destination_data.php'>Back to Destinations</a>";
} else {
    echo "Error deleting destination.";
}

// Close the connection
$destinationDelete->closeConnection();
?>
