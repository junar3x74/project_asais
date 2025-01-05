<?php

require_once '../configs/db.php'; 


session_start();


if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'student') {
    header('Location: logout.php');  
    exit();
}

$student_id = $_SESSION['id'];


if (isset($_GET['assignment_id'])) {
    $assignment_id = $_GET['assignment_id'];

    
    $assignment = [];
    try {
        $assignmentQuery = "
            SELECT a.id, a.title, a.description, a.due_date, u.fname AS teacher_name
            FROM assignments a
            JOIN users u ON a.teacher_id = u.id
            WHERE a.id = :assignment_id";
        
        $stmt = $pdo->prepare($assignmentQuery);
        $stmt->execute(['assignment_id' => $assignment_id]);
        $assignment = $stmt->fetch();
    } catch (PDOException $e) {
        die("Database query failed: " . $e->getMessage());
    }

    
    if (!$assignment) {
        header('Location: view_assignments.php');
        exit();
    }

    
    $submissionStatus = "";
    try {
        $submissionQuery = "
            SELECT status FROM submissions
            WHERE assignment_id = :assignment_id AND student_id = :student_id";
        
        $stmt = $pdo->prepare($submissionQuery);
        $stmt->execute([
            'assignment_id' => $assignment_id,
            'student_id' => $student_id
        ]);
        $submission = $stmt->fetch();

        if ($submission) {
            if ($submission['status'] == 'graded') {
                $submissionStatus = 'graded'; 
            } else {
                $submissionStatus = 'submitted'; 
            }
        }
    } catch (PDOException $e) {
        die("Database query failed: " . $e->getMessage());
    }
} else {
   
    header('Location: view_assignments.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Details</title>
    <link rel="icon" href="images/AW-Favicon.png" type="image/png">
    <link rel="stylesheet" href="ass.css">
</head>
<body>

    
    <nav class="navbar">
        <ul>
            <li><a href="student_dashboard.php">Home</a></li>
            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>
    </nav>

   
    <div class="sidebar">
        <div class="profile">
            <h3 class="username"><?php echo isset($_SESSION['fname']) ? $_SESSION['fname'] : 'User'; ?></h3>
            <p class="role">Student</p>
        </div>
        <ul>
            <li><a href="view_assignments.php">Assignments</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
    </div>

    
    <div class="content">
        <h1>Assignment Details</h1>

       
        <h2><?php echo htmlspecialchars($assignment['title']); ?></h2>
        <p><strong>Teacher:</strong> <?php echo htmlspecialchars($assignment['teacher_name']); ?></p>
        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($assignment['description'])); ?></p>
        <p><strong>Due Date:</strong> <?php echo htmlspecialchars($assignment['due_date']); ?></p>

        
        <?php if ($submissionStatus == 'graded'): ?>
            <p style="color: green;">This assignment has already been graded.</p>
        <?php elseif ($submissionStatus == 'submitted'): ?>
            <p style="color: orange;">You have already submitted this assignment.</p>
        <?php else: ?>
            
            <form action="submit_assignment.php" method="POST">
                <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                <textarea name="submission_content" placeholder="Your submission here..." required></textarea>
                <button type="submit">Submit Assignment</button>
            </form>
        <?php endif; ?>

    </div>

</body>
</html>
