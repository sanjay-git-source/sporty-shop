<?php
session_start();
include('connection.php');
?>
<?php
include('adminsessionChecker.php');

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission and insert data into the database

    $color=$_POST['color'];


    $stmt = $conn->prepare("INSERT INTO colors 
    (color_name)
    VALUES (?)");

$stmt->bind_param("s", $color);

$response = array('status' => 'error', 'message' => 'Failed to upload data.');

if ($stmt->execute()) {
    $response = array('status' => 'success', 'message' => 'Color Added Successfully!');
} else {
    $response['message'] = "Error uploading data: " . $stmt->error;
}

$stmt->close();

// Output JSON response
echo json_encode($response);
exit();
}
?>