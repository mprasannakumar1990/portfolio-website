<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

$_ENV = parse_ini_file('.env');
$mail = new PHPMailer(true);


// print_r($_POST);
// exit;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Invalid email address."]);
        exit;
    }
    try 
    {
    $mail->isSMTP();
    $mail->Host       = $_ENV['SMTP_HOST'];    // Gmail SMTP server
    $mail->SMTPAuth   = true;                // Enable SMTP authentication
    $mail->Username   = $_ENV['SMTP_USER'];  // Your Gmail address
    $mail->Password   = $_ENV['SMTP_PASS'];  // Use app password if 2FA enabled
    $mail->SMTPSecure = $_ENV['SMTP_SECURE'];  // Use STARTTLS encryption
    $mail->Port       = $_ENV['SMTP_PORT'];  // Port 587 for STARTTLS

    // Set sender and recipient
    $mail->setFrom($_ENV['SMTP_USER'], 'Your Website');
    $mail->addAddress('prasanna@pdmrindia.com'); // Replace with actual recipient email
    $mail->addReplyTo($email, $name);

    // Content
    $mail->isHTML(true);  
    $mail->Subject = $subject;
        $mail->Body    = "<strong>Name:</strong> $name <br>
                          <strong>Email:</strong> $email <br>
                          <strong>Message:</strong> <p>$message</p>";

    // SMTP Debugging - Increase to 2 or 3 for more detailed output
    $mail->SMTPDebug = 2;

    // Send email
    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
}
?>
