<?php
// Include database connection
require_once '../configs/db.php'; // Adjust the path as needed

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'student') {
    // Redirect to login page if not logged in or not a student
    header("Location: login.php");
    exit();
}

// Get submission ID from the query string and validate it
$submission_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($submission_id && is_numeric($submission_id)) {  // Check if the ID is numeric
    try {
        // Fetch the details of the submission
        $query = "
            SELECT s.id, s.assignment_id, s.student_id, s.content, s.submission_date, s.status, s.grade, s.feedback, u.fname AS student_name, a.title AS assignment_title
            FROM submissions s
            JOIN users u ON s.student_id = u.id
            JOIN assignments a ON s.assignment_id = a.id
            WHERE s.id = :submission_id";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute(['submission_id' => $submission_id]);
        $submission = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the submission exists and return it as JSON
        if ($submission) {
            echo json_encode($submission);
        } else {
            echo json_encode(['error' => 'No submission found with the provided ID']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid or missing submission ID']);
}
?>
