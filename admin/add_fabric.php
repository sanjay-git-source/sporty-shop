<?php
session_start();
include('connection.php');
?>
<?php
include('adminsessionChecker.php');

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission and insert data into the database

    $fabric_name=$_POST['fabric_name'];


    $stmt = $conn->prepare("INSERT INTO fabrics 
    (fabric_name)
    VALUES (?)");

$stmt->bind_param("s", $fabric_name);

$response = array('status' => 'error', 'message' => 'Failed to upload data.');

if ($stmt->execute()) {
    $response = array('status' => 'success', 'message' => 'Fabric Added Successfully!');
} else {
    $response['message'] = "Error uploading data: " . $stmt->error;
}

$stmt->close();

// Output JSON response
echo json_encode($response);
exit();
}
?>