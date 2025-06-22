<?php
session_start();
include('connection.php'); 

// Fetch categories, sizes, colors, and fabrics
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
$sizes = $conn->query("SELECT * FROM sizes ORDER BY size_name ASC");
$colors = $conn->query("SELECT * FROM colors ORDER BY color_name ASC");
$fabrics = $conn->query("SELECT * FROM fabrics ORDER BY fabric_name ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = trim($_POST['product_name']);
    $category_id = $_POST['category']; // Single category selection
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $sizes_selected = $_POST['sizes'] ?? [];
    $colors_selected = $_POST['colors'] ?? [];
    $fabrics_selected = $_POST['fabrics'] ?? [];

    // Insert into `products` table
    $stmt = $conn->prepare("INSERT INTO products (product_name, category_id, price, stock) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sidi", $product_name, $category_id, $price, $stock);

    if ($stmt->execute()) {
        $product_id = $stmt->insert_id;
        $stmt->close();

        // Insert sizes
        $stmt = $conn->prepare("INSERT INTO product_sizes (product_id, size_id) VALUES (?, ?)");
        foreach ($sizes_selected as $size_id) {
            $stmt->bind_param("ii", $product_id, $size_id);
            $stmt->execute();
        }
        $stmt->close();

        // Insert colors
        $stmt = $conn->prepare("INSERT INTO product_colors (product_id, color_id) VALUES (?, ?)");
        foreach ($colors_selected as $color_id) {
            $stmt->bind_param("ii", $product_id, $color_id);
            $stmt->execute();
        }
        $stmt->close();

        // Insert fabrics
        $stmt = $conn->prepare("INSERT INTO product_fabrics (product_id, fabric_id) VALUES (?, ?)");
        foreach ($fabrics_selected as $fabric_id) {
            $stmt->bind_param("ii", $product_id, $fabric_id);
            $stmt->execute();
        }
        $stmt->close();

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
                $image_url = mysqli_real_escape_string($conn, $target_file);

                if (move_uploaded_file($image_tmp, $target_file)) {
                    $stmt = $conn->prepare("INSERT INTO images (product_id, image_url) VALUES (?, ?)");
                    $stmt->bind_param("is", $product_id, $image_url);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }

        echo "<script>alert('Product added successfully!'); window.location.href='sample.php';</script>";
    } else {
        echo "<script>alert('Error adding product!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h2 {
            text-align: center;
        }

        form {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        button {
            margin-top: 15px;
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<h2>Add New Product</h2>
<form action="" method="post" enctype="multipart/form-data">
    
    <label>Product Name:</label>
    <input type="text" name="product_name" required>

    <label>Category:</label>
    <select name="category" required>
        <option value="">Select Category</option>
        <?php while ($row = $categories->fetch_assoc()): ?>
            <option value="<?= $row['category_id']; ?>"><?= $row['category_name']; ?></option>
        <?php endwhile; ?>
    </select>

    <label>Price:</label>
    <input type="number" name="price" step="0.01" required>

    <label>Stock:</label>
    <input type="number" name="stock" required>

    <label>Sizes:</label>
    <div class="checkbox-group">
        <?php while ($row = $sizes->fetch_assoc()): ?>
            <label><input type="checkbox" name="sizes[]" value="<?= $row['size_id']; ?>"> <?= $row['size_name']; ?></label>
        <?php endwhile; ?>
    </div>

    <label>Colors:</label>
    <div class="checkbox-group">
        <?php while ($row = $colors->fetch_assoc()): ?>
            <label><input type="checkbox" name="colors[]" value="<?= $row['color_id']; ?>"> <?= $row['color_name']; ?></label>
        <?php endwhile; ?>
    </div>

    <label>Fabrics:</label>
    <div class="checkbox-group">
        <?php while ($row = $fabrics->fetch_assoc()): ?>
            <label><input type="checkbox" name="fabrics[]" value="<?= $row['fabric_id']; ?>"> <?= $row['fabric_name']; ?></label>
        <?php endwhile; ?>
    </div>

    <label>Upload Images:</label>
    <input type="file" name="images[]" accept="image/*" multiple required>

    <button type="submit">Add Product</button>
</form>

</body>
</html>
