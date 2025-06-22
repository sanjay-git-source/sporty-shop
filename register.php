<?php
include('connection.php');

// Handling Form Submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format!";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        // Check if email already exists
        $check_email = $conn->prepare("SELECT user_id FROM customers WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();

        if ($check_email->num_rows > 0) {
            $message = "Email already registered! Please use another email.";
        } else {
            // Hash Password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert into Database
            $stmt = $conn->prepare("INSERT INTO customers (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $password_hash);

            if ($stmt->execute()) {
                $message = "Registration successful!";
            } else {
                $message = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
        $check_email->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'header.php';?>
    <style>
         #whole-div {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .signup-container {
            background: #f4f4f4;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width:400px;
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

        .signup-btn {
            background: #F76100;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .signup-btn:hover {
            color: #F76100;
           background-color: #fff;
          border: 1px solid #F76100;      
  }

        .message {
            margin-top: 10px;
            font-size: 14px;
            color: red;
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
    <script>
        function showMessage(message) {
            if (message) {
                alert(message);
            }
        }
    </script>
</head>
<body onload="showMessage('<?php echo $message; ?>')">


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
                        <span>Register</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <div id="whole-div">
    <div class="signup-container">
        <div class="header__logo">
            <a href="./index.php" style="color: #F76100; font-size: 24px; font-family: 'Arial Black', sans-serif;">Sporty Shop</a>
        </div>
        <h2>Sign Up</h2>
        <form action="" method="post">
            <div class="input-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="signup-btn">Sign Up</button>
        </form>
        <p class="signup">Already have an account? <a href="login.php">Log In</a></p>
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