<?php
session_start();
include('connection.php');

if (isset($_GET['cart_id'])) {
    $cart_id = $_GET['cart_id'];
    $sql = "DELETE FROM cart WHERE cart_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
}

header("Location: shop-cart.php");
exit();
?>
