<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('connection.php');
?>
<?php
include('customer_sessionChecker.php');

$user_id = $_SESSION['user_id']; // Get logged-in user ID

// Fetch cart items for the logged-in user
$sql = "SELECT cart.cart_id, cart.quantity, cart.fabric, cart.color, cart.size, cart.category,
               products.product_id, products.product_name, products.price, images.image_url
        FROM cart
        INNER JOIN products ON cart.product_id = products.product_id
        LEFT JOIN images ON products.product_id = images.product_id
        WHERE cart.user_id = ? 
        GROUP BY cart.cart_id"; // Grouping to avoid duplicate images

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_price = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
</head>
<body>
    <!-- Page Preloader -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <?php include('side_navbar.php'); ?>
    <?php include('navbar.php'); ?>

    <!-- Breadcrumb Begin -->
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="index.php"><i class="fa fa-home"></i> Home</a>
                        <span>Shopping Cart</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Shop Cart Section Begin -->
    <section class="shop-cart spad">
        <div class="container">
            <?php if (!empty($cart_items)) { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="shop__cart__table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item) { ?>
                                <tr>
                                    <td class="cart__product__item">
                                        <img src="admin/<?php echo $item['image_url']; ?>" alt="" width="80">
                                        <div class="cart__product__item__title">
                                            <h6><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                            <br>
                                            <p>Fabric: <?php echo htmlspecialchars($item['fabric']); ?></p>
                                            <p>Color: <?php echo htmlspecialchars($item['color']); ?></p>
                                            <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>
                                            <p>Category: <?php echo htmlspecialchars($item['category']); ?></p>

                                        </div>
                                    </td>
                                    
                                    <td class="cart__price">₹ <?php echo number_format($item['price'], 2); ?></td>
                                    <td class="cart__quantity">
    <form method="POST" action="update_cart_quantity.php" class="d-flex align-items-center">
        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
        <input type="number" name="quantity" class="form-control text-center mx-2" 
               value="<?php echo $item['quantity']; ?>" min="1" style="width: 60px;">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></button>
    </form>
</td>
                                    <td class="cart__total">₹ <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    <td class="cart__close">
                                        <a href="remove_cart.php?cart_id=<?php echo $item['cart_id']; ?>" class="btn btn-danger btn-sm">Remove</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="cart__btn">
                        <a href="shop.php">Continue Shopping</a>
                    </div>
                </div>
                <div class="col-lg-4 offset-lg-2">
                    <div class="cart__total__procced">
                        <h6>Cart total</h6>
                        <ul>
                            <li>Total <span>₹ <?php echo number_format($total_price, 2); ?></span></li>
                        </ul>
                        <a href="checkout.php" class="primary-btn" style="color: white;">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
            <?php } else { ?>
                <h3 class="text-center mb-3">Your cart is empty.</h3>
                <div class="text-center">
                    <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            <?php } ?>
        </div>
    </section>
    <!-- Shop Cart Section End -->

    <?php include('footer.php'); ?>

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
