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

// Fetch customer usernames
$customer_usernames = [];
$sql = "SELECT username FROM user_accounts WHERE role = 'customer'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customer_usernames[] = $row['username'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_username = $_POST['customer_username'];
    $exercises = $_POST['exercises'];
    $rounds = $_POST['rounds'];
    $reps = $_POST['reps'];
    $duration = $_POST['duration'];
    $difficulty = $_POST['difficulty'];
    $calories_consumed = $_POST['calories_consumed'];
    $day = $_POST['day'];

    // File upload handling
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["exercise_img"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["exercise_img"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $message = "Sorry, file is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists and rename the file to avoid conflicts
    if (file_exists($target_file)) {
        $base_name = pathinfo($target_file, PATHINFO_FILENAME);
        $extension = pathinfo($target_file, PATHINFO_EXTENSION);
        $i = 1;
        while (file_exists($target_file)) {
            $target_file = $target_dir . $base_name . "_" . $i . "." . $extension;
            $i++;
        }
    }

    // Check file size
    if ($_FILES["exercise_img"]["size"] > 500000) {
        $message = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowed_extensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowed_extensions)) {
        $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $message .= " Sorry, your file was not uploaded.";
    } else {
        // if everything is ok, try to upload file
        if (move_uploaded_file($_FILES["exercise_img"]["tmp_name"], $target_file)) {
            // File uploaded successfully, proceed with database insertion
            $sql_check_user = "SELECT username FROM user_accounts WHERE role = 'customer' AND username = ?";
            $stmt_check_user = $conn->prepare($sql_check_user);
            $stmt_check_user->bind_param("s", $customer_username);
            $stmt_check_user->execute();
            $stmt_check_user->store_result();

            if ($stmt_check_user->num_rows > 0) {
                // Valid customer username, proceed with insertion
                $sql_insert_plan = "INSERT INTO workout_plans (customer_username, exercises, rounds, reps, duration, exercise_img, difficulty, calories_consumed, day) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_insert_plan = $conn->prepare($sql_insert_plan);
                $stmt_insert_plan->bind_param("ssiiisssi", $customer_username, $exercises, $rounds, $reps, $duration, $target_file, $difficulty, $calories_consumed, $day);

                if ($stmt_insert_plan->execute()) {
                    $message .= " Workout plan sent successfully!";
                } else {
                    $message .= " Error sending workout plan: " . $stmt_insert_plan->error;
                }
            } else {
                // Invalid customer username
                $message .= " Error sending workout plan: Customer username does not exist or is not a customer.";
            }

            $stmt_check_user->close();
            $stmt_insert_plan->close();
        } else {
            $message = "Sorry, there was an error uploading your file.";
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
    <title>Send Workout Plan</title>
    <style>
        /* CSS for Neon Animation */
        @keyframes neon {
            from {
                text-shadow: 0 0 10px #fff, 0 0 20px #4bc3cc, 0 0 30px #4bc3cc, 0 0 40px #4bc3cc, 0 0 50px #4bc3cc, 0 0 60px #4bc3cc, 0 0 70px #4bc3cc;
            }

            to {
                text-shadow: 0 0 5px #fff, 0 0 10px #4bc3cc, 0 0 15px #4bc3cc, 0 0 20px #4bc3cc, 0 0 25px #4bc3cc, 0 0 30px #4bc3cc, 0 0 40px #4bc3cc;
            }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: url('trainer1 bg.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 50px auto;
            background-color: rgba(255, 255, 255, 0.8);
            /* Semi-transparent white background */
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            backdrop-filter: blur(10px);
            /* Blur effect for transparency */
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #4bc3cc;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        textarea,
        select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            background-color: #4bc3cc;
            color: #fff;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-right: 10px;
            position: relative;
            overflow: hidden;
        }

        button:hover {
            background-color: #58d9e0;
        }

        button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300%;
            height: 300%;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            z-index: 0;
            transition: all 0.5s ease-out;
            transform: translate(-50%, -50%);
        }

        button:hover::before {
            width: 0;
            height: 0;
            opacity: 0;
        }

        button span {
            position: relative;
            z-index: 1;
        }

        .message {
            text-align: center;
            color: green;
            margin-bottom: 20px;
        }

        .btn-back {
            background-color: #4bc3cc;
            color: #fff;
            border: none;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 style="color: #4bc3cc;">Send Workout Plan</h2>
        <?php if (!empty($message)) : ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div>
                <label for="customer_username">Customer Username:</label>
                <select name="customer_username" id="customer_username" required>
                    <option value="">Select Customer</option>
                    <?php foreach ($customer_usernames as $username) : ?>
                        <option value="<?php echo htmlspecialchars($username); ?>"><?php echo htmlspecialchars($username); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="exercises">Exercises:</label>
                <textarea name="exercises" id="exercises" rows="5" required></textarea>
            </div>
            <div>
                <label for="rounds">Rounds:</label>
                <input type="number" name="rounds" id="rounds" required>
            </div>
            <div>
                <label for="reps">Reps:</label>
                <input type="number" name="reps" id="reps" required>
            </div>
            <div>
                <label for="duration">Duration (minutes):</label>
                <input type="number" name="duration" id="duration" required>
            </div>
            <div>
                <label for="exercise_img">Exercise Image:</label>
                <input type="file" name="exercise_img" id="exercise_img" required>
            </div>
            <div>
                <label for="difficulty">Difficulty:</label>
                <input type="text" name="difficulty" id="difficulty" required>
            </div>
            <div>
                <label for="calories_consumed">Calories Consumed Per Round:</label>
                <input type="number" name="calories_consumed" id="calories_consumed" required>
            </div>
            <div>
                <label for="day">Day:</label>
                <input type="date" name="day" id="day" required>
            </div>
            <div style="text-align: center;">
                <button type="submit">Send Workout Plan</button>
                <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
            </div>
        </form>
    </div>
</body>

</html>