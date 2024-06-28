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
$search_keyword = '';

// Fetch existing attendance records
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search_keyword = $_POST['search'];
    $sql = "SELECT a.*, u.role FROM attendance a 
            INNER JOIN user_accounts u ON a.member_username = u.username 
            WHERE u.username LIKE '%$search_keyword%'";
} else {
    $sql = "SELECT a.*, u.role FROM attendance a 
            INNER JOIN user_accounts u ON a.member_username = u.username";
}

$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$attendance_records = [];

if ($result->num_rows > 0) {
    $attendance_records = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $message = "No attendance records found";
}

// Handle deletion of attendance records
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $attendance_id = $_POST['delete_id'];
    $sql = "DELETE FROM attendance WHERE id = $attendance_id";

    if ($conn->query($sql) === TRUE) {
        $message = "Attendance record deleted successfully";
        header("Location: manage_attendance.php");
        exit;
    } else {
        $message = "Error deleting attendance record: " . $conn->error;
    }
}

// Handle addition of new attendance record
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_attendance'])) {
    $member_username = $_POST['member_username'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    $sql = "INSERT INTO attendance (member_username, date, status) 
            VALUES ('$member_username', '$date', '$status')";

    if ($conn->query($sql) === TRUE) {
        $message = "New attendance record added successfully";
        header("Location: manage_attendance.php");
        exit;
    } else {
        $message = "Error adding attendance record: " . $conn->error;
    }
}

// Fetch all usernames from user_accounts table
$usernames = [];
$sql_usernames = "SELECT username FROM user_accounts";
$result_usernames = $conn->query($sql_usernames);

if ($result_usernames === false) {
    die("Error fetching usernames: " . $conn->error);
}

if ($result_usernames->num_rows > 0) {
    while ($row = $result_usernames->fetch_assoc()) {
        $usernames[] = $row['username'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendance</title>
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

        form {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group select {
            width: 70%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
        }

        button {
            display: inline-block;
            padding: 15px; /* Adjusted padding */
            text-decoration: none;
            border-radius: 25px;
            margin-right: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #4bc3cc; /* Button background color */
            border: 2px solid #54e6e8;
            cursor: pointer;
            font-size: 18px; /* Adjusted font size */
            color: #fff; /* Button text color */
        }

        button:hover {
            background-color: #02676c; /* Adjust hover color */
            transform: scale(1.1);
            box-shadow: 0 0 5px #54e6e8, 0 0 10px #54e6e8, 0 0 15px #54e6e8, 0 0 20px #54e6e8;
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

        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 10px;
            }

            .form-group input[type="text"],
            .form-group input[type="date"],
            .form-group select {
                font-size: 14px;
            }

            button {
                padding: 10px 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="color: #4bc3cc;">Manage Attendance</h2>
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Form to add new attendance record -->
        <h3 style="color: #4bc3cc;">Add New Attendance Record</h3>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form-group">
            <div>
                <label for="member_username">Member Username:</label>
                <select name="member_username" id="member_username" required>
                    <?php foreach ($usernames as $username): ?>
                        <option value="<?php echo $username; ?>"><?php echo $username; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="date">Date:</label>
                <input type="date" name="date" id="date" required>
            </div>
            <div>
                <label for="status">Status:</label>
                <select name="status" id="status" required>
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                </select>
            </div>
            <button type="submit" name="add_attendance">Add Attendance</button>
        </form>

        <!-- Display existing attendance records -->
        <h3 style="color: #4bc3cc;">Existing Attendance Records</h3>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form-group">
            <input type="text" name="search" placeholder="Search by Username" value="<?php echo $search_keyword; ?>">
            <button type="submit" class="btn-action">Search</button>
        </form>
        <?php if (!empty($attendance_records)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Member Username</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_records as $record): ?>
                        <tr>
                            <td><?php echo $record['id']; ?></td>
                            <td><?php echo $record['member_username']; ?></td>
                            <td><?php echo $record['date']; ?></td>
                            <td><?php echo $record['status']; ?></td>
                            <td>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="display: inline;">
                                    <input type="hidden" name="delete_id" value="<?php echo $record['id']; ?>">
                                    <button type="submit" class="btn-action btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No attendance records found.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
    </div>
</body>
</html>
