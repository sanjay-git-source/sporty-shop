<?php
session_start();
include('connection.php');
include('customer_sessionChecker.php');

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

// Fetch order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();
if ($order_result->num_rows == 0) {
    header("Location: index.php");
    exit();
}
$order = $order_result->fetch_assoc();

// Fetch ordered items
$order_items = [];
$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $order_items[] = $row;
}

// Fetch customer details
$stmt = $conn->prepare("SELECT * FROM customers_details WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

// Admin Email Notification
$admin_email = "22ifte096@ldc.edu.in"; // Change this to admin email
$customer_name = $customer['first_name'] . " " . $customer['last_name'];
$total_price = number_format($order['total_price'], 2);
$payment_method = "Cash on Delivery"; // Change based on actual method

// Order Items List in Email
$order_items_list = "";
foreach ($order_items as $item) {
    $order_items_list .= "🔹 {$item['product_name']} - Qty: {$item['quantity']} - ₹" . number_format($item['subtotal'], 2) . "\n";
}

// Email Message
$email_subject = "New Order Placed - Order # $order_id";
$email_message = "
Hello Admin,

A new order has been placed on the Sports Shop.

🔹 Order ID: $order_id  
🔹 Customer Name: $customer_name  
🔹 Total Amount: ₹ $total_price  
🔹 Payment Method: $payment_method  

Ordered Items:
$order_items_list

Please check the admin panel for more details.

Best Regards,  
Sporty Shop Team
";

// Send Email Using PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'sanjaymech2310@gmail.com'; // Change to your email
    $mail->Password = ''; // Use App Password if using Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom('sanjaymech2310@gmail.com', 'Sports Shop');
    $mail->addAddress($admin_email);

    $mail->isHTML(false);
    $mail->Subject = $email_subject;
    $mail->Body = $email_message;

    $mail->send();
} catch (Exception $e) {
    error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
    <style>
        /* Table Styling */
.table-responsive {
    overflow-x: auto;
}

table.table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 10px;
    overflow: hidden;
}

.table th, .table td {
    padding: 12px;
    text-align: center;
    border: 1px solid #dee2e6;
}

.table thead {
    background-color: #F76100;
    color: white;
}

.table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

.table tbody tr:hover {
    background-color: #ffe5d0;
    transition: 0.3s ease-in-out;
}

@media (max-width: 768px) {
    .table thead {
        display: none;
    }

    .table tbody tr {
        display: block;
        margin-bottom: 10px;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 10px;
        background: #fff;
    }

    .table tbody tr td {
        display: flex;
        justify-content: space-between;
        padding: 8px;
        border-bottom: 1px solid #dee2e6;
    }

    .table tbody tr td:last-child {
        border-bottom: none;
    }

    .table tbody tr td::before {
        content: attr(data-label);
        font-weight: bold;
        color: #F76100;
    }
}

    </style>
</head>
<body>
<!-- Page Preloder -->
<div id="preloder">
        <div class="loader"></div>
    </div>

<?php include('navbar.php'); ?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="./index.php"><i class="fa fa-home"></i> Home</a>
                    <span>Order Success</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="checkout spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h3 class="mb-2">🎉 Thank You for Your Order 🎉</h3>
                <p>Your order <strong># <?= $order_id ?></strong> has been successfully placed.</p>
                <p>Total Amount: <strong>₹ <?= $total_price ?></strong></p>
                <p>Payment Method: <strong> <?= $payment_method ?></strong></p>
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h5 class="mb-3" style="color: #F76100;">Order Summary</h5>
                <div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Fabric</th>
                <th>Size</th>
                <th>Color</th>
                <th>Category</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order_items as $item): ?>
            <tr>
                <td data-label="Product"><?= htmlspecialchars($item['product_name']) ?></td>
                <td data-label="Quantity"><?= $item['quantity'] ?></td>
                <td data-label="Fabric"><?= $item['fabric'] ?></td>
                <td data-label="Size"><?= $item['size'] ?></td>
                <td data-label="Color"><?= $item['color'] ?></td>
                <td data-label="Category"><?= $item['category']?></td>
                <td data-label="Subtotal">₹<?= number_format($item['subtotal'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

                <hr>
                <div class="text-center">
                    <a href="index.php" style="color:white" class="site-btn">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>

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