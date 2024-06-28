<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
} else {
    die("Edit ID not specified.");
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

$sql = "SELECT * FROM foods WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $edit_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $food = $result->fetch_assoc();
} else {
    die("Food item not found.");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Food</title>
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
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
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
        <h2>Edit Food</h2>
        <form method="post" action="update_food.php" enctype="multipart/form-data">
            <input type="hidden" name="edit_id" value="<?php echo $food['id']; ?>">
            <div class="form-group">
                <label>Food Name:</label>
                <input type="text" name="food_name" value="<?php echo htmlspecialchars($food['food_name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Food Type:</label>
                <input type="text" name="food_type" value="<?php echo htmlspecialchars($food['food_type']); ?>" required>
            </div>
            <div class="form-group">
                <label>Stock:</label>
                <input type="number" name="stock" value="<?php echo $food['stock']; ?>" required>
            </div>
            <div class="form-group">
                <label>Price:</label>
                <input type="number" name="price" value="<?php echo $food['price']; ?>" required>
            </div>
            <div class="form-group">
                <label>Status:</label>
                <select name="status" required>
                    <option value="Available" <?php echo ($food['status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                    <option value="Unavailable" <?php echo ($food['status'] == 'Unavailable') ? 'selected' : ''; ?>>Unavailable</option>
                </select>
            </div>
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" rows="4" required><?php echo htmlspecialchars($food['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label>Food Image:</label>
                <input type="file" name="food_image">
            </div>
            <div class="form-group">
                <input type="submit" name="update_food" value="Update Food" class="btn">
            </div>
        </form>
        <a href="manage_food.php" class="btn-back">Back to Foods List</a>
    </div>
</body>
</html>
