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

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header('Location: logout.php');
    exit();
}

$admin_id = $_SESSION['id'];
$teachers = [];
$students = [];

// Fetch all teachers and students
try {
    $teacherQuery = "SELECT * FROM users WHERE role = 'teacher'";
    $stmt = $pdo->prepare($teacherQuery);
    $stmt->execute();
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $studentQuery = "SELECT * FROM users WHERE role = 'student'";
    $stmt = $pdo->prepare($studentQuery);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}

// Handle deletion of users (teacher or student)
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $role = $_GET['role'];

    try {
        $deleteQuery = "DELETE FROM users WHERE id = :id";
        $stmt = $pdo->prepare($deleteQuery);
        $stmt->execute(['id' => $delete_id]);

        // Set a session variable to show the popup
        $_SESSION['delete_popup'] = true;
        // Reload the page after deletion to display the popup
        header("Location: admin_dashboard.php");
        exit();
    } catch (PDOException $e) {
        die("Error deleting user: " . $e->getMessage());
    }
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
    <title>Admin Dashboard</title>
    <style>
        /* Popup Styles */
        .popup {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #d9534f; /* Red background */
            color: white;
            padding: 15px;
            font-size: 1.2rem;
            border-radius: 5px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s, visibility 0s 0.5s;
        }

        .popup.show {
            opacity: 1;
            visibility: visible;
            transition: opacity 0.5s;
        }
    </style>
</head>
<body>
    <!-- Popup message for deletion -->
    <div id="delete-popup" class="popup">
        Item Deleted Successfully
    </div>

    <nav class="navbar">
        <div class="profile"></div>
        <ul>
            <li><a href="admin_dashboard.php">Home</a></li>
            <li><a href="about_us.php">About</a></li>
            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>
    </nav>

    <div class="sidebar">
        <div class="profile">
            <h3 class="username"><?php echo isset($_SESSION['fname']) ? $_SESSION['fname'] : 'User'; ?></h3>
            <p class="role">Administrator</p>
        </div>
        <ul>
            <li><a href="about_us.php">About</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="welcome-section">
            <h1>Welcome, <?php echo isset($_SESSION['fname']) ? $_SESSION['fname'] : 'User'; ?>!</h1>
            <p>Use the sidebar to manage teachers, students, and more.</p>
        </div>

        <div id="teachers-section" class="section">
            <h2>Teachers</h2>
            <ul>
                <?php foreach ($teachers as $teacher): ?>
                    <li>
                        <?php echo $teacher['fname']; ?>
                        <a href="?delete_id=<?php echo $teacher['id']; ?>&role=teacher" onclick="return confirm('Are you sure you want to delete this teacher?');">
                            <i class="fas fa-trash-alt"></i> Delete
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div id="students-section" class="section">
            <h2>Students</h2>
            <ul>
                <?php foreach ($students as $student): ?>
                    <li>
                        <?php echo $student['fname']; ?>
                        <a href="?delete_id=<?php echo $student['id']; ?>&role=student" onclick="return confirm('Are you sure you want to delete this student?');">
                            <i class="fas fa-trash-alt"></i> Delete
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        // Function to show the popup when an item is deleted
        function showDeletePopup() {
            const popup = document.getElementById('delete-popup');
            popup.classList.add('show');
            
            // Hide the popup after 1 second
            setTimeout(function() {
                popup.classList.remove('show');
            }, 1000); // 1000ms = 1 second
        }

        // Trigger the popup if set in the session
        <?php if (isset($_SESSION['delete_popup']) && $_SESSION['delete_popup']): ?>
            showDeletePopup();
            <?php unset($_SESSION['delete_popup']); ?>  // Clear the session variable after showing the popup
        <?php endif; ?>
    </script>
</body>
</html>
