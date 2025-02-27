<?php
// TouristLoginVerify.php

// Start session
session_start();

class TouristLogin {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "tourmanagementsystem";
    private $conn;

    // Constructor to establish database connection
    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Method to verify user credentials
    public function verifyUser($user, $pass) {
        $stmt = $this->conn->prepare("SELECT TOURIST_ID, TOURIST_NAME FROM tourist WHERE TOURIST_UserName = ? AND TOURIST_Password = ?");
        $stmt->bind_param("ss", $user, $pass);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['tourist_id'] = $row['TOURIST_ID'];
            $_SESSION['tourist_name'] = $row['TOURIST_NAME'];

            echo "<script>
                    alert('Your Tourist ID is: " . $row['TOURIST_ID'] . "');
                    alert('Tourist ID and name have been stored successfully.');
                    window.location.href='TouristInterface.html';
                  </script>";
        } else {
            echo "<script>alert('Incorrect username or password'); window.location.href='TouristLogin.html';</script>";
        }

        $stmt->close();
    }

    // Close the database connection
    public function closeConnection() {
        $this->conn->close();
    }
}

// Handling form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $touristLogin = new TouristLogin();
    $touristLogin->verifyUser($user, $pass);
    $touristLogin->closeConnection();
}
?>
