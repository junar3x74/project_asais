<?php
// Include database connection
require_once '../configs/db.php'; // Adjust the path as needed

// Start session
session_start();

// Ensure the user is logged in and is a student
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');  // Redirect to login if not logged in or not a student
    exit();
}

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assignment_id'], $_POST['submission_content'])) {
    // Get the student's ID
    $student_id = $_SESSION['id'];

    // Sanitize and retrieve form data
    $assignment_id = $_POST['assignment_id'];
    $submission_content = $_POST['submission_content'];

    // Insert the submission into the database
    try {
        $insertQuery = "
            INSERT INTO submissions (assignment_id, student_id, content, submission_date, status) 
            VALUES (:assignment_id, :student_id, :content, NOW(), 'submitted')";
        
        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute([
            'assignment_id' => $assignment_id,
            'student_id' => $student_id,
            'content' => $submission_content
        ]);

        // Redirect to the assignments list page or a confirmation page
        header('Location: view_assignments.php');
        exit();
    } catch (PDOException $e) {
        die("Database query failed: " . $e->getMessage());
    }
} else {
    // Redirect if the form is not properly submitted
    header('Location: view_assignments.php');
    exit();
}
?>
