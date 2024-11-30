<?php
session_start();

// Redirect to login if the patient is not logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
    exit;
}

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

// Fetch patient details
$patient_id = $_SESSION['patient_id'];
$sql = "SELECT * FROM patients WHERE patient_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $patient_data = $result->fetch_assoc();
} else {
    echo "Error fetching patient data.";
    exit;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - JHemophilia</title>
    <style>
        /* Embedded CSS */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1faee;
            color: #1d3557;
        }

        .dashboard-container {
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #e63946;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a.button {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #e63946;
            color: white;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
        }

        a.button:hover {
            background-color: #d62828;
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
    <div class="dashboard-container">
        <h2>Welcome, <?php echo htmlspecialchars($patient_data['patient_name']); ?>!</h2>
        <table>
            <tr>
                <th>Patient ID</th>
                <td><?php echo htmlspecialchars($patient_data['patient_id']); ?></td>
            </tr>
            <tr>
                <th>Age</th>
                <td><?php echo htmlspecialchars($patient_data['age']); ?></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><?php echo htmlspecialchars($patient_data['phone']); ?></td>
            </tr>
            <tr>
                <th>Home Information</th>
                <td><?php echo htmlspecialchars($patient_data['home_info']); ?></td>
            </tr>
            <tr>
                <th>Injections</th>
                <td><?php echo htmlspecialchars($patient_data['injections']); ?></td>
            </tr>
        </table>
        <p><a class="button" href="logout.php">Logout</a></p>
    </div>
    <footer>
        &copy; <?php echo date("Y"); ?> ISOLATE
    </footer>
</body>
</html>
