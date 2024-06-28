<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_food'])) {
    $edit_id = $_POST['edit_id'];
    $food_name = $_POST['food_name'];
    $food_type = $_POST['food_type'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $description = $_POST['description'];

    // File upload handling (if applicable)
    if ($_FILES['food_image']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['food_image']['name'];
        $file_tmp = $_FILES['food_image']['tmp_name'];
        move_uploaded_file($file_tmp, "uploads/" . $file_name);
        $food_image = "uploads/" . $file_name;
    } else {
        // If no new image is uploaded, retain the existing one or handle accordingly
        // Example assumes storing file path in database
        // $food_image = $_POST['current_food_image'];
        $food_image = ""; // Replace with your logic to handle existing image
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

    $sql = "UPDATE foods SET food_name=?, food_type=?, stock=?, price=?, status=?, description=?, food_image=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiissi", $food_name, $food_type, $stock, $price, $status, $description, $food_image, $edit_id);

    if ($stmt->execute()) {
        // Redirect to a success page or back to food list
        header("Location: manage_food.php");
        exit;
    } else {
        echo "Error updating food: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: edit_food.php");
    exit;
}
?>
