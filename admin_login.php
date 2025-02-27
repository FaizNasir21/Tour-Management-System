<?php
session_start();

class AdminLogin {
    private $username;
    private $password;
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function setCredentials($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function verifyLogin() {
        $query = "SELECT Admin_Password FROM admin WHERE Admin_UserName = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($storedPassword);
            $stmt->fetch();

            // **Simple password comparison (without hashing)**
            if ($this->password === $storedPassword) {
                $_SESSION['admin'] = $this->username; // Store admin session
                return true;
            }
        }
        return false;
    }
}

// Database connection
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "tourmanagementsystem";
    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $admin = new AdminLogin($database->conn);
    $admin->setCredentials($_POST['username'], $_POST['password']);
    
    if ($admin->verifyLogin()) {
        echo "<script>alert('Login Successful!'); window.location.href='AdminInterface.html';</script>";
    } else {
        echo "<script>alert('Invalid Credentials!'); window.location.href='AdminLogin.html';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Form</title>
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;600;700&display=swap');

        body,html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            
        }
    .bg-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('admin.jpg'); /* Replace with your image URL */
        background-size: cover; /* Cover the entire background */
        background-position: center; /* Center the background image */
        background-repeat: no-repeat; /* Prevent repeating the background image */
        filter: blur(10px); /* Apply blur effect */
        z-index: -1; /* Place behind all other content */
    }

    .navbar{
        background-color: black;
        width: 100%;
        height: 8%;
        display: flex;
        justify-content: space-around;
        align-items: center;
        color: orange;
        font-size: 1.5em;
        position: fixed;
        top: 0;
        z-index: 1; 
    }
    .navbar a{
        text-decoration: none;
        color: orange;
        font-family: 'Nunito', sans-serif;
    }
    .navbar a:hover {
            background-color: #333;
            color: white;
            border-radius: 5px;
        }
    .container{
        border: 10px solid orange;
        align-content: center;
        width: 40%;
        background-color: gray;
        padding-bottom: 18px;
        padding-left: 40px;
        margin-top: 15%;
        margin-left: 28%;
        position: fixed;
        z-index: 1;


    }
    .form-container {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 300px;
        margin-top: 10%; 
        }   
    h1{
        text-align: center;
        color: black;
        margin-left: -50px;
        font-family: 'Nunito', sans-serif;
    }
    label{
        display: block;
        margin: 10px 0 5px;
        color: #333;
        font-family: 'Nunito', sans-serif;
        color: black;
    }

    input[type="text"],input[type="password"]{
        width: 80%;
        padding: 10px;
        margin: 5px 0 10px;
        border: 2px solid orange;
        border-radius: 5px;

    }
    button{
        margin-left: 190px;
        text-decoration: none;


    }
    </style>
</head>
<body>
    <div class="bg-container"></div>

    <div class="navbar">
        <a href="index.html">Home</a>
        <a href="#">Contact Us</a>
        <a href="#">Help Center</a>  
    </div>

    <div class="container">
        <form method="post" action="">
            <h1>Admin Login</h1>
            <label>UserName</label>
            <input type="text" name="username" placeholder="Enter your UserName" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your Password" required><br>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
