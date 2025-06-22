<?php
session_start();
include('connection.php');
include('adminsessionChecker.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('./header.php'); ?>
    <?php include('./style.php'); ?>
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

    <div class="search_section">
        <h3>Order Management</h3><br>
        <input type="text" id="name_search" name="q" placeholder="Enter your search ....." pattern="[A-Za-z]+" title="Only alphabet characters are allowed" aria-label="Search Enquiry">
        <button onclick="searchOrders()" class="btn">Search</button>
        <button onclick="clearSearch()" class="btn">Clear</button>
    </div>

    <div class="wholetbl">
        <div class="container">
            <div class="header">
                <h2>Orders List</h2>
                <div class="buttons">
                    <button class="selectall" onclick="selectAll()">Select All</button>
                    <button class="delete" onclick="deleteSelected()">Delete</button>
                </div>            
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User ID</th>
                            <th>Total Price</th>
                            <th>Order Status</th>
                            <th>Payment Status</th>
                            <th>Delivery Date</th>
                            <th>Order At</th>
                            <th>Actions</th>
                            <th>Select</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $result = $conn->query("SELECT * FROM orders ORDER BY order_id DESC");
                        while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr data-id="<?= $row['order_id'] ?>">
                            <td data-label="Order ID"><?= $row['order_id'] ?></td>
                            <td data-label="User ID"><?= $row['user_id'] ?></td>
                            <td data-label="Total Price"><?= number_format($row['total_price'], 2) ?></td>
                            <td data-label="Order Status">
                                <select onchange="updateOrderStatus(<?= $row['order_id'] ?>, this.value)">
                                    <?php
                                    $statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
                                    foreach ($statuses as $status) {
                                        $selected = ($row['order_status'] == $status) ? 'selected' : '';
                                        echo "<option value='$status' $selected>$status</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td data-label="Payment Status">
                                <select onchange="updatePaymentStatus(<?= $row['order_id'] ?>, this.value)">
                                    <?php
                                    $payments = ['Pending', 'Paid', 'Failed'];
                                    foreach ($payments as $payment) {
                                        $selected = ($row['payment_status'] == $payment) ? 'selected' : '';
                                        echo "<option value='$payment' $selected>$payment</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td data-label="Delivery Date">
                            <input type="date" id="delivery_date_<?= $row['order_id'] ?>" value="<?= $row['delivery_date'] ?>"  min="<?= date('Y-m-d', strtotime('+1 day')) ?>"  onchange="updateDeliveryDate(<?= $row['order_id'] ?>)">
                            </td>
                            <td data-label="Order At"><?= $row['created_at'] ?></td>
                            <td data-label="Actions"><a href="order_details.php?order_id=<?= $row['order_id'] ?>">View Details</a></td>
                            <td data-label="Select"><input type="checkbox" name="record[]" value="<?= $row['order_id'] ?>"></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    
    function updateDeliveryDate(orderId) {
    let dateInput = document.querySelector(`tr[data-id='${orderId}'] input[type='date']`);
    let date = dateInput.value;

    fetch(`update_delivery_date.php?order_id=${orderId}&delivery_date=${date}`)
        .then(response => response.json())
        .then(data => alert(data.message))
        .catch(error => alert('Error updating delivery date.'));
}

</script>

<script>
function searchOrders() {
    const searchInput = document.getElementById('name_search').value.toLowerCase();
    const rows = document.querySelectorAll('.table-wrapper tbody tr');

    rows.forEach(row => {
        const cells = row.getElementsByTagName('td');
        let found = false;

        for (let cell of cells) {
            if (cell.textContent.toLowerCase().includes(searchInput)) {
                found = true;
                break;
            }
        }
        row.style.display = found ? '' : 'none';
    });

    document.getElementById('name_search').value = '';
}

function clearSearch() {
    document.getElementById('name_search').value = '';
    searchOrders();
}

function deleteSelected() {
    var selectedIds = [];
    var checkboxes = document.getElementsByName('record[]');

    checkboxes.forEach(function(checkbox) {
        if (checkbox.checked) {
            selectedIds.push(checkbox.value);
        }
    });

    if (selectedIds.length > 0) {
        if (confirm("Are you sure you want to delete the selected orders?")) {
            fetch('delete_orders.php?deleteid=' + selectedIds.join(','), {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    location.reload();
                }
            })
            .catch(error => alert('An error occurred while deleting orders.'));
        }
    } else {
        alert("Please select at least one order to delete.");
    }
}

function selectAll() {
    document.querySelectorAll('input[name="record[]"]').forEach(cb => cb.checked = true);
}

function updateOrderStatus(orderId, status) {
    fetch('update_order_status.php?order_id=' + orderId + '&status=' + status)
    .then(response => response.json())
    .then(data => alert(data.message))
    .catch(error => alert('Error updating order status.'));
}

function updatePaymentStatus(orderId, status) {
    fetch('update.php?order_id=' + orderId + '&status=' + status)
    .then(response => response.json())
    .then(data => alert(data.message))
    .catch(error => alert('Error updating payment status.'));
}
</script>

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