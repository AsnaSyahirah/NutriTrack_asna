<?php
session_start();
include '../includes/db.php';

// Ensure the user is logged in as a student
if ($_SESSION['role'] != 'student') {
    header('Location: ../auth/login.php');
    exit();
}

// Get the form ID from the query string
$form_id = $_GET['id'];

// Fetch feedback for the specified form
$query = "SELECT f.type, fb.comments, fb.created_at 
          FROM forms f
          LEFT JOIN feedback fb ON f.id = fb.form_id 
          WHERE f.id = '$form_id' AND f.user_id = " . $_SESSION['user_id'];
$result = $conn->query($query);

if ($result->num_rows == 0) {
    die("No feedback available for this form.");
}

$feedback = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white text-center py-4">
        <h1>View Feedback</h1>
    </header>

    <main class="container mt-4">
        <div class="card p-4 shadow">
            <h2 class="card-title"><?= htmlspecialchars($feedback['type']) ?> Form</h2>
            <p><strong>Feedback:</strong> <?= htmlspecialchars($feedback['comments']) ?></p>
            <p><small><em>Provided on <?= $feedback['created_at'] ?></em></small></p>
        </div>
        <a href="../dashboard/student.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </main>

    <footer class="bg-light text-center py-4 mt-4">
        <p>&copy; 2024 NutriTrack. All rights reserved.</p>
    </footer>
</body>
</html>
