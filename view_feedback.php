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

// Handle deletion of feedback
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $feedback_id = $_POST['delete_id'];
    $sql = "DELETE FROM feedbacks WHERE id = $feedback_id";

    if ($conn->query($sql) === TRUE) {
        $message = "Feedback deleted successfully";
        header("Location: view_feedback.php");
        exit;
    } else {
        $message = "Error deleting feedback: " . $conn->error;
    }
}

// Fetch existing feedback records
$sql = "SELECT * FROM feedbacks ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$feedback_records = [];

if ($result->num_rows > 0) {
    $feedback_records = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $message = "No feedback records found";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback</title>
    <style>
        /* General styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('admin1 bg.jpg'); /* Replace with your image path */
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            width: 90%; /* Adjusted width */
            max-width: 800px; /* Maximum width */
            background-color: rgba(255, 255, 255, 0.9); /* Transparent white background */
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }

        .message {
            margin-bottom: 10px;
            color: #ff5757;
            font-size: 1.1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
            background-color: #4bc3cc; /* Button background color */
            color: #fff; /* Button text color */
        }

        .btn-danger {
            background-color: #ff5757; /* Red color for delete button */
            border: 2px solid #ff5757; /* Matching border color */
            padding: 15px; /* Adjusted padding */
            cursor: pointer;
            font-size: 18px; /* Adjusted font size */
            color: #fff; /* Button text color */
            border-radius: 25px; /* Rounded border */
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #ff7777; /* Darker red on hover */
            transform: scale(1.1);
            box-shadow: 0 0 5px #ff7777, 0 0 10px #ff7777, 0 0 15px #ff7777, 0 0 20px #ff7777;
        }

        .btn-back {
            background-color: #4bc3cc;
            border: 2px solid #54e6e8;
            padding: 15px;
            cursor: pointer;
            font-size: 18px;
            color: #fff;
            border-radius: 25px;
            transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            display: inline-block;
            margin-top: 20px;
        }

        .btn-back:hover {
            background: #02676c;
            transform: scale(1.1);
            box-shadow: 0 0 5px #54e6e8, 0 0 10px #54e6e8, 0 0 15px #54e6e8, 0 0 20px #54e6e8;
        }

        h2 {
            color: #4bc3cc; /* Header color */
            margin-bottom: 20px;
        }

        table th {
            background-color: #4bc3cc; /* Table header background color */
        }

        .btn-back-container {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>View Feedback</h2>
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Display existing feedback records -->
        <?php if (!empty($feedback_records)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Feedback Type</th>
                        <th>Message</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($feedback_records as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['username']); ?></td>
                            <td><?php echo ucfirst($record['feedback_type']); ?></td>
                            <td><?php echo htmlspecialchars($record['message']); ?></td>
                            <td><?php echo $record['created_at']; ?></td>
                            <td>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="display: inline;">
                                    <input type="hidden" name="delete_id" value="<?php echo $record['id']; ?>">
                                    <button type="submit" class="btn-danger" name="delete_btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No feedback records found.</p>
        <?php endif; ?>

        <div class="btn-back-container">
            <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
