<?php
include('connection.php'); // Include your database connection

$query = "SELECT * FROM gallery";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
    <style>
        body { background-color: #f8f9fa; }
        .gallery-title { text-align: center; font-size: 2.5rem; font-weight: bold; margin: 40px 0; color: #F76100; }
        .gallery-item { position: relative; overflow: hidden; border-radius: 10px; transition: transform 0.3s ease; }
        .gallery-item img { width: 100%; height: 450px; display: block; border-radius: 10px; }
        .gallery-item:hover { transform: scale(1.05); }
        .gallery-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5);
            display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; border-radius: 10px; }
        .gallery-item:hover .gallery-overlay { opacity: 1; }
        .gallery-overlay i { font-size: 2rem; color: white; }
    </style>
</head>
<body>
    <?php include('side_navbar.php'); ?>
    <?php include('navbar.php'); ?>
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="./index.php"><i class="fa fa-home"></i> Home</a>
                        <span>Gallery</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <h1 class="gallery-title">Gallery</h1>
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <div class="col-lg-4 col-md-6 col-12 mb-5">
                    <div class="gallery-item">
                        <a href="admin/<?php echo $row['image_path']; ?>" class="gallery-popup">
                            <img src="admin/<?php echo $row['image_path']; ?>" alt="">
                            <div class="gallery-overlay">
                                <i class="fa fa-search-plus"></i>
                            </div>
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php include('footer.php'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.gallery-popup').magnificPopup({
                type: 'image',
                gallery: { enabled: true }
            });
        });
    </script>
</body>
</html>
