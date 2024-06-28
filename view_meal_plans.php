<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitness gym";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $meal_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM meal_plans WHERE id = $meal_id";

    if ($conn->query($sql) === TRUE) {
        $delete_message = "Meal plan deleted successfully.";
    } else {
        $delete_message = "Error deleting meal plan.";
    }
}

// Fetch meal plans
$customer_username = $_SESSION['username']; // Assuming you store the username in the session
$sql = "SELECT id, dietitian_username, date, breakfast_description, breakfast_calories, lunch_description, lunch_calories, dinner_description, dinner_calories, snacks_description, snacks_calories, total_calories 
        FROM meal_plans 
        WHERE customer_username = '$customer_username' 
        ORDER BY date DESC";
$result = $conn->query($sql);

$meal_plans = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $meal_plans[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Meal Plans</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            background-image: url('customer bg.jpg'); /* Specify the path to your background image */
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
            animation: fadein 1s ease-in-out;
            margin-top: 20px;
            position: relative;
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

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .message {
            text-align: center;
            color: green;
            margin-bottom: 10px;
        }

        .meal-plan {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
        }

        .meal-details {
            width: 90%;
        }

        .meal-details div {
            margin-bottom: 10px;
        }

        .btn-done {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4bc3cc;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-done:hover {
            background-color: #02676c;
            transform: scale(1.1);
            box-shadow: 0 0 5px #54e6e8, 0 0 10px #54e6e8, 0 0 15px #54e6e8, 0 0 20px #54e6e8;
        }

        .btn-done:before {
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

        @keyframes move {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(-50%, -50%);
            }
        }

        .btn-back {
            display: inline-block;
            padding: 8px 16px; /* Adjusted padding for smaller size */
            background-color: #4bc3cc;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-back:hover {
            background-color: #02676c;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 style="color: #4bc3cc;">View Meal Plans</h2>
        <?php if (isset($delete_message)) : ?>
            <div class="message"><?php echo htmlspecialchars($delete_message); ?></div>
        <?php endif; ?>
        <?php if (!empty($meal_plans)) : ?>
            <?php foreach ($meal_plans as $meal) : ?>
                <div class="meal-plan">
                    <div class="meal-details">
                        <div><strong style="color: #4bc3cc;">Dietitian:</strong> <?php echo htmlspecialchars($meal['dietitian_username']); ?></div>
                        <div><strong style="color: #4bc3cc;">Date:</strong> <?php echo htmlspecialchars($meal['date']); ?></div>
                        <div><strong style="color: #4bc3cc;">Breakfast:</strong> <?php echo htmlspecialchars($meal['breakfast_description']); ?> (<?php echo htmlspecialchars($meal['breakfast_calories']); ?> calories)</div>
                        <div><strong style="color: #4bc3cc;">Lunch:</strong> <?php echo htmlspecialchars($meal['lunch_description']); ?> (<?php echo htmlspecialchars($meal['lunch_calories']); ?> calories)</div>
                        <div><strong style="color: #4bc3cc;">Dinner:</strong> <?php echo htmlspecialchars($meal['dinner_description']); ?> (<?php echo htmlspecialchars($meal['dinner_calories']); ?> calories)</div>
                        <div><strong style="color: #4bc3cc;">Snacks:</strong> <?php echo htmlspecialchars($meal['snacks_description']); ?> (<?php echo htmlspecialchars($meal['snacks_calories']); ?> calories)</div>
                        <div><strong style="color: #4bc3cc;">Total Calories:</strong> <?php echo htmlspecialchars($meal['total_calories']); ?></div>
                    </div>
                    <a href="view_meal_plans.php?delete_id=<?php echo $meal['id']; ?>" class="btn-done" onclick="return confirm('Are you sure you want to delete this meal plan?');">Done</a>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="meal-plan">No meal plans available.</div>
        <?php endif; ?>
        <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
    </div>
</body>

</html>
