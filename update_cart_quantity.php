<?php
session_start();
include('connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cart_id = $_POST['cart_id'];
    $quantity = max(1, intval($_POST['quantity']));

    $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $cart_id);
    $stmt->execute();

    header("Location: shop-cart.php");
    exit();
}
?>
