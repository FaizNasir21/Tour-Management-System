<?php
class Spot{
    private $conn;

    public function __construct($servername, $username, $password, $dbname) {
        // Create connection
        $this->conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Fetch all destinations from the database
    public function getDestinations() {
        $result = $this->conn->query("SELECT DESTINATION_ID, DESTINATION_NAME FROM destination");
        $destinations = [];
        while ($row = $result->fetch_assoc()) {
            $destinations[] = $row;
        }
        return $destinations;
    }

    // Add a spot to a destination
    public function addSpot($destination_id, $spot_name, $spot_description) {
        // Check if the spot already exists for the given destination and name
        $stmt_check = $this->conn->prepare("SELECT COUNT(*) FROM spot WHERE DESTINATION_ID = ? AND SPOT_NAME = ?");
        $stmt_check->bind_param("is", $destination_id, $spot_name);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        // If the spot already exists, return an error message
        if ($count > 0) {
            return "Error: A spot with the name '$spot_name' already exists for this destination.";
        } else {
            // Insert the new spot into the database
            $stmt = $this->conn->prepare("INSERT INTO spot (DESTINATION_ID, SPOT_NAME, SPOT_DESCRIPTION) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $destination_id, $spot_name, $spot_description);

            if ($stmt->execute()) {
                $stmt->close();
                return "Spot added successfully!";
            } else {
                $stmt->close();
                return "Error: " . $stmt->error;
            }
        }
    }

    // Close the database connection
    public function closeConnection() {
        $this->conn->close();
    }
}

session_start(); // Start session to access DESTINATION_ID

// Database connection parameters
$servername = "localhost";
$username = "root"; // replace with your MySQL username
$password = ""; // replace with your MySQL password
$dbname = "tourmanagementsystem";

// Create an instance of the TourManagementSystem class
$tms = new TourManagementSystem($servername, $username, $password, $dbname);

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $destination_id = $_POST['DESTINATION_ID'];
    $spot_name = $_POST['SPOT_NAME'];
    $spot_description = $_POST['SPOT_DESCRIPTION'];

    // Add the spot and capture any messages
    if ($destination_id && $spot_name && $spot_description) {
        $message = $tms->addSpot($destination_id, $spot_name, $spot_description);
        if (strpos($message, "Error") !== false) {
            $error_message = $message;
        } else {
            $success_message = $message;
        }
    }
}

// Fetch all destinations
$destinations = $tms->getDestinations();

// Close the database connection
$tms->closeConnection();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Spot</title>
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
        input[type="text"], textarea {
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
    </style>
</head>
<body>
    <h1>Add a New Spot</h1>
    <?php if (!empty($success_message)): ?>
        <div style="color: green; text-align: center;"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div style="color: red; text-align: center;"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form method="post">
        <!-- Destination selection -->
        <label for="DESTINATION_ID">Select Destination:</label>
        <select id="DESTINATION_ID" name="DESTINATION_ID" required>
            <option value="">--Select a Destination--</option>
            <?php foreach ($destinations as $destination): ?>
                <option value="<?php echo $destination['DESTINATION_ID']; ?>">
                    <?php echo htmlspecialchars($destination['DESTINATION_NAME']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="SPOT_NAME">Spot Name:</label>
        <input type="text" id="SPOT_NAME" name="SPOT_NAME" required><br><br>
        
        <label for="SPOT_DESCRIPTION">Spot Description:</label>
        <textarea id="SPOT_DESCRIPTION" name="SPOT_DESCRIPTION" required></textarea><br><br>

        <input type="submit" value="Add Spot">
    </form>
    <a href="create_destination.php" class="back-link">Back to Create Destination</a>
</body>
</html>
