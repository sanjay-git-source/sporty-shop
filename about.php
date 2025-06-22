<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php'); ?>
      <style>
        .hero-section {
            text-align: center;
            padding: 60px 20px;
            border-radius: 10px;
        }
        .hero-section h1 {
            font-size: 36px;
            margin: 0;
            color: #F76100;
        }
        .hero-section p {
            font-size: 18px;
            margin-top: 10px;
        }
        .about-image img {
            width: 100%;
            max-width: 500px;
            border-radius: 10px;
        }
        .feature-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .feature-box i {
            font-size: 40px;
            color: #F76100;
            margin-bottom: 10px;
        }
        .cta-section {
            text-align: center;
            color: #F76100;
            padding: 40px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

 <!-- Page Preloader -->
 <div id="preloder">
        <div class="loader"></div>
    </div>

    <?php include('side_navbar.php'); ?>
    <?php include('navbar.php'); ?>

    <!-- Breadcrumb Begin -->
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="index.php"><i class="fa fa-home"></i> Home</a>
                        <span>About Us</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Hero Section -->
<div class="hero-section">
    <h1>Welcome to Sporty Shop</h1>
    <p>Your one-stop destination for high-quality sportswear.</p>
</div>

<div class="container mt-5">
    <!-- About Content -->
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2 style="color:#F76100" class="mb-3">Who We Are</h2>
            <p>SportyShop is dedicated to providing top-quality sports dresses for athletes, fitness lovers, and active individuals. Whether you're training for a marathon or just love a sporty look, we've got the perfect outfit for you.</p>
            <p>Our collection blends <strong>Style, Comfort, and Performance</strong>, ensuring that every piece you wear feels as good as it looks. Made from breathable, high-performance fabrics, our sportswear helps you stay active in style.</p>
        </div>
        <div class="col-md-6 text-center">
            <img src="img/about.png" class="img-fluid rounded" alt="Sportswear">
        </div>
    </div>

    <!-- Features -->
    <div class="row mt-5">
        <div class="col-md-4" style="background-color: #F76100;">
            <div class="feature-box p-4">
            <i class="fa fa-support"></i>
            <h3>Premium Quality</h3>
                <p>Our dresses are made from the best fabrics, ensuring durability, comfort, and style.</p>
            </div>
        </div>
        <div class="col-md-4" >
            <div class="feature-box p-4">
            <i class="fa fa-truck"></i>
            <h3>Fast & Free Shipping</h3>
                <p>Get your favorite sportswear delivered to your doorstep with quick and free shipping.</p>
            </div>
        </div>
        <div class="col-md-4" style="background-color: #F76100;">
            <div class="feature-box p-4" >
            <i class="fa fa-lock"></i>
            <h3>Secure Payments</h3>

                <p>Shop with confidence using our secure payment method Cash on Delivery.</p>
                </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="cta-section mt-5">
        <h2 style="color: #F76100;">Upgrade Your Activewear Today!</h2>
        <p>Shop now and experience the perfect blend of performance and fashion.</p>
        <a href="shop.php" class="btn fw-bold" style="text-decoration: underline;color:#F76100;">Shop Now</a>
    </div>
</div>

<?php include('footer.php'); ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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