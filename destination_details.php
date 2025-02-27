<?php
session_start(); // Start session to access tourist ID

// Define the class
class TourManagementSystem {
    private $conn;
    
    public function __construct($servername, $username, $password, $dbname) {
        // Create connection
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getDestinationDetails($destination_id) {
        // SQL query to get details for the selected destination
        $sql = "
            SELECT 
                d.DESTINATION_ID,  
                d.DESTINATION_NAME,
                d.BUDGET,
                d.DAYS,
                GROUP_CONCAT(DISTINCT h.HOTEL_NAME ORDER BY h.HOTEL_NAME SEPARATOR ', ') AS HOTELS,
                GROUP_CONCAT(DISTINCT h.HOTEL_DESCRIPTION ORDER BY h.HOTEL_NAME SEPARATOR '; ') AS HOTEL_DESCRIPTIONS,
                GROUP_CONCAT(DISTINCT r.RESTAURANT_NAME ORDER BY r.RESTAURANT_NAME SEPARATOR ', ') AS RESTAURANTS,
                GROUP_CONCAT(DISTINCT r.RESTAURANT_DESCRIPTION ORDER BY r.RESTAURANT_NAME SEPARATOR '; ') AS RESTAURANT_DESCRIPTIONS,
                GROUP_CONCAT(DISTINCT s.SPOT_NAME ORDER BY s.SPOT_NAME SEPARATOR ', ') AS SPOTS,
                GROUP_CONCAT(DISTINCT s.SPOT_DESCRIPTION ORDER BY s.SPOT_NAME SEPARATOR '; ') AS SPOT_DESCRIPTIONS,
                GROUP_CONCAT(DISTINCT t.TRANSPORT_TYPE ORDER BY t.TRANSPORT_TYPE SEPARATOR ', ') AS TRANSPORTS
            FROM 
                destination d
            LEFT JOIN hotel h ON d.DESTINATION_ID = h.DESTINATION_ID
            LEFT JOIN restaurant r ON d.DESTINATION_ID = r.DESTINATION_ID
            LEFT JOIN spot s ON d.DESTINATION_ID = s.DESTINATION_ID
            LEFT JOIN transport t ON d.DESTINATION_ID = t.DESTINATION_ID
            WHERE d.DESTINATION_ID = ?
            GROUP BY 
                d.DESTINATION_ID, d.DESTINATION_NAME, d.BUDGET, d.DAYS;
        ";

        // Prepare and execute SQL statement
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("i", $destination_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $destination = $result->fetch_assoc();

        // Check if destination details are found
        if (!$destination) {
            die("No details found for the selected destination.");
        }

        // Close statement
        $stmt->close();
        return $destination;
    }

    public function closeConnection() {
        $this->conn->close();
    }
}

class DestinationDetailsView {
    public function render($destination) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Destination Details</title>
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
                .details-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                .details-table, th, td {
                    border: 1px solid orange;
                }
                th, td {
                    padding: 15px;
                    text-align: left;
                }
                th {
                    background-color: black;
                    color: orange;
                }
                tr:nth-child(even) {
                    background-color: #333;
                }
                tr:hover {
                    background-color: #555;
                }
                .navbar {
                    background-color: black;
                    color: orange;
                    padding: 15px;
                    text-align: center;
                    margin-top: -20px;
                    width: 100%;
                }
                .navbar a {
                    color: orange;
                    text-decoration: none;
                    padding: 10px 20px;
                }
                .navbar a:hover {
                    background-color: #444;
                }
                .confirm-button {
                    background-color: orange;
                    color: black;
                    border: none;
                    padding: 10px 20px;
                    font-size: 16px;
                    cursor: pointer;
                }
                .confirm-button:hover {
                    background-color: #ff8c00;
                }
            </style>
        </head>
        <body>

        <div class="navbar">
            <a href="index.html">Home</a>
            <a href="TouristLogin.html">Login Page</a>
            <a href="AdminContact.html">Admin Contact</a>
        </div>

        <h1>Destination Details</h1>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Destination Name</th>
                    <th>Budget</th>
                    <th>Days</th>
                    <th>Hotels</th>
                    <th>Hotel Descriptions</th>
                    <th>Restaurants</th>
                    <th>Restaurant Descriptions</th>
                    <th>Spots</th>
                    <th>Spot Descriptions</th>
                    <th>Transports</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($destination["DESTINATION_NAME"]); ?></td>
                    <td><?php echo htmlspecialchars($destination["BUDGET"]); ?></td>
                    <td><?php echo htmlspecialchars($destination["DAYS"]); ?></td>
                    <td><?php echo htmlspecialchars($destination["HOTELS"]); ?></td>
                    <td><?php echo htmlspecialchars($destination["HOTEL_DESCRIPTIONS"]); ?></td>
                    <td><?php echo htmlspecialchars($destination["RESTAURANTS"]); ?></td>
                    <td><?php echo htmlspecialchars($destination["RESTAURANT_DESCRIPTIONS"]); ?></td>
                    <td><?php echo htmlspecialchars($destination["SPOTS"]); ?></td>
                    <td><?php echo htmlspecialchars($destination["SPOT_DESCRIPTIONS"]); ?></td>
                    <td><?php echo htmlspecialchars($destination["TRANSPORTS"]); ?></td>
                </tr>
            </tbody>
        </table>

        <form action="confirm_tour.php" method="POST">
            <input type="hidden" name="DESTINATION_ID" value="<?php echo htmlspecialchars($destination["DESTINATION_ID"]); ?>">
            <input type="hidden" name="Planned_Destination" value="<?php echo htmlspecialchars($destination["DESTINATION_NAME"]); ?>">
            <input type="hidden" name="Planned_Days" value="<?php echo htmlspecialchars($destination["DAYS"]); ?>">
            <input type="hidden" name="Planned_Budget" value="<?php echo htmlspecialchars($destination["BUDGET"]); ?>">
            <input type="submit" class="confirm-button" value="Confirm Tour Plan">
        </form>

        </body>
        </html>
        <?php
    }
}

// Main code execution
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tourmanagementsystem";

$tourSystem = new TourManagementSystem($servername, $username, $password, $dbname);

// Retrieve the destination ID
$destination_id = $_POST['DESTINATION_ID'] ?? null;
if ($destination_id === null) {
    die("Destination ID is missing.");
}

// Get destination details
$destination = $tourSystem->getDestinationDetails($destination_id);

// Render the view
$view = new DestinationDetailsView();
$view->render($destination);

// Close the connection
$tourSystem->closeConnection();
?>
