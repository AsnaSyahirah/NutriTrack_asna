<?php
session_start();
include '../includes/db.php';

// Ensure the user is logged in as a student
if ($_SESSION['role'] != 'student') {
    header('Location: ../auth/login.php');
    exit();
}

// Fetch the form to edit
$form_id = $_GET['id'];
$query = "SELECT * FROM forms WHERE id='$form_id' AND user_id=" . $_SESSION['user_id'];
$result = $conn->query($query);

if ($result->num_rows == 1) {
    $form = $result->fetch_assoc();
} else {
    echo "Form not found.";
    exit();
}

$success_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST['data'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];

    // Recalculate BMI
    $bmi = $weight / ($height * $height);

    // Determine BMI category
    $bmi_category = '';
    if ($bmi < 18.5) {
        $bmi_category = 'Underweight';
    } elseif ($bmi < 24.9) {
        $bmi_category = 'Normal';
    } elseif ($bmi < 29.9) {
        $bmi_category = 'Overweight';
    } else {
        $bmi_category = 'Obese';
    }

    $query = "UPDATE forms SET data='$data', height='$height', weight='$weight', bmi_category='$bmi_category', status='draft' WHERE id='$form_id'";
    if ($conn->query($query)) {
        $success_message = "Form updated successfully!";
    } else {
        $success_message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="bg-primary text-white text-center py-3">
        <h1>Edit Form</h1>
    </header>

    <main class="container mt-4">
        <?php if (!empty($success_message)) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($success_message) ?>
                <a href="../dashboard/student.php" class="btn btn-link">Back to Dashboard</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <form method="POST" class="bg-light p-4 rounded shadow">
            <div class="mb-3">
                <label for="data" class="form-label">Form Data</label>
                <textarea id="data" name="data" class="form-control" rows="5" required><?= htmlspecialchars($form['data']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="height" class="form-label">Height (in meters)</label>
                <input type="number" step="0.01" name="height" id="height" class="form-control" value="<?= htmlspecialchars($form['height']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="weight" class="form-label">Weight (in kilograms)</label>
                <input type="number" step="0.01" name="weight" id="weight" class="form-control" value="<?= htmlspecialchars($form['weight']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="../dashboard/student.php" class="btn btn-secondary">Cancel</a>
        </form>
    </main>

    <footer class="bg-light text-center py-4 mt-4">
        <p>&copy; 2024 NutriTrack. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
