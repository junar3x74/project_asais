<?php
// Include database connection
require_once '../configs/db.php'; // Adjust the path as needed

// Start session
session_start();

// Ensure the user is logged in and is a student
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'student') {
    header('Location: logout.php');  // Redirect to login if not logged in or not a student
    exit();
}

// Get the student's ID
$student_id = $_SESSION['id'];

// Fetch submissions for the student
$submissions = [];
try {
    $submissionQuery = "
        SELECT s.id, s.assignment_id, s.grade, s.feedback, s.submission_date, a.title AS assignment_title
        FROM submissions s
        JOIN assignments a ON s.assignment_id = a.id
        WHERE s.student_id = :student_id";
    
    $stmt = $pdo->prepare($submissionQuery);
    $stmt->execute(['student_id' => $student_id]);
    $submissions = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Submissions</title>
    <link rel="icon" href="images/AW-Favicon.png" type="image/png">
    <link rel="stylesheet" href="ass.css"> <!-- Link to the external CSS file -->
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <ul>
            <li><a href="student_dashboard.php">Home</a></li>
            <li><a href="view_assignments.php">Assignments</a></li>
            <li><a href="submitted.php">Submissions</a></li>
            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="profile">
            <h3 class="username"><?php echo isset($_SESSION['fname']) ? $_SESSION['fname'] : 'User'; ?></h3>
            <p class="role">Student</p>
        </div>
        <ul>
            <li><a href="student_dashboard.php">Home</a></li>
            <li><a href="submitted.php">My Submissions</a></li>
            <li><a href="about_us.php">About Us</a></li>
        </ul>
    </div>

    <!-- Content -->
    <div class="content">
        <h1>My Submissions</h1>

        <!-- Submissions List -->
        <table class="submissions-table">
            <thead>
                <tr>
                    <th>Assignment Title</th>
                    <th>Submission Date</th>
                    <th>Grade</th>
                    <th>Feedback</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $submission): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($submission['assignment_title']); ?></td>
                        <td><?php echo htmlspecialchars($submission['submission_date']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($submission['grade']) ? htmlspecialchars($submission['grade']) : 'Not Graded Yet'; ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($submission['feedback']) ? htmlspecialchars($submission['feedback']) : 'No Feedback Yet'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

</body>
</html>
