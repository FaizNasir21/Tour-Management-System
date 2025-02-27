<?php
// Start session to access DESTINATION_ID if needed
session_start();

class Restaurant {
    private $conn;

    public function __construct($servername, $username, $password, $dbname) {
        // Create connection
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getDestinations() {
        // Fetch all destinations to display in the dropdown (for selection)
        $result = $this->conn->query("SELECT DESTINATION_ID, DESTINATION_NAME FROM destination");
        $destinations = [];
        while ($row = $result->fetch_assoc()) {
            $destinations[] = $row;
        }
        return $destinations;
    }

    public function addRestaurant($destination_name, $restaurant_name, $restaurant_description, $restaurant_cuisine) {
        // Fetch the DESTINATION_ID based on the selected destination name
        $stmt = $this->conn->prepare("SELECT DESTINATION_ID FROM destination WHERE DESTINATION_NAME = ?");
        $stmt->bind_param("s", $destination_name);
        $stmt->execute();
        $stmt->bind_result($destination_id);
        $stmt->fetch();
        $stmt->close();

        // Check if a valid destination ID was retrieved
        if ($destination_id) {
            // Insert restaurant data into the table with the fetched DESTINATION_ID
            if ($restaurant_name && $restaurant_description && $restaurant_cuisine) {
                $stmt = $this->conn->prepare("INSERT INTO restaurant (DESTINATION_ID, RESTAURANT_NAME, RESTAURANT_DESCRIPTION, RESTAURANT_CUISINE_TYPE) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $destination_id, $restaurant_name, $restaurant_description, $restaurant_cuisine);

                if ($stmt->execute()) {
                    return "Restaurant added successfully!";
                } else {
                    return "Error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                return "Please fill in all fields.";
            }
        } else {
            return "Error: The selected destination does not exist.";
        }
    }

    public function closeConnection() {
        $this->conn->close();
    }
}

// Database connection parameters
$servername = "localhost";
$username = "root"; // replace with your MySQL username
$password = ""; // replace with your MySQL password
$dbname = "tourmanagementsystem";

// Create Restaurant object
$restaurantObj = new Restaurant($servername, $username, $password, $dbname);

// Fetch destinations for dropdown
$destinations = $restaurantObj->getDestinations();

// Handle the form submission
$successMessage = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $destination_name = $_POST['DESTINATION_NAME'];
    $restaurant_name = $_POST['RESTAURANT_NAME'];
    $restaurant_description = $_POST['RESTAURANT_DESCRIPTION'];
    $restaurant_cuisine = $_POST['RESTAURANT_CUISINE_TYPE'];

    // Add restaurant
    $successMessage = $restaurantObj->addRestaurant($destination_name, $restaurant_name, $restaurant_description, $restaurant_cuisine);
}

// Close the connection
$restaurantObj->closeConnection();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Restaurant</title>
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
        input[type="text"], textarea, select {
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
    <div class="container">
        <h1>Add a New Restaurant</h1>
        <?php if ($successMessage) : ?>
            <p><?php echo $successMessage; ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="DESTINATION_NAME">Select Destination:</label>
            <select id="DESTINATION_NAME" name="DESTINATION_NAME" required>
                <option value="">--Select a Destination--</option>
                <?php foreach ($destinations as $destination): ?>
                    <option value="<?php echo htmlspecialchars($destination['DESTINATION_NAME']); ?>">
                        <?php echo htmlspecialchars($destination['DESTINATION_NAME']); ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>
            
            <label for="RESTAURANT_NAME">Restaurant Name:</label>
            <input type="text" id="RESTAURANT_NAME" name="RESTAURANT_NAME" required><br><br>
            
            <label for="RESTAURANT_DESCRIPTION">Restaurant Description:</label>
            <textarea id="RESTAURANT_DESCRIPTION" name="RESTAURANT_DESCRIPTION" required></textarea><br><br>
            
            <label for="RESTAURANT_CUISINE_TYPE">Cuisine Type:</label>
            <input type="text" id="RESTAURANT_CUISINE_TYPE" name="RESTAURANT_CUISINE_TYPE" required><br><br>

            <input type="submit" value="Add Restaurant">
        </form>
        <a href="create_destination.php" class="back-link">Back to Create Destination</a>
    </div>
</body>
</html>
