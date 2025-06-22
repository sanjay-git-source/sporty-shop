<?php 
session_start();
if (isset($_SESSION['error'])) {
    echo "<script>alert('" . $_SESSION['error'] . "');</script>";
    unset($_SESSION['error']); // Clear error after displaying
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('./header.php')?>
<style>
    #whole-div {
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.login-container {
    background: #f4f4f4;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 350px;
}

.logo img {
    width: 100px;
    margin-bottom: 20px;
}

h2 {
    color: #333;
}

.input-group {
    margin-bottom: 15px;
    text-align: left;
}

.input-group label {
    display: block;
    margin-bottom: 5px;
    color: #555;
}

.input-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.checkbox-group {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.checkbox-group input {
    margin-right: 10px;
}

.checkbox-group label {
    color: #555;
}

.login-btn {
    background: #F76100  ;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
}

.login-btn:hover {
  color: #F76100;
  background-color: #fff;
  border: 1px solid #F76100;
}
.signup {
    margin-top: 15px;
    font-size: 14px;
    font-weight: bold;
    color: #F76100;
    cursor: pointer;
    transition: color 0.3s ease;
}

.signup:hover {
    color: #F76100;
    text-decoration: underline;
}


</style>
</head>
<body>
    
<div id="preloder">
        <div class="loader"></div>
    </div>
    
    <?php include('side_navbar.php'); ?>
    <?php include('navbar.php'); ?>

    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="./index.php"><i class="fa fa-home"></i> Home</a>
                        <span>Login</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
  <div id="whole-div">
    <div class="login-container">
    <div style="text-align: right;">
        <span class="close-btn" onclick="window.location.href='index.php';">&times;</span>
        </div>
        <div class="header__logo">
                    <a href="./index.php" style="color: #F76100; font-size: 24px; font-family: 'Arial Black', sans-serif;text-decoration: underline">Sporty Shop</a>
                </div>
       <h2>Login</h2>
        <form action="customer_loginChecker.php" method="post">
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I accept terms & conditions</label>
            </div>
            <button type="submit" class="login-btn">Sign In</button>
            <p class="signup">Don't have an account? <a href="register.php">Sign Up</a></p>
            </form>
    </div>
    </div>
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