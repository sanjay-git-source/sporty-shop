<?php
include('connection.php');
?>
<!DOCTYPE html>
<html lang="zxx">
<head>
   <?php include('header.php'); ?>
   <style>
        .filter-section {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 8px;
        }
        .filter-section label {
            display: block;
            margin: 5px 0;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="preloder">
        <div class="loader"></div>
    </div>
    <?php include('side_navbar.php'); ?>
    <?php include('navbar.php'); ?>

    <!-- Shop Section Begin -->
    <section class="shop spad">
        <div class="container">
            <div class="row">
                <!-- Sidebar Filters -->
                <div class="col-lg-3 col-md-3">
                    <div class="shop__sidebar filter-section">
                        <h4>Filter by</h4>

                        <!-- Category Filter -->
                        <h5>Category</h5>
                        <?php
                        $categoryQuery = "SELECT * FROM categories";
                        $categoryResult = $conn->query($categoryQuery);
                        while ($category = $categoryResult->fetch_assoc()) {
                            echo '<label><input type="checkbox" class="filter-checkbox category" value="'.$category['category_id'].'"> '.$category['category_name'].'</label>';
                        }
                        ?>

                        <!-- Fabric Filter -->
                        <h5>Fabric</h5>
                        <?php
                        $fabricQuery = "SELECT * FROM fabrics";
                        $fabricResult = $conn->query($fabricQuery);
                        while ($fabric = $fabricResult->fetch_assoc()) {
                            echo '<label><input type="checkbox" class="filter-checkbox fabric" value="'.$fabric['fabric_id'].'"> '.$fabric['fabric_name'].'</label>';
                        }
                        ?>

                        <!-- Size Filter -->
                        <h5>Size</h5>
                        <?php
                        $sizeQuery = "SELECT * FROM sizes";
                        $sizeResult = $conn->query($sizeQuery);
                        while ($size = $sizeResult->fetch_assoc()) {
                            echo '<label><input type="checkbox" class="filter-checkbox size" value="'.$size['size_id'].'"> '.$size['size_name'].'</label>';
                        }
                        ?>

                        <!-- Color Filter -->
                        <h5>Color</h5>
                        <?php
                        $colorQuery = "SELECT * FROM colors";
                        $colorResult = $conn->query($colorQuery);
                        while ($color = $colorResult->fetch_assoc()) {
                            echo '<label><input type="checkbox" class="filter-checkbox color" value="'.$color['color_id'].'"> '.$color['color_name'].'</label>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Product Listing -->
                <div class="col-lg-9 col-md-9">
                    <div class="row" id="product-container">
                        <!-- Filtered products will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include('footer.php'); ?>

    <!-- AJAX Script for Filters -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script>
        $(document).ready(function () {
            function fetchFilteredProducts() {
                var categories = [];
                var fabrics = [];
                var sizes = [];
                var colors = [];

                $(".category:checked").each(function () {
                    categories.push($(this).val());
                });

                $(".fabric:checked").each(function () {
                    fabrics.push($(this).val());
                });

                $(".size:checked").each(function () {
                    sizes.push($(this).val());
                });

                $(".color:checked").each(function () {
                    colors.push($(this).val());
                });

                $.ajax({
                    url: "fetch_products.php",
                    method: "POST",
                    data: {
                        categories: categories,
                        fabrics: fabrics,
                        sizes: sizes,
                        colors: colors
                    },
                    success: function (data) {
                        $("#product-container").html(data);
                    }
                });
            }

            $(".filter-checkbox").change(fetchFilteredProducts);
            fetchFilteredProducts();
        });
    </script>
<!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/mixitup.min.js"></script>
    <script src="js/jquery.countdown.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.nicescroll.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>