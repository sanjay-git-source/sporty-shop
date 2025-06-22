<?php
include('connection.php'); // Include database connection

// Query to get product count per category
$sql = "SELECT c.category_name, COUNT(p.product_id) AS product_count
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        WHERE c.category_name IN ('Male', 'Female', 'Kids')
        GROUP BY c.category_name";

$result = mysqli_query($conn, $sql);

// Store counts in an array
$category_counts = ['Male' => 0, 'Female' => 0, 'Kids' => 0];

while ($row = mysqli_fetch_assoc($result)) {
    $category_counts[$row['category_name']] = $row['product_count'];
}

?>
<!DOCTYPE html>
<html lang="zxx">
<head>
   <?php include('header.php'); ?>
    <style>
 .categories__stack {
    display: flex;
    flex-direction: column;
}
.trend__item {
    display: flex;
    align-items: center; /* Align image and text properly */
    gap: 10px; /* Adds spacing between image and text */
}

.trend__item__pic img {
    width: 180px; /* Adjust image width */
    height: 150px; /* Maintain a uniform height */
    object-fit: cover; /* Ensure the image scales properly */
    border-radius: 5px; /* Optional: adds rounded corners */
}

.trend__item__text {
    flex: 1; /* Allow text to take up remaining space */
    overflow: hidden;
}

.trend__item__text h6 {
    font-size: 14px; /* Adjust font size */
    white-space: nowrap; /* Prevents wrapping */
    overflow: hidden; /* Prevents overflow */
    text-overflow: ellipsis; /* Adds '...' for long names */
}

.product__price {
    font-size: 14px;
    color: #F76100; /* Custom price color */
    font-weight: bold;
}

  </style>
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

        <?php include('side_navbar.php');?>
    
    <?php include('navbar.php'); ?>

    <!-- Banner Section Begin -->
<section class="banner set-bg" data-setbg="img/banner/banner-1.jpg">
    <div class="container">
        <div class="row">
            <div class="col-xl-7 col-lg-8 m-auto">
                <div class="banner__slider owl-carousel">
                    <div class="banner__item">
                        <div class="banner__text">
                            <span>Dominate the Field</span>
                            <h1>Premium Sports Apparel</h1>
                            <a href="shop.php" class="btn">Shop Now</a>
                        </div>
                    </div>
                    <div class="banner__item">
                        <div class="banner__text">
                            <span>Performance Meets Style</span>
                            <h1>New Season Activewear</h1>
                            <a href="shop.php" class="btn">Explore Collection</a>
                        </div>
                    </div>
                    <div class="banner__item">
                        <div class="banner__text">
                            <span>Gear Up, Stand Out</span>
                            <h1>Elite Sports Fashion</h1>
                            <a href="shop.php" class="btn">Discover More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->
<!-- Categories Section Begin -->
<section class="categories">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 p-0">
                <div class="categories__item categories__large__item set-bg" data-setbg="img/categories/girl.jpg">
                    <div class="categories__text">
                        <h1>Women’s Fashion</h1>
                        <p style="color: black;">Discover top-quality sportswear and apparel tailored for female athletes.</p>
                        <a href="shop.php">Shop now</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 p-0">
                <div class="categories__stack">
                    <div class="categories__item set-bg" data-setbg="img/categories/men.jpg">
                        <div class="categories__text">
                            <h2>Men’s Fashion</h2>
                            <p style="color: black;"><?php echo $category_counts['Male']; ?> items</p>
                            <a href="shop.php">Shop now</a>
                        </div>
                    </div>
                    <div class="categories__item set-bg" data-setbg="img/categories/kid.jpg">
                        <div class="categories__text">
                            <h4>Kid’s Fashion</h4>
                            <p style="color: black;"><?php echo $category_counts['Kids']; ?> items</p>
                            <a href="shop.php">Shop now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Categories Section End -->
<?php

$category_filter = isset($_GET['category']) ? $_GET['category'] : 'All';

// Fetch products with categories, fabrics, sizes, colors, and images
$sql = "SELECT 
            p.product_id, 
            p.product_name, 
            p.price, 
            p.stock, 
            p.created_at,
            c.category_name,
            (SELECT image_url FROM images WHERE product_id = p.product_id LIMIT 1) AS image_url,
            GROUP_CONCAT(DISTINCT f.fabric_name SEPARATOR ', ') AS fabrics,
            GROUP_CONCAT(DISTINCT s.size_name SEPARATOR ', ') AS sizes,
            GROUP_CONCAT(DISTINCT co.color_name SEPARATOR ', ') AS colors
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN product_fabrics pf ON p.product_id = pf.product_id
        LEFT JOIN fabrics f ON pf.fabric_id = f.fabric_id
        LEFT JOIN product_sizes ps ON p.product_id = ps.product_id
        LEFT JOIN sizes s ON ps.size_id = s.size_id
        LEFT JOIN product_colors pc ON p.product_id = pc.product_id
        LEFT JOIN colors co ON pc.color_id = co.color_id";

// Apply category filter if selected
if ($category_filter !== 'All') {
    $sql .= " WHERE c.category_name = '$category_filter'";
}

$sql .= " GROUP BY p.product_id LIMIT 9"; // Limit to 9 products

$result = mysqli_query($conn, $sql);

?>
<!-- Product Section Begin -->
<section class="product spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4">
                <div class="section-title">
                    <h4>New Arrivals</h4>
                </div>
            </div>
            <div class="col-lg-8 col-md-8">
                <ul class="filter__controls">
                    <li class="filter-btn active" data-filter="All">All</li>
                    <li class="filter-btn" data-filter="Male">Men’s</li>
                    <li class="filter-btn" data-filter="Female">Women’s</li>
                    <li class="filter-btn" data-filter="Kids">Kid’s</li>
                </ul>
            </div>
        </div>
        <div class="row product-list">
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <div class="col-lg-4 col-md-6 product-item" data-category="<?php echo $row['category_name']; ?>">
                    <div class="product__item">
                        <div class="product__item__pic set-bg" data-setbg="admin/<?php echo $row['image_url']; ?>">
                            <?php 
                            $new_label_days = 7;
                            $created_date = strtotime($row['created_at']);
                            $days_diff = (time() - $created_date) / (60 * 60 * 24);
                            
                            if ($row['stock'] == 0) { ?>
                                <div class="label stockout stockblue">Out Of Stock</div>
                            <?php } elseif ($days_diff <= $new_label_days) { ?>
                                <div class="label new">New</div>
                            <?php } else { ?>
                                <div class="label sale">Sale</div>
                            <?php } ?>
                            
                            <ul class="product__hover">
                                <li><a href="admin/<?php echo $row['image_url']; ?>" class="image-popup"><span class="arrow_expand"></span></a></li>
                                <li><a href="#"><span class="icon_heart_alt"></span></a></li>
                                <li><a href="shop-cart.php"><span class="icon_bag_alt"></span></a></li>
                            </ul>
                        </div>
                         <div class="product__item__text">
                            <h6><a href="product-details.php?id=<?php echo $row['product_id']; ?>" style="color: #F76100;"><?php echo $row['product_name']; ?></a></h6><br>
                            <p>Category: <?php echo $row['category_name']; ?></p>
                            <p>Fabrics: <?php echo $row['fabrics'] ?: 'N/A'; ?></p>
                            <p>Sizes: <?php echo $row['sizes'] ?: 'N/A'; ?></p>
                            <p>Colors: <?php echo $row['colors'] ?: 'N/A'; ?></p>
                            <div class="product__price">₹ <?php echo number_format($row['price'], 2); ?></div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
<!-- Product Section End -->
<!-- Discount Section Begin 
<section class="discount">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 p-0">
                <div class="discount__pic">
                    <img src="img/discount.jpg" alt="">
                </div>
            </div>
            <div class="col-lg-6 p-0">
                <div class="discount__text">
                    <div class="discount__text__title">
                        <span>Discount</span>
                        <h2>Summer 2025</h2>
                        <h5><span>Sale</span> 50%</h5>
                    </div>
                    <div class="discount__countdown" id="countdown-time">
                        <div class="countdown__item">
                            <span>22</span>
                            <p>Days</p>
                        </div>
                        <div class="countdown__item">
                            <span>18</span>
                            <p>Hour</p>
                        </div>
                        <div class="countdown__item">
                            <span>46</span>
                            <p>Min</p>
                        </div>
                        <div class="countdown__item">
                            <span>05</span>
                            <p>Sec</p>
                        </div>
                    </div>
                    <a href="shop.php">Shop now</a>
                </div>
            </div>
        </div>
    </div>
</section>-->
<!-- Discount Section End -->
<!-- Services Section Begin --> 
 <div class="text-center">
   <a href="shop.php"><button class="btn btn-sm" style="color:white;background-color:#F76100;border-radius:8px;">View More</button></a>
 </div>
<section class="services spad">
    <div class="container">
        <div class="row">
            <!-- Free Shipping -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-truck"></i>
                    <h6>Free Shipping</h6>
                    <p>On orders over ₹999</p>
                </div>
            </div>
            <!-- Money Back Guarantee -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-undo"></i>
                    <h6>Money Back Guarantee</h6>
                    <p>Hassle-free returns within 30 days</p>
                </div>
            </div>
            <!-- 24/7 Online Support -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-support"></i>
                     <h6>24/7 Online Support</h6>
                    <p>Dedicated assistance anytime</p>
                </div>
            </div>
            <!-- Secure Payment -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-lock"></i>
                    <h6>Secure Payment</h6>
                    <p>100% secure payment processing</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Services Section End -->
<div>
    <?php include('footer.php')?>
</div>
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
<!-- <script>
    async function fetchVisitorDetails() {
        try {
            // Get the visitor's IP and location details
            const response = await fetch('https://ipapi.co/json/'); // Using ipapi.co API for location details
            if (!response.ok) throw new Error("Failed to fetch location data.");
            
            const data = await response.json();

            // Prepare the visitor data to send to the server
            const visitorDetails = {
                ip: data.ip,
                city: data.city,
                region: data.region,
                country: data.country_name
            };

            // Send visitor data to your PHP script
            const result = await fetch('store_visitor.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(visitorDetails)
            });

            const resultData = await result.json();
            console.log(resultData.message);
        } catch (error) {
            console.error("Error:", error);
        }
    }
    // Run the function when the page loads
    document.addEventListener('DOMContentLoaded', fetchVisitorDetails);
</script> -->

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const filterButtons = document.querySelectorAll(".filter-btn");
    const productItems = document.querySelectorAll(".product-item");

    filterButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");

            const category = this.getAttribute("data-filter");

            productItems.forEach(item => {
                if (category === "All" || item.getAttribute("data-category") === category) {
                    item.style.display = "block";
                } else {
                    item.style.display = "none";
                }
            });
        });
    });
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
<?php mysqli_close($conn); // Close connection
?>