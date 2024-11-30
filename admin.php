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

// Initialize query
$sql = "SELECT id, patient_name, patient_id, age, phone, home_info, injections FROM patients";
$search_id = '';

// Handle search query
if (isset($_POST['search_id'])) {
    $search_id = $_POST['search_id'];
    if (!empty($search_id)) {
        $sql .= " WHERE patient_id = ?";  // Search by patient_id
    }
}

// Prepare and execute statement
$stmt = $conn->prepare($sql);
if (!empty($search_id)) {
    $stmt->bind_param("s", $search_id);
}
$stmt->execute();
$result = $stmt->get_result();

// Handle injection update
if (isset($_POST['update_injections']) && isset($_POST['patient_id']) && isset($_POST['injections'])) {
    $update_id = $_POST['patient_id'];
    $new_injections = $_POST['injections'];
    
    $update_sql = "UPDATE patients SET injections = ? WHERE patient_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("is", $new_injections, $update_id);
    
    if ($update_stmt->execute()) {
        echo "<p class='success'>Injection count updated successfully.</p>";
    } else {
        echo "<p class='error'>Error updating injection count: " . $update_stmt->error . "</p>";
    }
    $update_stmt->close();
}

// Handle patient information update
if (isset($_POST['update_patient']) && isset($_POST['patient_id'])) {
    $update_id = $_POST['patient_id'];
    $name = $_POST['patient_name'];
    $age = $_POST['age'];
    $phone = $_POST['phone'];
    $home_info = $_POST['home_info'];
    $injections = $_POST['injections'];

    $update_sql = "UPDATE patients SET patient_name = ?, age = ?, phone = ?, home_info = ?, injections = ? WHERE patient_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssss", $name, $age, $phone, $home_info, $injections, $update_id);
    
    if ($update_stmt->execute()) {
        echo "<p class='success'>Patient information updated successfully.</p>";
    } else {
        echo "<p class='error'>Error updating patient information: " . $update_stmt->error . "</p>";
    }
    $update_stmt->close();
}

// Handle patient deletion
if (isset($_POST['delete_patient']) && isset($_POST['delete_patient_id'])) {
    $delete_id = $_POST['delete_patient_id'];
    
    $delete_sql = "DELETE FROM patients WHERE patient_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("s", $delete_id);
    
    if ($delete_stmt->execute()) {
        echo "<p class='success'>Patient deleted successfully.</p>";
    } else {
        echo "<p class='error'>Error deleting patient: " . $delete_stmt->error . "</p>";
    }
    $delete_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Patient Information</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f8fb; /* Light blue-gray background */
            margin: 0;
            padding: 0;
        }

        .container {
            width: 85%;
            margin: 50px auto;
            background: #ffffff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 30px;
            border: 1px solid #ddd;
        }

        h2 {
            text-align: center;
            color: #004085; /* Dark blue for headings */
        }

        /* Search Form */
        .search-form {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-form input {
            padding: 10px;
            width: 250px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .search-form button {
            padding: 10px 20px;
            background-color: #007bff; /* Blue for search button */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #0056b3; /* Darker blue for hover effect */
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th {
            background-color: #004085; /* Dark blue for header */
            color: white;
            text-align: left;
            padding: 12px;
        }

        td {
            text-align: left;
            padding: 10px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Form Styles */
        .update-form input, .update-form textarea {
            padding: 12px;
            width: 100%;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .update-form button {
            padding: 12px 20px;
            background-color: #28a745; /* Green for update button */
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        .update-form button:hover {
            background-color: #218838; /* Darker green for hover effect */
        }

        /* Delete Button */
        .delete-btn {
            background-color: #dc3545; /* Red for delete button */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #c82333; /* Darker red for hover effect */
        }

        /* Success and Error Messages */
        .success {
            color: #28a745; /* Green for success message */
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }

        .error {
            color: #dc3545; /* Red for error message */
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            padding-top: 60px;
            transition: all 0.3s ease;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 40px;
            border-radius: 12px;
            width: 80%;
            max-width: 600px;
            border: 1px solid #888;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #004085;
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Patient Information</h2>
        
        <!-- Search Form -->
        <form action="admin.php" method="post" class="search-form">
            <label for="search_id">Search by Patient ID:</label>
            <input type="text" id="search_id" name="search_id" value="<?php echo htmlspecialchars($search_id); ?>">
            <button type="submit">Search</button>
        </form>
        
        <!-- Patient Information Table -->
        <table>
            <tr>
                <th>ID</th>
                <th>Patient Name</th>
                <th>Patient ID</th>
                <th>Age</th>
                <th>Phone Number</th>
                <th>Home Information</th>
                <th>Injections</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["patient_name"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["patient_id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["age"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["phone"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["home_info"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["injections"]) . "</td>";
                    echo "<td>
                        <button onclick=\"document.getElementById('editForm" . $row["id"] . "').style.display='block'\">Edit</button>
                    </td>";
                    echo "<td>
                        <form method='post'>
                            <input type='hidden' name='delete_patient_id' value='" . $row["patient_id"] . "'>
                            <button type='submit' name='delete_patient' class='delete-btn'>Delete</button>
                        </form>
                    </td>";
                    echo "</tr>";

                    // Edit Form for each patient
                    echo "<div id='editForm" . $row["id"] . "' class='modal'>
                        <div class='modal-content'>
                            <span class='close' onclick=\"document.getElementById('editForm" . $row["id"] . "').style.display='none'\">&times;</span>
                            <div class='modal-header'>
                                <h3>Edit Patient Information</h3>
                            </div>
                            <form action='admin.php' method='post' class='update-form'>
                                <input type='hidden' name='patient_id' value='" . htmlspecialchars($row["patient_id"]) . "'>
                                <label>Patient Name:</label>
                                <input type='text' name='patient_name' value='" . htmlspecialchars($row["patient_name"]) . "' required>
                                <label>Age:</label>
                                <input type='number' name='age' value='" . htmlspecialchars($row["age"]) . "' required>
                                <label>Phone Number:</label>
                                <input type='text' name='phone' value='" . htmlspecialchars($row["phone"]) . "' required>
                                <label>Home Information:</label>
                                <textarea name='home_info' required>" . htmlspecialchars($row["home_info"]) . "</textarea>
                                <label>Injections:</label>
                                <input type='number' name='injections' value='" . htmlspecialchars($row["injections"]) . "' required>
                                <button type='submit' name='update_patient'>Update Patient Info</button>
                            </form>
                        </div>
                    </div>";
                }
            } else {
                echo "<tr><td colspan='9'>No records found</td></tr>";
            }
            ?>
        </table>
    </div>

    <script>
        // JavaScript to open/close the modal edit form
        window.onclick = function(event) {
            var modal = document.querySelector('.modal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
