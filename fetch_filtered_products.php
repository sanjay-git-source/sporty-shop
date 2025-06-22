<?php
include('connection.php');

$conditions = [];
$params = [];

// Pagination settings
$products_per_page = 9; 
$page = isset($_POST['page']) ? (int) $_POST['page'] : 1; // Changed from $_GET to $_POST
$offset = ($page - 1) * $products_per_page;

// Filtering conditions
if (!empty($_POST['categories'])) {
    $categoryIds = implode(",", array_map('intval', $_POST['categories']));
    $conditions[] = "p.category_id IN ($categoryIds)";
}

if (!empty($_POST['fabrics'])) {
    $fabricIds = implode(",", array_map('intval', $_POST['fabrics']));
    $conditions[] = "p.product_id IN (SELECT product_id FROM product_fabrics WHERE fabric_id IN ($fabricIds))";
}

if (!empty($_POST['sizes'])) {
    $sizeIds = implode(",", array_map('intval', $_POST['sizes']));
    $conditions[] = "p.product_id IN (SELECT product_id FROM product_sizes WHERE size_id IN ($sizeIds))";
}

if (!empty($_POST['colors'])) {
    $colorIds = implode(",", array_map('intval', $_POST['colors']));
    $conditions[] = "p.product_id IN (SELECT product_id FROM product_colors WHERE color_id IN ($colorIds))";
}

// Base query
$query = "SELECT 
            p.product_id, 
            p.product_name, 
            p.price, 
            p.stock, 
            p.created_at,
            c.category_name,
            (SELECT image_url FROM images WHERE product_id = p.product_id LIMIT 1) AS image_url,
            GROUP_CONCAT(DISTINCT f.fabric_name SEPARATOR ', ') AS fabrics,
            GROUP_CONCAT(DISTINCT s.size_name SEPARATOR ', ') AS sizes,
            GROUP_CONCAT(DISTINCT co.color_name SEPARATOR ', ') AS colors
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN product_fabrics pf ON p.product_id = pf.product_id
        LEFT JOIN fabrics f ON pf.fabric_id = f.fabric_id
        LEFT JOIN product_sizes ps ON p.product_id = ps.product_id
        LEFT JOIN sizes s ON ps.size_id = s.size_id
        LEFT JOIN product_colors pc ON p.product_id = pc.product_id
        LEFT JOIN colors co ON pc.color_id = co.color_id";

// Apply filters
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " GROUP BY p.product_id LIMIT $products_per_page OFFSET $offset";
$result = mysqli_query($conn, $query);

// Fetch total products for pagination
$total_query = "SELECT COUNT(DISTINCT p.product_id) AS total FROM products p";
if (!empty($conditions)) {
    $total_query .= " WHERE " . implode(" AND ", $conditions);
}
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $products_per_page);

// Generate product output
$output = '';
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $new_label_days = 7;
        $created_date = strtotime($row['created_at']);
        $days_diff = (time() - $created_date) / (60 * 60 * 24);
        $image_url = !empty($row['image_url']) ? 'admin/' . $row['image_url'] : 'assets/no-image.jpg';

        $output .= '<div class="col-lg-4 col-md-6">
                        <div class="product__item">
                         <div class="product__item__pic">
                        <img class="product__item__pic" src="' . $image_url . '" alt="' . htmlspecialchars($row['product_name']) . '" class="product-img">';
            
        if ($row['stock'] == 0) {
            $output .= '<div class="label stockout stockblue">Out Of Stock</div>';
        } elseif ($days_diff <= $new_label_days) {
            $output .= '<div class="label new">New</div>';
        } else {
            $output .= '<div class="label sale">Sale</div>';
        }

        $output .= '<ul class="product__hover">
                        <li><a href="' . $image_url . '" class="image-popup"><span class="arrow_expand"></span></a></li>
                        <li><a href="#"><span class="icon_heart_alt"></span></a></li>
                        <li><a href="shop-cart.php"><span class="icon_bag_alt"></span></a></li>
                    </ul>
                </div>
                <div class="product__item__text">
                    <h6><a href="product-details.php?id=' . $row['product_id'] . '" style="color: #F76100;">' . $row['product_name'] . '</a></h6>
                    <p>Category: ' . $row['category_name'] . '</p>
                    <p>Fabrics: ' . ($row['fabrics'] ?: 'N/A') . '</p>
                    <p>Sizes: ' . ($row['sizes'] ?: 'N/A') . '</p>
                    <p>Colors: ' . ($row['colors'] ?: 'N/A') . '</p>
                    <div class="product__price">₹ ' . number_format($row['price'], 2) . '</div>
                </div>
            </div>
        </div>';
    }
} else {
    $output .= '<div class="col-12"><p>No products found</p></div>';
}


// Pagination output
$output .= '<div class="col-lg-12 text-center">
    <div class="pagination__option">';
if ($page > 1) {
    $output .= '<a href="#" onclick="changePage(' . ($page - 1) . ')"><i class="fa fa-angle-left"></i></a>';
}
for ($i = 1; $i <= $total_pages; $i++) {
    $output .= '<a href="#" onclick="changePage(' . $i . ')" ' . ($i == $page ? 'class="active"' : '') . '>' . $i . '</a>';
}
if ($page < $total_pages) {
    $output .= '<a href="#" onclick="changePage(' . ($page + 1) . ')"><i class="fa fa-angle-right"></i></a>';
}
$output .= '</div></div>';

echo $output;
?>
