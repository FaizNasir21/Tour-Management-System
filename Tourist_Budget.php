<?php
class BudgetSearch {
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

    // Method to retrieve destinations within the given budget
    public function getDestinationsWithinBudget($tourist_budget) {
        // Validate budget input
        if (!is_numeric($tourist_budget) || $tourist_budget < 0) {
            die("Invalid budget input.");
        }

        // SQL query to find destinations within the given budget
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
            WHERE d.BUDGET <= ?
            GROUP BY 
                d.DESTINATION_ID, d.DESTINATION_NAME, d.BUDGET, d.DAYS;
        ";

        // Prepare and execute SQL statement
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("d", $tourist_budget);
        $stmt->execute();
        return $stmt->get_result(); // Return result for further processing in the view
    }

    // Destructor to close the connection
    public function __destruct() {
        $this->conn->close();
    }
}

// Handling the form submission and displaying results
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tourist_budget = isset($_POST['tourist_budget']) ? $_POST['tourist_budget'] : 0;
    
    // Create an instance of DestinationSearch class and fetch the results
    $destinationSearch = new BudgetSearch();
    $result = $destinationSearch->getDestinationsWithinBudget($tourist_budget);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Destinations within Budget</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
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
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.html">Home</a>
    <a href="TouristLogin.html">Login Page</a>
    <a href="AdminContact.html">Admin Contact</a>
</div>

<h1>Destinations within Your Budget</h1>

<table>
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
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Check if there are results and display them
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . htmlspecialchars($row["DESTINATION_NAME"]) . "</td>
                    <td>" . htmlspecialchars($row["BUDGET"]) . "</td>
                    <td>" . htmlspecialchars($row["DAYS"]) . "</td>
                    <td>" . htmlspecialchars($row["HOTELS"]) . "</td>
                    <td>" . htmlspecialchars($row["HOTEL_DESCRIPTIONS"]) . "</td>
                    <td>" . htmlspecialchars($row["RESTAURANTS"]) . "</td>
                    <td>" . htmlspecialchars($row["RESTAURANT_DESCRIPTIONS"]) . "</td>
                    <td>" . htmlspecialchars($row["SPOTS"]) . "</td>
                    <td>" . htmlspecialchars($row["SPOT_DESCRIPTIONS"]) . "</td>
                    <td>" . htmlspecialchars($row["TRANSPORTS"]) . "</td>
                    <td><form action='destination_details.php' method='POST'>
                        <input type='hidden' name='DESTINATION_ID' value='" . htmlspecialchars($row["DESTINATION_ID"]) . "'>
                        <input type='submit' value='Select'>
                    </form></td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='11'>No destinations found within your budget.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
