<?php
session_start();
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "patient_db"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";

// Handle login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $patient_id = $_POST['patient_id'];

    // Validate input
    if (!empty($username) && !empty($patient_id)) {
        // Query to check credentials
        $sql = "SELECT * FROM patients WHERE patient_name = ? AND patient_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Login successful
            $patient_data = $result->fetch_assoc();
            $_SESSION['patient_id'] = $patient_data['patient_id'];
            $_SESSION['patient_name'] = $patient_data['patient_name'];
            header("Location: patient_dashboard.php");
            exit;
        } else {
            // Login failed
            $error_message = "Invalid username or Patient ID.";
        }
        $stmt->close();
    } else {
        $error_message = "Please fill in all fields.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Login - JHemophilia</title>
    <style>
        /* CSS styles here */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1faee;
            color: #1d3557;
        }

        .login-container {
            width: 90%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #e63946;
        }

        h2 {
            color: #e63946;
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: #457b9d;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #e63946;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #d62828;
        }

        .error {
            color: #e63946;
            text-align: center;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Patient Login</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="patient_login.php" method="post">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <label for="patient_id">Patient ID</label>
            <input type="text" id="patient_id" name="patient_id" required>
            <input type="submit" value="Login">
        </form>
    </div>
    <footer>
        &copy; <?php echo date("Y"); ?> ISOLATE
    </footer>
</body>
</html>
