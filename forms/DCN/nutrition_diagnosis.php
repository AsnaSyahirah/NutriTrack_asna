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
    $problems = $_POST['problem'];
    $etiologies = $_POST['etiology'];
    $symptoms = $_POST['symptoms'];

    // Insert data into the database
    for ($i = 0; $i < count($problems); $i++) {
        $problem = $problems[$i];
        $etiology = $etiologies[$i];
        $symptoms = $symptoms[$i];

        $sql = "INSERT INTO nutrition_diagnosis (problem, etiology, symptoms) VALUES ('$problem', '$etiology', '$symptoms')";
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
    <title>Nutrition Diagnosis Form</title>
    <link href="../css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var diagnosisCount = 1;

            // Add new diagnosis form
            $('.add-button').click(function() {
                diagnosisCount++;
                var newDiagnosis = `
                    <h3 class="diagnosis-title">${diagnosisCount} Diagnosis</h3>
                    <label for="problem" class="form-label">Problem:</label>
                    <textarea id="problem" name="problem[]" class="input-field" aria-label="Problem description"></textarea>
                    
                    <label for="etiology" class="form-label">Etiology:</label>
                    <textarea id="etiology" name="etiology[]" class="input-field" aria-label="Etiology description"></textarea>
                    
                    <label for="symptoms" class="form-label">Sign and Symptoms:</label>
                    <textarea id="symptoms" name="symptoms[]" class="input-field" aria-label="Signs and symptoms description"></textarea>
                `;
                $(newDiagnosis).insertBefore('.button-group');
            });
        });
    </script>
</head>
<body>
<div class="nutrition-container">
  
  <main class="content-wrapper">
    <h1 class="page-title">Dietetic Care Notes</h1>

    <h2 class="section-title">Nutrition Diagnosis</h2>
    <h3 class="diagnosis-title">1st Diagnosis</h3>
    <form action="nutrition_diagnosis.php" method="POST">
      <label for="problem" class="form-label">Problem:</label>
      <textarea id="problem" name="problem[]" class="input-field" aria-label="Problem description"></textarea>
      
      <label for="etiology" class="form-label">Etiology:</label>
      <textarea id="etiology" name="etiology[]" class="input-field" aria-label="Etiology description"></textarea>
      
      <label for="symptoms" class="form-label">Sign and Symptoms:</label>
      <textarea id="symptoms" name="symptoms[]" class="input-field" aria-label="Signs and symptoms description"></textarea>
      
      <div class="button-group">
        <button type="button" class="add-button">Add</button>
        <button type="submit" class="save-button">Save</button>
      </div>
    </form>
  </main>
</div>
</body>
</html>