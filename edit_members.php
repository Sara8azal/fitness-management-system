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
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id > 0) {
    // Fetch existing user details
    $sql = "SELECT * FROM user_accounts WHERE id = $user_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $message = "User not found";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $username = $_POST['username'];
    $role = $_POST['role'];

    if (!empty($username) && !empty($role)) {
        $sql = "UPDATE user_accounts SET username = '$username', role = '$role' WHERE id = $user_id";

        if ($conn->query($sql) === TRUE) {
            $message = "User updated successfully";
            header("Location: view_members.php");
            exit;
        } else {
            $message = "Error updating user: " . $conn->error;
        }
    } else {
        $message = "All fields are required";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member</title>
    <style>
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
            width: 400px;
            background-color: rgba(255, 255, 255, 0.8); /* Transparent white background */
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center; /* Center align text and elements */
        }

        h2 {
            color: #4bc3cc;
            margin-bottom: 20px;
        }

        .message {
            color: #ff5757;
            margin-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        .form-group input[type="text"],
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
        }

        .btn {
            background-color: #4bc3cc;
            color: #fff;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn:hover {
            background-color: #02676c;
            transform: scale(1.1);
            box-shadow: 0 0 5px #54e6e8, 0 0 10px #54e6e8, 0 0 15px #54e6e8, 0 0 20px #54e6e8;
        }

        .btn-back {
            background-color: #4bc3cc;
            color: #fff;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-back:hover {
            background-color: #02676c;
            transform: scale(1.1);
            box-shadow: 0 0 5px #54e6e8, 0 0 10px #54e6e8, 0 0 15px #54e6e8, 0 0 20px #54e6e8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Member</h2>
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <?php if (!empty($user)): ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $user_id; ?>" method="post">
                <div class="form-group">
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="form-group">
                    <select id="role" name="role" required>
                        <option value="customer" <?php echo ($user['role'] == 'customer') ? 'selected' : ''; ?>>Customer</option>
                        <option value="trainer" <?php echo ($user['role'] == 'trainer') ? 'selected' : ''; ?>>Trainer</option>
                        <option value="dietitian" <?php echo ($user['role'] == 'dietitian') ? 'selected' : ''; ?>>Dietitian</option>
                        <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="submit" name="update_user" value="Update User" class="btn">
                </div>
            </form>
        <?php else: ?>
            <p style="color: #ff5757;">User details not found. <a href="view_members.php" style="color: #4bc3cc;">Go back to members list.</a></p>
        <?php endif; ?>
        <a href="view_members.php" class="btn-back">Back to Members List</a>
    </div>
</body>
</html>
