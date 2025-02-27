<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "tourmanagementsystem";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['TOURIST_ID'])) {
    $tourist_id = $_POST['TOURIST_ID'];

    // SQL to delete the tourist record
    $sql = "DELETE FROM tourist WHERE TOURIST_ID = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $tourist_id);
        if ($stmt->execute()) {
            echo "Tourist deleted successfully.";
        } else {
            echo "Error deleting tourist: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Failed to prepare statement.";
    }
}

$conn->close();
?>
