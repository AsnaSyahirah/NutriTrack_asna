<?php
session_start();
include '../includes/db.php';

// Ensure the user is logged in as a student
if ($_SESSION['role'] != 'student') {
    header('Location: ../auth/login.php');
    exit();
}

// Get the form ID from POST data
$form_id = $_POST['form_id'];

// Update the form's status to 'submitted'
$query = "UPDATE forms SET status='submitted' WHERE id='$form_id' AND user_id=" . $_SESSION['user_id'];
if ($conn->query($query)) {
    header('Location: ../dashboard/student.php?success=Form submitted successfully!');
    exit();
} else {
    echo "Error: " . $conn->error;
}
?>
