<?php
// Include database connection
require_once '../configs/db.php'; // Adjust the path as needed

// Start session
session_set_cookie_params([
    'lifetime' => 86400,  // Session lifetime (1 day)
    'path' => '/',        // Set the path where the cookie is available
    'domain' => '',       // Set to your domain, e.g., '.example.com', or leave empty for current domain
    'secure' => false,    // Set to true if using HTTPS
    'httponly' => true,   // Prevent access to cookie from JavaScript
]);

session_start();  // Start the session

// Ensure the user is logged in and is a teacher
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');  // Redirect to login if not logged in or not a teacher
    exit();
}

// Fetch the list of students
$students = [];
try {
    $query = "SELECT id, fname FROM users WHERE role = 'student'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $students = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}

// Fetch submission data for a specific student (AJAX handling)
if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    try {
        $submissionQuery = "
            SELECT a.title, s.submission_date, s.status, s.grade, s.feedback 
            FROM submissions s
            JOIN assignments a ON s.assignment_id = a.id
            WHERE s.student_id = :student_id";
        $stmt = $pdo->prepare($submissionQuery);
        $stmt->execute(['student_id' => $student_id]);
        $submissions = $stmt->fetchAll();

        if ($submissions) {
            // Output submissions in a format suitable for modal display
            foreach ($submissions as $submission) {
                echo "<li><strong>" . htmlspecialchars($submission['title']) . "</strong><br>
                      Submitted on: " . htmlspecialchars($submission['submission_date']) . "<br>
                      Status: " . htmlspecialchars($submission['status']) . "<br>
                      Grade: " . htmlspecialchars($submission['grade']) . "<br>
                      Feedback: " . htmlspecialchars($submission['feedback']) . "</li>";
            }
        } else {
            echo "<p>No submissions found for this student.</p>";
        }
    } catch (PDOException $e) {
        die("Database query failed: " . $e->getMessage());
    }
    exit(); // Ensure no further code is executed after the AJAX request
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="ass.css"> <!-- Ensure this is the correct path -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Student List</title>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <ul>
            <li><a href="teacher_dashboard.php">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="login.php" class="logout">Logout</a></li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="profile">
            <h3 class="username"><?php echo isset($_SESSION['fname']) ? $_SESSION['fname'] : 'User'; ?></h3>
            <p class="role">Professor</p>
        </div>
        <ul>
            <li><a href="make_assignments.php">Assignments</a></li>
            <li><a href="student_list.php">Students</a></li>
            <li><a href= "submissions.php">Student Submissions</a></li>
            <li><a href="#about">About Us</a></li>
        </ul>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="welcome-section">
            <h1>Student List</h1>
            <p>Below is the list of all students:</p>
        </div>

        <!-- Students List Section -->
        <div class="students-list">
            <table class="student-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>View Submissions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['fname']); ?></td>
                            <td>
                                <button class="view-button" onclick="viewSubmissions(<?php echo $student['id']; ?>)">
                                    View Submissions
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for displaying submissions -->
    <div id="submissionModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2>Submissions for Student</h2>
            <div id="submissionsContainer">
                <!-- Submissions will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // View submissions in the modal
        function viewSubmissions(studentId) {
            console.log("viewSubmissions called for student ID: " + studentId); // Debugging
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'student_list.php?student_id=' + studentId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var responseText = xhr.responseText.trim();
                    // Check if response is empty, meaning no submissions
                    if (responseText === "") {
                        document.getElementById('submissionsContainer').innerHTML = "<p>No submissions found for this student.</p>";
                    } else {
                        document.getElementById('submissionsContainer').innerHTML = '<ul>' + responseText + '</ul>';
                    }
                    document.getElementById('submissionModal').style.display = 'block';
                } else {
                    alert("Failed to load submissions.");
                }
            };
            xhr.onerror = function() {
                alert("Network error occurred while fetching submissions.");
            };
            xhr.send();
        }

        // Close the modal
        function closeModal() {
            document.getElementById('submissionModal').style.display = 'none';
        }

        // Close the modal if user clicks anywhere outside of the modal content
        window.onclick = function(event) {
            if (event.target == document.getElementById('submissionModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>
