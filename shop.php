<?php
include('connection.php');
?>
<!DOCTYPE html>
<html lang="zxx">
<head>
   <?php include('header.php'); ?>
   <style>
       .filter-checkbox {
           margin-right: 5px;
       }
   </style>
</head>
<body>
    <div id="preloder">
        <div class="loader"></div>
    </div>
    
    <?php include('side_navbar.php'); ?>
    <?php include('navbar.php'); ?>

    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="./index.php"><i class="fa fa-home"></i> Home</a>
                        <span>Shop</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <section class="shop spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-3">
                    <div class="shop__sidebar">
                        <div class="sidebar__categories">
                            <div class="section-title">
                                <h4>Purchase by Category</h4>
                            </div>
                            <div class="categories__accordion">
                                <div class="accordion" id="accordionExample">
                                    <?php                    
                                    $categoryQuery = "SELECT * FROM categories";
                                    $categoryResult = $conn->query($categoryQuery);
                                    if ($categoryResult->num_rows > 0) {
                                        while ($category = $categoryResult->fetch_assoc()) {
                                            echo '<label><input type="checkbox" class="filter-checkbox category" value="'.$category['category_id'].'"> '.htmlspecialchars($category['category_name']).'</label><br>';
                                        }
                                    } else {
                                        echo "";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="sidebar__categories">
                            <div class="section-title">
                                <h4>purchase by Fabric</h4>
                            </div>
                            <div class="categories__accordion">
                            <div class="accordion" id="accordionExample">          
                              <?php
                                $fabricQuery = "SELECT * FROM fabrics";
                                $fabricResult = $conn->query($fabricQuery);
                                while ($fabric = $fabricResult->fetch_assoc()) {
                                    echo '<label><input type="checkbox" class="filter-checkbox fabric" value="'.$fabric['fabric_id'].'"> '.htmlspecialchars($fabric['fabric_name']).'</label><br>';
                                }
                                ?>
                            </div>
                        </div>
                        </div>
                        <div class="sidebar__categories">
                            <div class="section-title">
                                <h4>purchase by size</h4>
                            </div>
                            <div class="categories__accordion">
                            <div class="accordion" id="accordionExample">     
                             <?php
                                $sizeQuery = "SELECT * FROM sizes";
                                $sizeResult = $conn->query($sizeQuery);
                                while ($size = $sizeResult->fetch_assoc()) {
                                    echo '<label><input type="checkbox" class="filter-checkbox size" value="'.$size['size_id'].'"> '.htmlspecialchars($size['size_name']).'</label><br>';
                                }
                                ?>
                            </div>
                        </div>
                        </div>
                        <div class="sidebar__categories">
                            <div class="section-title">
                                <h4>Purchase by Color</h4>
                            </div>
                            <div class="categories__accordion">
                            <div class="accordion" id="accordionExample"> 
                                <?php
                                $colorQuery = "SELECT * FROM colors";
                                $colorResult = $conn->query($colorQuery);
                                while ($color = $colorResult->fetch_assoc()) {
                                    echo '<label><input type="checkbox" class="filter-checkbox color" value="'.$color['color_id'].'"> '.htmlspecialchars($color['color_name']).'</label><br>';
                                }
                                ?>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9">
                    <div class="row" id="filteredProducts">
                        <!-- Filtered products will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <?php include('footer.php')?>
    
    <script src="js/jquery-3.3.1.min.js"></script>
    
    <script>
  var currentPage = 1; // Track current page

function changePage(newPage) {
    currentPage = newPage;
    filterProducts();
}

function filterProducts() {
    var categories = [];
    var fabrics = [];
    var sizes = [];
    var colors = [];

    $('.filter-checkbox.category:checked').each(function() {
        categories.push($(this).val());
    });
    $('.filter-checkbox.fabric:checked').each(function() {
        fabrics.push($(this).val());
    });
    $('.filter-checkbox.size:checked').each(function() {
        sizes.push($(this).val());
    });
    $('.filter-checkbox.color:checked').each(function() {
        colors.push($(this).val());
    });

    $.ajax({
        url: 'fetch_filtered_products.php',
        method: 'POST',
        data: {
            categories: categories,
            fabrics: fabrics,
            sizes: sizes,
            colors: colors,
            page: currentPage // Include current page in POST data
        },
        success: function(response) {
            $('#filteredProducts').html(response);
        }
    });
}

// Reset to page 1 when any filter changes
$('.filter-checkbox').on('change', function() {
    currentPage = 1;
    filterProducts();
});

// Initial load
filterProducts();
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