<?php
include('connection.php');
session_start();

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT user_id, email, password_hash FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password_hash'])) {  
            $otp = random_int(100000, 999999);

            $_SESSION['otp'] = $otp;
            $_SESSION['user'] = $email;

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

                $mail->setFrom('sanjaymech2310@gmail.com', 'Sporty Shop');
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
                error_log("Mailer Error: {$mail->ErrorInfo}");
                echo "<script>alert('An error occurred while sending OTP. Please contact support.'); window.location.href='sporty_shop@admin.php';</script>";
            }
        } else {
            echo "<script>alert('Incorrect password.'); window.location.href='sporty_shop@admin.php';</script>";
        }
    } else {
        echo "<script>alert('Email not found.'); window.location.href='sporty_shop@admin.php';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
