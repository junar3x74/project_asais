<?php

require_once '../configs/db.php'; 

session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
]);

session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'student') {
    header('Location: logout.php');
    exit();
}

$student_id = $_SESSION['id'];

$assignmentCount = 0;
$submissionCount = 0;

try {
    $assignmentQuery = "
        SELECT COUNT(*) AS total_assignments 
        FROM assignments a
        WHERE a.id NOT IN (SELECT assignment_id FROM submissions WHERE student_id = :student_id)";
    $stmt = $pdo->prepare($assignmentQuery);
    $stmt->execute(['student_id' => $student_id]);
    $assignmentCount = $stmt->fetchColumn();

    $submissionQuery = "
        SELECT COUNT(*) AS total_submissions 
        FROM submissions 
        WHERE student_id = :student_id";
    $stmt = $pdo->prepare($submissionQuery);
    $stmt->execute(['student_id' => $student_id]);
    $submissionCount = $stmt->fetchColumn();
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dash.css">
    <link rel="icon" href="images/AW-Favicon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Student Dashboard</title>
</head>
<body>
  <nav class="navbar">
    <div class="profile"></div>
    <ul>
            <li><a href="student_dashboard.php">Home</a></li>
            <li><a href="view_assignments.php">Assignments</a></li>
            <li><a href="submitted.php">Submissions</a></li>
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
      <li><a href="submitted.php">Submitted assignments</a></li>
      <li><a href="about_us.php">About Us</a></li>
    </ul>
  </div>

  <div class="content">
    <div class="welcome-section">
      <h1>Welcome, <?php echo isset($_SESSION['fname']) ? $_SESSION['fname'] : 'User'; ?>!</h1>
      <p>Use the sidebar to view and manage your assignments and submissions.</p>
    </div>

    <div class="stats-container">
      <div class="stats-card">
        <i class="fas fa-clipboard-list"></i>
        <div class="counter"><?php echo $assignmentCount; ?></div>
        <div class="label">Assignments</div>
      </div>

      <div class="stats-card">
        <i class="fas fa-paper-plane"></i>
        <div class="counter"><?php echo $submissionCount; ?></div>
        <div class="label">Submissions</div>
      </div>
    </div>
  </div>
</body>
</html>
