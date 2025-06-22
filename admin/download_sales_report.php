<?php
// download_sales_report.php
session_start();
include('connection.php');
include('adminsessionChecker.php');

// Get all filter parameters from the request
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$fabric_filter = isset($_GET['fabric_type']) ? $_GET['fabric_type'] : '';
$customer_filter = isset($_GET['customer_name']) ? $_GET['customer_name'] : '';
$size_filter = isset($_GET['size']) ? $_GET['size'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$color_filter = isset($_GET['color']) ? $_GET['color'] : '';

// Prevent SQL Injection
$start_date = mysqli_real_escape_string($conn, $start_date);
$end_date = mysqli_real_escape_string($conn, $end_date);
$fabric_filter = mysqli_real_escape_string($conn, $fabric_filter);
$customer_filter = mysqli_real_escape_string($conn, $customer_filter);
$size_filter = mysqli_real_escape_string($conn, $size_filter);
$category_filter = mysqli_real_escape_string($conn, $category_filter);
$color_filter = mysqli_real_escape_string($conn, $color_filter);

// Build WHERE clause (same as main page)
$where_clause = "WHERE o.created_at BETWEEN '$start_date' AND '$end_date' AND o.order_status != 'Cancelled'";

if (!empty($fabric_filter)) $where_clause .= " AND oi.fabric = '$fabric_filter'";
if (!empty($customer_filter)) $where_clause .= " AND u.name LIKE '%$customer_filter%'";
if (!empty($size_filter)) $where_clause .= " AND oi.size = '$size_filter'";
if (!empty($category_filter)) $where_clause .= " AND oi.category = '$category_filter'";
if (!empty($color_filter)) $where_clause .= " AND oi.color = '$color_filter'";

// Fetch data for CSV
$query = "SELECT o.order_id, u.name AS customer_name, o.user_id, oi.subtotal, 
                 oi.fabric AS fabric_type, oi.size, oi.category, oi.color, 
                 o.payment_status, o.delivery_date, o.created_at
          FROM orders o
          JOIN order_items oi ON o.order_id = oi.order_id
          JOIN customers u ON o.user_id = u.user_id
          $where_clause
          ORDER BY o.created_at DESC";

$result = $conn->query($query);

// Handle query errors
if (!$result) {
    die("Query Failed: " . $conn->error);
}

// Set CSV headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=sales_report_' . date('Y-m-d') . '.csv');

// Create output pointer
$output = fopen('php://output', 'w');

// CSV header row
fputcsv($output, [
    'Order ID', 'Customer Name', 'User ID', 'Total Price', 
    'Fabric Type', 'Size', 'Category', 'Color', 
    'Payment Status', 'Delivery Date', 'Order Date'
]);

// Add data rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['order_id'],
            $row['customer_name'],
            $row['user_id'],
            number_format($row['subtotal'], 2),
            $row['fabric_type'],
            $row['size'],
            $row['category'],
            $row['color'],
            $row['payment_status'],
            $row['delivery_date'],
            $row['created_at']
        ]);
    }
} else {
    fputcsv($output, ['No records found']);
}

fclose($output);
exit;
?>