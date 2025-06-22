<?php
session_start();
include('connection.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Check if required parameters are set
if (isset($_GET['order_id'], $_GET['delivery_date'])) {
    $order_id = intval($_GET['order_id']);
    $delivery_date = $_GET['delivery_date'];

    // Update the database with delivery date
    $stmt = $conn->prepare("UPDATE orders SET delivery_date = ? WHERE order_id = ?");
    $stmt->bind_param("si", $delivery_date, $order_id);

    if ($stmt->execute()) {
        // Fetch order details
        $query = "SELECT o.*, c.name, c.email FROM orders o 
                  JOIN customers c ON o.user_id = c.user_id 
                  WHERE o.order_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();

        if (!$order) {
            echo json_encode(["status" => "error", "message" => "Order not found"]);
            exit;
        }

        $customer_name = $order['name'];
        $customer_email = $order['email'];
        $total_price = $order['total_price'];
        $order_status = $order['order_status'];
        $payment_status = $order['payment_status'];

        // Fetch order items
        $items_query = "SELECT product_name, quantity, price, subtotal FROM order_items WHERE order_id = ?";
        $items_stmt = $conn->prepare($items_query);
        $items_stmt->bind_param("i", $order_id);
        $items_stmt->execute();
        $items_result = $items_stmt->get_result();

        $order_items_html = "";
        while ($item = $items_result->fetch_assoc()) {
            $order_items_html .= "
                <tr>
                    <td>{$item['product_name']}</td>
                    <td>{$item['quantity']}</td>
                    <td>₹{$item['price']}</td>
                    <td>₹{$item['subtotal']}</td>
                </tr>";
        }

        // Email content
        $email_body = "
        <h2>Order Delivery Confirmation</h2>
        <p><strong>Order ID:</strong> #$order_id</p>
        <p><strong>Customer Name:</strong> $customer_name</p>
        <p><strong>Total Price:</strong>₹$total_price</p>
        <p><strong>Order Status:</strong> $order_status</p>
        <p><strong>Payment Status:</strong> $payment_status</p>
        <p><strong>Estimated Delivery Date:</strong> $delivery_date</p>
        <h3>Order Details</h3>
        <table border='1' cellpadding='5' cellspacing='0'>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
            $order_items_html
        </table>
        <p>Thank you for shopping with us!</p>
        ";

        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // SMTP settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Change to your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'sanjaymech2310@gmail.com'; // Your email
            $mail->Password = ''; // Your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email content
            $mail->setFrom('sanjaymech2310@gmail.com', 'Sporty Shop');
            $mail->addAddress($customer_email, $customer_name);
            $mail->isHTML(true);
            $mail->Subject = "Order Delivery Confirmation - Order #$order_id";
            $mail->Body = $email_body;

            $mail->send();
            echo json_encode(["status" => "success", "message" => "Delivery date updated and email sent successfully!"]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Error sending email: " . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update delivery date"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
