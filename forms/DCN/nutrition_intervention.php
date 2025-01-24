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
    $objectives = $_POST['objective'];
    $delivery = $_POST['delivery'];
    $education = $_POST['education'];
    $counseling = $_POST['counseling'];
    $coordination = $_POST['coordination'];

    // Insert data into the database
    for ($i = 0; $i < count($objectives); $i++) {
        $objective = $objectives[$i];

        $sql = "INSERT INTO nutrition_intervention (objective, delivery, education, counseling, coordination) VALUES ('$objective', '$delivery', '$education', '$counseling', '$coordination')";
        if ($conn->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition Intervention Form</title>
    <link href="../css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var objectiveCount = 1;

            // Add new objective form
            $('.add-button').click(function() {
                objectiveCount++;
                var newObjective = `
                    <label for="objective" class="form-label">Objective ${objectiveCount}:</label>
                    <textarea id="objective" name="objective[]" class="input-field" aria-label="Enter objective ${objectiveCount}"></textarea>
                `;
                $(newObjective).insertBefore('.plan-section');
            });
        });
    </script>
</head>
<body>
    <form action="nutrition_intervention.php" method="POST">
        <div class="form-section">
            <h1 class="page-title">Dietetic Care Notes</h1>
            <h2 class="section-title">Nutrition Intervention</h2>
        </div>
        <label for="objective" class="form-label">Objective 1:</label>
        <textarea id="objective" name="objective[]" class="input-field" aria-label="Enter objective 1"></textarea>
      
        <div class="button-group">
            <button type="button" class="add-button">Add Objective</button>
        </div>

        <section class="plan-section">
            <h3 class="plan-title">Plan</h3>
            <hr class="divider" />
            
            <label for="delivery" class="section-label">Food and/or Nutrient Delivery:</label>
            <textarea id="delivery" class="text-area" name="delivery" aria-label="Enter food and nutrient delivery details"></textarea>
            
            <label for="education" class="section-label">Nutrient Education:</label>
            <textarea id="education" class="text-area" name="education" aria-label="Enter nutrient education details"></textarea>
            
            <label for="counseling" class="section-label">Nutrient Counseling:</label>
            <textarea id="counseling" class="text-area" name="counseling" aria-label="Enter nutrient counseling details"></textarea>
            
            <label for="coordination" class="section-label">Coordination of Nutrition Care:</label>
            <textarea id="coordination" class="text-area" name="coordination" aria-label="Enter coordination details"></textarea>
            
            <button type="submit" class="save-button">Save</button>
        </section>
    </form>
</body>
</html>