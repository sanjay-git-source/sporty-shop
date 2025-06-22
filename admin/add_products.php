<?php
session_start();
include('connection.php');
?>
<?php
include('adminsessionChecker.php');
// Fetch categories, sizes, colors, and fabrics
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
$sizes = $conn->query("SELECT * FROM sizes ORDER BY size_name ASC");
$colors = $conn->query("SELECT * FROM colors ORDER BY color_name ASC");
$fabrics = $conn->query("SELECT * FROM fabrics ORDER BY fabric_name ASC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('./header.php'); ?>
    
    <style>
        .btn-custom {
            background-color: #F76100 ;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #FF7F50 ;
            color: white;
        }
    </style>
</head>
<body>

<!-- Offcanvas Menu Begin -->
<div class="offcanvas-menu-overlay"></div>
    <div class="offcanvas-menu-wrapper">
        <div class="offcanvas__close">+</div>
        <div class="offcanvas__logo">
            <a href="./index.html"><img src="img/logo.png" alt=""></a>
        </div>
        <div id="mobile-menu-wrap"></div>
       
    </div>
    <?php include('./navbar.php'); ?>

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header text-white" style="background-color:#F76100;">
                <h4 class="text-center">Add New Product</h4>
            </div>
            <div class="card-body">
                <form action="upload_products.php" method="post" id="productUpload" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Product Name:</label>
                        <input type="text" name="product_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category:</label><br>
                        <select name="category" class="form-select" style="width: 100%;height:40px" required>
                            <option value="">Select Category</option>
                            <?php while ($row = $categories->fetch_assoc()): ?>
                                <option value="<?= $row['category_id']; ?>"><?= $row['category_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price:</label>
                        <input type="number" name="price" class="form-control" step="0.01" required>
                    </div>
                   
            <div class="mb-3">
            <label class="form-label">Availability:</label>
            <select name="stock" class="form-control" required>
            <option value="1">Available</option>
            <option value="0">Not Available</option>
            </select>
            </div>


                    <div class="mb-3">
                        <label class="form-label">Sizes:</label><br>
                        <?php while ($row = $sizes->fetch_assoc()): ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="sizes[]" value="<?= $row['size_id']; ?>">
                                <label class="form-check-label"><?= $row['size_name']; ?></label>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Colors:</label><br>
                        <?php while ($row = $colors->fetch_assoc()): ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="colors[]" value="<?= $row['color_id']; ?>">
                                <label class="form-check-label"><?= $row['color_name']; ?></label>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fabrics:</label><br>
                        <?php while ($row = $fabrics->fetch_assoc()): ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="fabrics[]" value="<?= $row['fabric_id']; ?>">
                                <label class="form-check-label"><?= $row['fabric_name']; ?></label>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Images:</label>
                        <input type="file" name="images[]" class="form-control" accept="image/*" multiple required>
                    </div>

                    <button type="submit" class="btn btn-custom w-100">Add Product</button>
                </form>
            </div>
        </div>
    </div>
<script>function handleFormSubmit(event) {
    event.preventDefault(); // Prevent the default form submission

    const formData = new FormData(event.target);

    fetch('upload_products.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Convert response to JSON
    .then(data => {
        alert(data.message); // Display message from response

        if (data.status === 'success') {
            document.getElementById('productUpload').reset(); // Reset form after success
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An unexpected error occurred.');
    });
}

// Attach the form submit handler
document.getElementById('productUpload').addEventListener('submit', handleFormSubmit);
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Search Begin -->
<div class="search-model">
    <div class="h-100 d-flex align-items-center justify-content-center">
        <div class="search-close-switch">+</div>
        <form class="search-model-form">
            <input type="text" id="search-input" placeholder="Search here.....">
        </form>
    </div>
</div>
<!-- Search End -->

<!-- Js Plugins -->
<script src="../js/jquery-3.3.1.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery.magnific-popup.min.js"></script>
<script src="../js/jquery-ui.min.js"></script>
<script src="../js/mixitup.min.js"></script>
<script src="../js/jquery.countdown.min.js"></script>
<script src="../js/jquery.slicknav.js"></script>
<script src="../js/owl.carousel.min.js"></script>
<script src="../js/jquery.nicescroll.min.js"></script>
<script src="../js/main.js"></script>
</body>

</html>