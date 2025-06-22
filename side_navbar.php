<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}include('connection.php');

$cart_count = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['userName'];

    // Get cart count
    $cart_query = "SELECT COUNT(*) AS count FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($cart_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();
    
    if ($cart_row = $cart_result->fetch_assoc()) {
        $cart_count = $cart_row['count'];
    }
    $stmt->close();
}
?>

<!-- Offcanvas Menu Begin -->
<div class="offcanvas-menu-overlay"></div>
<div class="offcanvas-menu-wrapper">
    <div class="offcanvas__close">+</div>
   
    <div class="offcanvas__logo">
    <a href="./index.php" style="color: #F76100; font-size: 24px; font-family: 'Arial Black', sans-serif;">Sporty Shop</a>
    </div>
    <div id="mobile-menu-wrap"></div>

    <div class="offcanvas__auth">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span>Hi, <?php echo htmlspecialchars($user_name); ?></span><br>
            <a href="logout.php">Logout</a>
            <a href="shop-cart.php">Cart</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
            <a href="shop-cart.php">Cart</a>
        <?php endif; ?>
    </div>
</div>
<!-- Offcanvas Menu End -->
