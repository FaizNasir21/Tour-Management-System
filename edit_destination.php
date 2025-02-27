<?php
// EditDestination.php

class DestinationEdit {
    private $conn;
    
    public function __construct($servername, $username, $password, $dbname) {
        $this->conn = new mysqli($servername, $username, $password, $dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getDestinationDetails($destination_id) {
        $sql = "SELECT * FROM destination WHERE DESTINATION_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $destination_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function closeConnection() {
        $this->conn->close();
    }
}

$destination_id = $_GET['DESTINATION_ID'] ?? '';
$destinationEdit = new DestinationEdit('localhost', 'root', '', 'tourmanagementsystem');
$destination = $destinationEdit->getDestinationDetails($destination_id);

if (!$destination) {
    die("Destination not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Destination</title>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(to right, #434343, #000000);
            color: white;
            margin: 0;
            padding: 20px;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: #333;
            padding: 20px;
            border-radius: 8px;
        }
        .form-container h1 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #555;
            background: #222;
            color: white;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: orange;
            border: none;
            color: black;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .form-group button:hover {
            background-color: #ffb84d;
            transform: scale(1.05);
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
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.html">Home</a>
    <a href="destination_data.php">Back to Destinations</a>
</div>

<div class="form-container">
    <h1>Edit Destination</h1>

    <form action="update_destination.php" method="post">
        <input type="hidden" name="DESTINATION_ID" value="<?php echo htmlspecialchars($destination['DESTINATION_ID']); ?>">
        
        <div class="form-group">
            <label for="DESTINATION_NAME">Destination Name:</label>
            <input type="text" id="DESTINATION_NAME" name="DESTINATION_NAME" value="<?php echo htmlspecialchars($destination['DESTINATION_NAME']); ?>" required>
        </div>

        <div class="form-group">
            <label for="DAYS">Days:</label>
            <input type="number" id="DAYS" name="DAYS" value="<?php echo htmlspecialchars($destination['DAYS']); ?>" required>
        </div>

        <div class="form-group">
            <label for="BUDGET">Budget:</label>
            <input type="number" id="BUDGET" name="BUDGET" value="<?php echo htmlspecialchars($destination['BUDGET']); ?>" required>
        </div>

        <div class="form-group">
            <button type="submit">Update Destination</button>
        </div>
    </form>
</div>

<?php
$destinationEdit->closeConnection();
?>

</body>
</html>
