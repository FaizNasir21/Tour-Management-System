<?php
session_start(); // Start session to store the DESTINATION_ID

class Destination {
    private $conn;
    private $destination_id;
    private $message;

    public function __construct($servername, $username, $password, $dbname) {
        $this->connectDatabase($servername, $username, $password, $dbname);
    }

    private function connectDatabase($servername, $username, $password, $dbname) {
        $this->conn = new mysqli($servername, $username, $password, $dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

     public function createDestination($destination_name, $days, $budget) {
        // Prepare and execute the insert query
        $sql = "INSERT INTO destination (DESTINATION_NAME, DAYS, BUDGET, ADMIN_ID) VALUES (?, ?, ?, 'ADMIN001')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $destination_name, $days, $budget);

        if ($stmt->execute()) {
            // Get the newly generated DESTINATION_ID
            $this->destination_id = $stmt->insert_id;

            // Store the DESTINATION_ID in a session variable
            $_SESSION['new_destination_id'] = $this->destination_id;

            // Set success message
            $this->message = "<p>New destination created successfully. Destination ID: " . htmlspecialchars($this->destination_id) . "</p>";
            echo "Session DESTINATION_ID: " . $_SESSION['new_destination_id']; // Add this for debugging

        } else {
            $this->message = "<p>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }

    public function getDestinationId() {
        return $_SESSION['new_destination_id'] ?? null;
    }

    public function getMessage() {
        return $this->message;
    }

    public function closeConnection() {
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $destination_name = $_POST['DESTINATION_NAME'];
    $days = $_POST['DAYS'];
    $budget = $_POST['BUDGET'];

    // Create a new destination
    $destination->createDestination($destination_name, $days, $budget);
}

// Retrieve DESTINATION_ID from session
$destination_id = $destination->getDestinationId();

// Close the database connection
$destination->closeConnection();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Destination</title>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(to right, #434343, #000000);
            color: white;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: #333;
            border-radius: 8px;
        }
        h1 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="number"] {
            padding: 10px;
            border: 1px solid orange;
            border-radius: 4px;
            background-color: #222;
            color: white;
            margin-bottom: 20px;
        }
        input[type="submit"] {
            padding: 10px;
            background-color: orange;
            border: none;
            color: black;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #ffb84d;
            transform: scale(1.05);
        }
        .back-link {
            display: block;
            max-width: 200px;
            margin: 20px auto;
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
        .back-link:hover {
            background-color: #ffb84d;
            transform: scale(1.05);
        }
        .buttons {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            background-color: orange;
            color: black;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .btn:hover {
            background-color: #ffb84d;
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Create New Destination</h1>
    <form action="create_destination.php" method="POST">
        <label for="DESTINATION_NAME">Destination Name:</label>
        <input type="text" id="DESTINATION_NAME" name="DESTINATION_NAME" required>

        <label for="DAYS">Days:</label>
        <input type="number" id="DAYS" name="DAYS" required>

        <label for="BUDGET">Budget:</label>
        <input type="number" id="BUDGET" name="BUDGET" required>

        <input type="submit" value="Create Destination">
    </form>

    <a href="destination_data.php" class="back-link">Back to Destination Data</a>


    <div class="buttons">
        <a href="add_spot.php?DESTINATION_ID=<?= htmlspecialchars($destination_id) ?>" class="btn">Add Spot</a>
        <a href="add_hotel.php?DESTINATION_ID=<?= htmlspecialchars($destination_id) ?>" class="btn">Add Hotel</a>
        <a href="add_restaurant.php?DESTINATION_ID=<?= htmlspecialchars($destination_id) ?>" class="btn">Add Restaurant</a>
    </div>
    

</body>
</html>
