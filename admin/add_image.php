<?php
session_start();
include('connection.php');

header('Content-Type: application/json'); // Ensure the response is JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $targetDir = "gallery/";
    $fileName = uniqid() . "_" . basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Validate file type
    $allowedTypes = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(["status" => "error", "message" => "Invalid file type"]);
        exit();
    }

    // Move file and insert into database
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        $stmt = $conn->prepare("INSERT INTO gallery (image_name, image_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $fileName, $targetFilePath);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Image uploaded successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database error: Unable to save image"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "File upload failed"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
