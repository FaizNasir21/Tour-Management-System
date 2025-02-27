<?php
// TouristManager.php
class TouristManager {
    private $conn;

    public function __construct() {
        $this->connectDB();
    }

    private function connectDB() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "tourmanagementsystem";

        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getTouristById($tourist_id) {
        $sql = "SELECT * FROM tourist WHERE TOURIST_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $tourist_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tourist = $result->fetch_assoc();
        $stmt->close();
        return $tourist;
    }

    public function closeConnection() {
        $this->conn->close();
    }
}

// Handle request
$touristData = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['TOURIST_ID'])) {
    $tourist_id = $_POST['TOURIST_ID'];
    $touristManager = new TouristManager();
    $touristData = $touristManager->getTouristById($tourist_id);
    $touristManager->closeConnection();
}

if (!$touristData) {
    die("Invalid Tourist ID or No Data Found");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Tourist</title>
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
        }
        .form-group button:hover {
            background-color: #ffb84d;
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
    <a href="tourist_data.php">Back to Tourist Data</a>
</div>

<div class="form-container">
    <h1>Edit Tourist</h1>
    <form action="update_tourist.php" method="post">
        <input type="hidden" name="TOURIST_ID" value="<?php echo htmlspecialchars($touristData['TOURIST_ID']); ?>">
        <div class="form-group">
            <label for="TOURIST_NAME">Name</label>
            <input type="text" name="TOURIST_NAME" value="<?php echo htmlspecialchars($touristData['TOURIST_NAME']); ?>" required>
        </div>
        <div class="form-group">
            <label for="TOURIST_LOCATION">Location</label>
            <input type="text" name="TOURIST_LOCATION" value="<?php echo htmlspecialchars($touristData['TOURIST_LOCATION']); ?>" required>
        </div>
        <div class="form-group">
            <label for="TOURIST_UserName">Username</label>
            <input type="text" name="TOURIST_UserName" value="<?php echo htmlspecialchars($touristData['TOURIST_UserName']); ?>" required>
        </div>
        <div class="form-group">
            <label for="TOURIST_Password">Password</label>
            <input type="password" name="TOURIST_Password" value="<?php echo htmlspecialchars($touristData['TOURIST_Password']); ?>" required>
        </div>
        <div class="form-group">
            <label for="TOURIST_Phone">Phone</label>
            <input type="text" name="TOURIST_Phone" value="<?php echo htmlspecialchars($touristData['TOURIST_Phone']); ?>" required>
        </div>
        <div class="form-group">
            <button type="submit">Update</button>
        </div>
    </form>
</div>

</body>
</html>
