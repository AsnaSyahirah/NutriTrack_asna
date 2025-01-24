<?php
session_start();
include '../includes/db.php';

// Ensure the user is logged in as an educator
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'educator') {
    header('Location: ../auth/login.php');
    exit();
}

// Fetch submitted forms
$query = "SELECT forms.id AS form_id, forms.type, forms.status, users.name AS student_name 
          FROM forms 
          JOIN users ON forms.user_id = users.id 
          WHERE forms.status = 'submitted'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educator Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="bg-primary text-white text-center p-3">
        <h1>Educator Dashboard</h1>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="#">NutriTrack</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="../dashboard/educator.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mt-4">
        <h2>Submitted Forms</h2>
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Form ID</th>
                        <th>Type</th>
                        <th>Student</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($form = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($form['form_id']) ?></td>
                            <td><?= htmlspecialchars($form['type']) ?></td>
                            <td><?= htmlspecialchars($form['student_name']) ?></td>
                            <td><?= htmlspecialchars($form['status']) ?></td>
                            <td>
                                <a href="../feedback/add_feedback.php?id=<?= $form['form_id'] ?>" class="btn btn-sm btn-success">Provide Feedback</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No submitted forms available.</p>
        <?php endif; ?>
    </main>

    <footer class="bg-light text-center p-3 mt-4">
        <p>&copy; 2024 NutriTrack. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
