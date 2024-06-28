<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Function to get dashboard content based on user role
function getDashboardContent($role, $username) {
    switch ($role) {
        case 'admin':
            return "
                <div class='dashboard admin-dashboard'>
                    <h2>Welcome, Admin $username!</h2>
                    <div class='dashboard-links'>
                        <a href='manage_users.php'>Add Users</a>
                        <a href='view_members.php'>Manage Members</a>
                        <a href='manage_attendance.php'>Manage Attendance</a>
                        <a href='view_feedback.php'>View Feedback</a>
                        <a href='manage_food.php'>Manage Food</a>
                    </div>
                </div>
            ";
        case 'customer':
            return "
                <div class='dashboard customer-dashboard'>
                    <h2>Welcome, Customer $username!</h2>
                    <div class='dashboard-links'>
                        <a href='view_classes.php'>Classes</a>
                        <a href='add_feedback.php'>Add Feedback</a>
                        <a href='view_workout_plans.php'>Workout Plan</a>
                        <a href='view_meal_plans.php'>Meal Plan</a>
                    </div>
                </div>
            ";
        case 'trainer':
            return "
                <div class='dashboard trainer-dashboard'>
                    <h2>Welcome, Trainer $username!</h2>
                    <div class='dashboard-links'>
                        <a href='add_class.php'>Add Class</a>
                        <a href='send_workout_plan.php'>Send Report to Customer</a>
                    </div>
                </div>
            ";
        case 'dietitian':
            return "
                <div class='dashboard dietitian-dashboard'>
                    <h2>Welcome, Dietitian $username!</h2>
                    <div class='dashboard-links'>
                        <a href='send_meal_plan.php'>Send Meal Plan to Customer</a>
                    </div>
                </div>
            ";
        default:
            return "<h2>Welcome, $username!</h2>";
    }
}

// Function to get dashboard background image based on user role
function getDashboardBackground($role) {
    switch ($role) {
        case 'admin':
            return "url('admin1 bg.jpg')";
        case 'customer':
            return "url('customer bg.jpg')";
        case 'trainer':
            return "url('trainer1 bg.jpg')";
        case 'dietitian':
            return "url('dietitian1 bg.jpg')";
        default:
            return "none";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            width: 100%;
            background-image: <?php echo getDashboardBackground($role); ?>;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .container {
            width: 100%;
            max-width: 400px; /* Adjusted for a smaller size */
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: fadein 1s ease-in-out;
        }

        @keyframes fadein {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard h2 {
            margin-top: 0;
            color: #4bc3cc;
        }

        .dashboard-links {
            text-align: left; /* Align links to the left for vertical stacking */
            margin-top: 20px;
        }

        .dashboard-links a {
            display: block; /* Change to block to stack links */
            padding: 10px 20px;
            background-color: #4bc3cc;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 10px; /* Add space between each link */
        }

        .dashboard-links a:before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
            background-size: 50px 50px;
            z-index: 2;
            animation: move 2s linear infinite;
        }

        .dashboard-links a:hover {
            background-color: #02676c;
            transform: scale(1.1);
            box-shadow: 0 0 5px #54e6e8, 0 0 10px #54e6e8, 0 0 15px #54e6e8, 0 0 20px #54e6e8;
        }

        .btn-logout {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4bc3cc;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
        }

        .btn-logout:before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
            background-size: 50px 50px;
            z-index: 2;
            animation: move 2s linear infinite;
        }

        .btn-logout:hover {
            background-color: #004d52;
            transform: scale(1.1);
            box-shadow: 0 0 5px #54e6e8, 0 0 10px #54e6e8, 0 0 15px #54e6e8, 0 0 20px #54e6e8;
        }

        @keyframes move {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(-50%, -50%);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard">
            <?php echo getDashboardContent($role, $username); ?>
        </div>
        <a class="btn-logout" href="logout.php">Logout</a>
    </div>
</body>
</html>
