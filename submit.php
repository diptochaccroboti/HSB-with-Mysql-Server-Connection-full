<?php
$servername = "localhost";
$username = "root";  // Replace with your MySQL username
$password = "";      // Replace with your MySQL password
$dbname = "patient_db"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO patients (patient_name, patient_id, age, phone, home_info) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssiss", $patient_name, $patient_id, $age, $phone, $home_info);

// Set parameters and execute
$patient_name = $_POST['patient_name'];
$patient_id = $_POST['patient_id'];
$age = $_POST['age'];
$phone = $_POST['phone'];
$home_info = $_POST['home_info'];

if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
