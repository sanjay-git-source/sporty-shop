<?php
session_start();
include('connection.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Error: User not logged in or session expired.'); window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];
$fabric = $_POST['fabric'];
$color = $_POST['color'];
$size = $_POST['size'];
$category = $_POST['category'];

// Check if user exists in customers table
$check_user_query = "SELECT user_id FROM customers WHERE user_id = ?";
$check_stmt = $conn->prepare($check_user_query);
$check_stmt->bind_param('i', $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
if ($check_result->num_rows == 0) {
    echo "<script>alert('Error: User does not exist.'); window.location.href='shop.php';</script>";
    exit();
}

// Check if product exists in cart
$query = "SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND fabric = ? AND color = ? AND size = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('iisss', $user_id, $product_id, $fabric, $color, $size);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update quantity if product exists in cart
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;
    $update_query = "UPDATE cart SET quantity = ?, updated_at = NOW() WHERE cart_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('ii', $new_quantity, $row['cart_id']);
    $update_stmt->execute();
} else {
    // Insert new product into cart
    $insert_query = "INSERT INTO cart (user_id, product_id, quantity, fabric, color, size, category) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param('iiissss', $user_id, $product_id, $quantity, $fabric, $color, $size, $category);
    if ($insert_stmt->execute()) {
        echo "<script>alert('Product added to cart successfully!'); window.location.href='shop-cart.php';</script>";
    } else {
        echo "<script>alert('Error adding product to cart.'); window.location.href='shop.php';</script>";
    }
    exit();
}

// Redirect to cart page
header('Location: shop-cart.php');
exit();
?>
