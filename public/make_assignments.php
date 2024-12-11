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

$teacher_id = $_SESSION['id'];  // Use 'id' instead of 'user_id'

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

// Handle form submission to create a new assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'], $_POST['description'], $_POST['due_date'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    try {
        $insertQuery = "
            INSERT INTO assignments (teacher_id, title, description, due_date) 
            VALUES (:teacher_id, :title, :description, :due_date)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute([
            'teacher_id' => $teacher_id,
            'title' => $title,
            'description' => $description,
            'due_date' => $due_date
        ]);

        // Redirect after successful insert
        header('Location: make_assignments.php');
        exit();
    } catch (PDOException $e) {
        die("Database query failed: " . $e->getMessage());
    }
}

// Handle assignment deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    try {
        $deleteQuery = "DELETE FROM assignments WHERE id = :id AND teacher_id = :teacher_id";
        $stmt = $pdo->prepare($deleteQuery);
        $stmt->execute(['id' => $delete_id, 'teacher_id' => $teacher_id]);

        // Redirect after successful delete
        header('Location: make_assignments.php');
        exit();
    } catch (PDOException $e) {
        die("Database query failed: " . $e->getMessage());
    }
}

// Handle form submission to update an assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'], $_POST['edit_title'], $_POST['edit_description'], $_POST['edit_due_date'])) {
    $edit_id = $_POST['edit_id'];
    $edit_title = $_POST['edit_title'];
    $edit_description = $_POST['edit_description'];
    $edit_due_date = $_POST['edit_due_date'];

    try {
        $updateQuery = "
            UPDATE assignments SET title = :title, description = :description, due_date = :due_date 
            WHERE id = :id AND teacher_id = :teacher_id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute([
            'id' => $edit_id,
            'title' => $edit_title,
            'description' => $edit_description,
            'due_date' => $edit_due_date,
            'teacher_id' => $teacher_id
        ]);

        // Redirect after successful update
        header('Location: make_assignments.php');
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
    <title>Teacher Assignments</title>
    <link rel="stylesheet" href="ass.css"> <!-- Link to the external CSS file -->
    <link rel="icon" href="images/AW-Favicon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
    <ul>
      <li><a href="teacher_dashboard.php">Home</a></li>
      <li><a href="about_us.php">About</a></li>
      <li><a href="#contact">Contact</a></li>
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
      <li><a href= "submissions.php">Student Submissions</a></li>
      <li><a href="about_us.php">About Us</a></li>
    </ul>
  </div>

  <!-- Content -->
  <div class="content">
    <h1>Assignments</h1>

    <!-- Add Assignment Form -->
    <h2>Create a New Assignment</h2>
    <form method="POST" action="make_assignments.php">
      <label for="title">Assignment Title:</label>
      <input type="text" name="title" id="title" required><br>

      <label for="description">Assignment Description:</label>
      <textarea name="description" id="description" required></textarea><br>

      <label for="due_date">Due Date:</label>
      <input type="date" name="due_date" id="due_date" required><br>

      <button type="submit">Create Assignment</button>
    </form>

    <!-- List of Current Assignments -->
    <h2>Current Assignments</h2>
    <table class="assignments-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($assignments as $assignment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                    <td><?php echo htmlspecialchars($assignment['description']); ?></td>
                    <td><?php echo htmlspecialchars($assignment['due_date']); ?></td>
                    <td>
    <!-- Edit and Delete Buttons Inline -->
                    <a href="make_assignments.php?edit_id=<?php echo $assignment['id']; ?>" class="action-btn edit-btn">Edit</a>
                    <a href="make_assignments.php?delete_id=<?php echo $assignment['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this assignment?')">Delete</a>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Edit Assignment Form (only visible when editing) -->
    <?php if (isset($_GET['edit_id'])): 
        $edit_id = $_GET['edit_id'];
        $stmt = $pdo->prepare("SELECT * FROM assignments WHERE id = :id AND teacher_id = :teacher_id");
        $stmt->execute(['id' => $edit_id, 'teacher_id' => $teacher_id]);
        $assignment = $stmt->fetch();
        if ($assignment):
    ?>
    <h2>Edit Assignment</h2>
    <form method="POST" action="make_assignments.php">
      <input type="hidden" name="edit_id" value="<?php echo $assignment['id']; ?>">
      <label for="edit_title">Assignment Title:</label>
      <input type="text" name="edit_title" id="edit_title" value="<?php echo htmlspecialchars($assignment['title']); ?>" required><br>

      <label for="edit_description">Assignment Description:</label>
      <textarea name="edit_description" id="edit_description" required><?php echo htmlspecialchars($assignment['description']); ?></textarea><br>

      <label for="edit_due_date">Due Date:</label>
      <input type="date" name="edit_due_date" id="edit_due_date" value="<?php echo htmlspecialchars($assignment['due_date']); ?>" required><br>

      <button type="submit">Update Assignment</button>
    </form>
    <?php endif; endif; ?>
  </div>

</body>
</html>
