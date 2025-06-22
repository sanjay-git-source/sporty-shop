<?php
session_start();
include('connection.php');
include('adminsessionChecker.php');

// Get filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$fabric_filter = isset($_GET['fabric_type']) ? $_GET['fabric_type'] : '';
$customer_filter = isset($_GET['customer_name']) ? $_GET['customer_name'] : '';
$size_filter = isset($_GET['size']) ? $_GET['size'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$color_filter = isset($_GET['color']) ? $_GET['color'] : '';

// Fetch dropdown options
$fabric_query = "SELECT DISTINCT fabric FROM order_items";
$fabric_result = $conn->query($fabric_query);

$category_query = "SELECT DISTINCT category FROM order_items";
$category_result = $conn->query($category_query);

$color_query = "SELECT DISTINCT color FROM order_items";
$color_result = $conn->query($color_query);

$size_query = "SELECT DISTINCT size FROM order_items";
$size_result = $conn->query($size_query);

// Prevent SQL Injection
$start_date = mysqli_real_escape_string($conn, $start_date);
$end_date = mysqli_real_escape_string($conn, $end_date);
$fabric_filter = mysqli_real_escape_string($conn, $fabric_filter);
$customer_filter = mysqli_real_escape_string($conn, $customer_filter);
$size_filter = mysqli_real_escape_string($conn, $size_filter);
$category_filter = mysqli_real_escape_string($conn, $category_filter);
$color_filter = mysqli_real_escape_string($conn, $color_filter);

// Build WHERE clause
$where_clause = "WHERE o.created_at BETWEEN '$start_date' AND '$end_date' AND o.order_status != 'Cancelled'";

if (!empty($fabric_filter)) $where_clause .= " AND oi.fabric = '$fabric_filter'";
if (!empty($customer_filter)) $where_clause .= " AND u.name LIKE '%$customer_filter%'";
if (!empty($size_filter)) $where_clause .= " AND oi.size = '$size_filter'";
if (!empty($category_filter)) $where_clause .= " AND oi.category = '$category_filter'";
if (!empty($color_filter)) $where_clause .= " AND oi.color = '$color_filter'";

// Fetch total orders & revenue
$sql = "SELECT COUNT(DISTINCT o.order_id) AS total_orders, SUM(oi.subtotal) AS total_revenue 
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN customers u ON o.user_id = u.user_id
        $where_clause";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$total_orders = $row['total_orders'] ?? 0;
$total_revenue = $row['total_revenue'] ?? 0.00;

// Fetch detailed sales report
$sales_query = "SELECT o.order_id, u.name AS customer_name, o.user_id, oi.subtotal, oi.fabric AS fabric_type, 
                oi.size, oi.category, oi.color, o.payment_status, o.delivery_date, o.created_at
                FROM orders o
                JOIN order_items oi ON o.order_id = oi.order_id
                JOIN customers u ON o.user_id = u.user_id
                $where_clause
                ORDER BY o.created_at DESC";
                
$sales_result = $conn->query($sales_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sales Report</title>
    <?php include('./header.php'); ?>
    <?php include('./style.php'); ?>
    <style>
       .container { 
    max-width: 100%; 
    margin: auto; 
    padding: 20px; 
}

h2, h3 { 
    text-align: center; 
    margin-bottom: 10px; 
}

.filter-form { 
    display: flex; 
    flex-wrap: wrap; 
    justify-content: center; 
    gap: 10px; 
    margin-bottom: 20px; 
}

.filter-form input, 
.filter-form select, 
.filter-form button { 
    padding: 8px; 
    font-size: 14px;
}

.summary { 
    text-align: center; 
    margin-bottom: 20px; 
}

.table-container { 
    max-height: 500px; 
    overflow-x: auto; 
    border: 1px solid #ddd; 
}

table { 
    width: 100%; 
    border-collapse: collapse; 
    white-space: nowrap; 
}

table, th, td { 
    border: 1px solid #ddd; 
}

th, td { 
    padding: 10px; 
    text-align: center; 
}

.btn { 
    background-color: #FF7F50; 
    border: none; 
    color: white; 
    padding: 10px 20px; 
    border-radius: 5px; 
    cursor: pointer;        
    font-size: 14px; 
    transition: background-color 0.3s ease; 
}

.btn:hover { 
    background-color: #F76100; 
    color: white; 
}

/* Responsive Styles */
@media (max-width: 1024px) {
    .container { 
        padding: 15px; 
    }
    
    .filter-form { 
        flex-direction: column; 
        align-items: center; 
    }

    .filter-form input, 
    .filter-form select, 
    .filter-form button { 
        width: 100%; 
        max-width: 400px; 
        padding: 10px; 
    }

    .btn { 
        width: 100%; 
        max-width: 200px; 
    }

    .table-container { 
        overflow-x: auto; 
    }

    table { 
        font-size: 12px; 
    }
}

@media (max-width: 600px) {
    h2, h3 { 
        font-size: 20px; 
    }

    .filter-form input, 
    .filter-form select, 
    .filter-form button { 
        width: 100%; 
        max-width: 300px; 
    }

    th, td { 
        padding: 6px; 
        font-size: 12px; 
    }

    .btn { 
        font-size: 12px; 
        padding: 8px 15px; 
    }
}

</style>
</head>
<body>

<?php include('./navbar.php'); ?>

<div class="container">
    <h2 style="color:#F76100">Sales Report</h2>

    <form method="GET" class="filter-form">
        <input type="date" name="start_date" value="<?= $start_date ?>">
        <input type="date" name="end_date" value="<?= $end_date ?>">
        
        <input type="text" name="customer_name" placeholder="Customer Name" value="<?= $customer_filter ?>">

        <select name="fabric_type">
            <option value="">All Fabrics</option>
            <?php while ($fabric = $fabric_result->fetch_assoc()) { ?>
                <option value="<?= $fabric['fabric'] ?>" <?= ($fabric_filter == $fabric['fabric']) ? 'selected' : '' ?>>
                    <?= $fabric['fabric'] ?>
                </option>
            <?php } ?>
        </select>

        <select name="size">
            <option value="">All Sizes</option>
            <?php while ($size = $size_result->fetch_assoc()) { ?>
                <option value="<?= $size['size'] ?>" <?= ($size_filter == $size['size']) ? 'selected' : '' ?>>
                    <?= $size['size'] ?>
                </option>
            <?php } ?>
        </select>

        <select name="category">
            <option value="">All Categories</option>
            <?php while ($category = $category_result->fetch_assoc()) { ?>
                <option value="<?= $category['category'] ?>" <?= ($category_filter == $category['category']) ? 'selected' : '' ?>>
                    <?= $category['category'] ?>
                </option>
            <?php } ?>
        </select>

        <select name="color">
            <option value="">All Colors</option>
            <?php while ($color = $color_result->fetch_assoc()) { ?>
                <option value="<?= $color['color'] ?>" <?= ($color_filter == $color['color']) ? 'selected' : '' ?>>
                    <?= $color['color'] ?>
                </option>
            <?php } ?>
        </select>

        <button type="submit" class="btn">Filter</button>
        <button type="button" class="btn" onclick="downloadCSV()">Download CSV</button>
    </form>

    <div class="summary">
        <h3>Total Sales: ₹<?= number_format($total_revenue, 2) ?></h3>
        <h3>Total Orders: <?= $total_orders ?></h3>
    </div>

    <div class="table-container">
        <table id="salesTable">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>User ID</th>
                    <th>Total Price</th>
                    <th>Fabric Type</th>
                    <th>Size</th>
                    <th>Category</th>
                    <th>Color</th>
                    <th>Payment Status</th>
                    <th>Delivery Date</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($sale = $sales_result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $sale['order_id'] ?></td>
                    <td><?= $sale['customer_name'] ?></td>
                    <td><?= $sale['user_id'] ?></td>
                    <td>₹<?= number_format($sale['subtotal'], 2) ?></td>
                    <td><?= $sale['fabric_type'] ?></td>
                    <td><?= $sale['size'] ?></td>
                    <td><?= $sale['category'] ?></td>
                    <td><?= $sale['color'] ?></td>
                    <td><?= $sale['payment_status'] ?></td>
                    <td><?= $sale['delivery_date'] ?></td>
                    <td><?= $sale['created_at'] ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function downloadCSV() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = 'download_sales_report.php?' + params.toString();
}
</script>

</body>
</html>
