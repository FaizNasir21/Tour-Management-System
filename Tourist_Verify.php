<?php
class TouristRegistration {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "tourmanagementsystem";
    private $conn;

    // Constructor to establish the connection
    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Method to register a new tourist
    public function registerTourist($full_name, $user_name, $phone, $location, $password, $confirm_password) {
        // Admin ID to be automatically added
        $admin_id = 'ADMIN001';

        // Check if passwords match
        if ($password === $confirm_password) {
            // Prepare the SQL query
            $sql = "INSERT INTO tourist (TOURIST_NAME, TOURIST_USERNAME, TOURIST_PHONE, TOURIST_LOCATION, TOURIST_PASSWORD, ADMIN_ID) 
                    VALUES (?, ?, ?, ?, ?, ?)";

            if ($stmt = $this->conn->prepare($sql)) {
                // Bind parameters
                $stmt->bind_param("ssssss", $full_name, $user_name, $phone, $location, $password, $admin_id);

                // Execute the statement and check for errors
                if ($stmt->execute()) {
                    // Redirect to the AfterSignUp page
                    header("Location: AfterSignUp.html");
                    exit();
                } else {
                    // Show detailed error information
                    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
                }
                $stmt->close();
            } else {
                // Show detailed error information for prepare statement
                echo "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
            }
        } else {
            echo "Passwords do not match!";
        }
    }

    // Destructor to close the connection
    public function __destruct() {
        $this->conn->close();
    }
}

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $user_name = $_POST['user_name'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Create an instance of the TouristRegistration class and call the register method
    $touristRegistration = new TouristRegistration();
    $touristRegistration->registerTourist($full_name, $user_name, $phone, $location, $password, $confirm_password);
}
?>
