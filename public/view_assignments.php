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

// Fetch all assignments with teacher information
$assignments = [];
try {
    $assignmentQuery = "
        SELECT a.id AS assignment_id, a.title, a.description, a.due_date, u.fname AS teacher_name
        FROM assignments a
        JOIN users u ON a.teacher_id = u.id
        ORDER BY a.due_date ASC";
    
    $stmt = $pdo->prepare($assignmentQuery);
    $stmt->execute();
    $assignments = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assignments</title>
    <link rel="icon" href="images/AW-Favicon.png" type="image/png">
    <link rel="stylesheet" href="ass.css"> <!-- Link to the external CSS file -->
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <ul>
            <li><a href="student_dashboard.php">Home</a></li>
            <li><a href="view_assignments.php">Assignments</a></li>
           
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
            <li><a href="view_assignments.php">Assignments</a></li>
            <li><a href="submitted.php">Submitted Assignments</a></li>

            
        </ul>
    </div>

    <!-- Content -->
    <div class="content">
        <h1>Assignments</h1>

        <!-- List of Assignments -->
        <table class="assignments-table">
            <thead>
                <tr>
                    <th>Assignment Title</th>
                    <th>Description</th>
                    <th>Teacher</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $assignment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['description']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['teacher_name']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['due_date']); ?></td>
                        <td>
                            <!-- Button to view more details or download -->
                            <a href="view_assignment_details.php?assignment_id=<?php echo $assignment['assignment_id']; ?>">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

</body>
</html>
