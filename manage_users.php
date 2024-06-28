<?php
session_start();

// Include db.php to establish database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitness gym";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    // Redirect if user is not admin
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Server-side password validation
    if (strlen($password) < 8) {
        $message = '<span style="color: red;">Password must be greater than 8 characters.</span>';
    } elseif (!preg_match('/[A-Za-z]/', $password)) {
        $message = '<span style="color: red;">Password must contain at least one letter.</span>';
    } else {
        $sql = "INSERT INTO user_accounts (username, password, role) VALUES ('$username', '$password', '$role')";

        if ($conn->query($sql) === TRUE) {
            $message = '<span style="color: green;">User added successfully</span>';
        } else {
            $message = '<span style="color: red;">Error adding user: ' . $conn->error . '</span>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        /* CSS styles */
        body {
            font-family: 'Arial', sans-serif;
            background: url('admin1 bg.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            width: 400px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.8); /* Transparent background */
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            transition: transform 0.3s ease;
            text-align: center; /* Center content */
        }

        .container:hover {
            transform: scale(1.05);
        }

        .login-box {
            text-align: center;
        }

        .login-box h2 {
            margin: 0 0 20px;
            padding: 0;
            color: #4bc3cc; /* Color changed to match button */
            font-size: 28px;
            font-weight: bold;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            border-bottom: 2px solid #54e6e8;
            padding-bottom: 10px;
            display: inline-block;
        }

        .textbox {
            position: relative;
            margin-bottom: 30px;
        }

        .textbox input,
        .custom-select {
            width: 100%;
            padding: 10px;
            background: #f2f2f2;
            border: none;
            outline: none;
            color: #333;
            font-size: 18px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .custom-select-wrapper {
            position: relative;
            width: 100%;
            display: inline-block;
            margin-bottom: 20px; /* Spacing between inputs and button */
        }

        .custom-select {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            background-color: #4bc3cc; /* Background color of the custom select */
            padding: 10px;
            border-radius: 5px;
            color: #fff; /* Text color of the custom select */
        }

        .custom-select-options {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 10;
            width: 100%; /* Adjust width to fit container */
            max-height: 200px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid #ccc;
            border-top: none;
            border-radius: 0 0 5px 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .custom-select-option {
            padding: 10px;
            transition: background 0.3s ease, color 0.3s ease;
            cursor: pointer;
        }

        .custom-select-option:hover {
            background: #54e6e8;
            color: #fff;
        }

        .btn {
            width: 250px; /* Increased width */
            background: #4bc3cc; /* Button background color */
            border: 2px solid #54e6e8;
            padding: 15px; /* Increased padding */
            cursor: pointer;
            font-size: 20px; /* Adjusted font size */
            color: #fff; /* Button text color */
            border-radius: 25px; /* Rounded border */
            transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-top: 20px; /* Adjust margin */
            margin-bottom: 20px; /* Added margin bottom */
        }

        .btn:before {
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

        .btn:hover {
            background: #02676c; /* Adjust hover color if needed */
            transform: scale(1.1);
            box-shadow: 0 0 5px #54e6e8, 0 0 10px #54e6e8, 0 0 15px #54e6e8, 0 0 20px #54e6e8;
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
        <div class="login-box">
            <h2>Add Users</h2>
            <?php echo $message; ?>

            <!-- Add User Form -->
            <form id="addUserForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="textbox">
                    <input type="text" placeholder="Username" name="username" required>
                </div>
                <div class="textbox">
                    <input type="password" placeholder="Password" name="password" required>
                </div>
                <div class="textbox">
                    <select name="role" class="custom-select" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="customer">Customer</option>
                        <option value="trainer">Trainer</option>
                        <option value="dietitian">Dietitian</option>
                    </select>
                </div>
                <button class="btn" type="submit" name="add_user">Add User</button>
            </form>

            <!-- Link to go back to dashboard -->
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
        </div>
    </div>

    <script>
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            var password = document.querySelector('input[name="password"]').value;
            if (password.length < 8) {
                alert("Password must be greater than 8 characters.");
                e.preventDefault();
            } else if (!/[A-Za-z]/.test(password)) {
                alert("Password must contain at least one letter.");
                e.preventDefault();
            }
        });

        // JavaScript for animated gradient effect on button
        document.querySelector('.btn').addEventListener('mousemove', function(e) {
            const btn = e.target;
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            btn.style.setProperty('--x', x + 'px');
            btn.style.setProperty('--y', y + 'px');
        });
    </script>
</body>
</html>
