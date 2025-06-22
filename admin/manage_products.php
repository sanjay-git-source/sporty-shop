<?php
session_start();
include('connection.php');
?>
<?php
include('adminsessionChecker.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('./header.php')?>
    <?php include('./style.php')?>
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
    <!-- Offcanvas Menu End -->
    <?php include('./navbar.php'); ?>

    <!-- Your existing search section remains the same -->
    
    <?php
    // Get colors, sizes, and fabrics for all products
    $color_map = [];
    $size_map = [];
    $fabric_map = [];

    // Get all product colors
    $color_result = $conn->query("
        SELECT pc.product_id, GROUP_CONCAT(c.color_name ORDER BY c.color_name SEPARATOR ', ') AS colors 
        FROM product_colors pc 
        JOIN colors c ON pc.color_id = c.color_id 
        GROUP BY pc.product_id
    ");
    while ($row = $color_result->fetch_assoc()) {
        $color_map[$row['product_id']] = $row['colors'];
    }

    // Get all product sizes
    $size_result = $conn->query("
        SELECT ps.product_id, GROUP_CONCAT(s.size_name ORDER BY s.size_name SEPARATOR ', ') AS sizes 
        FROM product_sizes ps 
        JOIN sizes s ON ps.size_id = s.size_id 
        GROUP BY ps.product_id
    ");
    while ($row = $size_result->fetch_assoc()) {
        $size_map[$row['product_id']] = $row['sizes'];
    }

    // Get all product fabrics
    $fabric_result = $conn->query("
        SELECT pf.product_id, GROUP_CONCAT(f.fabric_name ORDER BY f.fabric_name SEPARATOR ', ') AS fabrics 
        FROM product_fabrics pf 
        JOIN fabrics f ON pf.fabric_id = f.fabric_id 
        GROUP BY pf.product_id
    ");
    while ($row = $fabric_result->fetch_assoc()) {
        $fabric_map[$row['product_id']] = $row['fabrics'];
    }

    $result = $conn->query("
        SELECT p.product_id, p.product_name, c.category_name, p.price, p.stock
        FROM products p 
        JOIN categories c ON p.category_id = c.category_id
        ORDER BY p.product_id DESC
    ");
    ?>

<div class="search_section" >
  <h3>Products List</h3><br>
  <input type="text" id="name_search" name="q" placeholder="Enter your search ....." pattern="[A-Za-z]+" title="Only alphabet characters are allowed" aria-label="Search Enquiry">
  <button onclick="search()" class="btn" aria-label="Search Button">Search</button>
  <button onclick="clearSearch()" class="btn" aria-label="Clear Search">Clear</button>
</div>

    <div class="wholetbl">
        <div class="container">
            <div class="header">
                <h2>Product Management</h2>
                <div class="buttons">
                    <button class="selectall"><a href="add_products.php" style="color:white;">Add New</a></button>
                    <button class="selectall" onclick="selectAll()">Select All</button>
                    <button class="delete" onclick="deleteSelected()">Delete</button>
                </div>
            </div>
            <div class="table-wrapper">
            <table>
    <thead>
        <tr>
            <th>Sno</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Colors</th>
            <th>Sizes</th>
            <th>Fabrics</th>
            <th>Images</th>
            <th>Select</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $n = 1;
        while ($row = $result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $colors = $color_map[$product_id] ?? 'None';
            $sizes = $size_map[$product_id] ?? 'None';
            $fabrics = $fabric_map[$product_id] ?? 'None';
        ?>
        <tr data-id="<?= $product_id ?>">
            <td data-label="Sno"><?= $n ?></td>
            <td data-label="Product Name"><?= htmlspecialchars($row['product_name']) ?></td>
            <td data-label="Category"><?= htmlspecialchars($row['category_name']) ?></td>
            <td data-label="Price"><?= number_format($row['price'], 2) ?></td>
            <td data-label="Stock">

    <form action="update_stock.php" method="POST">
        <input type="hidden" name="product_id" value="<?= $product_id ?>">
        <span class="<?= $row['stock'] == 1 ? 'text-success' : 'text-danger' ?>">
            <?= $row['stock'] == 1 ? 'Available' : 'Not Available' ?>
        </span>
        <button type="submit" class="btn-primary btn-sm" style="margin-left: 5px;" name="update_stock">
            <i class="fa fa-refresh"></i>
        </button>
    </form>
</td>

            <td data-label="Color"><?= $colors ?></td>
            <td data-label="Sizes"><?= $sizes ?></td>
            <td data-label="Fabrics"><?= $fabrics ?></td>
            <td data-label="Images">
                <?php
                $images = $conn->query("SELECT image_url FROM images WHERE product_id = $product_id");
                while ($img = $images->fetch_assoc()) {
                    echo '<img src="'.htmlspecialchars($img['image_url']).'" width="70px" height="70px">';
                }
                ?>
            </td>
            <td><input type="checkbox" name="record[]" value="<?= $product_id ?>"></td>
        </tr>
        <?php 
            $n++;
        } 
        ?>
    </tbody>
</table>

            </div>
        </div>
    </div>

    <script>

function search() {
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
        search();  // Trigger the search to show all records
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
    if (confirm("Are you sure you want to delete the selected records?")) {
        fetch('delete_products.php?deleteid=' + selectedIds.join(','), {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                location.reload();
         }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while trying to delete records.');
        });
    }
} else {
    alert("Please select at least one record to delete.");
}
}


function selectAll() {
    var checkboxes = document.getElementsByName('record[]');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = true;
    });
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