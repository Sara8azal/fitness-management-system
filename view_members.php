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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search_keyword = $_POST['search'];
    $sql = "SELECT * FROM user_accounts WHERE username LIKE '%$search_keyword%'";
} else {
    $sql = "SELECT * FROM user_accounts";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $users = []; // Initialize $users as an empty array if no users are found
    $message = "No users found";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $user_id = $_POST['delete_id'];
    $sql = "DELETE FROM user_accounts WHERE id = $user_id";

    if ($conn->query($sql) === TRUE) {
        $message = "User deleted successfully";
        header("Location: view_members.php");
        exit;
    } else {
        $message = "Error deleting user: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Members</title>
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

        .form-group {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-group input[type="text"] {
            width: 70%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
        }

        .btn-action {
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

        .btn-action:hover {
            background-color: #02676c; /* Adjust hover color */
            transform: scale(1.1);
            box-shadow: 0 0 5px #54e6e8, 0 0 10px #54e6e8, 0 0 15px #54e6e8, 0 0 20px #54e6e8;
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
    </style>
</head>
<body>
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 20px; color: #4bc3cc;">View Members</h2>
        <!-- <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?> -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form-group">
            <input type="text" name="search" placeholder="Search by Username" value="<?php echo $search_keyword; ?>">
            <button type="submit" class="btn-action">Search</button>
        </form>
        <?php if (!empty($users)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['role']; ?></td>
                            <td>
                                <a href="edit_members.php?id=<?php echo $user['id']; ?>" class="btn-action">Edit</a>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="display: inline;">
                                    <input type="hidden" name="delete_id" value="<?php echo $user['id']; ?>">
                                    <input type="submit" class="btn-action btn-danger" value="Delete" onclick="return confirm('Are you sure you want to delete this user?');">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #ff5757;">No users found.</p>
        <?php endif; ?>
        <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
    </div>
</body>
</html>
