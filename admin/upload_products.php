<?php
session_start();
include('connection.php');
include('adminsessionChecker.php');

header("Content-Type: application/json");

$response = ["status" => "error", "message" => "An error occurred."];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = trim($_POST['product_name']);
    $category_id = $_POST['category'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $sizes_selected = $_POST['sizes'] ?? [];
    $colors_selected = $_POST['colors'] ?? [];
    $fabrics_selected = $_POST['fabrics'] ?? [];

    // Insert into products table
    $stmt = $conn->prepare("INSERT INTO products (product_name, category_id, price, stock) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sidi", $product_name, $category_id, $price, $stock);

    if ($stmt->execute()) {
        $product_id = $stmt->insert_id;
        $stmt->close();

        // Insert sizes
        if (!empty($sizes_selected)) {
            $stmt = $conn->prepare("INSERT INTO product_sizes (product_id, size_id) VALUES (?, ?)");
            foreach ($sizes_selected as $size_id) {
                $stmt->bind_param("ii", $product_id, $size_id);
                $stmt->execute();
            }
            $stmt->close();
        }

        // Insert colors
        if (!empty($colors_selected)) {
            $stmt = $conn->prepare("INSERT INTO product_colors (product_id, color_id) VALUES (?, ?)");
            foreach ($colors_selected as $color_id) {
                $stmt->bind_param("ii", $product_id, $color_id);
                $stmt->execute();
            }
            $stmt->close();
        }

        // Insert fabrics
        if (!empty($fabrics_selected)) {
            $stmt = $conn->prepare("INSERT INTO product_fabrics (product_id, fabric_id) VALUES (?, ?)");
            foreach ($fabrics_selected as $fabric_id) {
                $stmt->bind_param("ii", $product_id, $fabric_id);
                $stmt->execute();
            }
            $stmt->close();
        }

        // Handle multiple image uploads
        if (!empty($_FILES['images']['name'][0])) {
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            foreach ($_FILES['images']['name'] as $key => $image_name) {
                $image_tmp = $_FILES['images']['tmp_name'][$key];

                // Generate unique image name
                $unique_name = time() . "_" . uniqid() . "_" . basename($image_name);
                $target_file = $target_dir . $unique_name;
                $image_url = $target_file;

                if (move_uploaded_file($image_tmp, $target_file)) {
                    $stmt = $conn->prepare("INSERT INTO images (product_id, image_url) VALUES (?, ?)");
                    $stmt->bind_param("is", $product_id, $image_url);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }

        $response = ["status" => "success", "message" => "Product added successfully!"];
    } else {
        $response = ["status" => "error", "message" => "Error adding product."];
    }
}

echo json_encode($response);
?>
