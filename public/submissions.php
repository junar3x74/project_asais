<?php
// Include database connection
require_once '../configs/db.php'; // Adjust the path as needed

// Start session
session_start();

// Ensure the user is logged in and is a teacher
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: logout.php');  // Redirect to login if not logged in or not a teacher
    exit();
}

// Get the teacher's ID
$teacher_id = $_SESSION['id'];

// Fetch all assignments for the teacher
$assignments = [];
try {
    $assignmentQuery = "SELECT * FROM assignments WHERE teacher_id = :teacher_id";
    $stmt = $pdo->prepare($assignmentQuery);
    $stmt->execute(['teacher_id' => $teacher_id]);
    $assignments = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}

// Fetch submission data for each assignment and student
$submissions = [];
try {
    $submissionQuery = "
        SELECT s.id, s.assignment_id, s.student_id, s.content, s.submission_date, s.status, s.grade, s.feedback, u.fname AS student_name, a.title AS assignment_title
        FROM submissions s
        JOIN users u ON s.student_id = u.id
        JOIN assignments a ON s.assignment_id = a.id
        WHERE a.teacher_id = :teacher_id";
    
    $stmt = $pdo->prepare($submissionQuery);
    $stmt->execute(['teacher_id' => $teacher_id]);
    $submissions = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}

// Handle grading and feedback submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['grade'], $_POST['feedback'], $_POST['submission_id'])) {
    $grade = $_POST['grade'];
    $feedback = $_POST['feedback'];
    $submission_id = $_POST['submission_id'];

    try {
        // Update the submission with grade and feedback
        $updateQuery = "
            UPDATE submissions 
            SET grade = :grade, feedback = :feedback, status = 'graded' 
            WHERE id = :submission_id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute([
            'grade' => $grade,
            'feedback' => $feedback,
            'submission_id' => $submission_id
        ]);

        // Redirect to refresh the page
        header('Location: submissions.php');
        exit();
    } catch (PDOException $e) {
        die("Database query failed: " . $e->getMessage());
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Submissions</title>
    <link rel="icon" href="images/AW-Favicon.png" type="image/png">
    <link rel="stylesheet" href="ass.css"> <!-- Link to the external CSS file -->
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <ul>
            <li><a href="teacher_dashboard.php">Home</a></li>
            <li><a href="make_assignments.php">Assignments</a></li>
            <li><a href="student_list.php">Students</a></li>
            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="profile">
            <h3 class="username"><?php echo isset($_SESSION['fname']) ? $_SESSION['fname'] : 'User'; ?></h3>
            <p class="role">Teacher</p>
        </div>
        <ul>
            <li><a href="make_assignments.php">Assignments</a></li>
            <li><a href="student_list.php">Students</a></li>
            <li><a href="submissions.php">Student Submissions</a></li>
            <li><a href="about_us.php">About Us</a></li>
        </ul>
    </div>

    <!-- Content -->
    <div class="content">
        <h1>Student Submissions</h1>

        <!-- Submissions List -->
        <table class="submissions-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Assignment Title</th>
                    <th>Submission Date</th>
                    <th>Status</th>
                    <th>Grade & Feedback</th>
                    <th>Submission Content</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $submission): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($submission['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($submission['assignment_title']); ?></td>
                        <td><?php echo htmlspecialchars($submission['submission_date']); ?></td>
                        <td><?php echo htmlspecialchars($submission['status']); ?></td>
                        <td>
                            <?php if ($submission['status'] == 'graded'): ?>
                                <?php echo htmlspecialchars($submission['grade']); ?>
                                <br>
                                <?php echo htmlspecialchars($submission['feedback']); ?>
                            <?php else: ?>
                                <!-- Form to input grade and feedback -->
                                <form method="POST" action="submissions.php">
                                    <input type="number" name="grade" placeholder="Grade" required>
                                    <textarea name="feedback" placeholder="Feedback" required></textarea>
                                    <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                                    <button type="submit">Submit Grade & Feedback</button>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Display the submission content -->
                            <pre><?php echo htmlspecialchars($submission['content']); ?></pre>
                        </td>
                        <td>
                            <!-- View Details Button -->
                            <button onclick="viewDetails(<?php echo $submission['id']; ?>)">View Details</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

    <!-- Modal for Viewing Details -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2>Submission Details</h2>
            <div id="modal-body">
                <!-- Content will be dynamically added via JavaScript -->
            </div>
        </div>
    </div>

    <script>
    // Function to open the modal and show submission details
    function viewDetails(submissionId) {
        // Fetch the details of the submission from the server via AJAX
        fetch('get_submission_details.php?id=' + submissionId)
            .then(response => response.json())
            .then(data => {
                // Display the submission details in the modal
                const modalBody = document.getElementById('modal-body');
                modalBody.innerHTML = `
                    <p><strong>Student Name:</strong> ${data.student_name}</p>
                    <p><strong>Assignment Title:</strong> ${data.assignment_title}</p>
                    <p><strong>Submission Date:</strong> ${data.submission_date}</p>
                    <p><strong>Status:</strong> ${data.status}</p>
                    <p><strong>Grade:</strong> ${data.grade}</p>
                    <p><strong>Feedback:</strong> ${data.feedback}</p>
                    <p><strong>Submission Content:</strong> <pre>${data.content}</pre></p>
                `;
                // Open the modal
                document.getElementById('modal').style.display = 'block';
            })
            .catch(error => console.error('Error fetching submission details:', error));
    }

    // Function to close the modal
    function closeModal() {
        document.getElementById('modal').style.display = 'none';
    }

    // Ensure the modal is closed on page load (in case of refresh)
    window.onload = function() {
        document.getElementById('modal').style.display = 'none';
    }
</script>

</body>
</html>
