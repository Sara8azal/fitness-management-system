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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['username'];
    $feedback_type = $_POST['feedback_type'];
    $feedback_message = $_POST['message'];

    $sql_insert_feedback = "INSERT INTO feedbacks (username, feedback_type, message) VALUES (?, ?, ?)";
    $stmt_insert_feedback = $conn->prepare($sql_insert_feedback);
    $stmt_insert_feedback->bind_param("sss", $username, $feedback_type, $feedback_message);

    if ($stmt_insert_feedback->execute()) {
        $message = "Feedback submitted successfully!";
    } else {
        $message = "Error submitting feedback: " . $stmt_insert_feedback->error;
    }

    $stmt_insert_feedback->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Feedback</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            background-image: url('customer bg.jpg');
            /* Specify the path to your background image */
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            width: 80%;
            max-width: 600px;
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

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            text-align: left;
            color: #4bc3cc;
        }

        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button,
        .btn-back {
            width: 45%; /* Adjusted width to accommodate spacing */
            background-color: #4bc3cc;
            color: #fff;
            border: none;
            padding: 12px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            margin-top: 10px; /* Added margin */
            margin-right: 5px; /* Added margin between buttons */
        }

        button:hover,
        .btn-back:hover {
            background-color: #02676c;
        }

        .message {
            text-align: center;
            color: green;
            margin-bottom: 20px;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            margin-top: 20px; /* Adjusted margin for button alignment */
        }
    </style>

</head>

<body>
    <div class="container">
        <h2 style="color: #4bc3cc;">Add Feedback</h2>
        <?php if (!empty($message)) : ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="feedback_type">Feedback Type:</label>
                <select name="feedback_type" id="feedback_type" required>
                    <option value="">Select Feedback Type</option>
                    <option value="trainer">Trainer</option>
                    <option value="admin">Admin</option>
                    <option value="dietitian">Dietitian</option>
                    <option value="customer">Customer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea name="message" id="message" rows="5" required></textarea>
            </div>
            <div class="btn-container">
                <button type="submit">Submit Feedback</button>
                <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
            </div>
        </form>
    </div>
</body>

</html>
