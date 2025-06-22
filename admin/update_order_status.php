<?php
include('connection.php');

if (isset($_GET['order_id']) && isset($_GET['status'])) {
    $order_id = intval($_GET['order_id']);
    $status = $_GET['status'];

    $query = "UPDATE orders SET order_status='$status' WHERE order_id=$order_id";
    if ($conn->query($query)) {
        echo json_encode(['message' => 'Order status updated successfully.']);
    } else {
        echo json_encode(['message' => 'Failed to update order status.']);
    }
}
?>
