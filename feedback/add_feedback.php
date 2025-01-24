<?php
session_start();
include '../includes/db.php';

// Ensure the user is logged in as an educator
if ($_SESSION['role'] != 'educator') {
    header('Location: ../auth/login.php');
    exit();
}

// Get the form ID from the query string
$form_id = $_GET['id'] ?? null;
if (!$form_id) {
    die("Form ID is missing.");
}

// Verify that the form exists and has been submitted
$query_form = "SELECT * FROM forms WHERE id = '$form_id' AND status = 'submitted'";
$result_form = $conn->query($query_form);

if ($result_form->num_rows == 0) {
    die("The form does not exist or has not been submitted.");
}

// Check if feedback already exists for this form
$query_feedback_check = "SELECT * FROM feedback WHERE form_id = '$form_id'";
$result_feedback_check = $conn->query($query_feedback_check);

if ($result_feedback_check->num_rows > 0) {
    die("Feedback has already been provided for this form.");
}

// Handle feedback submission
$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comments = $conn->real_escape_string($_POST['comments']);
    $educator_id = $_SESSION['user_id'];

    // Insert feedback and update form status
    $query_feedback = "INSERT INTO feedback (form_id, educator_id, comments) VALUES ('$form_id', '$educator_id', '$comments')";
    $query_update_status = "UPDATE forms SET status = 'reviewed' WHERE id = '$form_id'";

    if ($conn->query($query_feedback) && $conn->query($query_update_status)) {
        $success_message = "Feedback submitted successfully!";
    } else {
        $error_message = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provide Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white text-center py-4">
        <h1>Provide Feedback</h1>
    </header>

    <main class="container mt-4">
        <?php if (!empty($success_message)) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($success_message) ?>
                <a href="../dashboard/educator.php" class="btn btn-link">Back to Dashboard</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } elseif (!empty($error_message)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <form method="POST" class="bg-light p-4 rounded shadow">
            <div class="mb-3">
                <label for="comments" class="form-label">Feedback</label>
                <textarea id="comments" name="comments" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Feedback</button>
            <a href="../dashboard/educator.php" class="btn btn-secondary">Cancel</a>
        </form>
    </main>

    <footer class="bg-light text-center py-4 mt-4">
        <p>&copy; 2024 NutriTrack. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
