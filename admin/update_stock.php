<?php
session_start();
include('connection.php');

if (isset($_POST['update_stock'])) {
    $product_id = $_POST['product_id'];

    // Get current stock status
    $query = "SELECT stock FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // Toggle stock status (1 -> 0 or 0 -> 1)
        $new_stock = ($row['stock'] == 1) ? 0 : 1;

        // Update query
        $update_query = "UPDATE products SET stock = ? WHERE product_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('ii', $new_stock, $product_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['message'] = "Stock status updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating stock status.";
        }
    }
}

// Redirect back to the product page
header('Location:manage_products.php');
exit;
?>
