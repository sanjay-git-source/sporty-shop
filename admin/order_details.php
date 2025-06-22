<?php
session_start();
include('connection.php');
include('adminsessionChecker.php');

if (!isset($_GET['order_id'])) {
    die("Order ID is required.");
}

$order_id = $_GET['order_id'];

// Fetch order details along with customer details
$query = "
    SELECT o.order_id, o.total_price, o.order_status, o.payment_status,o.delivery_date, o.created_at,
           c.first_name, c.last_name, c.email, c.phone, c.address, c.city, c.state, c.country, c.zipcode
    FROM orders o
    JOIN customers_details c ON o.user_id = c.user_id
    WHERE o.order_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Order not found.");
}

$order = $result->fetch_assoc();

// Fetch purchased items with product details
$query = "
    SELECT oi.product_name, oi.quantity, oi.price, oi.subtotal,oi.category, 
           p.product_id, COALESCE(i.image_url, '../assets/no-image.jpg') AS image_url
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    LEFT JOIN images i ON p.product_id = i.product_id
    WHERE oi.order_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('./header.php'); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<style>
.container {
    max-width: 1000px;
    margin: auto;
    padding: 20px;
}

.card {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    background: #fff;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
}

h2 {
    color: #F76100;
    font-weight: bold;
    margin-bottom: 20px;
}

h4 {
    color: #F76100;
    font-weight: 600;
}

p {
    font-size: 16px;
    margin: 5px 0;
    color: #555;
}

.table {    
    border-radius: 10px;
    overflow: hidden;
}

.table th {
    background-color: #F76100;
    color: white;
    text-align: center;
    padding: 12px;
    font-weight: bold;
}

.table td {
    padding: 10px;
    text-align: center;
    vertical-align: middle;
}

.img-fluid {
    border-radius: 8px;
    border: 1px solid #ddd;
    background: #f8f8f8;
}

.d-flex {
    gap: 20px;
    flex-wrap: wrap;
    justify-content: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .d-flex {
        flex-direction: column;
        align-items: center;
    }

    .table {
        font-size: 14px;
    }
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
    
<div class="container mt-4">
    <h2 class="text-center">Order Details</h2>
<div class="d-flex">
    <div class="card p-3">
        <h4>Customer Information</h4>
        <p><strong>Name:</strong> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($order['address'] . ', ' . $order['city'] . ', ' . $order['state'] . ', ' . $order['country'] . ', ' . $order['zipcode']) ?></p>
    </div>
    <div class="card p-3">
        <h4>Order Summary</h4>
        <p><strong>Order Status:</strong> <?= htmlspecialchars($order['order_status']) ?></p>
        <p><strong>Payment Status:</strong> <?= htmlspecialchars($order['payment_status']) ?></p>
        <p><strong>Total Price:</strong> <?= htmlspecialchars(number_format($order['total_price'], 2)) ?></p>
        <p><strong>Delivery Date:</strong> <?= htmlspecialchars($order['delivery_date']) ?></p>
        <p><strong>Order Date:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
    </div>
    </div>
    <h4 class="mt-4 mb-3">Purchased Items</h4>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td>
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="Product Image" class="img-fluid" style="max-width: 80px; height: auto;">
                    </td>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= htmlspecialchars($item['category'])?></td>
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                    <td><?= htmlspecialchars(number_format($item['price'], 2)) ?></td>
                    <td><?= htmlspecialchars(number_format($item['subtotal'], 2)) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
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