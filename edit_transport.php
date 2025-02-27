<?php
// edit_transport.php

class Transport {
    private $conn;
    private $transport_id;

    public function __construct($transport_id) {
        $this->conn = new mysqli("localhost", "root", "", "tourmanagementsystem");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        $this->transport_id = $transport_id;
    }

    // Fetch transport data based on TRANSPORT_ID
    public function fetchTransportData() {
        $sql = "SELECT * FROM transport WHERE TRANSPORT_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->transport_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Update transport data
    public function updateTransport($transport_type, $transport_capacity, $transport_price, $destination_id) {
        $sql = "UPDATE transport SET TRANSPORT_TYPE = ?, TRANSPORT_CAPACITY = ?, TRANSPORT_PRICE = ?, DESTINATION_ID = ? WHERE TRANSPORT_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sissi", $transport_type, $transport_capacity, $transport_price, $destination_id, $this->transport_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Close the database connection
    public function __destruct() {
        $this->conn->close();
    }
}

// Handle form submission for updating the transport data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['TRANSPORT_ID'])) {
    $transport_id = $_GET['TRANSPORT_ID'];
    $transport = new Transport($transport_id);

    $transport_type = $_POST['TRANSPORT_TYPE'];
    $transport_capacity = $_POST['TRANSPORT_CAPACITY'];
    $transport_price = $_POST['TRANSPORT_PRICE'];
    $destination_id = $_POST['DESTINATION_ID'];

    if ($transport->updateTransport($transport_type, $transport_capacity, $transport_price, $destination_id)) {
        echo "<script>alert('Transport updated successfully.'); window.location.href = 'transport_data.php';</script>";
    } else {
        echo "<p>Error updating transport.</p>";
    }
}

// If the transport ID is not set in the URL, show an error
if (isset($_GET['TRANSPORT_ID'])) {
    $transport_id = $_GET['TRANSPORT_ID'];
    $transport = new Transport($transport_id);
    $transport_data = $transport->fetchTransportData();
} else {
    die("Transport ID is missing.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Transport</title>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(to right, #434343, #000000);
            color: white;
            margin: 0;
            padding: 20px;
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
    </style>
</head>
<body>

<h1>Edit Transport</h1>

<form method="post">
    <label for="TRANSPORT_TYPE">Transport Type:</label>
    <input type="text" id="TRANSPORT_TYPE" name="TRANSPORT_TYPE" value="<?php echo htmlspecialchars($transport_data['TRANSPORT_TYPE']); ?>" required>

    <label for="TRANSPORT_CAPACITY">Transport Capacity:</label>
    <input type="number" id="TRANSPORT_CAPACITY" name="TRANSPORT_CAPACITY" value="<?php echo htmlspecialchars($transport_data['TRANSPORT_CAPACITY']); ?>" required>

    <label for="TRANSPORT_PRICE">Transport Price:</label>
    <input type="number" id="TRANSPORT_PRICE" name="TRANSPORT_PRICE" value="<?php echo htmlspecialchars($transport_data['TRANSPORT_PRICE']); ?>" required>

    <label for="DESTINATION_ID">Destination ID:</label>
    <input type="number" id="DESTINATION_ID" name="DESTINATION_ID" value="<?php echo htmlspecialchars($transport_data['DESTINATION_ID']); ?>" required>

    <input type="submit" value="Update Transport">
</form>

</body>
</html>
