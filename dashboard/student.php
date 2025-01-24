<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../includes/db.php';

// Ensure the user is logged in as a student
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header('Location: ../auth/login.php');
    exit();
}

// Handle Follow-Up and Discharge actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $form_id = $_POST['form_id'];
    $action = $_POST['action'];

    if ($action === 'follow_up') {
        // Increment the follow-up count and update the status to "follow_up"
        $conn->query("UPDATE forms SET follow_up = follow_up + 1, status = 'follow_up' WHERE id = $form_id AND user_id = " . $_SESSION['user_id']);
    } elseif ($action === 'discharged') {
        // Clear follow-up count and set status to "discharged"
        $conn->query("UPDATE forms SET follow_up = 0, status = 'discharged' WHERE id = $form_id AND user_id = " . $_SESSION['user_id']);
    }
}

// Fetch all forms submitted by the current student
$query = "SELECT * FROM forms WHERE user_id=" . $_SESSION['user_id'];
$result = $conn->query($query);

// Fetch BMI distribution data
$bmiQuery = "SELECT bmi_category, COUNT(*) AS count 
             FROM forms 
             WHERE user_id = " . $_SESSION['user_id'] . " 
             GROUP BY bmi_category";
$bmiResult = $conn->query($bmiQuery);

// Prepare data for the chart
$bmiData = [
    'Underweight' => 0,
    'Normal' => 0,
    'Overweight' => 0,
    'Obese' => 0
];

while ($row = $bmiResult->fetch_assoc()) {
    $bmiData[$row['bmi_category']] = $row['count'];
}

// Check if there is data to display in the chart
$hasBMIData = array_sum($bmiData) > 0;

// Fetch form counts by status
$countQuery = "SELECT status, COUNT(*) as count FROM forms WHERE user_id = " . $_SESSION['user_id'] . " GROUP BY status";
$countResult = $conn->query($countQuery);

// Initialize counts for each status
$counts = [
    'submitted' => 0,
    'reviewed' => 0,
    'draft' => 0,
    'follow_up' => 0,
    'discharged' => 0,
];

while ($row = $countResult->fetch_assoc()) {
    $counts[$row['status']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <header class="bg-primary text-white text-center p-3">
        <h1>Student Dashboard</h1>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="#">NutriTrack</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="../dashboard/student.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="../forms/create.php">Create Form</a></li>
                        <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mt-4">
        <!-- Status Summary Cards -->
        <div class="row text-center mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h4>Submitted</h4>
                        <h2><?= $counts['submitted'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h4>Reviewed</h4>
                        <h2><?= $counts['reviewed'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h4>Follow-Up</h4>
                        <h2><?= $counts['follow_up'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h4>Draft</h4>
                        <h2><?= $counts['draft'] ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <h2>Start New DCN Form</h2>
        <a href="../forms/create.php" class="btn btn-primary mb-3">New Entry</a>
        <div class="table-responsive">
            <table class="table table-striped text-center">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($form = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($form['type']) ?></td>
                            <td>
                                <span class="badge bg-<?= $form['status'] == 'draft' ? 'warning' : ($form['status'] == 'submitted' || $form['status'] == 'reviewed' ? 'success' : ($form['status'] == 'follow_up' ? 'info' : 'danger')) ?>">
                                    <?= htmlspecialchars(ucfirst($form['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($form['status'] == 'draft') { ?>
                                    <a href="../forms/view.php?id=<?= $form['id'] ?>" class="btn btn-sm btn-info">View</a>
                                    <a href="../forms/edit.php?id=<?= $form['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form method="POST" action="../forms/submit.php" style="display: inline;">
                                        <input type="hidden" name="form_id" value="<?= $form['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success">Submit</button>
                                    </form>
                                <?php } elseif ($form['status'] == 'reviewed') { ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="form_id" value="<?= $form['id'] ?>">
                                        <input type="hidden" name="action" value="follow_up">
                                        <button type="submit" class="btn btn-sm btn-warning">Follow Up</button>
                                    </form>
                                <?php } elseif ($form['status'] == 'follow_up') { ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="form_id" value="<?= $form['id'] ?>">
                                        <input type="hidden" name="action" value="discharged">
                                        <button type="submit" class="btn btn-sm btn-danger">Discharged</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="bg-light text-center p-3 mt-4">
        <p>&copy; 2025 NutriTrack. All rights reserved.</p>
    </footer>
</body>

</html>
