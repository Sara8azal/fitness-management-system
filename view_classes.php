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

// Delete classes with past dates
$current_date = date('Y-m-d');
$sql = "DELETE FROM classes WHERE class_date < '$current_date'";
$conn->query($sql);

// Handle delete request
if (isset($_GET['delete_id'])) {
    $class_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM classes WHERE id = $class_id";

    if ($conn->query($sql) === TRUE) {
        $delete_message = "Class deleted successfully.";
    } else {
        $delete_message = "Error deleting class.";
    }
}

// Fetch classes
$sql = "SELECT id, class_name, class_date, class_time, class_duration FROM classes ORDER BY class_date, class_time";
$result = $conn->query($sql);

$classes = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Classes</title>
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
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
        }

        .container {
            width: 90%;
            max-width: 900px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: fadein 1s ease-in-out;
            margin-top: 20px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        .btn-done,
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
            position: relative;
            overflow: hidden;
        }

        .btn-done:hover,
        .btn-back:hover {
            background-color: #02676c;
            transform: scale(1.1);
            box-shadow: 0 0 5px #54e6e8, 0 0 10px #54e6e8, 0 0 15px #54e6e8, 0 0 20px #54e6e8;
        }

        .btn-done:before,
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
        <h2 style="color: #4bc3cc;">View Classes</h2>
        <?php if (isset($delete_message)) : ?>
            <div class="message"><?php echo htmlspecialchars($delete_message); ?></div>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Duration (minutes)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($classes)) : ?>
                    <?php foreach ($classes as $class) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($class['class_date']); ?></td>
                            <td><?php echo htmlspecialchars($class['class_time']); ?></td>
                            <td><?php echo htmlspecialchars($class['class_duration']); ?></td>
                            <td>
                                <a href="view_classes.php?delete_id=<?php echo $class['id']; ?>" class="btn-done" onclick="return confirm('Are you sure you want to delete this class?');">Done</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5">No classes available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
    </div>
</body>

</html>
