<?php
session_start();
include('connection.php');
?>
<?php
include('adminsessionChecker.php');

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission and insert data into the database

    $size=$_POST['size'];


    $stmt = $conn->prepare("INSERT INTO sizes 
    (size_name)
    VALUES (?)");

$stmt->bind_param("s", $size);

$response = array('status' => 'error', 'message' => 'Failed to upload data.');

if ($stmt->execute()) {
    $response = array('status' => 'success', 'message' => 'Size Added Successfully!');
} else {
    $response['message'] = "Error uploading data: " . $stmt->error;
}

$stmt->close();

// Output JSON response
echo json_encode($response);
exit();
}
?>