<?php
session_start();

// Database connection (replace with your actual credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitness gym";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate inputs (sanitize as needed)
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);
    $role = mysqli_real_escape_string($conn, $role);

    $sql = "SELECT * FROM user_accounts WHERE username='$username' AND role='$role'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Validate password (consider hashing in production)
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username']; // Store username in session
            header("Location: dashboard.php"); // Redirect to dashboard or desired page
            exit;
        } else {
            $message = "Invalid password";
        }
    } else {
        $message = "No user found with this role";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* CSS styles */
        body {
            font-family: 'Arial', sans-serif;
            background: url('login bg.jpg') no-repeat center center fixed;
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
            <h2>Login</h2>
            <?php
            $message = '';

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Handle form submission
                $username = $_POST['username'];
                $password = $_POST['password'];
                $role = $_POST['role'];

                // Example validation (you should use proper sanitization and validation)
                if ($username === 'example' && $password === 'password') {
                    $message = 'Login successful!';
                    // Redirect to dashboard or handle login logic
                    // Replace with your logic
                } else {
                    $message = 'Invalid username or password';
                }
            }
            ?>
            <?php if ($message): ?>
                <p style="color: red;"><?php echo $message; ?></p>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
                <button class="btn" type="submit">Login</button>
            </form>
        </div>
    </div>

    <script>
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
