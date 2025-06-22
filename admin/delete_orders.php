<?php
include('connection.php');

if (isset($_GET['deleteid'])) {
    $ids = explode(',', $_GET['deleteid']);
    $ids = array_map('intval', $ids); 

    $query = "DELETE FROM orders WHERE order_id IN (" . implode(',', $ids) . ")";
    if ($conn->query($query)) {
        echo json_encode(['status' => 'success', 'message' => 'Orders deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete orders.']);
    }
}
?>
