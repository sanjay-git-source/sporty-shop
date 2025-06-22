<?php
session_start();
include('connection.php');

header('Content-Type: application/json');

if (isset($_GET['deleteid'])) {
    $deleteIds = explode(',', $_GET['deleteid']);
    
    // Sanitize input to prevent SQL injection
    $deleteIds = array_map('intval', $deleteIds); // Convert to integers
    $deleteIdsList = implode(',', $deleteIds); // Create a comma-separated list

    // Fetch image paths before deleting records
    $imageQuery = "SELECT image_url FROM images WHERE product_id IN ($deleteIdsList)";
    $imageResult = $conn->query($imageQuery);

    $deletedImages = [];
    if ($imageResult && $imageResult->num_rows > 0) {
        while ($row = $imageResult->fetch_assoc()) {
            $imagePath = './' . $row['image_url']; // Adjust this path according to your project structure
            
            if (file_exists($imagePath)) {
                if (unlink($imagePath)) { // Delete file from server
                    $deletedImages[] = $imagePath;
                }
            }
        }
    }

    // Delete from related tables first
    $conn->query("DELETE FROM product_colors WHERE product_id IN ($deleteIdsList)");
    $conn->query("DELETE FROM product_sizes WHERE product_id IN ($deleteIdsList)");
    $conn->query("DELETE FROM product_fabrics WHERE product_id IN ($deleteIdsList)");
    $conn->query("DELETE FROM images WHERE product_id IN ($deleteIdsList)");

    // Delete products from main table
    $deleteQuery = "DELETE FROM products WHERE product_id IN ($deleteIdsList)";
    $result = $conn->query($deleteQuery);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Records deleted successfully.',
            'deleted_images' => $deletedImages
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to delete records: ' . $conn->error
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No records selected for deletion.'
    ]);
}
?>
