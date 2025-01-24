<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include '../includes/db.php';

if ($_SESSION['role'] != 'student') {
    header('Location: ../auth/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Form - Dietetic Care Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header class="bg-primary text-white text-center p-3">
        <h1>Create New Dietetic Care Note</h1>
    </header>
    <main class="container mt-4">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#nutrition_assessment" data-bs-toggle="tab">Nutrition Assessment</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#nutrition_diagnosis" data-bs-toggle="tab">Nutrition Diagnosis</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#nutrition_intervention" data-bs-toggle="tab">Nutrition Intervention</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#nutrition_monitoring" data-bs-toggle="tab">Nutrition Monitoring</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="nutrition_assessment">
                <?php include('nutrition_assessment.php'); ?>
            </div>
            <div class="tab-pane fade" id="nutrition_diagnosis">
                <?php include('nutrition_diagnosis.php'); ?>
            </div>
            <div class="tab-pane fade" id="nutrition_intervention">
                <?php include('nutrition_intervention.php'); ?>
            </div>
            <div class="tab-pane fade" id="nutrition_monitoring">
                <?php include('nutrition_monitoring.php'); ?>
            </div>
        </div>
    </main>
    <footer class="bg-light text-center p-3 mt-4">
        <p>&copy; 2025 NutriTrack. All rights reserved.</p>
    </footer>
</body>
</html>
