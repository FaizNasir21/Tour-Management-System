<?php
session_start(); // Start session to access DESTINATION_ID

class Hotel {
    private $conn;
    private $destination_id;

    // Constructor to initialize database connection and destination_id
    public function __construct($conn, $destination_id) {
        $this->conn = $conn;
        $this->destination_id = $destination_id;

        if (empty($this->destination_id)) {
            die("No DESTINATION_ID found. Please create a destination first.");
        }
    }

    // Method to insert a new hotel
    public function addHotel($hotel_name, $hotel_description, $hotel_price) {
        // Insert data into hotel table
        $stmt = $this->conn->prepare("INSERT INTO hotel (DESTINATION_ID, HOTEL_NAME, HOTEL_DESCRIPTION, HOTEL_PRICE_PER_NIGHT) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $this->destination_id, $hotel_name, $hotel_description, $hotel_price);

        if ($stmt->execute()) {
            return "Hotel added successfully!";
        } else {
            return "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Database connection parameters
$servername = "localhost";
$username = "root"; // replace with your MySQL username
$password = ""; // replace with your MySQL password
$dbname = "tourmanagementsystem";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hotel_name = $_POST['HOTEL_NAME'];
    $hotel_description = $_POST['HOTEL_DESCRIPTION'];
    $hotel_price = $_POST['HOTEL_PRICE_PER_NIGHT'];
    $destination_id = $_POST['DESTINATION_ID'];

    // Create Hotel object and add the hotel
    $hotel = new Hotel($conn, $destination_id);
    $message = $hotel->addHotel($hotel_name, $hotel_description, $hotel_price);
    if (strpos($message, "Error") === false) {
        $success_message = $message;
    } else {
        $error_message = $message;
    }
}

// Fetch destinations to populate the dropdown
$destinations_result = $conn->query("SELECT DESTINATION_ID, DESTINATION_NAME FROM destination");
$destinations = [];
while ($row = $destinations_result->fetch_assoc()) {
    $destinations[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Hotel</title>
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
        input[type="text"], textarea, input[type="number"], select {
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
    <h1>Add a New Hotel</h1>
    <?php if (!empty($success_message)): ?>
        <div style="color: green; text-align: center;"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div style="color: red; text-align: center;"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form method="post">
        <label for="DESTINATION_ID">Select Destination:</label>
        <select name="DESTINATION_ID" required>
            <option value="">Select a destination</option>
            <?php foreach ($destinations as $destination): ?>
                <option value="<?php echo $destination['DESTINATION_ID']; ?>"><?php echo $destination['DESTINATION_NAME']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="HOTEL_NAME">Hotel Name:</label>
        <input type="text" id="HOTEL_NAME" name="HOTEL_NAME" required><br><br>
        
        <label for="HOTEL_DESCRIPTION">Hotel Description:</label>
        <textarea id="HOTEL_DESCRIPTION" name="HOTEL_DESCRIPTION" required></textarea><br><br>
        
        <label for="HOTEL_PRICE_PER_NIGHT">Price per Night:</label>
        <input type="number" id="HOTEL_PRICE_PER_NIGHT" name="HOTEL_PRICE_PER_NIGHT" required><br><br>

        <input type="submit" value="Add Hotel">
    </form>
    <a href="create_destination.php" class="back-link">Back to Create Destination</a>
</body>
</html>
