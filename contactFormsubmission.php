<?php
// Include the database connection file
include 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    // Prepare the SQL statement
    $sql = "INSERT INTO contacts  (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";

    // Execute the query and check for success
    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("status" => "success", "message" => "Message sent successfully."));
    } else {
        echo json_encode(array("status" => "error", "message" => "Error: " . $sql . "<br>" . $conn->error));
    }

    // Close the database connection
    $conn->close();
}
?>
