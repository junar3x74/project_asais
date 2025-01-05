<?php
require_once '../configs/db.php'; 

session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'student') {
    header('Location: logout.php');  
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assignment_id'], $_POST['submission_content'])) {

    $student_id = $_SESSION['id'];
    $assignment_id = $_POST['assignment_id'];
    $submission_content = $_POST['submission_content'];

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
        
        header('Location: view_assignments.php');
        exit();
    } catch (PDOException $e) {
        die("Database query failed: " . $e->getMessage());
    }
} else {
    header('Location: view_assignments.php');
    exit();
}
?>
