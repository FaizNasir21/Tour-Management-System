<?php
session_start(); // Start session to access tourist ID and name

// Database connection parameters
class TourManagementSystem {
    private $conn;

    public function __construct() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "tourmanagementsystem";

        // Create connection
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getDestinationId($planned_destination) {
        $sql = "SELECT DESTINATION_ID FROM destination WHERE DESTINATION_NAME = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $planned_destination);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['DESTINATION_ID'];
        }
        return null;
    }

    public function insertTourPlan($admin_id, $destination_id, $tourist_id, $planned_days) {
        $sql = "
            INSERT INTO tourplan (ADMIN_ID, DESTINATION_ID, TOURIST_ID, START_DATE, END_DATE)
            VALUES (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY))
        ";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("siii", $admin_id, $destination_id, $tourist_id, $planned_days);
        return $stmt->execute();
    }

    public function closeConnection() {
        $this->conn->close();
    }
}

class TourPlan {
    private $tourManagementSystem;

    public function __construct() {
        $this->tourManagementSystem = new TourManagementSystem();
    }

    public function createTourPlan() {
        // Retrieve data from the form
        $planned_destination = $_POST['Planned_Destination'];
        $planned_days = $_POST['Planned_Days'];
        $planned_budget = $_POST['Planned_Budget'];

        // Retrieve tourist ID and name from the session
        $tourist_id = $_SESSION['tourist_id'];
        $tourist_name = $_SESSION['tourist_name'];

        // Check if necessary data is available
        if (empty($planned_destination) || empty($planned_days) || empty($planned_budget) || empty($tourist_id) || empty($tourist_name)) {
            die("Required data is missing. Please make sure all fields are filled.");
        }

        // Get destination ID from the planned destination name
        $destination_id = $this->tourManagementSystem->getDestinationId($planned_destination);
        if ($destination_id === null) {
            die("Destination not found.");
        }

        // Admin ID (assuming it's a constant or retrieved from another source)
        $admin_id = 'ADMIN001';

        // Insert tour plan into the database
        if ($this->tourManagementSystem->insertTourPlan($admin_id, $destination_id, $tourist_id, $planned_days)) {
            // Successful insert
            header("Location: tour_confirmation.php"); // Redirect to confirmation page
            exit();
        } else {
            die("Error inserting tour plan: " . $this->tourManagementSystem->conn->error);
        }
    }

    public function __destruct() {
        $this->tourManagementSystem->closeConnection();
    }
}

// Create a TourPlan object and process the tour plan creation
$tourPlan = new TourPlan();
$tourPlan->createTourPlan();
?>
