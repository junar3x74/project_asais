<?php

require_once '../configs/db.php'; 


session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$submission_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($submission_id && is_numeric($submission_id)) {  
    try {
        $query = "
            SELECT s.id, s.assignment_id, s.student_id, s.content, s.submission_date, s.status, s.grade, s.feedback, u.fname AS student_name, a.title AS assignment_title
            FROM submissions s
            JOIN users u ON s.student_id = u.id
            JOIN assignments a ON s.assignment_id = a.id
            WHERE s.id = :submission_id";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute(['submission_id' => $submission_id]);
        $submission = $stmt->fetch(PDO::FETCH_ASSOC);

    
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
