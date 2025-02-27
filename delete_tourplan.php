<?php

class TourPlanManager {
    private $conn;

    // Constructor to initialize the database connection
    public function __construct($servername, $username, $password, $dbname) {
        $this->connectDB($servername, $username, $password, $dbname);
    }

    // Method to connect to the database
    private function connectDB($servername, $username, $password, $dbname) {
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Method to delete a tour plan by TOURPLAN_ID
    public function deleteTourPlan($tourplan_id) {
        $stmt = $this->conn->prepare("DELETE FROM tourplan WHERE TOURPLAN_ID = ?");
        $stmt->bind_param("s", $tourplan_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }

        $stmt->close();
    }

    // Method to close the database connection
    public function closeConnection() {
        $this->conn->close();
    }
}

// Get the TOURPLAN_ID from the URL parameters
$tourplan_id = $_GET['TOURPLAN_ID'] ?? '';

// Validate required parameter
if (empty($tourplan_id)) {
    die("Required parameter TOURPLAN_ID is missing.");
}

// Instantiate the TourPlanManager class
$servername = "localhost";
$username = "root"; // replace with your MySQL username
$password = ""; // replace with your MySQL password
$dbname = "tourmanagementsystem";

$tourPlanManager = new TourPlanManager($servername, $username, $password, $dbname);

// Attempt to delete the tour plan
if ($tourPlanManager->deleteTourPlan($tourplan_id)) {
    header("Location: tourplan_data.php");
    exit();
} else {
    echo "<p>Error deleting tour plan.</p>";
}

// Close connection
$tourPlanManager->closeConnection();

?>
