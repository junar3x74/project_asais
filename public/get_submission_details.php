<?php
// Include database connection
require_once '../configs/db.php'; // Adjust the path as needed

// Get submission ID from the query string
$submission_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($submission_id) {
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

        // Return the submission details as JSON
        echo json_encode($submission);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Submission ID is missing']);
}
?>
