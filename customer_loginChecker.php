<?php
session_start();
header("Content-Security-Policy: default-src 'self'");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");

include('connection.php');
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    // Validate inputs
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header("Location: login.php");
        exit();
    }

    // Rate limiting
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_login_attempt'] = time();
    }

    if ($_SESSION['login_attempts'] > 5 && (time() - $_SESSION['last_login_attempt']) < 3600) {
        $_SESSION['error'] = "Too many attempts. Try again later.";
        header("Location: login.php");
        exit();
    }

    // Database query
    try {
        $sql = "SELECT user_id, name, email, password FROM customers WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Generic error message to prevent email enumeration
            $_SESSION['error'] = "Check Email and Password";
            $_SESSION['login_attempts']++;
            header("Location: login.php");
            exit();
        }

        $row = $result->fetch_assoc();

        
        if (password_verify($password, $row['password'])) {
            // Generate OTP
            $otp = random_int(100000, 999999);
            $otp_expiry = time() + 300; // 5 minutes expiration

            // Store in session
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_expiry'] = $otp_expiry;
            $_SESSION['user'] = $row['email'];
            $_SESSION['userName'] = $row['name'];
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['login_attempts'] = 0; // Reset attempts

            // Send OTP via email
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'sanjaymech2310@gmail.com';
                $mail->Password = '';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];

                $mail->setFrom('sanjaymech2310@gmail.com', 'sanjay kumar');
                $mail->addAddress($email);
                $mail->Subject = 'Your Login OTP Code';
                
                // HTML email template
                $mail->isHTML(true);
                $mail->Body = "
                    <html>
                    <body>
                        <h2>Login Verification</h2>
                        <p>Your OTP code is: <strong>$otp</strong></p>
                        <p>This code will expire in 5 minutes.</p>
                        <hr>
                        <p>If you didn't request this, please contact support.</p>
                    </body>
                    </html>
                ";

                $mail->send();
                header("Location: verify_otp.php");
                exit();
            } catch (Exception $e) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
                $_SESSION['error'] = "Failed to send OTP. Please try again.";
                header("Location: login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid credentials";
            $_SESSION['login_attempts']++;
            header("Location: login.php");
            exit();
        }
    } catch (Exception $e) {
        error_log("Database Error: " . $e->getMessage());
        $_SESSION['error'] = "A system error occurred. Please try again later.";
        header("Location: login.php");
        exit();
    } finally {
        $stmt->close();
        $conn->close();
    }
} else {
    header("Location: login.php");
    exit();
}
?>