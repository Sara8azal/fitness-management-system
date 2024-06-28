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

// Fetch existing food records
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search_keyword = $_POST['search'];
    $sql = "SELECT * FROM foods 
            WHERE food_name LIKE '%$search_keyword%' OR food_type LIKE '%$search_keyword%'";
} else {
    $sql = "SELECT * FROM foods";
}

$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$food_records = [];

if ($result->num_rows > 0) {
    $food_records = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $message = "No food records found";
}

// Handle deletion of food records
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $food_id = $_POST['delete_id'];
    $sql = "DELETE FROM foods WHERE id = $food_id";

    if ($conn->query($sql) === TRUE) {
        $message = "Food record deleted successfully";
        header("Location: manage_food.php");
        exit;
    } else {
        $message = "Error deleting food record: " . $conn->error;
    }
}

// Handle addition or editing of food record
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_food']) || isset($_POST['edit_food'])) {
        $food_name = $_POST['food_name'];
        $food_type = $_POST['food_type'];
        $stock = $_POST['stock'];
        $price = $_POST['price'];
        $status = $_POST['status'];
        $description = $_POST['description'];

        // Handle file upload
        $target_dir = "uploads/";
        $food_image = $target_dir . basename($_FILES["food_image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($food_image, PATHINFO_EXTENSION));

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["food_image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $message = "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["food_image"]["size"] > 500000) {
            $message = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $message = "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["food_image"]["tmp_name"], $food_image)) {
                // File is uploaded successfully
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        }

        if ($uploadOk == 1) {
            if (isset($_POST['add_food'])) {
                $sql = "INSERT INTO foods (food_name, food_type, stock, price, status, description, food_image) 
                        VALUES ('$food_name', '$food_type', $stock, $price, '$status', '$description', '$food_image')";

                if ($conn->query($sql) === TRUE) {
                    $message = "New food record added successfully";
                    header("Location: manage_food.php");
                    exit;
                } else {
                    $message = "Error adding food record: " . $conn->error;
                }
            } elseif (isset($_POST['edit_food'])) {
                $food_id = $_POST['edit_id'];

                $sql = "UPDATE foods SET 
                        food_name = '$food_name', 
                        food_type = '$food_type', 
                        stock = $stock, 
                        price = $price, 
                        status = '$status', 
                        description = '$description', 
                        food_image = '$food_image' 
                        WHERE id = $food_id";

                if ($conn->query($sql) === TRUE) {
                    $message = "Food record updated successfully";
                    header("Location: manage_food.php");
                    exit;
                } else {
                    $message = "Error updating food record: " . $conn->error;
                }
            }
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
    <title>Manage Food</title>
    <style>
        /* General styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('admin1 bg.jpg');
            /* Replace with your image path */
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
            /* Adjusted width */
            max-width: 800px;
            /* Maximum width */
            background-color: rgba(255, 255, 255, 0.9);
            /* Transparent white background */
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
        .form-group input[type="number"],
        .form-group input[type="file"],
        .form-group select,
        .form-group textarea {
            width: calc(70% - 20px);
            /* Adjusted width */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
            margin-right: 20px;
        }

        .form-group select {
            width: calc(70% - 20px);
            /* Adjusted width */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
            margin-right: 20px;
        }

        button {
            display: inline-block;
            padding: 15px;
            /* Adjusted padding */
            text-decoration: none;
            border-radius: 25px;
            margin-right: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #4bc3cc;
            /* Button background color */
            border: 2px solid #54e6e8;
            cursor: pointer;
            font-size: 18px;
            /* Adjusted font size */
            color: #fff;
            /* Button text color */
        }

        button:hover {
            background-color: #02676c;
            /* Adjust hover color */
            transform: scale(1.1);
            box-shadow: 0 0 5px #54e6e8, 0 0 10px #54e6e8, 0 0 15px #54e6e8, 0 0 20px #54e6e8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
            background-color: #4bc3cc;
            /* Button background color */
            color: #fff;
            /* Button text color */
        }

        .btn-danger,
        .btn-edit {
            background-color: #ff5757;
            /* Red color for delete button */
            border: 2px solid #ff5757;
            /* Matching border color */
            padding: 15px;
            /* Adjusted padding */
            cursor: pointer;
            font-size: 18px;
            /* Adjusted font size */
            color: #fff;
            /* Button text color */
            border-radius: 25px;
            /* Rounded border */
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-edit {
            background-color: #ffc107;
            /* Yellow color for edit button */
            border: 2px solid #ffc107;
            /* Matching border color */
        }

        .btn-danger:hover {
            background-color: #cc0000;
            /* Adjust hover color */
            transform: scale(1.1);
            box-shadow: 0 0 5px #ff0000, 0 0 10px #ff0000, 0 0 15px #ff0000, 0 0 20px #ff0000;
        }

        .btn-edit:hover {
            background-color: #ffae00;
            /* Adjust hover color */
            transform: scale(1.1);
            box-shadow: 0 0 5px #ffae00, 0 0 10px #ffae00, 0 0 15px #ffae00, 0 0 20px #ffae00;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 style="color: #4bc3cc;">Manage Food</h1>
        <div class="message"><?php echo $message; ?></div>

        <!-- Search Form -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" name="search" placeholder="Search food by name or type"
                    value="<?php echo htmlspecialchars($search_keyword); ?>">
                <button type="submit">Search</button>
            </div>
        </form>

        <!-- Add/Edit Form -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" name="food_name" placeholder="Food Name" required>
                <input type="text" name="food_type" placeholder="Food Type" required>
            </div>
            <div class="form-group">
                <input type="number" name="stock" placeholder="Stock" required>
                <input type="number" name="price" placeholder="Price" required>
            </div>
            <div class="form-group">
                <select name="status" required>
                    <option value="" disabled selected>Status</option>
                    <option value="Available">Available</option>
                    <option value="Unavailable">Unavailable</option>
                </select>
            </div>
            <div class="form-group">
                <textarea name="description" placeholder="Description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <input type="file" name="food_image" required>
            </div>
            <button type="submit" name="add_food">Add Food</button>
        </form>

        <!-- Display Food Records -->
        <table>
            <thead>
                <tr>
                    <th>Food Name</th>
                    <th>Food Type</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($food_records as $food) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($food['food_name']); ?></td>
                    <td><?php echo htmlspecialchars($food['food_type']); ?></td>
                    <td><?php echo htmlspecialchars($food['stock']); ?></td>
                    <td><?php echo htmlspecialchars($food['price']); ?></td>
                    <td><?php echo htmlspecialchars($food['status']); ?></td>
                    <td><?php echo htmlspecialchars($food['description']); ?></td>
                    <td><img src="<?php echo htmlspecialchars($food['food_image']); ?>" alt="Food Image"
                            style="width: 50px; height: 50px;"></td>
                    <td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                            style="display:inline-block;">
                            <input type="hidden" name="delete_id" value="<?php echo $food['id']; ?>">
                            <button type="submit" class="btn-danger">Delete</button>
                        </form>
                        <form method="post" action="edit_food.php" style="display:inline-block;">
                            <input type="hidden" name="edit_id" value="<?php echo $food['id']; ?>">
                            <button type="submit" class="btn-edit">Edit</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Back to Dashboard Button -->
        <div class="form-group" style="margin-top: 20px;">
            <a href="dashboard.php"><button type="button">Back to Dashboard</button></a>
        </div>
    </div>
</body>

</html>