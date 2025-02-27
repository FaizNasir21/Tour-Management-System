<?php
// create_transport.php

class Transport {
    private $conn;
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "tourmanagementsystem";

    // Constructor to establish the database connection
    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Method to insert new transport data into the database
    public function createTransport($transport_type, $transport_capacity, $transport_price, $destination_id) {
        $admin_id = "ADMIN001"; // Default value

        // Get the next TRANSPORT_ID
        $result = $this->conn->query("SELECT MAX(TRANSPORT_ID) AS max_id FROM transport");
        $row = $result->fetch_assoc();
        $next_id = $row['max_id'] + 1;

        // Prepare and bind the insert query
        $stmt = $this->conn->prepare("INSERT INTO transport (TRANSPORT_ID, TRANSPORT_TYPE, TRANSPORT_CAPACITY, TRANSPORT_PRICE, DESTINATION_ID, ADMIN_ID) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("isiisi", $next_id, $transport_type, $transport_capacity, $transport_price, $destination_id, $admin_id);

        if ($stmt->execute()) {
            return "New transport added successfully.";
        } else {
            return "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    // Destructor to close the database connection
    public function __destruct() {
        $this->conn->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Create an instance of the Transport class
    $transport = new Transport();

    // Get data from the form
    $transport_type = $_POST['TRANSPORT_TYPE'];
    $transport_capacity = $_POST['TRANSPORT_CAPACITY'];
    $transport_price = $_POST['TRANSPORT_PRICE'];
    $destination_id = $_POST['DESTINATION_ID'];

    // Call the method to insert transport data and store the response
    $message = $transport->createTransport($transport_type, $transport_capacity, $transport_price, $destination_id);
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Create Transport</title>
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
        form {
            max-width: 600px;
            margin: auto;
            background-color: #333;
            padding: 20px;
            border-radius: 10px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            color: orange;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 5px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: orange;
            border: none;
            border-radius: 5px;
            color: black;
            font-size: 16px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #ffb84d;
        }
        .back-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #555;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            margin-top: 20px;
            cursor: pointer;
        }
        .back-button:hover {
            background-color: #666;
        }
    </style>
</head>
<body>
    <h1>Create New Transport</h1>

    <?php if (isset($message)) { echo "<p>$message</p>"; } ?>

    <form method="POST" action="create_transport.php">
        <label for="TRANSPORT_TYPE">Transport Type:</label><br>
        <input type="text" id="TRANSPORT_TYPE" name="TRANSPORT_TYPE" required><br><br>
        
        <label for="TRANSPORT_CAPACITY">Transport Capacity:</label><br>
        <input type="number" id="TRANSPORT_CAPACITY" name="TRANSPORT_CAPACITY" required><br><br>
        
        <label for="TRANSPORT_PRICE">Transport Price:</label><br>
        <input type="number" id="TRANSPORT_PRICE" name="TRANSPORT_PRICE" required><br><br>
        
        <label for="DESTINATION_ID">Destination ID:</label><br>
        <input type="number" id="DESTINATION_ID" name="DESTINATION_ID" required><br><br>
        
        <input type="submit" value="Create Transport">
    </form>
    <a href="transport_data.php" class="back-button">Back to Transport Data</a>
</body>
</html>
