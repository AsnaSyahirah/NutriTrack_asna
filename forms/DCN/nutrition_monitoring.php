<?php
// Start the session and potentially check for user authentication
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    // Redirect to login page if not authenticated or not a student
    header('Location: ../auth/login.php');
    exit();
}

// Placeholder for handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include '../includes/db.php';
    
    // Process your form data here
    // Extract and sanitize input data
    $hospital = htmlspecialchars($_POST['hospital']);
    $ward_bed = htmlspecialchars($_POST['ward_bed']);
    $date = htmlspecialchars($_POST['date']);
    $time = htmlspecialchars($_POST['time']);
    $name = htmlspecialchars($_POST['name']);
    $height = floatval($_POST['height']);
    $weight = floatval($_POST['weight']);

    // Prepare SQL and bind parameters
    $stmt = $conn->prepare("INSERT INTO nutrition_assessment (hospital, ward_bed, date, time, name, height, weight) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssd", $hospital, $ward_bed, $date, $time, $name, $height, $weight);

    if ($stmt->execute()) {
        echo "Form submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition Assessment Form</title>
    <link href="../css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <form action="nutrition_assessment.php" method="POST">
        <div class="form-section">
            <label for="hospital">Hospital:</label>
            <input type="text" id="hospital" name="hospital" required><br>

            <label for="ward_bed">Ward/Bed:</label>
            <input type="text" id="ward_bed" name="ward_bed"><br>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required><br>

            <label for="time">Time:</label>
            <input type="time" id="time" name="time"><br>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="height">Height (cm):</label>
            <input type="number" id="height" name="height" step="0.1"><br>

            <label for="weight">Weight (kg):</label>
            <input type="number" id="weight" name="weight" step="0.1"><br>

            <div id="additionalMeasurements"></div>
            <button type="button" onclick="addMeasurement()">Add Another Measurement</button><br>

            <input type="submit" value="Submit">
        </div>
    </form>

    <script>
        function addMeasurement() {
            var container = document.getElementById("additionalMeasurements");
            var html = '<label for="height">Height (cm):</label>' +
                '<input type="number" name="height" step="0.1"><br>' +
                '<label for="weight">Weight (kg):</label>' +
                '<input type="number" name="weight" step="0.1"><br>';
            container.innerHTML += html;
        }
    </script>
</body>
</html>
