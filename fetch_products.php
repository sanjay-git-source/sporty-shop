<?php
include('connection.php');

$categoryFilter = isset($_POST['categories']) ? implode(",", $_POST['categories']) : '';
$fabricFilter = isset($_POST['fabrics']) ? implode(",", $_POST['fabrics']) : '';
$sizeFilter = isset($_POST['sizes']) ? implode(",", $_POST['sizes']) : '';
$colorFilter = isset($_POST['colors']) ? implode(",", $_POST['colors']) : '';

$sql = "SELECT 
            p.product_id, 
            p.product_name, 
            p.price, 
            p.stock,
            p.created_at,
            c.category_name,
            (SELECT image_url FROM images WHERE product_id = p.product_id LIMIT 1) AS image_url
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        WHERE 1";

// Apply Filters
if (!empty($categoryFilter)) {
    $sql .= " AND p.category_id IN ($categoryFilter)";
}
if (!empty($fabricFilter)) {
    $sql .= " AND p.product_id IN (SELECT product_id FROM product_fabrics WHERE fabric_id IN ($fabricFilter))";
}
if (!empty($sizeFilter)) {
    $sql .= " AND p.product_id IN (SELECT product_id FROM product_sizes WHERE size_id IN ($sizeFilter))";
}
if (!empty($colorFilter)) {
    $sql .= " AND p.product_id IN (SELECT product_id FROM product_colors WHERE color_id IN ($colorFilter))";
}

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="col-lg-4 col-md-6">
            <div class="product__item">
                <div class="product__item__pic set-bg" data-setbg="admin/<?php echo $row['image_url']; ?>">
                    <div class="label sale">Sale</div>
                </div>
                <div class="product__item__text">
                    <h6><a href="product-details.php?id=<?php echo $row['product_id']; ?>"><?php echo $row['product_name']; ?></a></h6>
                    <p>Category: <?php echo $row['category_name']; ?></p>
                    <div class="product__price">₹ <?php echo number_format($row['price'], 2); ?></div>
                </div>
            </div>
        </div>
    <?php }
} else {
    echo "<p>No products found.</p>";
}
?>
