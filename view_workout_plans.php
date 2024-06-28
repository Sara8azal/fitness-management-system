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
    $workout_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM workout_plans WHERE id = $workout_id";

    if ($conn->query($sql) === TRUE) {
        $delete_message = "Workout plan deleted successfully.";
    } else {
        $delete_message = "Error deleting workout plan.";
    }
}

// Fetch workout plans
$customer_username = $_SESSION['username']; // Assuming you store the username in the session
$sql = "SELECT id, exercises, rounds, reps, duration, exercise_img, difficulty, calories_consumed, day 
        FROM workout_plans 
        WHERE customer_username = '$customer_username' 
        ORDER BY day DESC";
$result = $conn->query($sql);

$workout_plans = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $workout_plans[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Workout Plans</title>

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
            background-color: #f0f2f5;
            background-image: url('customer bg.jpg');
            /* Adjust background image */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            /* Increased max-width for larger container */
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: fadein 1s ease-in-out;
            margin-top: 20px;
            position: relative;
            /* Ensure container acts as positioning context */
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
            color: green;
            margin-bottom: 10px;
        }

        .workout-plan {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            overflow: hidden;
            position: relative;
        }

        .workout-details {
            width: 75%;
            padding-right: 20px;
            text-align: left;
        }

        .workout-details img {
            max-width: 100px;
            border-radius: 4px;
        }

        .workout-details div {
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
            margin-left: auto;
            /* Move button to the right */
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
            padding: 10px 20px;
            background-color: #4bc3cc;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            margin-left: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            position: absolute;
            bottom: 10px;
            left: 50%;
            /* Adjusted left position */
            transform: translateX(-50%);
            /* Center horizontally */
            z-index: 1;
        }

        .btn-back:hover {
            background-color: #02676c;
            transform: scale(1.1);
            box-shadow: 0 0 5px #54e6e8, 0 0 10px #54e6e8, 0 0 15px #54e6e8, 0 0 20px #54e6e8;
        }

        .btn-back:before {
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
    </style>


</head>

<body>
    <div class="container">
        <h2 style="color: #4bc3cc;">View Workout Plans</h2>
        <?php if (isset($delete_message)) : ?>
            <div class="message"><?php echo htmlspecialchars($delete_message); ?></div>
        <?php endif; ?>
        <?php if (!empty($workout_plans)) : ?>
            <?php foreach ($workout_plans as $workout) : ?>
                <div class="workout-plan">
                    <div class="workout-details">
                        <div><strong style="color: #4bc3cc;">Exercises:</strong> <?php echo htmlspecialchars($workout['exercises']); ?></div>
                        <div><strong style="color: #4bc3cc;">Rounds:</strong> <?php echo htmlspecialchars($workout['rounds']); ?></div>
                        <div><strong style="color: #4bc3cc;">Reps:</strong> <?php echo htmlspecialchars($workout['reps']); ?></div>
                        <div><strong style="color: #4bc3cc;">Duration:</strong> <?php echo htmlspecialchars($workout['duration']); ?> minutes</div>
                        <div><strong style="color: #4bc3cc;">Difficulty:</strong> <?php echo htmlspecialchars($workout['difficulty']); ?></div>
                        <div><strong style="color: #4bc3cc;">Calories Consumed:</strong> <?php echo htmlspecialchars($workout['calories_consumed']); ?></div>
                        <div><strong style="color: #4bc3cc;">Date:</strong> <?php echo htmlspecialchars($workout['day']); ?></div>
                        <img src="<?php echo htmlspecialchars($workout['exercise_img']); ?>" alt="Exercise Image">
                    </div>
                    <a href="view_workout_plans.php?delete_id=<?php echo $workout['id']; ?>" class="btn-done" onclick="return confirm('Are you sure you want to delete this workout plan?');">Done</a>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="workout-plan">No workout plans available.</div>
        <?php endif; ?>
        <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
    </div>
</body>

</html>