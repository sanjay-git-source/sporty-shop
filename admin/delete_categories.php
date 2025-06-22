<?php
session_start();
include('connection.php');
?>
<?php
include('adminsessionChecker.php');

header('Content-Type: application/json');

if (isset($_GET['deleteid'])) {
    $deleteIds = explode(',', $_GET['deleteid']);
    
    // Sanitize input to prevent SQL injection
    $deleteIds = array_map('intval', $deleteIds); // Convert to integers
    $deleteIdsList = implode(',', $deleteIds); // Create a comma-separated list

    // Add your deletion logic here (e.g., deleting from the database)
    $sql = "DELETE FROM categories WHERE category_id IN ($deleteIdsList)";
    $result = $conn->query($sql);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Records deleted successfully.'
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
