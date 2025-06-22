<?php
session_start();
include('connection.php');
include('customer_sessionChecker.php');

$user_id = $_SESSION['user_id'];
$errors = [];

// Fetch customer details if exists
$customer_details = [];
$stmt = $conn->prepare("SELECT * FROM customers_details WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0) {
    $customer_details = $result->fetch_assoc();
}

// Fetch cart items
$cart_items = [];
$total_price = 0;
$stmt = $conn->prepare("SELECT c.*, p.product_name, p.price 
                       FROM cart c
                       JOIN products p ON c.product_id = p.product_id
                       WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

// Process Checkout
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $first_name = filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING);
    $last_name = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
    $address = filter_var(trim($_POST['address']), FILTER_SANITIZE_STRING);
    $city = filter_var(trim($_POST['city']), FILTER_SANITIZE_STRING);
    $state = filter_var(trim($_POST['state']), FILTER_SANITIZE_STRING);
    $zip = filter_var(trim($_POST['zip']), FILTER_SANITIZE_STRING);
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || 
        empty($phone) || empty($address) || empty($city) || empty($state) || empty($zip)) {
        $errors[] = "All fields are required!";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }

    if (empty($payment_method)) {
        $errors[] = "Please select a payment method!";
    }

    if (empty($cart_items)) {
        $errors[] = "Your cart is empty!";
    }

    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            // Insert/Update customer details
            if(empty($customer_details)) {
                $stmt = $conn->prepare("INSERT INTO customers_details 
                                      (user_id, first_name, last_name, email, phone, address, city, state, country, zipcode)
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Country', ?)");
                $stmt->bind_param("issssssss", $user_id, $first_name, $last_name, $email, $phone, 
                                $address, $city, $state, $zip);
            } else {
                $stmt = $conn->prepare("UPDATE customers_details SET
                                      first_name = ?, last_name = ?, email = ?, phone = ?,
                                      address = ?, city = ?, state = ?, zipcode = ?
                                      WHERE user_id = ?");
                $stmt->bind_param("ssssssssi", $first_name, $last_name, $email, $phone,
                                 $address, $city, $state, $zip, $user_id);
            }
            $stmt->execute();

            // Create order
            $stmt = $conn->prepare("INSERT INTO orders 
                                  (user_id, total_price, order_status, payment_status)
                                  VALUES (?, ?, 'Pending', 'Pending')");
            $stmt->bind_param("id", $user_id, $total_price);
            $stmt->execute();
            $order_id = $conn->insert_id;

            // Insert order items
foreach($cart_items as $item) {
    $subtotal = $item['price'] * $item['quantity'];
    $stmt = $conn->prepare("INSERT INTO order_items
                          (order_id, product_name, fabric, size, color, quantity, price, subtotal,category,product_id)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?,?,?)");
    $stmt->bind_param("issssiddsi", $order_id, $item['product_name'], $item['fabric'], 
                     $item['size'], $item['color'], $item['quantity'], $item['price'],$subtotal,$item['category'],$item['product_id']);
    $stmt->execute();
}


            // Create payment record
            $stmt = $conn->prepare("INSERT INTO payments 
                                  (order_id, payment_method, payment_status)
                                  VALUES (?, ?, 'Pending')");
            $payment_method = ($payment_method === 'COD') ? 'Cash on Delivery' : $payment_method;
            $stmt->bind_param("is", $order_id, $payment_method);
            $stmt->execute();

            // Clear cart
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $conn->commit();
            header("Location: order_success.php?order_id=" . $order_id);
            exit();
            
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Order failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zxx">
<head>
    <?php include('header.php'); ?>
    <!-- <script>
    function showGPayMessage() {
    alert("You have selected Google Pay! Please complete the payment using your GPay app. Use the number +91 8838089032 for the transaction. If you encounter any issues, please contact support.");

    // Show confirmation alert
    if (confirm("Do you want to proceed with the order?")) {
        document.querySelector(".checkout__form").submit();
    }
}
</script> -->
</head>
<body>

<?php include('navbar.php'); ?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="./index.php"><i class="fa fa-home"></i> Home</a>
                    <span>Checkout</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="checkout spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php if(!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach($errors as $error): ?>
                            <p><?= $error ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <form method="POST" class="checkout__form">
            <div class="row">
                <div class="col-lg-8">
                    <h5>Billing Details</h5>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="checkout__form__input">
                                <p>First Name <span>*</span></p>
                                <input type="text" name="first_name" required
                                    value="<?= htmlspecialchars($customer_details['first_name'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="checkout__form__input">
                                <p>Last Name <span>*</span></p>
                                <input type="text" name="last_name" required
                                    value="<?= htmlspecialchars($customer_details['last_name'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="checkout__form__input">
                                <p>Email <span>*</span></p>
                                <input type="email" name="email" required
                                    value="<?= htmlspecialchars($customer_details['email'] ?? '') ?>">
                            </div>
                            <div class="checkout__form__input">
                                <p>Phone <span>*</span></p>
                                <input type="text" name="phone" required
                                    value="<?= htmlspecialchars($customer_details['phone'] ?? '') ?>">
                            </div>
                            <div class="checkout__form__input">
                                <p>Address <span>*</span></p>
                                <input type="text" name="address" required
                                    value="<?= htmlspecialchars($customer_details['address'] ?? '') ?>">
                            </div>
                            <div class="checkout__form__input">
                                <p>City <span>*</span></p>
                                <input type="text" name="city" required
                                    value="<?= htmlspecialchars($customer_details['city'] ?? '') ?>">
                            </div>
                            <div class="checkout__form__input">
                                <p>State <span>*</span></p>
                                <input type="text" name="state" required
                                    value="<?= htmlspecialchars($customer_details['state'] ?? '') ?>">
                            </div>
                            <div class="checkout__form__input">
                                <p>Pin Code <span>*</span></p>
                                <input type="text" name="zip" required
                                    value="<?= htmlspecialchars($customer_details['zipcode'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

    <div class="col-lg-4">
    <div class="card shadow-sm border-0">
        <div class="card-header text-white text-center" style="background-color: #F76100;">
            <h5 class="mb-0" style="color: white;">Your Order</h5>
        </div>
        <div class="card-body">
            <div class="checkout__order__product">
                <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between fw-bold bg-light">
                        <span>Product</span>
                        <span>Total</span>
                    </li>
                    <?php foreach($cart_items as $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= htmlspecialchars($item['product_name']) ?></strong> <br>
                                <span class="" style="color: black;"><?= htmlspecialchars($item['fabric']) ?>, <?= htmlspecialchars($item['color']) ?>, <?= htmlspecialchars($item['size']) ?>, <?= htmlspecialchars($item['category']) ?></span> <br>
                                <span class="badge text-white" style="background-color: #F76100;">Qty: <?= htmlspecialchars($item['quantity']) ?></span>
                            </div>
                            <span class="fw-bold">₹<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="checkout__order__total mb-3">
                <div class="d-flex justify-content-between fs-5 fw-bold border-top pt-2">
                    <span>Total:</span>
                    <span style="color: #F76100;">₹<?= number_format($total_price, 2) ?></span>
                </div>
            </div>
            <div class="checkout__order__widget mb-3">
                <label class="d-block mb-2">
                    <input type="radio" name="payment_method" value="Cash on Delivery" required> Cash on Delivery
                </label>
                <!-- <label class="d-block mb-2">
                    <input type="radio" name="payment_method" value="Google Pay" required onclick="showGPayMessage()"> Google Pay
                    No. <span style="color:#F76100">+91 8838089032</span>  
                </label> -->
            </div>
            <button type="submit" class="btn text-white w-100" style="background-color: #F76100;">Place Order</button>
        </div>
    </div>
</div>

            </div>
        </form>
    </div>
</section>

<?php include('footer.php'); ?>
</body>
</html>
