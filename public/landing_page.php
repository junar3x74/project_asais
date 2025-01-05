<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/AW-Favicon.png" type="image/png">
    <title>Welcome to Assignment Management Platform</title>
    <style>
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            color: #333;
        }

        
        .welcome-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .welcome-message {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 40px 50px;
            border-radius: 12px;
            max-width: 800px;
            width: 90%;
            box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.1);
            color: white;
            animation: fadeIn 1.5s ease-out;
        }

        
        h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            text-transform: capitalize;
            letter-spacing: 1px;
        }

        
        p {
            font-size: 1.4rem;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        
        .description-box {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 10px;
            margin-top: 30px;
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.1);
            color: #444;
            text-align: left;
            width: 100%;
            max-width: 700px;
        }

        .description-box h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #007bff;
        }

        .description-box p {
            font-size: 1.2rem;
            color: #555;
            line-height: 1.8;
        }

        .get-started-btn {
            padding: 15px 30px;
            background-color: #28a745;
            color: white;
            font-size: 1.2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 30px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .get-started-btn:hover {
            background-color: #218838;
        }

        
        @media (max-width: 768px) {
            .welcome-message {
                padding: 20px;
            }

            .description-box {
                padding: 20px;
            }

            h1 {
                font-size: 2.4rem;
            }

            p, .description-box p {
                font-size: 1rem;
            }

            .get-started-btn {
                padding: 12px 25px;
                font-size: 1rem;
            }
        }

        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-message">
            <h1>Welcome to Our Assignment Management Platform</h1>
            <p>A simple and secure platform to help manage assignments and activities. It offers an easy-to-use interface for both professors and students, making it simple to create, submit, and assign deadlines to activities.</p>
            
        
            <div class="description-box">
                <h2>About the Platform</h2>
                <p>The platform ensures that information is kept safe through strong security measures, including encryption and access control. It also sends real-time notifications and updates to both students and professors to keep everyone on track.</p>
                <p>Whether you're a professor assigning tasks or a student submitting them, this platform helps make the process smoother and more efficient. The system is designed to offer the best experience for both students and educators.</p>
            </div>
            
        
            <a href="login.php" class="get-started-btn">Get Started</a>
        </div>
    </div>
</body>
</html>
