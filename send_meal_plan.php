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
    $dietitian_username = $_SESSION['username'];
    $customer_username = $_POST['customer_username'];
    $date = $_POST['date'];
    $breakfast_description = $_POST['breakfast_description'];
    $breakfast_calories = $_POST['breakfast_calories'];
    $lunch_description = $_POST['lunch_description'];
    $lunch_calories = $_POST['lunch_calories'];
    $dinner_description = $_POST['dinner_description'];
    $dinner_calories = $_POST['dinner_calories'];
    $snacks_description = $_POST['snacks_description'];
    $snacks_calories = $_POST['snacks_calories'];
    $total_calories = $breakfast_calories + $lunch_calories + $dinner_calories + $snacks_calories;

    $sql_insert_plan = "INSERT INTO meal_plans (dietitian_username, customer_username, date, breakfast_description, breakfast_calories, lunch_description, lunch_calories, dinner_description, dinner_calories, snacks_description, snacks_calories, total_calories) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert_plan = $conn->prepare($sql_insert_plan);
    $stmt_insert_plan->bind_param("sssississiii", $dietitian_username, $customer_username, $date, $breakfast_description, $breakfast_calories, $lunch_description, $lunch_calories, $dinner_description, $dinner_calories, $snacks_description, $snacks_calories, $total_calories);

    if ($stmt_insert_plan->execute()) {
        $message = "Meal plan sent successfully!";
    } else {
        $message = "Error sending meal plan: " . $stmt_insert_plan->error;
    }

    $stmt_insert_plan->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Meal Plan</title>
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
        width: 90%;
        max-width: 800px;
        background-color: rgba(255, 255, 255, 0.9); /* Transparent white background */
        padding: 20px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        margin-top: 20px;
        text-align: center;
    }

    h1 {
        color: #4bc3cc;
        margin-bottom: 20px;
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

    .form-group select,
    .form-group textarea,
    .form-group input[type="date"],
    .form-group input[type="number"],
    .form-group input[type="text"] {
        width: 70%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        outline: none;
    }

    .meal-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .meal-container {
        background-color: #f9f9f9;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        width: 23%;
        box-sizing: border-box;
    }

    .meal-container h3 {
        margin-top: 0;
        color: #555;
    }

    .total-calories {
        text-align: center;
        margin-top: 20px;
        font-size: 18px;
        font-weight: bold;
    }

    button {
        width: 100%;
        background-color: #4bc3cc;
        color: #fff;
        border: none;
        padding: 12px;
        cursor: pointer;
        border-radius: 4px;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #02676c;
    }

    .btn-back {
        display: block;
        text-align: center;
        margin-top: 10px;
        background-color: #4bc3cc;
        color: #fff;
        padding: 10px;
        border-radius: 4px;
        text-decoration: none;
    }

    .btn-back:hover {
        background-color: #02676c;
    }

    label {
        color: #4bc3cc; /* Color for labels */
        font-weight: bold;
        display: block;
        margin-bottom: 8px;
    }

    .message {
        text-align: center;
        color: #ff5757;
        margin-bottom: 20px;
    }
</style>

</head>

<body>
    <div class="container">
        <h1 style="color: #4bc3cc;">Send Meal Plan</h1>
        <div class="message"><?php echo $message; ?></div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="meal-plan-form">
            <div class="form-group">
                <label for="customer_username">Customer Username:</label>
                <select name="customer_username" id="customer_username" required>
                    <option value="">Select Customer</option>
                    <?php foreach ($customer_usernames as $username) : ?>
                        <option value="<?php echo htmlspecialchars($username); ?>"><?php echo htmlspecialchars($username); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="date">Date of Plan:</label>
                <input type="date" name="date" id="date" required>
            </div>
            <div class="meal-row">
                <div class="meal-container">
                    <h3 style="color: #4bc3cc;">Breakfast</h3>
                    <label for="breakfast_description">Description:</label>
                    <textarea name="breakfast_description" id="breakfast_description" rows="3" required></textarea>
                    <label for="breakfast_calories">Calories:</label>
                    <input type="number" name="breakfast_calories" id="breakfast_calories" required>
                </div>
                <div class="meal-container">
                    <h3 style="color: #4bc3cc;">Lunch</h3>
                    <label for="lunch_description">Description:</label>
                    <textarea name="lunch_description" id="lunch_description" rows="3" required></textarea>
                    <label for="lunch_calories">Calories:</label>
                    <input type="number" name="lunch_calories" id="lunch_calories" required>
                </div>
                <div class="meal-container">
                    <h3 style="color: #4bc3cc;">Dinner</h3>
                    <label for="dinner_description">Description:</label>
                    <textarea name="dinner_description" id="dinner_description" rows="3" required></textarea>
                    <label for="dinner_calories">Calories:</label>
                    <input type="number" name="dinner_calories" id="dinner_calories" required>
                </div>
                <div class="meal-container">
                    <h3 style="color: #4bc3cc;">Snacks</h3>
                    <label for="snacks_description">Description:</label>
                    <textarea name="snacks_description" id="snacks_description" rows="3" required></textarea>
                    <label for="snacks_calories">Calories:</label>
                    <input type="number" name="snacks_calories" id="snacks_calories" required>
                </div>
            </div>
            <div class="total-calories" style="color: #4bc3cc;">
                Total Calories: <span id="total-calories" style="color: #4bc3cc;">0</span>
            </div>
            <button type="submit">Send Meal Plan</button>
            <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
        </form>
    </div>
    <script>
        document.getElementById('meal-plan-form').addEventListener('input', function() {
            var totalCalories = 0;
            var calorieInputs = document.querySelectorAll('input[type="number"]');
            calorieInputs.forEach(function(input) {
                totalCalories += parseInt(input.value) || 0;
            });
            document.getElementById('total-calories').textContent = totalCalories;
        });
    </script>
</body>

</html>
