<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    $user_logged_in = true;
    $user_name = $_SESSION['userName'];
include('connection.php'); // Include DB connection
}
?>
  <!-- Header Section Begin -->
    <header class="header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-3 col-lg-2">
                    <div class="header__logo">
                    <a href="./index.php" style="color: #F76100; font-size: 24px; font-family: 'Arial Black', sans-serif;">Sporty Shop</a>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-7">
                    <nav class="header__menu">
                        <ul>
                            <li><a href="view_enquirydatas.php">Enquiry</a></li>
                            <!--<li><a href="view_visitors.php">Visitor</a></li>-->  
                               <li><a href="#">Manage</a>
                                <ul class="dropdown">
                                <li><a href="manage_colors.php">Colors</a></li>
                                <li><a href="manage_category.php">Categories</a></li>
                                <li><a href="manage_fabrics.php">Fabrics</a></li>
                                <li><a href="manage_sizes.php">Sizes</a></li>
                                </ul>
                            </li>
                            <li><a href="manage_gallery.php">Gallery</a></li>
                            <li><a href="manage_products.php">Product</a></li>
                            <li><a href="manage_orders.php">Orders</a></li>
                            <li><a href="sales_report.php">Report</a></li>
                            <li><a href="./Logout.php">Logout</a></li>
                        </ul>
                    </nav>
                </div>
              
            </div>
            <div class="canvas__open">
                <i class="fa fa-bars"></i>
            </div>
        </div>
    </header>
    <!-- Header Section End -->