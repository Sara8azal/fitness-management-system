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

$message = '';

// Handle form submission to add a new class
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = $_POST['class_name'];
    $class_date = $_POST['class_date'];
    $class_time = $_POST['class_time'];
    $class_duration = $_POST['class_duration'];

    // Calculate the end time of the class
    $class_end_time = date("H:i:s", strtotime($class_time) + $class_duration * 60);

    // Check for time conflicts
    $sql = "SELECT * FROM classes 
            WHERE class_date = '$class_date' 
            AND (
                (class_time <= '$class_time' AND ADDTIME(class_time, SEC_TO_TIME(class_duration * 60)) > '$class_time') 
                OR 
                (class_time < '$class_end_time' AND ADDTIME(class_time, SEC_TO_TIME(class_duration * 60)) >= '$class_end_time')
            )";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $message = "Time conflict detected with an existing class.";
    } else {
        $sql = "INSERT INTO classes (class_name, class_date, class_time, class_duration) 
                VALUES ('$class_name', '$class_date', '$class_time', $class_duration)";

        if ($conn->query($sql) === TRUE) {
            $message = "New class added successfully";
        } else {
            $message = "Error adding class: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Class</title>
    <style>
        /* General styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('trainer1 bg.jpg'); /* Replace with your background image path */
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 90%;
            max-width: 800px;
            background-color: rgba(255, 255, 255, 0.8); /* Transparent white background */
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
            animation: slideInFromBottom 1s ease;
        }

        @keyframes slideInFromBottom {
            0% {
                transform: translateY(100%);
            }
            100% {
                transform: translateY(0);
            }
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
            text-align: center; /* Center form elements */
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-weight: bold;
            color: #4bc3cc;
        }

        input[type="text"], input[type="date"], input[type="time"], input[type="number"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        button, .btn-back {
            background-color: #4bc3cc;
            color: #fff;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover, .btn-back:hover {
            background-color: #02676c;
        }

        .btn-back {
            background-color: #4bc3cc; /* Match color to Add Class button */
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 10px;
            }

            input[type="text"], input[type="date"], input[type="time"], input[type="number"] {
                width: 100%;
                font-size: 14px;
            }

            button, .btn-back {
                padding: 10px 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="color: #4bc3cc;">Add Class</h2>
        <?php if (!empty($message)): ?>
            <p style="color: #ff5757;"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Form to add a new class -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label for="class_name">Class Name:</label>
                <input type="text" name="class_name" id="class_name" required>
            </div>
            <div>
                <label for="class_date">Class Date:</label>
                <input type="date" name="class_date" id="class_date" required>
            </div>
            <div>
                <label for="class_time">Class Time:</label>
                <input type="time" name="class_time" id="class_time" required>
            </div>
            <div>
                <label for="class_duration">Class Duration (in minutes):</label>
                <input type="number" name="class_duration" id="class_duration" required>
            </div>
            <div style="text-align: center;">
                <button type="submit">Add Class</button>
            </div>
        </form>

        <!-- Back to Dashboard Button -->
        <div style="text-align: center;">
            <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
