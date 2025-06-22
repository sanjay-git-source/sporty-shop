<?php
include('connection.php');

// Get Product ID from URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];
} else {
    die("Invalid product ID.");
}

// Fetch Product Details
$sql = "SELECT p.product_name, p.price, p.stock, c.category_name
        FROM products p 
        JOIN categories c ON p.category_id = c.category_id
        WHERE p.product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    die("Product not found.");
}

// Fetch Product Images
$image_sql = "SELECT image_url FROM images WHERE product_id = ?";
$image_stmt = $conn->prepare($image_sql);
$image_stmt->bind_param("i", $product_id);
$image_stmt->execute();
$image_result = $image_stmt->get_result();
$images = [];
while ($row = $image_result->fetch_assoc()) {
    $images[] = $row['image_url'];
}

// Fetch Sizes
$size_sql = "SELECT s.size_name FROM product_sizes ps 
             JOIN sizes s ON ps.size_id = s.size_id 
             WHERE ps.product_id = ?";
$size_stmt = $conn->prepare($size_sql);
$size_stmt->bind_param("i", $product_id);
$size_stmt->execute();
$size_result = $size_stmt->get_result();
$sizes = [];
while ($row = $size_result->fetch_assoc()) {
    $sizes[] = $row['size_name'];
}

// Fetch Colors
$color_sql = "SELECT c.color_name FROM product_colors pc 
              JOIN colors c ON pc.color_id = c.color_id 
              WHERE pc.product_id = ?";
$color_stmt = $conn->prepare($color_sql);
$color_stmt->bind_param("i", $product_id);
$color_stmt->execute();
$color_result = $color_stmt->get_result();
$colors = [];
while ($row = $color_result->fetch_assoc()) {
    $colors[] = $row['color_name'];
}

// Fetch Fabrics
$fabric_sql = "SELECT f.fabric_name FROM product_fabrics fc 
              JOIN fabrics f ON fc.fabric_id = f.fabric_id 
              WHERE fc.product_id = ?";
$fabric_stmt = $conn->prepare($fabric_sql);
$fabric_stmt->bind_param("i", $product_id);
$fabric_stmt->execute();
$fabric_result = $fabric_stmt->get_result();
$fabrics = [];
while ($row = $fabric_result->fetch_assoc()) {
    $fabrics[] = $row['fabric_name'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
</head>
<body>

    <div id="preloder">
        <div class="loader"></div>
    </div>

    <?php include('side_navbar.php'); ?>
  
    <?php include('navbar.php'); ?>

    <!-- Breadcrumb -->
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="./index.php"><i class="fa fa-home"></i> Home</a>
                        <a href="#"><?php echo htmlspecialchars($product['category_name']); ?></a>
                        <span><?php echo htmlspecialchars($product['product_name']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details Section -->
    <section class="product-details spad">
        <div class="container">
            <div class="row">
                <!-- Product Images -->
                <div class="col-lg-6">
                <div class="product__details__pic">
    <div class="product__details__pic__left product__thumb nice-scroll">
        <?php foreach ($images as $key => $image): ?>
            <a class="pt <?php echo $key === 0 ? 'active' : ''; ?>" href="#product-<?php echo $key; ?>">
                <img src="admin/<?php echo htmlspecialchars($image); ?>" alt="Product Image">
            </a>
        <?php endforeach; ?>
    </div>
    <div class="product__details__slider__content">
        <div class="product__details__pic__slider owl-carousel">
            <?php foreach ($images as $key => $image): ?>
                <img data-hash="product-<?php echo $key; ?>" class="product__big__img" src="admin/<?php echo htmlspecialchars($image); ?>" alt="Product Image">
            <?php endforeach; ?>
        </div>
    </div>
</div>
 </div>
<!-- Product Info -->
<div class="col-lg-6">
    <div class="product__details__text">
        <h3><?php echo htmlspecialchars($product['product_name']); ?> </h3>
        <div class="product__details__price">₹ <?php echo number_format($product['price'], 2); ?></div>

        <form action="add-to-cart.php" method="POST">
            <div class="product__details__button">
                <div class="quantity">
                    <span>Quantity:</span>
                    <div class="pro-qty">
                        <input type="number" name="quantity" value="1" min="1" required>
                    </div>
                </div>
                <button type="submit" class="cart-btn" onclick="checkLogin();" <?php echo ($product['stock'] <= 0) ? 'disabled style="background-color: grey; cursor: not-allowed;"' : ''; ?>>
    <span class="icon_bag_alt"></span> Add to cart
</button>
            </div>

            <div class="product__details__widget">
                <ul>
                    <li>
                        <span>Category</span>
                        <div class="size__btn">
                        <label><?php echo htmlspecialchars($product['category_name']); ?></label>
                      </div>   

                    </li>
                    <li>
                        <span>Fabric Type:</span>
                        <div class="size__btn">
                            <?php foreach ($fabrics as $fabric): ?>
                                <label>
                                    <input type="radio" name="fabric" value="<?php echo htmlspecialchars($fabric); ?>" required>
                                    <?php echo htmlspecialchars($fabric); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <li>
                        <span>Availability:</span>
                        <div class="stock__checkbox">
                            <label>
                                <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                            </label>
                        </div>
                    </li>
                    <li>
                        <span>Available color:</span>
                        <div class="color__checkbox">
                            <?php foreach ($colors as $color): ?>
                                <label>
                                    <input type="radio" name="color" value="<?php echo htmlspecialchars($color); ?>" required <?php echo ($color == $colors[0]) ? 'checked' : ''; ?>>
                                    <span class="checkmark" style="background-color: <?php echo htmlspecialchars($color); ?>;"></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <li>
                        <span>Available size:</span>
                        <div class="size__btn">
                            <?php foreach ($sizes as $size): ?>
                                <label>
                                    <input type="radio" name="size" value="<?php echo htmlspecialchars($size); ?>" required>
                                    <?php echo htmlspecialchars($size); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </li>
                </ul>
            </div>

            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>">
            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
            <input type="hidden" name="category" value="<?php echo $product['category_name']; ?>">

        </form>
    </div>
</div>
        
      <div class="col-lg-12">
    <div class="product__details__tab">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Description</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Specification</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab">Reviews ( 3 )</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tabs-1" role="tabpanel">
                <h6>Description</h6>
                <p>Stay stylish and comfortable with our premium sporty dress collection. Designed for active 
                   individuals, these dresses provide breathability, flexibility, and a modern athletic look. 
                   Whether you’re hitting the gym, going for a run, or just looking for casual sporty fashion, 
                   our collection has the perfect outfit for you.</p>
            </div>
            <div class="tab-pane" id="tabs-2" role="tabpanel">
                <h6>Specification</h6>
                <ul>
                    <li>Material: 85% Polyester, 15% Spandex</li>
                    <li>Moisture-wicking and quick-dry fabric</li>
                    <li>Stretchable and lightweight for maximum comfort</li>
                    <li>Available in various sizes (S, M, L, XL)</li>
                    <li>Perfect for sports, gym, and casual outings</li>
                </ul>
            </div>
            <div class="tab-pane" id="tabs-3" role="tabpanel">
                <h6>Reviews ( 3 )</h6>
                <div class="review">
                    <strong>Alice R.</strong> <span>★★★★★</span>
                    <p>"Absolutely love this dress! The material is super soft, and it fits perfectly. It's great for my morning jogs and casual outings. Highly recommend!"</p>
                </div>
                <div class="review">
                    <strong>Michael S.</strong> <span>★★★★☆</span>
                    <p>"The design is fantastic, and it feels really comfortable. I just wish they had more color options. Other than that, it's perfect for workouts!"</p>
                </div>
                <div class="review">
                    <strong>Jessica L.</strong> <span>★★★★★</span>
                    <p>"This sporty dress exceeded my expectations! It's breathable and looks super stylish. I even got compliments at my yoga class. Will buy again!"</p>
                </div>
            </div>
        </div>
    </div>
</div>

            <?php
// Fetch fabrics of the current product
$fabric_sql = "SELECT fabric_id FROM product_fabrics WHERE product_id = ?";
$fabric_stmt = $conn->prepare($fabric_sql);
$fabric_stmt->bind_param("i", $product_id);
$fabric_stmt->execute();
$fabric_result = $fabric_stmt->get_result();

$fabric_ids = [];
while ($row = $fabric_result->fetch_assoc()) {
    $fabric_ids[] = $row['fabric_id'];
}

// If the product has fabrics, fetch related products
if (!empty($fabric_ids)) {
    $fabric_ids_placeholder = implode(',', array_fill(0, count($fabric_ids), '?'));

    $sql = "SELECT 
                p.product_id, 
                p.product_name, 
                p.price, 
                p.stock, 
                c.category_name,
                (SELECT image_url FROM images WHERE product_id = p.product_id LIMIT 1) AS image_url
            FROM products p
            JOIN categories c ON p.category_id = c.category_id
            JOIN product_fabrics pf ON p.product_id = pf.product_id
            WHERE pf.fabric_id IN ($fabric_ids_placeholder) 
            AND p.product_id != ? 
            GROUP BY p.product_id 
            LIMIT 4";

    $stmt = $conn->prepare($sql);
    $types = str_repeat('i', count($fabric_ids)) . 'i';
    $params = array_merge($fabric_ids, [$product_id]);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $related_result = $stmt->get_result();
}
?>
<!-- Related Products Section 
<div class="col-lg-12 text-center">
    <div class="related__title">
        <h5>RELATED PRODUCTS</h5>
    </div>
</div>

<div class="row">
    <?php while ($row = $related_result->fetch_assoc()) { ?>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="product__item">
                <div class="product__item__pic set-bg">
                <img class="product__item__pic" src="admin/<?php echo htmlspecialchars($row['image_url']); ?>" alt="" class="product-img">';
                        <ul class="product__hover">
                        <li><a href="admin/<?php echo htmlspecialchars($row['image_url']); ?>" class="image-popup"><span class="arrow_expand"></span></a></li>
                        <li><a href="#"><span class="icon_heart_alt"></span></a></li>
                        <li><a href="#"><span class="icon_bag_alt"></span></a></li>
                    </ul>
                </div>
                <div class="product__item__text">
                    <h6><a href="product-details.php?id=<?php echo $row['product_id']; ?>"><?php echo htmlspecialchars($row['product_name']); ?></a></h6>
                    <p>Category: <?php echo htmlspecialchars($row['category_name']); ?></p>
                    <div class="product__price">₹ <?php echo number_format($row['price'], 2); ?></div>
                </div>
            </div>
        </div>
    <?php } ?>
</div> -->
        </div>
    </section>
     <?php include('footer.php'); ?>
  <script>
    function checkLogin() {
        <?php if (!isset($_SESSION['user_id'])): ?> 
            window.location.href = "login.php";
        <?php else: ?>
            window.location.href = "shop-cart.php";
        <?php endif; ?>
    }
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
<?php $conn->close(); ?>