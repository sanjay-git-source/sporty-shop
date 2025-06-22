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
        $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = "Email already registered!";
        } else {
            // Hash Password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert into Database
            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $password_hash);

            if ($stmt->execute()) {
                $message = "Registration successful!";
            } else {
                $message = "Error: " . $stmt->error;
            }

            $stmt->close();
        }

        $check_stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f4f4f4;
        }

        .signup-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 380px;
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
            padding: 8px;
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
        
p.signup {
    margin-top: 15px;
    font-size: 14px;
    font-weight: bold;
    color: #F76100;
    cursor: pointer;
    transition: color 0.3s ease;
}

p.signup:hover {
    color: #8b0000;
    text-decoration: underline;
}
    </style>
</head>
<body>
    <div class="signup-container">
    <div class="header__logo">
    <a href="../index.php" style="color: #F76100; font-size: 24px; font-family: 'Arial Black', sans-serif;text-decoration: underline">Sporty Shop</a>
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
        <p class="signup">Already have an account? <a href="sporty_shop@admin.php">Log In</a></p>
    </div>
    <script>
    window.onload = function () {
        var message = "<?php echo $message; ?>";
        if (message !== "") {
            alert(message);
        }
    };
</script>

</body>
</html>
