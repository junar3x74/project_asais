<?php
require_once '../configs/db.php';

// Start session
session_start();

// Check if the user is logged in and is either a teacher or student
if (!isset($_SESSION['id']) || ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'student')) {
    // Redirect to login page if not logged in or not a teacher/student
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/AW-Favicon.png" type="image/png">
    <title>About Us - Assignment Works Platform</title>
    <style>
        /* Body and basic layout */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            color: #333;
        }

        /* About Us Container */
        .about-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 50px 20px;
            text-align: center;
        }

        /* Heading Style */
        h1 {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 20px;
        }

        /* Paragraph and Description */
        .about-description {
            max-width: 800px;
            margin: 0 auto;
            font-size: 1.2rem;
            line-height: 1.8;
            color: #555;
        }

        /* Mission Section */
        .mission {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            width: 100%;
            max-width: 900px;
        }

        .mission h2 {
            font-size: 2.2rem;
            color: #007bff;
            margin-bottom: 15px;
        }

        .mission p {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.7;
        }

        /* Team Section - Horizontal Layout */
        .team {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            width: 100%;
            max-width: 1200px; /* Set a max-width to keep the content aligned */
        }

        .team-member {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            max-width: 300px;
            flex: 1; /* Distribute the team members evenly */
            text-align: center;
            margin: 0 15px; /* Add margin for spacing between items */
        }

        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .team-member h3 {
            font-size: 1.4rem;
            color: #007bff;
            margin-bottom: 10px;
        }

        .team-member p {
            font-size: 1rem;
            color: #555;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            h1 {
                font-size: 2.4rem;
            }

            .about-description {
                font-size: 1rem;
            }

            .mission {
                padding: 20px;
            }

            .team-member {
                max-width: 200px;
                flex: 0 1 auto; /* Allow items to shrink but not grow */
                margin: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="about-container">
        <!-- Main Heading -->
        <h1>About Us</h1>

        <!-- About Description Section -->
        <div class="about-description">
            <p>Welcome to our Assignment Management Platform! We are a passionate team dedicated to simplifying the way assignments and activities are managed. Our goal is to provide a user-friendly platform for professors and students to create, submit, and manage academic tasks efficiently. Our platform combines security, ease of use, and real-time updates to ensure a smooth experience for all users.</p>
            <p>With a strong focus on accessibility and user experience, we aim to make education more organized and streamlined for both students and educators. Whether you're a professor looking to manage your class activities or a student staying on top of deadlines, we're here to help!</p>
        </div>

        <!-- Mission Section -->
        <div class="mission">
            <h2>Our Mission</h2>
            <p>Our mission is to make assignment management easier and more efficient for everyone. By offering a platform that's secure, intuitive, and reliable, we strive to help professors save time on administrative tasks, while giving students the tools they need to stay organized and meet deadlines. With real-time notifications, secure login systems, and a simple interface, we hope to be your go-to platform for managing assignments.</p>
        </div>

        <!-- Team Section -->
        <div class="team">
            <div class="team-member">
                <img src="images/budi2.jpg" alt="Team Member 1">
                <h3>Budi</h3>
                <p>Founder & CEO</p>
            </div>
            <div class="team-member">
                <img src="images/emman2.jpg" alt="Team Member 2">
                <h3>Emman</h3>
                <p>Co-Founder & CTO</p>
            </div>
            <div class="team-member">
                <img src="images/hunar2.jpg" alt="Team Member 3">
                <h3>Junar</h3>
                <p>Lead Developer</p>
            </div>
        </div>
    </div>

</body>
</html>
