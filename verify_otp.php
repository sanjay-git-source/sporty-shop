<?php
session_start();
?>
<?php
include('customer_sessionChecker.php');
?>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_otp = $_POST['otp'];

    if ($user_otp == $_SESSION['otp']) {
        echo "<script>alert('Login successful!');</script>";
        
        unset($_SESSION['otp']);
        echo "<script>window.location.href='index.php';</script>";
        exit();
    } else {
        echo "<script>alert('Invalid OTP. Please try again.'); window.location.href='verify_otp.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('./header.php'); ?>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .otp-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }
        .otp-container h2 {
            font-weight: bold;
            margin-bottom: 20px;
            color: #343a40;
        }
        .btn-primary {
            background-color: #F76100;
            border-color: #F76100;
        }
        .btn-primary:hover {
        color: #F76100;
        background-color: #fff;
        border: 1px solid #F76100;
        }
    </style>
</head>
<body>
    <div class="otp-container">
        <h2 class="text-center">Verify OTP</h2>
        <form action="" method="post">
            <div class="mb-3">
                <label for="otp" class="form-label">Enter OTP</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="otp" 
                    name="otp" 
                    maxlength="6" 
                    placeholder="Enter 6-digit code"
                    required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
        </form>
    </div>
</body>
</html>
