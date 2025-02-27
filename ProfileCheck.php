<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class TouristProfile
{
    private $conn;
    private $tourist_id;
    private $tourist;

    public function __construct($tourist_id)
    {
        $this->tourist_id = $tourist_id;
        $this->connectDatabase();
    }

    private function connectDatabase()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "tourmanagementsystem";

        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getTouristInfo()
    {
        $sql = "SELECT * FROM tourist WHERE TOURIST_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->tourist_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->tourist = $result->fetch_assoc();
        $stmt->close();
        return $this->tourist;
    }

    public function updateProfile($name, $location, $username, $phone, $password)
    {
        if (!empty($name) && !empty($location) && !empty($username) && !empty($phone) && !empty($password)) {
            $sql = "UPDATE tourist SET TOURIST_NAME = ?, TOURIST_LOCATION = ?, TOURIST_UserName = ?, TOURIST_Phone = ?, TOURIST_Password = ? WHERE TOURIST_ID = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssssi", $name, $location, $username, $phone, $password, $this->tourist_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    return "Profile updated successfully.";
                } else {
                    return "No changes made or update failed.";
                }
            } else {
                return "Update failed: " . $stmt->error;
            }
            $stmt->close();
        } else {
            return "Please fill all fields.";
        }
    }

    public function deleteProfile()
    {
        $sql = "DELETE FROM tourist WHERE TOURIST_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->tourist_id);
        $stmt->execute();
        $stmt->close();
        session_destroy();
        header("Location: index.html");
        exit();
    }

    public function deleteTourPlan($plan_id)
    {
        $sql = "DELETE FROM tourplan WHERE TOURIST_ID = ? AND TOURPLAN_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $this->tourist_id, $plan_id);
        $stmt->execute();
        $stmt->close();
    }

    public function getTourPlans()
    {
        $sql = "SELECT * FROM tourplan WHERE TOURIST_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->tourist_id);
        $stmt->execute();
        $tour_plans = $stmt->get_result();
        $stmt->close();
        return $tour_plans;
    }

    public function closeConnection()
    {
        $this->conn->close();
    }
}

// Check if user is logged in
if (!isset($_SESSION['tourist_id'])) {
    header("Location: TouristLogin.html"); // Redirect to login page if not logged in
    exit();
}

$tourist_id = $_SESSION['tourist_id'];
$touristProfile = new TouristProfile($tourist_id);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $message = $touristProfile->updateProfile($_POST['name'], $_POST['location'], $_POST['username'], $_POST['phone'], $_POST['password']);
    echo "<p style='color: green;'>$message</p>";
    // Refresh the page to show updated data
    header("Refresh: 1; url=ProfileCheck.php");
    exit();
}

// Handle profile deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $touristProfile->deleteProfile();
}

// Handle tour plan deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_plan'])) {
    $plan_id = $_POST['plan_id'];
    $touristProfile->deleteTourPlan($plan_id);
    header("Refresh: 0; url=ProfileCheck.php"); // Refresh the page
    exit();
}

$tourist = $touristProfile->getTouristInfo();
$tour_plans = $touristProfile->getTourPlans();
$touristProfile->closeConnection();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile</title>
    <style>
        /* Styles are the same as your existing styles */
         body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(to right, #434343, #000000);
            color: white;
            margin: 0;
            padding: 20px;
        }
        .navbar {
            background-color: black;
            color: orange;
            padding: 15px;
            text-align: center;
            margin-top: -20px;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1;
        }
        .navbar a {
            color: orange;
            text-decoration: none;
            padding: 10px 20px;
        }
        .navbar a:hover {
            background-color: #444;
        }
        .container {
            margin-top: 100px;
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            margin-top: 0;
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
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .form-container input[type="text"],
        .form-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-container button {
            background-color: orange;
            color: black;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }
        .form-container button:hover {
            background-color: #ff8c00;
        }
        .delete-button {
            background-color: #dc3545;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.html">Home</a>
        <a href="TouristLogin.html">Login Page</a>
        <a href="AdminContact.html">Admin Contact</a>
        <a href="ProfileCheck.php">Profile</a>
    </div>

    <div class="container">
        <h1>Tourist Profile</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Location</th>
                <th>Username</th>
                <th>Phone</th>
                <th>Password</th>
            </tr>
            <tr>
                <td><?php echo htmlspecialchars($tourist['TOURIST_ID']); ?></td>
                <td><?php echo htmlspecialchars($tourist['TOURIST_NAME']); ?></td>
                <td><?php echo htmlspecialchars($tourist['TOURIST_LOCATION']); ?></td>
                <td><?php echo htmlspecialchars($tourist['TOURIST_UserName']); ?></td>
                <td><?php echo htmlspecialchars($tourist['TOURIST_Phone']); ?></td>
                <td><?php echo htmlspecialchars($tourist['TOURIST_Password']); ?></td>
            </tr>
        </table>

        <div class="form-container">
            <h2>Update Profile</h2>
            <form method="post">
                <input type="text" name="name" value="<?php echo htmlspecialchars($tourist['TOURIST_NAME']); ?>" placeholder="Name">
                <input type="text" name="location" value="<?php echo htmlspecialchars($tourist['TOURIST_LOCATION']); ?>" placeholder="Location">
                <input type="text" name="username" value="<?php echo htmlspecialchars($tourist['TOURIST_UserName']); ?>" placeholder="Username">
                <input type="text" name="phone" value="<?php echo htmlspecialchars($tourist['TOURIST_Phone']); ?>" placeholder="Phone">
                <input type="password" name="password" value="<?php echo htmlspecialchars($tourist['TOURIST_Password']); ?>" placeholder="Password">
                <button type="submit" name="update">Update</button>
            </form>
        </div>

        <div class="form-container">
            <h2>Delete Profile</h2>
            <form method="post">
                <button type="submit" name="delete" class="delete-button">Delete Profile</button>
            </form>
        </div>

        <div class="form-container">
            <h2>Your Tour Plans</h2>
            <?php if ($tour_plans->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Plan ID</th>
                        <th>Admin ID</th>
                        <th>Destination ID</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($plan = $tour_plans->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($plan['TOURPLAN_ID']); ?></td>
                            <td><?php echo htmlspecialchars($plan['ADMIN_ID']); ?></td>
                            <td><?php echo htmlspecialchars($plan['DESTINATION_ID']); ?></td>
                            <td><?php echo htmlspecialchars($plan['START_DATE']); ?></td>
                            <td><?php echo htmlspecialchars($plan['END_DATE']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="plan_id" value="<?php echo htmlspecialchars($plan['TOURPLAN_ID']); ?>">
                                    <button type="submit" name="delete_plan" class="delete-button">Delete Plan</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>You have no tour plans.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
