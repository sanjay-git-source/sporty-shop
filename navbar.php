<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('connection.php'); // Include DB connection

$cart_count = 0;
$user_logged_in = false;
$user_name = "";

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_logged_in = true;
    $user_name = $_SESSION['userName']; // Assuming you store the name in session

    // Get cart count for the logged-in user
    $user_id = $_SESSION['user_id'];
    $cart_query = "SELECT COUNT(*) AS cart_count FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($cart_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();
    if ($cart_result->num_rows > 0) {
        $row = $cart_result->fetch_assoc();
        $cart_count = $row['cart_count'];
    }
}
?>

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
                        <li><a href="./index.php">Home</a></li>
                        <li><a href="./about.php">About</a></li>
                        <li><a href="./gallery.php">Gallery</a></li>
                        <li><a href="./shop.php">Purchase</a></li>
                        <li><a href="./contact.php">Contact</a></li>
                    </ul>
                </nav>
            </div>
            <div class="col-lg-3">
                <div class="header__right">
                    <div class="header__right__auth">
                        <?php if ($user_logged_in): ?>
                            <span>Hi, <?php echo htmlspecialchars($user_name); ?></span>&emsp;
                            <a href="logout.php">Logout</a>
                        <?php else: ?>
                            <a href="login.php">Login</a>
                            <a href="register.php">Register</a>
                        <?php endif; ?>
                    </div>
                    <ul class="header__right__widget">
                        <!-- <li><a href="#"><span class="icon_heart_alt"></span>
                            <div class="tip">2</div>
                        </a></li> -->
                        <li><a href="shop-cart.php"><span class="icon_bag_alt"></span>
                            <div class="tip"><?php echo $cart_count; ?></div>
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="canvas__open">
            <i class="fa fa-bars"></i>
        </div>
    </div>
</header>
