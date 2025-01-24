<?php
session_start();
include '../includes/db.php';

$form_id = $_GET['id'];
$query = "SELECT * FROM forms WHERE id='$form_id'";
$result = $conn->query($query);

if ($result->num_rows == 1) {
    $form = $result->fetch_assoc();
} else {
    echo "<div class='alert alert-danger'>Form not found.</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="bg-primary text-white text-center p-3">
        <h1>View Form</h1>
    </header>
    <main class="container mt-4">
        <div class="card shadow">
            <div class="card-header">
                <h5>Form Details</h5>
            </div>
            <div class="card-body">
                <p><strong>Type:</strong> <?= htmlspecialchars($form['type']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($form['status']) ?></p>
                <p><strong>Data:</strong> <?= nl2br(htmlspecialchars($form['data'])) ?></p>
                <p><strong>Height:</strong> <?= htmlspecialchars($form['height']) ?> meters</p>
                <p><strong>Weight:</strong> <?= htmlspecialchars($form['weight']) ?> kg</p>
                <p><strong>BMI Category:</strong> <?= htmlspecialchars($form['bmi_category']) ?></p>
            </div>
        </div>
        <a href="../dashboard/student.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </main>
    <footer class="bg-light text-center p-3 mt-4">
        <p>&copy; 2024 NutriTrack. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
