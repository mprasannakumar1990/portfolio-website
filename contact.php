<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load Composer's autoloader
require 'vendor/autoload.php';


$_ENV = parse_ini_file('.env');
$mail = new PHPMailer(true);

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

    try {
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'];
        $mail->Password   = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
        $mail->Port       = $_ENV['SMTP_PORT'];

        // Set sender and recipient
        $mail->setFrom($_ENV['SMTP_USER'], 'Prasanna Portfolio');
        $mail->addAddress('prasanna@pdmrindia.com'); // Replace with actual recipient email
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = "<strong>Name:</strong> $name <br>
                          <strong>Email:</strong> $email <br>
                          <strong>Message:</strong> <p>$message</p>";

        $mail->send();
        echo json_encode(["success" => true, "message" => "Message has been sent successfully."]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
}
?>